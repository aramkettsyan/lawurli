<?php

namespace app\controllers;

use app\models\AboutUs;
use yii\filters\AccessControl;
use app\components\AdminAccessControl;
use \app\models\LoginForm;
use app\models\Users;
use \app\models\Forms;
use yii\data\Pagination;
use app\models\Request;
use app\models\ContactForm;
use Yii;
use app\models\UserForms;
use app\models\EducationForm;
use app\models\Education;

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
                            'actions' => [
                                'logout',
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
                                'load-education',
                                'delete-education',
                                'get-not-connected-users',
                                'error',
                                'contact-us',
                                'delete-image'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => [
                                'logout',
                                'index',
                                'confirm',
                                'reset-password',
                                'reset-password-notifications',
                                'reset',
                                'error',
                                'contact-us'
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
                            'actions' => ['index', 'edit', 'profile', 'search', 'error', 'contact-us'],
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

    public function actions() {
        return [
//            'error' => [
//                'class' => 'yii\web\ErrorAction',
//            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action) {
        $action_id = \Yii::$app->controller->action->id;

        $aboutUsModel = AboutUs::findOne(['static_id' => '1']);

        \Yii::$app->view->params['contact_email'] = $aboutUsModel['contact_us_email'];

        $aboutUs = explode('.', $aboutUsModel['about_us']);

        $aboutUs = implode('.', [$aboutUs[0], $aboutUs[1], $aboutUs[2]]) . '.';

        \Yii::$app->view->params['about_us'] = $aboutUs;

        //Not Connected Users
        if ($action_id === 'profile' && !\Yii::$app->user->isGuest && !\Yii::$app->request->isAjax) {
            $notConnectedUsers = $this->actionGetNotConnectedUsers();
            \Yii::$app->view->params['notConnectedUsers'] = $notConnectedUsers;
        }
        //end Not Connected Users
        //404 error

        if ($action_id === 'error') {
            $this->layout = 'error';
        }

        //end 404 error
        //login and registration


        $actions = [
            'index',
            'profile',
            'search',
            'contact-us'
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
            if (Yii::$app->controller->module->requestedRoute = 'users/profile' && Yii::$app->request->get('notificationsTab') == "open") {
                Users::updateSeenRows();
            }
            $notify = $this->getNotifications($pageSize = 6);
            Yii::$app->view->params['notify'] = $notify[1];
            Yii::$app->view->params['notifyCount'] = Users::getNotificationCount();
        }
        return parent::beforeAction($action);
    }

    public function actionError() {
        if (Yii::$app->errorHandler->exception->statusCode == 403) {
            return $this->redirect('/users/index');
        }

        return $this->render('error');
    }

    public function actionLogin() {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/users/profile');
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
                    ->setFrom([\Yii::$app->params['welcomeEmail'] => \Yii::$app->name])
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

    public function actionDeleteImage() {
        $id = Yii::$app->user->id;
        $model = Users::findOne(['id' => $id]);
        if ($model) {
            $model->image = 'default.png';
            $model->save();
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return $this->redirect('/users/index');
        }
    }

    public function actionEdit($action = 'general', $id = false) {
        if ($id === false && !\Yii::$app->user->isGuest) {
            if ($action === 'detailed') {
                return $this->actionLoadDetailedEdit();
            } else {
                return $this->actionLoadGeneralEdit();
            }
        } else {
            return $this->redirect('/users/index');
        }
    }

    public function actionLoadGeneralEdit() {

        $user = Users::findOne(['id' => Yii::$app->user->identity->id]);
        $user->scenario = 'update';
        if (Yii::$app->request->post()) {

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();
                if (empty($post['Users']['password'])) {
                    unset($post['Users']['password']);
                    unset($post['Users']['confirm_password']);
                }

                if (empty($post['Users']['latlng'])) {
                    $post['Users']['latlng'] = NULL;
                }

                $user->old_password = $user->password;
                $user->scenario = 'update';
                if ($user->load($post) && $user->save()) {
                    $transaction->commit();
                    return $this->redirect('/users/profile?profileTab=open');
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
                $r = 0;
                $g = 0;
                $validate = true;
                $keys_array = [];
                foreach ($custom_fields as $key => $custom_field) {
                    foreach ($custom_field as $k => $field) {
//                        var_dump($field . ' ' . $k);
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
                        $r++;
                    }
                    $g++;
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
                            return $this->redirect('/users/profile?profileTab=open');
                        } else {
                            Yii::$app->getSession()->writeSession('updateError', 'Can\'t insert form(s), try again.');
                            $transaction->rollBack();
                        }
                    } else if (!$deleteFailed) {
                        $transaction->commit();
                        return $this->redirect('/users/profile?profileTab=open');
                    }
                } else {
                    Yii::$app->getSession()->writeSession('updateError', 'There are some error(s) in form(s), try again.');
                    return $this->redirect('/users/edit');
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
            $subSectionId = $user_form['subSectionId'];
            $index = $user_form['index'];
            $form_id = $user_form['form_id'];
            if (isset($new_user_forms[$subSectionId][$index][$form_id])) {
                if (!is_array($new_user_forms[$subSectionId][$index][$form_id])) {
                    $new_user_forms[$subSectionId][$index][$form_id] = [$new_user_forms[$subSectionId][$index][$form_id]];
                }
                $new_user_forms[$subSectionId][$index][$form_id][] = $user_form['value'];
            } else {
                $new_user_forms[$subSectionId][$index][$form_id] = $user_form['value'];
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
        foreach ($new_user_forms as $key => $uf) {
            $new_user_forms[$key] = array_values($uf);
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
                if ($numeric != 0 && !((int) $value)) {
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
        $ext = strtolower(end($explode));
        $allowed = array('png', 'jpg', 'jpeg');
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

        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/users/profile');
        }

        $models = $this->models;

        return $this->render('index', $models);
    }

    public function actionLogout() {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/users/index');
        }
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
                        ->setFrom([\Yii::$app->params['resetEmail'] => \Yii::$app->name])
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

        if (Yii::$app->user->isGuest) {
            return $this->redirect('/users/index');
        }

        if (isset(Yii::$app->request->post()['EducationForm'])) {
            if (isset(Yii::$app->request->post()['EducationForm']['id']) && Yii::$app->request->post()['EducationForm']['id']) {
                $response = $this->editEducation();
            } else {
                $response = $this->addEducation();
            }

            if ($response === true) {
                return $this->redirect('/users/profile?educationTab=open');
            } else {
                Yii::$app->view->params['educationModel'] = $response;
                return $this->redirect('/users/profile?educationTab=open');
            }
        } else if (isset(Yii::$app->request->get()['cleid']) && (int) Yii::$app->request->get()['cleid']) {
            $cle_id = (int) Yii::$app->request->get()['cleid'];
            $model = Education::findOne(['id' => $cle_id, 'user_id' => Yii::$app->user->id]);
            if ($model) {
                $form_model = new EducationForm();
                $form_model->id = $model->id;
                $form_model->organization = $model->organization;
                $form_model->number_of_units = $model->number_of_units;
                $form_model->date = $model->date;
                $form_model->ethics = $model->ethics;
                $form_model->certificate = $model->certificate;
                Yii::$app->view->params['educationModel'] = $form_model;

                Yii::$app->session->writeSession('addEducation', 'true');
            } else {
                $educationModel = new EducationForm();
                Yii::$app->view->params['educationModel'] = $educationModel;
            }
        } else {
            $educationModel = new EducationForm();
            Yii::$app->view->params['educationModel'] = $educationModel;
        }

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

            foreach ($new_user_forms as $key => $uf) {
                $new_user_forms[$key] = array_values($uf);
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

        if (Yii::$app->user->isGuest) {
            return $this->redirect('/users/index');
        }
        $query = trim(\Yii::$app->request->get('query'));
        $first_name = str_replace('_', '', str_replace('%', '', trim(\Yii::$app->request->get('first_name'))));
        $last_name = str_replace('_', '', str_replace('%', '', trim(\Yii::$app->request->get('last_name'))));
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
                if ($search === 'advanced') {
                    $search_result = $q->queryAll();
                } else {
                    $count = $q->queryAll();
                    $pages = new Pagination(['totalCount' => count($count), 'pageSize' => $this->pageSize]);
                    $limit = 'LIMIT ' . $pages->limit . ' ' . 'OFFSET ' . $pages->offset;
                    $q = \Yii::$app->db->createCommand('SELECT * FROM `users` '
                            . 'WHERE '
                            . $and
                            . 'ORDER BY first_name,last_name ' . $limit);
                    $search_result = $q->queryAll();
                    $models['pages'] = $pages;
                }
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
            $query_array[0] = str_replace('_', '', str_replace('%', '', trim($query_array[0])));
            if (isset($query_array[1])) {
                $query_array[1] = str_replace('_', '', str_replace('%', '', trim($query_array[1])));
            }
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
                                    $input = \Yii::$app->db->quoteValue($input);
                                    $key = \Yii::$app->db->quoteValue($key);
                                    if (empty($sqlIf)) {
                                        $sqlIf .= "GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $input . " ,user_forms.form_id,NULL))";
                                        $sqlIfValue .= "GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $input . " ,user_forms.value,NULL)) AS `" . $key . '` ';
                                    } else {
                                        $sqlIf .= " AND GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $input . ",user_forms.form_id,NULL))";
                                        $sqlIfValue .= " ,GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $input . " ,user_forms.value,NULL)) AS `" . $key . '` ';
                                    }
                                } else {
                                    foreach ($input as $k => $i) {
                                        $j = true;
                                        $i = \Yii::$app->db->quoteValue($i);

                                        $key = \Yii::$app->db->quoteValue($key);
                                        if (empty($sqlIf)) {
                                            $sqlIf .= "GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $i . " ,user_forms.form_id,NULL))";
                                            $sqlIfValue .= "GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $i . " ,user_forms.value,NULL)) AS `" . $key . "` ";
                                        } else {
                                            $sqlIf .= " AND GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $i . " ,user_forms.form_id,NULL))";
                                            $sqlIfValue .= ", GROUP_CONCAT(IF(user_forms.form_id=" . $key . " AND user_forms.value=" . $i . " ,user_forms.value,NULL)) AS `" . $key . "` ";
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
        $userId = Yii::$app->request->get('userId');
        $colleagues = Users::getColleagues($requestAccepted = 'Y', $userId);
        $contacts = ($userId ? Users::GetContactIds() : null);
        $countQuery = clone $colleagues;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 4]);
        $pages->pageSizeParam = false;
        $colleagues = $colleagues->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
        return $this->render('load-colleagues', ['colleagues' => $colleagues,
                    'pages' => $pages,
                    'userId' => $userId,
                    'contacts' => $contacts,
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

    public function actionLoadEducation() {
        $this->layout = false;
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $cles = Education::find(['user_id' => Yii::$app->user->id])->asArray()->all();
        $sum_of_units = 0;
        $sum_of_ethics = 0;
        foreach ($cles as $cle) {
            $sum_of_units+=$cle['number_of_units'];
            $sum_of_ethics+=$cle['ethics'];
        }

        return $this->render('load-education', [
                    'cles' => $cles,
                    'sum_of_ethics' => $sum_of_ethics,
                    'sum_of_units' => $sum_of_units
        ]);
    }

    protected function addEducation() {
        $this->layout = false;
        $model = new EducationForm();
        if (isset($_FILES['EducationForm']['name']['certificate']) && $_FILES['EducationForm']['name']['certificate']) {
            if ($model->load(Yii::$app->request->post())) {
                $file = \yii\web\UploadedFile::getInstance($model, 'certificate');
                if ($file && $file->tempName) {
                    $model->certificate = $file;
                    if ($model->validate()) {

                        $result = $this->actionUploadFile();
                        if ($result) {
                            $model->certificate = $result;
                            if ($model->saveData()) {
                                return true;
                            } else {
                                $name = Yii::getAlias('@web') . 'images/users_uploads/' . $result;
                                if (is_file($name)) {
                                    unset($name);
                                }
                                Yii::$app->session->writeSession('addEducation', 'true');
                                return $model;
                            }
                        }
                    }
                }
            }
        }
        print_r($model->getErrors());
        die;

        Yii::$app->session->writeSession('addEducation', 'true');
        return $model;
    }

    protected function editEducation() {
        $this->layout = false;
        if (isset(Yii::$app->request->post()['EducationForm']['id']) && (int) Yii::$app->request->post()['EducationForm']['id']) {

            $id = (int) Yii::$app->request->post()['EducationForm']['id'];
            $ed_model = Education::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
            $form_model = new EducationForm();
            $form_model->scenario = 'update';
            $form_model->id = $ed_model->id;
            if (!Yii::$app->request->post()['EducationForm']['organization']) {
                $form_model->organization = $ed_model->organization;
            } else {
                $form_model->organization = Yii::$app->request->post()['EducationForm']['organization'];
            }
            if (!Yii::$app->request->post()['EducationForm']['number_of_units']) {
                $form_model->number_of_units = $ed_model->number_of_units;
            } else {
                $form_model->number_of_units = Yii::$app->request->post()['EducationForm']['number_of_units'];
            }
            if (!Yii::$app->request->post()['EducationForm']['date']) {
                $form_model->date = $ed_model->date;
            } else {
                $form_model->date = Yii::$app->request->post()['EducationForm']['date'];
            }

            $form_model->ethics = Yii::$app->request->post()['EducationForm']['ethics'];

            $model = $form_model;
            if ($model->validate()) {
                if (isset($_FILES['EducationForm']['name']['certificate']) && $_FILES['EducationForm']['name']['certificate']) {
                    $result = $this->actionUploadFile();
                    if ($result) {
                        $model->certificate = $result;
                        if ($model->saveData()) {
                            return true;
                        } else {
                            $name = Yii::getAlias('@web') . 'images/users_uploads/' . $result;
                            if (is_file($name)) {
                                unset($name);
                            }
                            Yii::$app->session->writeSession('addEducation', 'true');
                            return $model;
                        }
                    }
                } else {
                    $model->certificate = $ed_model->certificate;
                    if ($model->saveData()) {
                        return true;
                    } else {
                        Yii::$app->session->writeSession('addEducation', 'true');
                        return $model;
                    }
                }
            } else {
                Yii::$app->session->writeSession('addEducation', 'true');
                return $model;
            }
        }
    }

    public function actionDeleteEducation() {
        if (isset(Yii::$app->request->get()['cleid']) && (int) Yii::$app->request->get()['cleid']) {
            $id = Yii::$app->request->get()['cleid'];
            $model = Education::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
            if ($model) {
                if (is_file(Yii::getAlias('@web') . 'images/users_uploads/' . $model->certificate)) {
                    unlink(Yii::getAlias('@web') . 'images/users_uploads/' . $model->certificate);
                }
                $model->delete();
                return $this->redirect('/users/profile?educationTab=open');
            } else {
                return $this->redirect('/users/index');
            }
        } else {
            return $this->redirect('/users/index');
        }
    }

    public function actionUploadFile() {

        if (isset($_FILES['EducationForm']['name']['certificate'])) {
            $file_name = $_FILES['EducationForm']['name']['certificate'];
            $file = $_FILES['EducationForm']['tmp_name']['certificate'];
            $ext = strtolower(end(explode('.', $file_name)));
            $sec = new \yii\base\Security();
            $random_string = $sec->generateRandomString(24);
            $file_name = $random_string . '.' . $ext;
            $res = move_uploaded_file($file, Yii::getAlias('@web') . 'images/users_uploads/' . $file_name);
            if ($res) {
                return $file_name;
            } else {
                return false;
            }
        }
        return false;
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

    public function actionGetNotConnectedUsers() {
        if (\Yii::$app->request->isAjax) {
            $user_id = (int) \Yii::$app->request->post()['id'];
            $allIds = \Yii::$app->request->post()['allIds'];
            if ($user_id != \Yii::$app->request->post()['id']) {
                echo false;
                die;
            }
            if (isset(\Yii::$app->request->post()['add'])) {
                $this->actionConnect($user_id);
            } else {
                Users::addNotColleaguesUser($user_id);
            }
            $limit = 1;
            $users = Users::getNotConnectedUsers($limit, $allIds);

            $title_form_model = Forms::findOne(['is_title' => 1]);
            if ($title_form_model) {
                $title_form_id = $title_form_model->id;
                $usersIds = '';
                foreach ($users as $user) {
                    if (empty($usersIds)) {
                        $usersIds.=$user['id'];
                    } else {
                        $usersIds.=',' . $user['id'];
                    }
                }
                $usersIds = '(' . $usersIds . ')';
                $user_forms_model = new UserForms();
                $users_titles = $user_forms_model->getById($title_form_id, $usersIds);
                $response['users'] = $users;
                if ($users_titles) {
                    $response['users_titles'] = $users_titles;
                } else {
                    $response['users_titles'] = [];
                }
            } else {
                $response['users'] = $users;
                $response['users_titles'] = [];
            }

            print_r(json_encode($response));
            die;
        } else {
            $limit = 5;
            $users = Users::getNotConnectedUsers($limit);

            $title_form_model = Forms::findOne(['is_title' => 1]);
            if ($title_form_model) {
                $title_form_id = $title_form_model->id;
                $usersIds = '';
                foreach ($users as $user) {
                    if (empty($usersIds)) {
                        $usersIds.=$user['id'];
                    } else {
                        $usersIds.=',' . $user['id'];
                    }
                }
                $usersIds = '(' . $usersIds . ')';
                $user_forms_model = new UserForms();
                $users_titles = $user_forms_model->getById($title_form_id, $usersIds);
                \Yii::$app->view->params['usersTitles'] = $users_titles;
            } else {
                \Yii::$app->view->params['usersTitles'] = [];
            }
            return $users;
        }
    }

    public function actionContactUs() {
        $aboutUsModel = AboutUs::findOne(['static_id' => '1']);
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact($aboutUsModel->contact_us_email)) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        } else {
            return $this->render('contact-us', ['aboutUsModel' => $aboutUsModel,
                        'model' => $model,
            ]);
        }
    }

}
