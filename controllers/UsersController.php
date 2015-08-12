<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use \app\models\LoginForm;
use app\models\Users;
use Yii;

class UsersController extends \yii\web\Controller {

    public function behaviors() {
        if (!\Yii::$app->admin->identity) {
            $access = ['access' => [
                    'class' => AccessControl::className(),
                    'user' => 'user',
                    'rules' => [
                        [
                            'actions' => ['logout', 'index', 'edit'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['login', 'registration', 'email-confirmation', 'confirm', 'reset-password', 'reset-password-notifications', 'reset'],
                            'allow' => true,
                            'roles' => ['?']
                        ],
                    ],
                    'denyCallback' => function($rule, $action) {
                if (Yii::$app->user->isGuest) {
                    $this->redirect('/users/login');
                } else {
                    $this->redirect('/users/index');
                }
            }
            ]];
        } else {
            $access = [
                'access' => [
                    'class' => AdminAccessControl::className(),
                    'user' => 'admin',
                    'rules' => [
                        [
                            'actions' => ['index', 'edit'],
                            'allow' => true,
                            'roles' => ['@'],
                        ]
                    ],
                    'denyCallback' => function($rule, $action) {
                $this->redirect('/admins/index');
            }
                ]
            ];
        }
        return $access;
    }

    public function actionEdit() {
        return $this->render('edit');
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionError() {
        return $this->render('index');
    }

    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/users/index');
        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionRegistration() {
        $model = new \app\models\Users();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $email = \Yii::$app->mailer->compose('confirmEmail', ['user' => $model])
                    ->setTo($model->email)
                    ->setFrom(['admin@email.com' => \Yii::$app->name . ' robot'])
                    ->setSubject('E-mail confirmation')
                    ->send();

            if ($email) {
                Yii::$app->getSession()->setFlash('success', 'Please check your email address!');
                return $this->render('/users/emailConfirmation', ['email' => $model->email]);
            } else {
                Yii::$app->getSession()->setFlash('warning', 'Failed, contact Admin!');
                return $this->render('registration', ['model' => $model]);
            }
        } else {
            return $this->render('registration', ['model' => $model]);
        }
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        $this->goHome();
    }

    public function actionEmailConfirmation($email = false) {
        if ($email) {
            return $this->render('/users/emailConfirmation', ['email' => $email]);
        } else {
            return $this->redirect('/users/login');
        }
    }

    public function actionConfirm($id, $key) {
        $user = \app\models\Users::find()->where([
                    'id' => $id,
                    'activation_token' => $key,
                    'active' => 0,
                ])->one();
        if (!empty($user)) {
            $user->active = 1;
            $user->activation_token = null;
            $user->save(false);
            Yii::$app->getSession()->setFlash('success', 'Your account successfully activated.');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Invalid token.');
        }
        $this->redirect('/users/login');
    }

    public function actionResetPassword($id = false, $key = false) {
        $model = new Users();
        if (Yii::$app->request->post('Users')) {
            $email = Yii::$app->request->post('Users')['email'];
            $model = $model->findByEmail($email);
            if ($model) {
                if ($model->active === 0) {
                    Yii::$app->getSession()->setFlash('warning', 'Your account is not activated.');
                    return $this->render('resetPassword', ['model' => $model]);
                }
                $model->generatePasswordResetToken();
                $model->save(false);
                $email = \Yii::$app->mailer->compose('resetPassword', ['user' => $model])
                        ->setTo($model->email)
                        ->setFrom(['admin@email.com' => \Yii::$app->name . ' robot'])
                        ->setSubject('Password reset')
                        ->send();
                if ($email) {
                    Yii::$app->getSession()->setFlash('success', 'Please check your email address.');
                    return $this->render('/users/resetPasswordNotifications');
                } else {
                    Yii::$app->getSession()->setFlash('warning', 'Failed,please contact to Admin.');
                    return $this->render('resetPassword', ['model' => $model]);
                }
            }else{
                $model = new Users();
                Yii::$app->getSession()->setFlash('warning', 'Incorrect email address.');
            }
        }
        return $this->render('resetPassword', ['model' => $model]);
    }

    public function actionResetPasswordNotifications() {
        
    }

    public function actionReset($id, $key) {
        $user = \app\models\Users::find()->where([
                    'id' => $id,
                    'password_reset_token' => $key,
                    'active' => 1,
                ])->one();

        if (!empty($user)) {
            if (Yii::$app->request->post('Users')) {
                $user->password = Yii::$app->request->post('Users')['password'];
                $user->confirm_password = Yii::$app->request->post('Users')['confirm_password'];
                $user->password_reset_token = null;
                $user->scenario = 'resetPassword';
                if ($user->save()) {
                    \Yii::$app->getSession()->setFlash('success', 'Your new password successfully saved.');
                    return $this->redirect('/users/login');
                } else {
                    return $this->render('reset', ['model' => $user]);
                }
            }
            $user->password = '';
            return $this->render('reset', ['model' => $user]);
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Invalid token.');
        }
        $this->redirect('/users/login');
    }

}
