<?php

/**
 * Created by PhpStorm.
 * User: artur
 * Date: 9/3/15
 * Time: 11:08 AM
 */

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use app\models\NewsResources;
use DateTime;

class NewsController extends \yii\web\Controller {

    public function behaviors() {
        if (!\Yii::$app->admin->identity) {
            $access = ['access' => [
                    'class' => AccessControl::className(),
                    'user' => 'user',
                    'rules' => [
                        [
                            'actions' => [
                                'load-news',
                                'cron'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => [
                                'cron'
                            ],
                            'allow' => true,
                            'roles' => ['?']
                        ],
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\ForbiddenHttpException();
            }
            ]];
        } else {
            $access = [
                'access' => [
                    'class' => AdminAccessControl::className(),
                    'user' => 'admin',
                    'rules' => [
                        [
                            'actions' => [
                            //todo
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ]
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\ForbiddenHttpException();
            }
                ]
            ];
        }
        return $access;
    }

    public function actionLoadNews() {
        $this->layout = false;
        if (!\Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $page = \Yii::$app->request->get('page');
        if ($page === NULL) {
            $current_page = 0;
        } else {
            $current_page = $page;
        }

        $model = new \app\models\News();
        $news = $model->find()->all();
        $newsArray = [];
        $k = 0;
        foreach ($news as $n) {
            $newsArray[$k]['title'] = $n->news_title;
            $newsArray[$k]['link'] = $n->news_url;
            $newsArray[$k]['pubDate'] = $n->news_pub_date;
            $k++;
        }
        usort($newsArray, function($a, $b) {
            return strcmp($b['pubDate'], $a['pubDate']);
        });




        $newsCount = count($newsArray);
        $pageSize = 10;
        if ($page) {
            $pageFrom = $page * $pageSize;
        } else {
            $pageFrom = 0;
        }
        $pagesCount = ceil($newsCount / $pageSize);
        $pageNews = array_slice($newsArray, $pageFrom, $pageSize);
        return $this->render('load-news', [
                    'resources' => $pageNews,
                    'pagesCount' => $pagesCount,
                    'current_page' => $current_page,
                    'pageSize' => $pageSize
        ]);
    }

    public function actionCron($token = false) {
        $cToken = md5('stdevcron');
        if ($token && $token === $cToken) {
            $this->layout = false;
            $limit = 10000;
            $newsResources = NewsResources::findeResources($limit);
            $resources = [];
            $urls = [];
            foreach ($newsResources as $resourcesKey => $resourcesValue) {
                $urls[] = $resourcesValue['resource_url'];
            }
            $content = $this->get_data($urls);
            foreach ($content as $c) {
                try {
                    libxml_use_internal_errors(true);
                    $xmlObject = simplexml_load_string($c);
                    $xml = explode("\n", $c);

                    if (!$xmlObject) {
                        $errors = libxml_get_errors();

                        foreach ($errors as $error) {
//                            echo display_xml_error($error, $xml);
                        }

                        libxml_clear_errors();
                    }
                } catch (Exception $ex) {
                    
                }

                $resources[] = $xmlObject;
            }

            $newsArray = [];
            $rows = [];
            $k = 0;
            foreach ($resources as $resource) {
                $j = 0;
                if (is_object($resource) && is_object($resource->channel) && is_object($resource->channel->item)) {

                    foreach ($resource->channel->item as $item) {
                        if ($j === 15) {
                            break;
                        }
                        $newsArray[$k]['title'] = (string) $item->title;
                        $newsArray[$k]['link'] = (string) $item->link;
                        $newsArray[$k]['pubDate'] = (isset($item->pubDate) && !empty($item->pubDate)) ? (string) $item->pubDate : (string) '';
                        if (!empty($newsArray[$k]['pubDate'])) {
                            $date = new DateTime($newsArray[$k]['pubDate']);
                            $date->setTimezone(new \DateTimeZone('Europe/London'));
                            $newsArray[$k]['pubDate'] = $date->format("d-m-Y H:i:s");
                        }

                        $cDate = new \yii\db\Expression('NOW()');
                        $rows[] = [
                            'news_title' => (string) $item->title,
                            'news_pub_date' => $newsArray[$k]['pubDate'],
                            'news_url' => (string) $item->link,
                            'created' => $cDate,
                            'modified' => $cDate
                        ];

                        $k++;
                        $j++;
                    }
                } else if (is_object($resource) && is_object($resource->entry)) {
                    foreach ($resource->entry as $item) {
                        if ($j === 15) {
                            break;
                        }
                        $newsArray[$k]['title'] = (string) $item->title;
                        $newsArray[$k]['link'] = (string) $item->link;
                        $newsArray[$k]['pubDate'] = (isset($item->published) && !empty($item->published)) ? (string) $item->published : (string) '';
                        if (!empty($newsArray[$k]['pubDate'])) {
                            $date = new DateTime($newsArray[$k]['pubDate']);
                            $date->setTimezone(new \DateTimeZone('Europe/London'));
                            $newsArray[$k]['pubDate'] = $date->format("d-m-Y H:i:s");
                        }

                        $cDate = new \yii\db\Expression('NOW()');
                        $rows[] = [
                            'news_title' => (string) $item->title,
                            'news_pub_date' => $newsArray[$k]['pubDate'],
                            'news_url' => (string) $item->link,
                            'created' => $cDate,
                            'modified' => $cDate
                        ];

                        $k++;
                        $j++;
                    }
                }
            }
            $model = new \app\models\News();
            $model->deleteAll();
            \Yii::$app->db->createCommand('ALTER TABLE news AUTO_INCREMENT = 1')->execute();
            \Yii::$app->db->createCommand()->batchInsert('news', ['news_title', 'news_pub_date', 'news_url', 'created', 'modified'], $rows)->execute();
        } else {
            throw new \yii\web\NotFoundHttpException();
        }

        return;
    }

    public function actionAddNewsUrl($url) {
        $newsModel = new NewsResources();
        $newsModel->resource_url = $url;
        $newsModel->save();
    }

    protected function get_data($data) {
        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

            // post?
            if (is_array($d)) {
                if (!empty($d['post'])) {
                    curl_setopt($curly[$id], CURLOPT_POST, 1);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                }
            }

            // extra options?
            if (!empty($options)) {
                curl_setopt_array($curly[$id], $options);
            }

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // get content and remove handles
        foreach ($curly as $id => $c) {
            $result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }

}
