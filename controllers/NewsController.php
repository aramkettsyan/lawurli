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

    public function actionLoadNews(){
        $this->layout = false;
        $page = (\Yii::$app->request->get('page') ? \Yii::$app->request->get('page') : 1);
        $limit = $page * 3;
        $newsResources = NewsResources::findeResources($limit);
        $resources = [];
        foreach($newsResources as $resourcesKey => $resourcesValue ){
            $content = file_get_contents($resourcesValue["resource_url"]);
            $xmlObject = new \SimpleXmlElement($content);

            $resources[] = $xmlObject;
        }
        return $this->render('load-news', ['resources' => $resources]);

    }
}