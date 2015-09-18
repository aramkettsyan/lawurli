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

    public function actionAddNewsUrl($url) {
        $newsModel = new NewsResources();
        $newsModel->resource_url = $url;
        $newsModel->save();
    }

}
