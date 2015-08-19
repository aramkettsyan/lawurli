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
                            'actions' => ['logout', 'edit', 'profile', 'upload-image'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['index', 'login', 'registration', 'email-confirmation', 'confirm', 'reset-password', 'reset-password-notifications', 'reset'],
                            'allow' => true,
                            'roles' => ['?']
                        ],
                    ],
                    'denyCallback' => function($rule, $action) {
                if (Yii::$app->user->isGuest) {
                    $this->redirect('/users/index');
                } else {
                    $this->redirect('/users/edit');
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
            $user_forms = $connection->createCommand("SELECT user_forms.form_id,user_forms.index,user_forms.value,forms.type,sub_sections.id as subSectionId FROM user_forms "
                            . "LEFT JOIN forms ON user_forms.form_id = forms.id "
                            . "LEFT JOIN sub_sections ON forms.sub_section_id = sub_sections.id "
                            . "WHERE user_id =" . Yii::$app->user->identity->id)->queryAll();
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
                            foreach ($custom_field as $k => $field) {
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
                                    Yii::$app->getSession()->writeSession('updateSuccess', 'Your profile updated successfully!');
                                    $transaction->commit();
                                    return $this->refresh();
                                } else {
                                    Yii::$app->getSession()->writeSession('updateError', 'Can\'t insert form(s), try again.');
                                    $transaction->rollBack();
                                }
                            } else if (!$deleteFailed) {
                                Yii::$app->getSession()->writeSession('updateSuccess', 'Your profile updated successfully!');
                                $transaction->commit();
                                return $this->refresh();
                            }
                        } else {
                            Yii::$app->getSession()->writeSession('updateError', 'There are some error(s) in form(s), try again.');
                        }
                    } else {
                        $transaction->rollBack();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
            if ($user) {
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
                print_r('textarea');
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
        $resetModel = new Users();
        $user = new Users();
        if ($action === 'reset_password') {
            if (Yii::$app->request->post('Users')) {
                \Yii::$app->getSession()->writeSession('resetPassword', true);
                $resetModel = $this->actionResetPassword($id, $key);
            }
        }

        if ($action === 'reset') {
            $user = $this->actionReset($id, $key);
            if ($user === true) {
                return $this->redirect('/users/index#loginTab');
            }
        }


        $registrationModel = new Users();
        $model = new LoginForm();
        $registrationModel->scenario = 'create';
        if ($action !== 'reset' && $action !== 'reset_password' && Yii::$app->request->post('Users')) {
            if ($registrationModel->load(Yii::$app->request->post()) && $registrationModel->save()) {
                $email = \Yii::$app->mailer->compose('confirmEmail', ['user' => $registrationModel])
                        ->setTo($registrationModel->email)
                        ->setFrom(['admin@email.com' => \Yii::$app->name])
                        ->setSubject('E-mail confirmation')
                        ->send();

                if ($email) {
                    Yii::$app->getSession()->setFlash('registrationSuccess', 'Please check your email address!');
                    $registrationModel = new Users();
                } else {
                    Yii::$app->getSession()->setFlash('registrationWarning', 'Error,please try again!');
                    $registrationModel->password = '';
                    $registrationModel->confirm_password = '';
                }
            }
        } else if (Yii::$app->request->post('LoginForm')) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->redirect('/users/edit');
            } else {
                Yii::$app->getSession()->writeSession('showLogin', true);
            }
        }

        return $this->render('index', [
                    'model' => $model,
                    'registrationModel' => $registrationModel,
                    'user' => $user,
                    'resetModel' => $resetModel
        ]);
    }

    public function actionError() {
        return $this->render('index');
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        $this->goHome();
    }

    public function actionEmailConfirmation($email = false) {
        if ($email) {
            return $this->render('/users/emailConfirmation', ['email' => $email]);
        } else {
            return $this->redirect('/users/index');
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
        Yii::$app->getSession()->writeSession('showLogin', true);
        $this->redirect('/users/index');
    }

    public function actionResetPassword($id = false, $key = false) {
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
                        Yii::$app->getSession()->setFlash('resetWarning', 'Please wait '.(60-$difference).' seconds and try again.');
                        return $model;
                    }
                }
                $model->generatePasswordResetToken();
                $model->save();
                $email = \Yii::$app->mailer->compose('resetPassword', ['user' => $model])
                        ->setTo($model->email)
                        ->setFrom(['admin@email.com' => \Yii::$app->name])
                        ->setSubject('Password reset')
                        ->send();
                if ($email) {
                    Yii::$app->getSession()->writeSession('emailTimeout', time());
                    Yii::$app->getSession()->setFlash('resetSuccess', 'Please check your email address.');
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

    public function actionProfile($id = false) {
        if ($id === false && !\Yii::$app->user->isGuest) {
            $user = Users::findOne(['id' => \Yii::$app->user->identity->id]);
            if ($user) {
                return $this->render('/users/profile', ['model' => $user]);
            }
        }
    }

}
