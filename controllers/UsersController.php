<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use \app\models\LoginForm;
use app\models\Users;
use \app\models\Sections;
use \app\models\Forms;
use app\models\UserForms;
use Yii;

class UsersController extends \yii\web\Controller {

    public function behaviors() {
        if (!\Yii::$app->admin->identity) {
            $access = ['access' => [
                    'class' => AccessControl::className(),
                    'user' => 'user',
                    'rules' => [
                        [
                            'actions' => ['logout', 'index', 'edit', 'profile', 'upload-image'],
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

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionEdit($id = false) {
        if ($id === false && !\Yii::$app->user->isGuest) {

            $user = Users::findOne(['id' => Yii::$app->user->identity->id]);

            $connection = Yii::$app->db;
            $sections = $connection->createCommand('SELECT '
                            . ' sections.name as sectionName,'
                            . 'sub_sections.name as subName,'
                            . 'sub_sections.multiple as subMultiple,'
                            . 'sub_sections.id as subId,'
                            . 'forms.id as formId,'
                            . 'forms.label as formLabel,'
                            . 'forms.type as formType,'
                            . 'forms.placeholder as formPlaceholder,'
                            . 'forms.numeric as formNumeric,'
                            . 'forms.options as formOptions '
                            . 'FROM sections '
                            . 'LEFT JOIN sub_sections '
                            . 'ON sub_sections.section_id = sections.id '
                            . 'LEFT JOIN forms '
                            . 'ON forms.sub_section_id = sub_sections.id '
                            . 'ORDER BY sections.id,sub_sections.id,forms.id ')->queryAll();
            $user_forms = $connection->createCommand("SELECT user_forms.form_id,user_forms.index,user_forms.value,sub_sections.id as subSectionId FROM user_forms "
                            . "LEFT JOIN forms ON user_forms.form_id = forms.id "
                            . "LEFT JOIN sub_sections ON forms.sub_section_id = sub_sections.id "
                            . "WHERE user_id =" . Yii::$app->user->identity->id)->queryAll();

            $new_user_forms = [];
            foreach ($user_forms as $user_form) {
                if (isset($new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']])) {
                    if (!is_array($new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']])) {
                        $new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']] = [$new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']]];
                    }
                    $new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']][] = $user_form['value'];
                } else {
                    $new_user_forms[$user_form['subSectionId']][$user_form['index']][$user_form['form_id']] = $user_form['value'];
                }
            }

            $newSections = [];
            foreach ($sections as $section) {

                if (!isset($newSections[$section['sectionName']])) {
                    $newSections[$section['sectionName']] = [];
                }

                if (!isset($newSections[$section['sectionName']][$section['subName']])) {
                    $newSections[$section['sectionName']][$section['subName']] = [
                        [
                            'subMultiple' => $section['subMultiple'],
                            'subId' => $section['subId']
                        ],
                        [
                            'formId' => $section['formId'],
                            'formLabel' => $section['formLabel'],
                            'formType' => $section['formType'],
                            'formPlaceholder' => $section['formPlaceholder'],
                            'formNumeric' => $section['formNumeric'],
                            'formOptions' => $section['formOptions']
                        ]
                    ];
                } else {
                    $newSections[$section['sectionName']][$section['subName']][] = [
                        'formId' => $section['formId'],
                        'formLabel' => $section['formLabel'],
                        'formType' => $section['formType'],
                        'formPlaceholder' => $section['formPlaceholder'],
                        'formNumeric' => $section['formNumeric'],
                        'formOptions' => $section['formOptions']
                    ];
                }
            }
            if (Yii::$app->request->post()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $post = Yii::$app->request->post();
                    if (empty($post['Users']['password'])) {
                        unset($post['Users']['password']);
                        unset($post['Users']['confirm_password']);
                    }
                    $old_user_forms = [];
                    foreach ($user_forms as $user_form) {
                        if (isset($old_user_forms[$user_form['form_id']][$user_form['index']])) {
                            if (!is_array($old_user_forms[$user_form['form_id']][$user_form['index']])) {
                                $old_user_forms[$user_form['form_id']][$user_form['index']] = [$old_user_forms[$user_form['form_id']][$user_form['index']]];
                            }
                            $old_user_forms[$user_form['form_id']][$user_form['index']][] = $user_form['value'];
                        } else {
                            $old_user_forms[$user_form['form_id']][$user_form['index']] = $user_form['value'];
                        }
                    }
                    $user->old_password = $user->password;
                    $user->scenario = 'update';
                    if ($user->load($post) && $user->save()) {
                        $custom_fields = $post['Users']['custom_fields'];
                        $newArr = [];
                        $updateArr = [];
                        $user_forms_delete = $user_forms;
                        $r = 0;
                        foreach ($custom_fields as $key => $custom_field) {
                            foreach ($custom_field as $k => $field) {
                                if (empty($field)) {
                                    continue;
                                }
                                if (!is_array($field)) {
                                    $token = false;
                                    if (isset($old_user_forms[$key][$k]) && $old_user_forms[$key][$k] === $field) {
                                        $updateArr[] = [Yii::$app->user->identity->id, $key, $k, $field, '2015-08-11 00:00:00', '2015-08-11 00:00:00'];
                                        $token = true;
                                    }
                                    if ($token === false) {
                                        $newArr[] = [Yii::$app->user->identity->id, $key, $k, $field, '2015-08-11 00:00:00', '2015-08-11 00:00:00'];
                                    }
                                } else {
                                    $token = false;
                                    foreach ($field as $f) {
                                        if (isset($old_user_forms[$key][$k]) && in_array($f, $old_user_forms[$key][$k])) {
                                            $updateArr[] = [Yii::$app->user->identity->id, $key, $k, $f, '2015-08-11 00:00:00', '2015-08-11 00:00:00'];
                                            $token = true;
                                        }
                                        if ($token === false) {
                                            $newArr[] = [Yii::$app->user->identity->id, $key, $k, $f, '2015-08-11 00:00:00', '2015-08-11 00:00:00'];
                                        }
                                    }
                                }
                                $r++;
                            }
                        }
                        if (!empty($newArr)) {
                            if (Yii::$app->db->createCommand()->batchInsert('user_forms', ['user_id', 'form_id', 'index', 'value', 'created', 'modified'], $newArr)->execute()) {
                                $transaction->commit();
                                $this->refresh();
                            }else{
                                $transaction->rollBack();
                            }
                        }
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
            if ($user) {
                return $this->render('/users/edit', [
                            'user' => $user,
                            'sections' => $newSections,
                            'user_forms' => $new_user_forms
                ]);
            } else {
                return $this->redirect('index');
            }
        }
    }

    public function actionUploadImage($file_name = false) {
        if (isset($_GET['qqfile'])) {
            $fileName = $_GET['qqfile'];
        } elseif (isset($_FILES['qqfile'])) {
            $fileName = $_FILES['qqfile']['name'];
        }

        $explode = explode('.', $fileName);
        $ext = end($explode);
        $allowed = array('png', 'jpg', 'jpeg', 'gif');
        if (!in_array($ext, $allowed)) {
            $response['success'] = false;
            $response['error'] = "Not supported format";
            echo json_encode($response);
            die();
        }

        $up = Yii::$app->FileUploader->upload(\Yii::getAlias('@web') . 'images/users_images/');

        if (isset($up['error'])) {
            $response['success'] = false;
            $response['error'] = $up['error'];
        } else {
            $ext = end(explode('.', $up['filename']));
            $newFileName = \Yii::$app->security->generateRandomString(32) . '.' . $ext;
            rename(\Yii::getAlias('@web') . 'images/users_images/' . $up['filename'], \Yii::getAlias('@web') . 'images/users_images/' . $newFileName);
            $up['filename'] = $newFileName;
            $model = \app\models\Users::findOne(['id' => \Yii::$app->user->identity->id]);
            if (!$model) {
                $response['success'] = false;
                $response['error'] = 'The image isn\'t saved!';
                return $response;
            } else {
                $oldLogo = $model->image;
            }
            $model->image = $up['filename'];
            $model->scenario = 'updateImage';
            if ($model->save()) {
                if ($oldLogo !== $model->default_image && !empty($oldLogo) && is_file(\Yii::getAlias('@web') . 'images/users_images/' . $oldLogo)) {
                    unlink(\Yii::getAlias('@web') . 'images/users_images/' . $oldLogo);
                }
                $response['fileName'] = $up['filename'];
                $response['success'] = true;
            } else {
                if (is_file(\Yii::getAlias('@web') . 'images/users_images/' . $up['filename'])) {
                    unlink(\Yii::getAlias('@web') . 'images/users_images/' . $up['filename']);
                }
                $response['success'] = false;
                $response['error'] = 'The image isn\'t saved!';
            }
        }

        $response = json_encode($response);

        return $response;
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
            $user->save();
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
                $model->save();
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
            } else {
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

    public function actionProfile($id = false) {
        if ($id === false && !\Yii::$app->user->isGuest) {
            $user = Users::findOne(['id' => \Yii::$app->user->identity->id]);
            if ($user) {
                return $this->render('/users/profile', ['model' => $user]);
            }
        }
    }

}
