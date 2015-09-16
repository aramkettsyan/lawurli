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
                            'actions' => ['load-news'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => [
                            //todo
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
            $limit = 10;
            $newsResources = NewsResources::findeResources($limit);
            $resources = [];
            $urls = [];
            foreach ($newsResources as $resourcesKey => $resourcesValue) {
                $urls[] = $resourcesValue['resource_url'];
            }
            $content = $this->get_data($urls);
            foreach ($content as $c) {
                try {
                    $xmlObject = new \SimpleXmlElement($c);
                } catch (Exception $ex) {
                    
                }

                $resources[] = $xmlObject;
            }

//            print_r($resources);
//            die;


            $newsArray = [];
            $k = 0;
            foreach ($resources as $resource) {
                $j = 0;
                if (is_object($resource->channel->item)) {

                    foreach ($resource->channel->item as $item) {
                        if ($j === 15) {
                            break;
                        }
                        $newsArray[$k]['title'] = (string) $item->title;
                        $newsArray[$k]['link'] = (string) $item->link;
                        $newsArray[$k]['pubDate'] = (isset($item->pubDate) && !empty($item->pubDate)) ? (string) $item->pubDate : (string) '';
                        if (!empty($newsArray[$k]['pubDate'])) {
                            $date = new DateTime($newsArray[$k]['pubDate']);

                            $newsArray[$k]['pubDate'] = $date->format("d-m-Y H:i:s");
                        }
                        $k++;
                        $j++;
                    }
                    usort($newsArray, function($a, $b) {
                        return strcmp($b['pubDate'], $a['pubDate']);
                    });
//                    $k = 0;
//                    foreach ($newsArray as $news) {
//                        if (!empty($newsArray[$k]['pubDate'])) {
//                            $curentTime = time();
//                            $time = strtotime($newsArray[$k]['pubDate']);
//                            if (($curentTime - $time) < 3600) {
//                                $newsArray[$k]['pubDate'] = floor(($curentTime - $time) / 60);
//                            }else if(($curentTime - $time) < 86400){
//                                $newsArray[$k]['pubDate'] = 'Today '.date("H:i:s", strtotime($newsArray[$k]['pubDate']));
//                            } else {
//                                $newsArray[$k]['pubDate'] = date("d-m-Y H:i:s", strtotime($newsArray[$k]['pubDate']));
//                            }
//                        }
//                        $k++;
//                    }
                }
            }
            $current_page = 0;
            \Yii::$app->session->writeSession('news', $newsArray);
        } else {
            $current_page = $page;
            $newsArray = \Yii::$app->session->readSession('news');
        }

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
