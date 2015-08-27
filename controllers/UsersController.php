<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use \app\models\LoginForm;
use app\models\Users;
use \app\models\Forms;
use yii\data\Pagination;
use app\models\Request;
use Yii;

class UsersController extends \yii\web\Controller {

    public $pageSize = 10;
    public $models;

    public function behaviors() {
        if (!\Yii::$app->admin->identity) {
            $access = ['access' => [
                    'class' => AccessControl::className(),
                    'user' => 'user',
                    'rules' => [
                        [
                            'actions' => ['logout',
                                'search',
                                'edit',
                                'profile',
                                'upload-image',
                                'index',
                                'connect',
                                'accetpt',
                                'decline',
                                'load-detailed',
                                'load-general',
                                'load-colleagues',
                                'load-notifications',
                                'error'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['index',
//                                'search',
                                'confirm',
                                'reset-password',
                                'reset-password-notifications',
                                'reset',
//                                'profile',
                                'error'
                            ],
                            'allow' => true,
                            'roles' => ['?']
                        ],
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\NotFoundHttpException();
            }
            ]];
        } else {
            $access = [
                'access' => [
                    'class' => AdminAccessControl::className(),
                    'user' => 'admin',
                    'rules' => [
                        [
                            'actions' => ['index', 'edit', 'profile', 'search', 'error'],
                            'allow' => true,
                            'roles' => ['@'],
                        ]
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\NotFoundHttpException();
            }
                ]
            ];
        }
        return $access;
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action) {

        $action_id = \Yii::$app->controller->action->id;

        //404 error

        if ($action_id === 'error') {
            $this->layout = 'error';
        }

        //end 404 error
        //login and registration


        $actions = [
            'index',
            'profile',
            'search'
        ];

        if (in_array($action_id, $actions) && \Yii::$app->user->isGuest) {
            $id = false;
            $key = false;
            $act = false;
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            if (isset($_GET['key'])) {
                $key = $_GET['key'];
            }
            if (isset($_GET['action'])) {
                $act = $_GET['action'];
            }
            $resetModel = new Users();
            $user_reset = new Users();
            $user_reset->scenario = 'resetPassword';
            $registrationModel = new Users();
            $registrationModel->scenario = 'create';
            $model = new LoginForm();
            if ($act === 'reset_password') {
                if (Yii::$app->request->post('Users')) {
                    $reset_action = 'users/' . $action_id;
                    \Yii::$app->getSession()->writeSession('resetPassword', true);
                    $resetModel = $this->actionResetPassword($id, $key, $reset_action);
                }
            }

            if ($act === 'reset') {
                $u = $this->actionReset($id, $key);
                if ($u === true) {
                    Yii::$app->getSession()->writeSession('showLogin', true);
                    return $this->redirect('/users/index');
                } else {
                    $user_reset = $u;
                }
            }


            if ($act !== 'reset' && $act !== 'reset_password' && Yii::$app->request->post('Users')) {
                $registrationModel = $this->actionRegistration();
            } else if (Yii::$app->request->post('LoginForm')) {
                $model = $this->actionLogin();
            }
            \Yii::$app->view->params['registrationModel'] = $registrationModel;
            \Yii::$app->view->params['resetModel'] = $resetModel;
            \Yii::$app->view->params['user_reset'] = $user_reset;
            \Yii::$app->view->params['model'] = $model;
            \Yii::$app->view->params['id'] = $id;
            $this->models = [
                'registrationModel' => $registrationModel,
                'resetModel' => $resetModel,
                'user_reset' => $user_reset,
                'model' => $model,
                'id' => $id,
            ];
        } else {
            $this->models = [];
        }

        //end login and registration

        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->identity->id;
            $user = Users::findOne(['id' => $id]);
            \Yii::$app->view->params['current_user'] = $user;
        }

        \Yii::$app->view->params['logo'] = $this->getLogo();
        $this->enableCsrfValidation = false;
        if (!\Yii::$app->user->isGuest) {
            $notify = $this->getNotifications($pageSize = 6);
            Yii::$app->view->params['notify'] = $notify[1];
            Yii::$app->view->params['notifyCount'] = Users::getNotificationCount();
        }
        return parent::beforeAction($action);
    }

    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->refresh();
        } else {
            Yii::$app->getSession()->writeSession('showLogin', true);
        }
        return $model;
    }

    public function actionRegistration() {
        $registrationModel = new Users();
        $registrationModel->scenario = 'create';
        if ($registrationModel->load(Yii::$app->request->post()) && $registrationModel->save()) {
            $email = \Yii::$app->mailer->compose('confirmEmail', ['user' => $registrationModel])
                    ->setTo($registrationModel->email)
                    ->setFrom(['admin@email.com' => \Yii::$app->name])
                    ->setSubject('E-mail confirmation')
                    ->send();

            if ($email) {
                Yii::$app->getSession()->setFlash('registrationSuccess', 'Please check your mail inbox (spam) folder for account activation.');
                $registrationModel = new Users();
            } else {
                Yii::$app->getSession()->setFlash('registrationWarning', 'Error,please try again!');
                $registrationModel->password = '';
                $registrationModel->confirm_password = '';
            }
        }
        Yii::$app->getSession()->writeSession('showRegistration', true);

        return $registrationModel;
    }

    public function actionEdit($action = 'general', $id = false) {
        if ($id === false && !\Yii::$app->user->isGuest) {
            if ($action === 'detailed') {
                return $this->actionLoadDetailedEdit();
            } else {
                return $this->actionLoadGeneralEdit();
            }
        } else {
            throw new \yii\web\NotFoundHttpException();
        }
    }

    public function actionLoadGeneralEdit() {

        $user = Users::findOne(['id' => Yii::$app->user->identity->id]);

        if (Yii::$app->request->post()) {

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();
                if (empty($post['Users']['password'])) {
                    unset($post['Users']['password']);
                    unset($post['Users']['confirm_password']);
                }

                $user->old_password = $user->password;
                $user->scenario = 'update';
                if ($user->load($post) && $user->save()) {
                    $transaction->commit();
                    return $this->redirect('/users/profile');
                } else {
                    $transaction->rollBack();
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }

        \Yii::$app->view->params['user'] = $user;
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            return $this->render('load-general', ['user' => $user]);
        }

        return $this->render('/users/edit', ['user' => $user]);
    }

    public function actionLoadDetailedEdit() {
        $user = Users::findOne(['id' => Yii::$app->user->identity->id]);
        $connection = Yii::$app->db;
        $user_forms = $connection->createCommand("SELECT user_forms.form_id,user_forms.index,user_forms.value,forms.type,sub_sections.id as subSectionId FROM user_forms "
                        . "LEFT JOIN forms ON user_forms.form_id = forms.id "
                        . "LEFT JOIN sub_sections ON forms.sub_section_id = sub_sections.id "
                        . "WHERE user_id =" . Yii::$app->user->identity->id)->queryAll();

        if (Yii::$app->request->post()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();

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

                $forms = Yii::$app->db->createCommand('SELECT forms.id,forms.type,forms.numeric,forms.options'
                                . ' FROM forms')->queryAll();
                $newForms = [];
                foreach ($forms as $form) {
                    $newForms[$form['id']] = [ 'type' => $form['type'], 'options' => $form['options'], 'numeric' => $form['numeric']];
                }
                $custom_fields = $post['Users']['custom_fields'];
                $newArr = [];
                $updateArr = [];
                $user_forms_delete = $user_forms;
                $r = 0;
                $validate = true;
                foreach ($custom_fields as $key => $custom_field) {
                    $k = 0;
                    foreach ($custom_field as $field) {
                        if (empty($field)) {
                            continue;
                        }
                        if (!is_array($field)) {
                            $token = false;
                            if (isset($old_user_forms[$key][$k]) && $old_user_forms[$key][$k] === $field) {
                                unset($old_user_forms[$key][$k]);
                                $token = true;
                            }
                            if ($token === false) {
                                $time = new \yii\db\Expression('NOW()');
                                $validate = $this->actionValidateForms($newForms, $key, $field);
                                if (!$validate) {
                                    $transaction->rollBack();
                                    break 2;
                                }
                                $newArr[] = [Yii::$app->user->identity->id, $key, $k, $field, $time, $time];
                            }
                        } else {
                            $token = false;
                            foreach ($field as $i => $f) {
                                if (isset($old_user_forms[$key][$k]) && is_array(isset($old_user_forms[$key][$k])) && in_array($f, $old_user_forms[$key][$k])) {
                                    foreach ($old_user_forms[$key][$k] as $optKey => $opt) {
                                        if ($opt == $f) {
                                            unset($old_user_forms[$key][$k][$optKey]);
                                        }
                                    }
                                    $token = true;
                                }
                                if ($token === false) {
                                    $time = new \yii\db\Expression('NOW()');
                                    $validate = $this->actionValidateForms($newForms, $key, $f);
                                    if (!$validate) {
                                        $transaction->rollBack();
                                        break 2;
                                    }
                                    $newArr[] = [Yii::$app->user->identity->id, $key, $k, $f, $time, $time];
                                }
                            }
                        }
                        $k++;
                        $r++;
                    }
                }


                if ($validate === true) {
                    $deleteFormId = [];
                    $deleteIndex = [];
                    $deleteValue = [];
                    foreach ($old_user_forms as $key_old_user_form => $old_user_form) {
                        if (is_array($old_user_form)) {
                            foreach ($old_user_form as $key_ouf => $ouf) {
                                if (!empty($ouf)) {
                                    if (is_array($ouf)) {
                                        foreach ($ouf as $o) {
                                            $deleteFormId[] = $key_old_user_form;
                                            $deleteIndex[] = $key_ouf;
                                            $deleteValue[] = $o;
                                        }
                                    } else {
                                        $deleteFormId[] = $key_old_user_form;
                                        $deleteIndex[] = $key_ouf;
                                        $deleteValue[] = $ouf;
                                    }
                                }
                            }
                        }
                    }
                    $deleteUserId = Yii::$app->user->identity->id;
                    $deleteFailed = false;
                    foreach ($deleteFormId as $key => $del) {
                        $sql = Yii::$app->db->createCommand("DELETE FROM `user_forms` WHERE `user_id` = :user_id AND `form_id`=:form_id AND `index`=:index AND `value`=:value ");
                        $sql->bindParam(':user_id', $deleteUserId);
                        $sql->bindParam(':form_id', $deleteFormId[$key]);
                        $sql->bindParam(':index', $deleteIndex[$key]);
                        $sql->bindParam(':value', $deleteValue[$key]);
                        if (!$sql->execute()) {
                            Yii::$app->getSession()->writeSession('updateError', 'Can\'t delete form(s), try again.');
                            $deleteFailed = true;
                            $transaction->rollBack();
                            break;
                        }
                    }

                    if (!empty($newArr) && !$deleteFailed) {
                        if (Yii::$app->db->createCommand()->batchInsert('user_forms', ['user_id', 'form_id', 'index', 'value', 'created', 'modified'], $newArr)->execute()) {
                            $transaction->commit();
                            return $this->redirect('/users/profile');
                        } else {
                            Yii::$app->getSession()->writeSession('updateError', 'Can\'t insert form(s), try again.');
                            $transaction->rollBack();
                        }
                    } else if (!$deleteFailed) {
                        $transaction->commit();
                        return $this->redirect('/users/profile');
                    }
                } else {
                    Yii::$app->getSession()->writeSession('updateError', 'There are some error(s) in form(s), try again.');
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
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


        \Yii::$app->view->params['user'] = $user;
        \Yii::$app->view->params['sections'] = $newSections;
        \Yii::$app->view->params['user_forms'] = $new_user_forms;

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            return $this->render('/users/load-detailed', [
                        'user' => $user,
                        'sections' => $newSections,
                        'user_forms' => $new_user_forms
            ]);
        }

        return $this->render('/users/edit', [
                    'user' => $user,
                    'sections' => $newSections,
                    'user_forms' => $new_user_forms
        ]);
    }

    private function actionValidateForms($forms, $formId, $value) {
        $type = $forms[$formId]['type'];
        $numeric = $forms[$formId]['numeric'];
        $options = $forms[$formId]['options'];
        $optionsArray = explode('-,-', $options);
        $validated = true;
        switch ($type) {
            case 'input':
                if (strlen($value) > 255) {
                    $validated = false;
                }
                if ($numeric != 0 && !is_int($value)) {
                    $validated = false;
                }
                break;
            case 'textarea':
                break;
            case 'select':
                if (!in_array($value, $optionsArray)) {
                    $validated = false;
                }
                break;
            case 'radio':
                if (!in_array($value, $optionsArray)) {
                    $validated = false;
                }
                break;
            case 'checkbox':
                if (!in_array($value, $optionsArray)) {
                    $validated = false;
                }
                break;

            default:
                $validated = false;
                break;
        }
        if ($validated === true) {
            return true;
        }
        return false;
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

    public function actionIndex($action = false, $id = false, $key = false) {

        $models = $this->models;

        return $this->render('index', $models);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect('/users/index');
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
            Yii::$app->getSession()->setFlash('success', 'Your account is now activated. Please login.');
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Invalid token.');
        }
        Yii::$app->getSession()->writeSession('showLogin', true);
        $this->redirect('/users/index');
    }

    public function actionResetPassword($id = false, $key = false, $action = false) {
        $model = new Users();
        if (Yii::$app->request->post('Users')) {
            $email = Yii::$app->request->post('Users')['email'];
            $model = $model->findByEmail($email);
            if ($model) {
                if ($model->active === 0) {
                    Yii::$app->getSession()->setFlash('resetWarning', 'Your account is not activated.');
                    return $model;
                }

                if (Yii::$app->getSession()->getHasSessionId('emailTimeout')) {
                    $emailTimeout = Yii::$app->getSession()->readSession('emailTimeout', time());
                    $currentTime = time();
                    $difference = $currentTime - $emailTimeout;
                    if ($difference < 60) {
                        Yii::$app->getSession()->setFlash('resetWarning', 'Please wait ' . (60 - $difference) . ' seconds and try again.');
                        return $model;
                    }
                }
                $model->generatePasswordResetToken();
                $model->save();
                $email = \Yii::$app->mailer->compose('resetPassword', ['user' => $model, 'action' => $action])
                        ->setTo($model->email)
                        ->setFrom(['admin@email.com' => \Yii::$app->name])
                        ->setSubject('Password reset')
                        ->send();
                if ($email) {
                    Yii::$app->getSession()->writeSession('emailTimeout', time());
                    Yii::$app->getSession()->setFlash('resetSuccess', 'Please check your mail inbox (spam) folder.');
                    return $model;
                } else {
                    Yii::$app->getSession()->setFlash('resetWarning', 'Failed,please contact to Admin.');
                    return $model;
                }
            } else {
                $model = new Users();
                Yii::$app->getSession()->setFlash('resetWarning', 'Incorrect email address.');
            }
        }
        return $model;
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
                    return true;
                } else {
                    \Yii::$app->getSession()->writeSession('newPassword', true);
                }
            }
            \Yii::$app->getSession()->writeSession('newPassword', true);
            $user->password = '';
            $user->confirm_password = '';
            return $user;
        } else {
            Yii::$app->getSession()->setFlash('warning', 'Invalid token.');
            return true;
        }
    }

    protected function getLogo() {
        $logo = \Yii::$app->db->createCommand('SELECT `value` FROM `site_settings` WHERE `key`="logo"')->queryOne();
        return $logo['value'];
    }

    public function actionProfile($action = false, $id = false, $key = false) {

        $models = $this->models;

        if ($id) {
            $user = Users::findOne(['id' => (int) $id]);
        } else if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->identity->id;
            $user = Users::findOne(['id' => $id]);
        } else {
            return $this->redirect('/users/index');
        }


        if ($user) {
            $connection = Yii::$app->db;
            $user_forms = $connection->createCommand("SELECT user_forms.form_id,user_forms.index,user_forms.value,forms.type,sub_sections.id as subSectionId "
                            . "FROM user_forms "
                            . "LEFT JOIN forms ON user_forms.form_id = forms.id "
                            . "LEFT JOIN sub_sections ON forms.sub_section_id = sub_sections.id "
                            . "WHERE user_id =" . $id)->queryAll();
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


            $new_user_forms = [];
            foreach ($user_forms as $user_form) {
                $subId = $user_form['subSectionId'];
                $formId = $user_form['form_id'];
                $index = $user_form['index'];

                if (isset($new_user_forms[$subId][$index][$formId])) {
                    if (!is_array($new_user_forms[$subId][$index][$formId])) {
                        $new_user_forms[$subId][$index][$formId] = [$new_user_forms[$subId][$index][$formId]];
                    }
                    $new_user_forms[$subId][$index][$formId][] = $user_form['value'];
                } else {
                    $new_user_forms[$subId][$index][$formId] = $user_form['value'];
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

            if ($id) {
                $relation = Users::checkRelationship($id);
                if ($relation && $relation['user_from_id'] == $id && $relation['request_seen'] == "N") {
                    Request::updateAll(['request_seen' => "Y"], 'request_id =' . $relation['request_id']);
                }
            } else {
                $relation = null;
            }

            $models['user'] = $user;
            $models['user_forms'] = $new_user_forms;
            $models['sections'] = $newSections;
            $models['relation'] = $relation;



            return $this->render('/users/profile', $models);
        } else {
            throw new \yii\web\NotFoundHttpException();
        }
    }

    public function actionSearch($action = false, $id = false, $key = false) {
        $query = trim(\Yii::$app->request->get('query'));
        $first_name = trim(\Yii::$app->request->get('first_name'));
        $last_name = trim(\Yii::$app->request->get('last_name'));
        $first_last = \Yii::$app->request->get('first_last');
        $search = \Yii::$app->request->get('search') ? \Yii::$app->request->get('search') : 'basic';
        if ($query === NULL) {
            throw new \yii\web\NotFoundHttpException();
        }
        $models = $this->models;
        $models['contacts'] = [];
        $models['query_response'] = [];
        $search_result = [];



        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->identity->id;
            $user = Users::findOne(['id' => $id]);
            \Yii::$app->view->params['user'] = $user;

            $models['user'] = $user;
            $contacts = Users::GetContactIds();
            $models['contacts'] = $contacts;
        }

        if ($first_last) {//If defined first name and last name
            $first_name = trim($first_name);
            $query_array_response[0] = trim($first_name);
            if ($last_name !== null) {
                $last_name = trim($last_name);
                $query_array_response[1] = trim($last_name);
            }


            if (empty($first_name) && (isset($last_name) && empty($last_name) || !isset($last_name))) {
                //If first name and last name are empty 
                $and = !Yii::$app->user->isGuest ? 'id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                        . 'WHERE '
                        . $and
                        . 'ORDER BY first_name,last_name ' . $limit);
                $count = $q->queryAll();
                $pages = new Pagination(['totalCount' => count($count), 'pageSize' => $this->pageSize]);
                $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                        . 'WHERE '
                        . $and
                        . 'ORDER BY first_name,last_name ' . $limit);
                $search_result = $q->queryAll();
                $models['pages'] = $pages;
            } else if (!isset($last_name) || empty($last_name)) {
                //If last name is empty 
                $likeQuery = $first_name . '%';
                $query_array = $first_name;
                $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                . 'WHERE (`first_name` LIKE :likeQuery) '
                                . $and
                                . 'ORDER BY (first_name=:query) DESC ' . $limit)
                        ->bindParam(':likeQuery', $likeQuery)
                        ->bindParam(':query', $query_array);

                if ($search === 'advanced') {
                    $search_result = $q->queryAll();
                } else {
                    $count = $q->queryAll();
                    $pages = new Pagination(['totalCount' => count($count), 'pageSize' => $this->pageSize]);
                    $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                    $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                    . 'WHERE (`first_name` LIKE :likeQuery) '
                                    . $and
                                    . 'ORDER BY (first_name=:query) DESC ' . $limit)
                            ->bindParam(':likeQuery', $likeQuery)
                            ->bindParam(':query', $query_array);
                    $search_result = $q->queryAll();
                    $models['pages'] = $pages;
                }
            } else if (!empty($last_name) && !empty($first_name)) {
                //If first name and last name are defined
                $first_name = $first_name;
                $last_name = $last_name;
                $like_first_name = $first_name . '%';
                $like_last_name = $last_name . '%';
                $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $command = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                . 'WHERE (`first_name` LIKE :likeFirstName '
                                . 'AND `last_name` LIKE :likeLastName) '
                                . $and
                                . 'ORDER BY ((first_name = :firstName)+(last_name = :lastName)+if(locate(:firstName,first_name),1,0)+if(locate(:lastName,last_name),1,0)) DESC ' . $limit)
                        ->bindParam(':likeFirstName', $like_first_name)
                        ->bindParam(':likeLastName', $like_last_name)
                        ->bindParam(':firstName', $first_name)
                        ->bindParam(':lastName', $last_name);
                if ($search === 'advanced') {
                    $search_result = $command->queryAll();
                } else {
                    $q = $command->queryAll();
                    $pages = new Pagination(['totalCount' => count($q), 'pageSize' => $this->pageSize]);
                    $limit = 'LIMIT ' . $pages->limit . ' OFFSET ' . $pages->offset;
                    $command = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                    . 'WHERE (`first_name` LIKE :likeFirstName '
                                    . 'AND `last_name` LIKE :likeLastName) '
                                    . $and
                                    . 'ORDER BY ((first_name = :firstName)+(last_name = :lastName)+if(locate(:firstName,first_name),1,0)+if(locate(:lastName,last_name),1,0)) DESC ' . $limit)
                            ->bindParam(':likeFirstName', $like_first_name)
                            ->bindParam(':likeLastName', $like_last_name)
                            ->bindParam(':firstName', $first_name)
                            ->bindParam(':lastName', $last_name);
                    $search_result = $command->queryAll();
                    $models['pages'] = $pages;
                }
            } else {
                //If first name is empty 
                $likeQuery = $last_name . '%';
                $query_array = $last_name;
                $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                . 'WHERE (`last_name` LIKE :likeQuery) '
                                . $and
                                . 'ORDER BY (last_name=:query) DESC ' . $limit)
                        ->bindParam(':likeQuery', $likeQuery)
                        ->bindParam(':query', $query_array);

                if ($search === 'advanced') {
                    $search_result = $q->queryAll();
                } else {
                    $count = $q->queryAll();
                    $pages = new Pagination(['totalCount' => count($count), 'pageSize' => $this->pageSize]);
                    $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                    $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                    . 'WHERE (`last_name` LIKE :likeQuery) '
                                    . $and
                                    . 'ORDER BY (last_name=:query) DESC ' . $limit)
                            ->bindParam(':likeQuery', $likeQuery)
                            ->bindParam(':query', $query_array);
                    $search_result = $q->queryAll();
                    $models['pages'] = $pages;
                }
            }
            \Yii::$app->view->params['first_name'] = $query_array_response[0];
            if (isset($last_name)) {
                \Yii::$app->view->params['last_name'] = $query_array_response[1];
            }
        } else {//If first name and last name undefined
            $query_array = explode(' ', $query, 2);
            if (empty($query_array[0]) && (isset($query_array[1]) && empty($query_array[1]) || !isset($query_array[1]))) {
                //If first name and last name are empty 
                $and = !Yii::$app->user->isGuest ? 'id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $q = \Yii::$app->db->createCommand('SELECT COUNT(id) as count FROM `users` '
                        . 'WHERE '
                        . $and . $limit);
                $count = $q->queryAll();
                $pages = new Pagination(['totalCount' => $count[0]['count'], 'pageSize' => $this->pageSize]);
                $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                        . 'WHERE '
                        . $and
                        . 'ORDER BY first_name,last_name ' . $limit);
                $search_result = $q->queryAll();
                $models['pages'] = $pages;
            } else if (!isset($query_array[1]) || empty($query_array[1])) {
                //If last name is empty 
                $likeQuery = $query_array[0] . '%';
                $query_array = $query_array[0];
                $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : '';
                $limit = '';
                $q = \Yii::$app->db->createCommand('SELECT COUNT(id) as count FROM `users` '
                                . 'WHERE (`first_name` LIKE :likeQuery '
                                . 'OR `last_name` LIKE :likeQuery) '
                                . $and . $limit)
                        ->bindParam(':likeQuery', $likeQuery)
                        ->bindParam(':query', $query_array);


                $count = $q->queryAll();
                $pages = new Pagination(['totalCount' => $count[0]['count'], 'pageSize' => $this->pageSize]);
                $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                . 'WHERE (`first_name` LIKE :likeQuery '
                                . 'OR `last_name` LIKE :likeQuery) '
                                . $and
                                . 'ORDER BY ((first_name=:query)+(last_name=:query)) DESC ' . $limit)
                        ->bindParam(':likeQuery', $likeQuery)
                        ->bindParam(':query', $query_array);
                $search_result = $q->queryAll();
                $models['pages'] = $pages;
            } else {
                //If first name and last name are defined
                if (!empty($query_array[1]) && !empty($query_array[0])) {
                    $first_name = $query_array[0];
                    $last_name = $query_array[1];
                    $like_first_name = $query_array[0] . '%';
                    $like_last_name = $query_array[1] . '%';
                    $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : '';
                    $limit = '';
                    $command = \Yii::$app->db->createCommand('SELECT COUNT(id) as count FROM `users` '
                                    . 'WHERE (`first_name` LIKE :likeFirstName '
                                    . 'OR `last_name` LIKE :likeFirstName '
                                    . 'OR `first_name` LIKE :likeLastName '
                                    . 'OR `last_name` LIKE :likeLastName) '
                                    . $and . $limit)
                            ->bindParam(':likeFirstName', $like_first_name)
                            ->bindParam(':likeLastName', $like_last_name)
                            ->bindParam(':firstName', $first_name)
                            ->bindParam(':lastName', $last_name);
                    $q = $command->queryAll();
                    $pages = new Pagination(['totalCount' => $q[0]['count'], 'pageSize' => $this->pageSize]);
                    $limit = 'LIMIT ' . $pages->limit . ' OFFSET ' . $pages->offset;
                    $command = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                                    . 'WHERE (`first_name` LIKE :likeFirstName '
                                    . 'OR `last_name` LIKE :likeFirstName '
                                    . 'OR `first_name` LIKE :likeLastName '
                                    . 'OR `last_name` LIKE :likeLastName) '
                                    . $and
                                    . 'ORDER BY ((first_name = :firstName)+(last_name = :lastName)+if(locate(:firstName,first_name),1,0)+if(locate(:lastName,last_name),1,0)'
                                    . '+(first_name = :lastName)+(last_name = :firstName)+if(locate(:lastName,first_name),1,0)+if(locate(:firstName,last_name),1,0)-IF((first_name=last_name),1,0) ) DESC ' . $limit)
                            ->bindParam(':likeFirstName', $like_first_name)
                            ->bindParam(':likeLastName', $like_last_name)
                            ->bindParam(':firstName', $first_name)
                            ->bindParam(':lastName', $last_name);
                    $search_result = $command->queryAll();
                    $models['pages'] = $pages;
                }
            }
        }
        \Yii::$app->view->params['query'] = $query;
        $models['search'] = $search_result;


        $advanced_search = Forms::find()->all();
        $models['advanced'] = $advanced_search;

        
        //Advanced search
        if ($search === 'advanced') {
            if (!empty($search_result)) {
                if (Yii::$app->request->get()) {
                    $advanced = Yii::$app->request->get('advanced');
                    $models['query_response'] = Yii::$app->request->get('advanced');
                    $emptyToken = true;
                    foreach ($advanced as $value) {
                        if (is_array($value)) {
                            foreach ($value as $v) {
                                if (!empty($v)) {
                                    $emptyToken = false;
                                }
                            }
                        } else {
                            if (!empty($value)) {
                                $emptyToken = false;
                            }
                        }
                    }
                    if ($emptyToken === false) {
                        //If advanced search is not empty 
                        $user_ids = '';
                        $i = 1;
                        foreach ($search_result as $user) {
                            if (count($search_result) === $i) {
                                $quote = '';
                            } else {
                                $quote = ',';
                            }
                            $user_ids .= $user['id'] . $quote;
                            $i++;
                        }
                        $sqlIf = '';
                        $sqlIfValue = '';
                        $j = false;
                        foreach ($advanced as $key => $input) {
                            if (!empty($input)) {
                                //If value is not empty
                                $formId = $key;
                                if (!is_array($input)) {
                                    $j = true;
                                    if (empty($sqlIf)) {
                                        $sqlIf .= "GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $input . "' ,user_forms.form_id,NULL))";
                                        $sqlIfValue .= "GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $input . "' ,user_forms.value,NULL)) AS `" . $key . '` ';
                                    } else {
                                        $sqlIf .= " AND GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $input . "' ,user_forms.form_id,NULL))";
                                        $sqlIfValue .= " ,GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $input . "' ,user_forms.value,NULL)) AS `" . $key . '` ';
                                    }
                                } else {
                                    foreach ($input as $k => $i) {
                                        $j = true;
                                        if (empty($sqlIf)) {
                                            $sqlIf .= "GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $i . "' ,user_forms.form_id,NULL))";
                                            $sqlIfValue .= "GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $i . "' ,user_forms.value,NULL)) AS `" . $key . '` ';
                                        } else {
                                            $sqlIf .= " AND GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $i . "' ,user_forms.form_id,NULL))";
                                            $sqlIfValue .= ", GROUP_CONCAT(IF(user_forms.form_id='" . $key . "' AND user_forms.value='" . $i . "' ,user_forms.value,NULL)) AS `" . $key . '` ';
                                        }
                                    }
                                }
                            }
                        }
                        if ($j) {
                            $and = !Yii::$app->user->isGuest ? 'AND id <> ' . Yii::$app->user->id . ' ' : ' ';
                            $limit = '';
                            $sql = 'SELECT IF(' . $sqlIf . ',1,0) AS is_result,user_forms.user_id,users.*,' . $sqlIfValue . ' '
                                    . 'FROM users '
                                    . 'LEFT JOIN user_forms ON user_forms.user_id = users.id '
                                    . 'WHERE users.id IN(' . $user_ids . ') '
                                    . 'GROUP BY user_forms.user_id '
                                    . 'HAVING is_result=1 '
                                    . $and . $limit;
                            $query = Users::findBySql($sql)->all();
                            $pages = new Pagination(['totalCount' => count($query), 'pageSize' => $this->pageSize]);
                            $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                            $sql = 'SELECT IF(' . $sqlIf . ',1,0) AS is_result,user_forms.user_id,users.*,' . $sqlIfValue . ' '
                                    . 'FROM users '
                                    . 'LEFT JOIN user_forms ON user_forms.user_id = users.id '
                                    . 'WHERE users.id IN(' . $user_ids . ') '
                                    . 'GROUP BY user_forms.user_id '
                                    . 'HAVING is_result=1 '
                                    . $and . $limit;
                            $count = Users::findBySql($sql);

                            $model = $count->all();

                            $models['search'] = $model;
                            $models['pages'] = $pages;
                        } else {
                            $models['search'] = [];
                            $models['pages'] = [];
                        }
                    }
                }
            } else {
                //If advanced search is empty 
                $models['query_response'] = Yii::$app->request->get('advanced');
                $models['search'] = [];
                $models['pages'] = [];
            }
        }

        return $this->render('search', $models);
    }

    public function actionConnect($id = null) {
        $user = Users::findOne(['id' => $id]);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException();
        } else {
            $checkRequest = Request::findOne(['user_from_id' => Yii::$app->user->identity->id, 'user_to_id' => $user->id]);
            if (!$checkRequest) {
                $dateNow = new \yii\db\Expression('NOW()');
                $requestModel = new Request();
                $requestModel->user_from_id = Yii::$app->user->identity->id;
                $requestModel->user_to_id = $user->id;
                $requestModel->request_created = $dateNow;
                $requestModel->request_modified = $dateNow;
                $requestModel->save(false);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                // TO DO
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    public function actionAccetpt($id = null) {
        $user = Users::findOne(['id' => $id]);
        $dateNow = new \yii\db\Expression('NOW()');
        if (!$user) {
            throw new \yii\web\NotFoundHttpException();
        } else {
            $request = Request::findOne(['user_from_id' => $user->id, 'user_to_id' => Yii::$app->user->identity->id]);
            if ($request) {
                $request->request_accepted = 'Y';
                $request->request_seen = 'Y';
                $request->request_modified = $dateNow;
                $request->save(false);
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                // TO DO
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    public function actionDecline($id = null) {
        $user = Users::findOne(['id' => $id]);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException();
        } else {
            $decline = Request::deleteAll('user_from_id = :declineId AND user_to_id = :identityId '
                            . 'OR user_from_id = :identityId AND  user_to_id = :declineId', [':declineId' => $id, ':identityId' => Yii::$app->user->identity->id]);
            if ($decline) {
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                // TO DO
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
    }

    public function actionLoadColleagues() {
        $this->layout = false;
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $userId =  Yii::$app->request->get('userId');
        $colleagues = Users::getColleagues($requestAccepted = 'Y',$userId);
        $contacts = ($userId ? Users::GetContactIds() : null);
        $countQuery = clone $colleagues;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 4]);
        $pages->pageSizeParam = false;
        $colleagues = $colleagues->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return $this->render('load-colleagues', ['colleagues' => $colleagues,
                                                 'pages'      => $pages,
                                                 'userId'     => $userId,
                                                 'contacts'   => $contacts,
        ]);
    }

    public function actionLoadNotifications() {
        $this->layout = false;
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $returnParams = $this->getNotifications($pageSize = 6);
        $pages = $returnParams[0];
        $notifications = $returnParams[1];
        $seenIds = [];
        foreach ($notifications as $notification) {
            $seenIds[] = $notification['request_id'];
        }
        if ($seenIds) {
            Request::updateAll(['request_seen' => "Y"], 'request_id IN(' . implode(',', $seenIds) . ')');
        }
        return $this->render('load-notifications', ['notifications' => $notifications,
                    'pages' => $pages
        ]);
    }

    /**
     * 
     * @param int $pageSize
     * @return array
     */
    protected function getNotifications($pageSize) {
        $notifications = Users::getColleagues($requestAccepted = 'N');
        $countQuery = clone $notifications;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 6]);
        $pages->pageSizeParam = false;
        $notifications = $notifications->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return [$pages, $notifications, $countQuery->count()];
    }

}
