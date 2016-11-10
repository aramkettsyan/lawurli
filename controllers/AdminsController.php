<?php

namespace app\controllers;

use Yii;
use \app\models\AdminLoginForm;
use app\models\Sections;
use app\models\SubSections;
use app\models\Forms;
use \app\components\AdminAccessControl;
use yii\filters\AccessControl;
use \app\models\FormsForm;
use app\models\Model;
use app\models\AboutUs;
use yii\data\Pagination;

class AdminsController extends \yii\web\Controller {

    public $layout = '/admin';

    public function behaviors() {

        if (\Yii::$app->user->identity) {
            $access = ['access' => [
                    'class' => AccessControl::className(),
                    'user' => 'user',
                    'rules' => [
                        [
                            'allow' => false,
                            'roles' => ['@'],
                        ]
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\NotFoundHttpException();
            }
            ]];
        } else {
            $access = [
                'access' => [
                    'class' => AdminAccessControl::className(),
                    'rules' => [
                        [
                            'actions' => ['logout', 'about-us', 'news','users', 'index', 'user-settings', 'site-settings', 'upload-image', 'delete-section', 'delete-sub-section', 'redirect-and-set-flash'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['login'],
                            'allow' => true,
                            'roles' => ['?']
                        ],
                    ],
                    'denyCallback' => function($rule, $action) {
                throw new \yii\web\NotFoundHttpException();
            }
                ]
            ];
        }

        return $access;
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        Yii::$app->user->loginUrl = ['users/index'];
        return parent::beforeAction($action);
    }

    public function actionUserSettings($section = false, $id = false) {
        $isUpdate = false;
        $form_ids = [];
        if ($id) {
            $isUpdate = true;
            if ($section === 'section') {
                Yii::$app->session->writeSession('showSection', true);
                $section_model = new Sections();
                $section_model = $section_model->findOne($id);
                $form_model = new Forms();
                $forms_form = new FormsForm();
                $multiple_form_model = [$forms_form];
                $sub_section_model = new SubSections();
                $modelsFormsForm[] = [new \app\models\FormsFormOptions()];
            } else {
                Yii::$app->session->writeSession('showSubSection', true);
                $section_model = new Sections();
                $sub_section_model = new SubSections();
                $sub_section_model = $sub_section_model->findOne($id);
                $form_model = new Forms();
                $form_model = $form_model->findAll(['sub_section_id' => $id]);
                $forms_form = new FormsForm();
                $forms_form_options = new \app\models\FormsFormOptions();
                $i = 0;
                foreach ($form_model as $form) {
                    $forms_form->label = $form->label;
                    $forms_form->placeholder = $form->placeholder;
                    $forms_form->type = $form->type;
                    $forms_form->numeric = $form->numeric;
                    $forms_form->id = $form->id;
                    $forms_form->sub_section_id = $form->sub_section_id;
                    $forms_form->options = $form->options;
                    $forms_form->show_in_search = $form->show_in_search;
                    $forms_form->is_title = $form->is_title;
                    $form_ids[] = $form->id;
                    $options = explode('-,-', $form->options);
                    foreach ($options as $option) {
                        $forms_form_options->options = $option;
                        $modelsFormsForm[$i][] = $forms_form_options;
                        $forms_form_options = new \app\models\FormsFormOptions();
                    }
                    $multiple_form_model[] = $forms_form;
                    $forms_form = new FormsForm();
                    $i++;
                }
            }
        } else {
            $section_model = new Sections();
            $form_model = new Forms();
            $forms_form = new FormsForm();
            $multiple_form_model = [$forms_form];
            $sub_section_model = new SubSections();
            $modelsFormsForm[] = [new \app\models\FormsFormOptions()];
        }
        $forms = Forms::find()->all();
        $sections = Sections::find()->all();
        $sections_array = [];
        foreach ($sections as $section) {
            $sections_array[$section->id] = $section->name;
        }
        $sub_sections = SubSections::find()->all();
        if (Yii::$app->request->post('Sections') !== null) {
            if ($section_model->load(Yii::$app->request->post()) && $section_model->save()) {

                if ($isUpdate) {
                    \Yii::$app->getSession()->addFlash('section_success', 'The section added successfully!');
                    return $this->redirect('/admins/user-settings');
                }
                \Yii::$app->getSession()->addFlash('section_success', 'The section added successfully!');
                return $this->refresh();
            } else {
                Yii::$app->session->writeSession('showSection', true);
            }
        }
        if (Yii::$app->request->post('SubSections') !== null && $sub_section_model->load(Yii::$app->request->post())) {

            $formsForm = Yii::$app->request->post('FormsForm');
            $formsFormOptionsBackup = Yii::$app->request->post('FormsFormOptions');

            $formsFormOptions = Yii::$app->request->post('FormsFormOptions');
            $options = '';
            $form = Yii::$app->request->post();
            foreach ($formsForm as $key => $value) {
                foreach ($formsFormOptions[$key] as $k => $v) {
                    if (empty($options)) {
                        $options = $v['options'];
                    } else {
                        $options = $options . '-,-' . $v['options'];
                    }
                }
                $form['FormsForm'][$key]['options'] = $options;
                $options = '';
            }
            $multiple_form_model = Model::createMultiple(FormsForm::classname());

            Model::loadMultiple($multiple_form_model, $form);

            $loadsData['_csrf'] = Yii::$app->request->post()['_csrf'];


            for ($i = 0; $i < count($multiple_form_model); $i++) {
                $newOptions = Yii::$app->request->post()['FormsFormOptions'];
                foreach ($newOptions as $key => $newOpt) {
                    foreach ($newOpt as $o) {
                        $no[] = $o;
                    }
                    $newOptions[$key] = $no;
                    $no = [];
                }
                $loadsData['FormsFormOptions'] = $newOptions[$i];
                $modelsFormsForm[$i] = Model::createMultiple(\app\models\FormsFormOptions::classname(), [], $loadsData);
                Model::loadMultiple($modelsFormsForm[$i], $loadsData);
            }
            // validate all models
            $valid = $sub_section_model->validate();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $sub_section_model->save(false)) {
                        if (Yii::$app->request->post()['FormsForm'][0]['id']) {
                            $sub_section_id = Yii::$app->request->post()['FormsForm'][0]['sub_section_id'];
                            $g = 0;
                            foreach ($multiple_form_model as $form) {
                                $id = Yii::$app->request->post()['FormsForm'][$g]['id'];
                                $form->id = $id;
                                $form->sub_section_id = $sub_section_id;
                                if ($form->is_title) {
                                    Forms::updateAll(['is_title' => 0]);
                                }
                                if (!in_array($form->type, ['select', 'input', 'checkbox', 'radio', 'textarea'])) {
                                    $flag = false;
                                    $form->addError('type', 'Type is required');
                                    $transaction->rollBack();
                                    break;
                                }
                                $form->scenario = $form->type;
                                if (!$form->save(false)) {
                                    $flag = false;
                                    $transaction->rollBack();
                                    break;
                                }
                                $g++;
                            }
                        } else {
                            $sub_section_id = Yii::$app->db->getLastInsertID();
                            foreach ($multiple_form_model as $form) {
                                $form->sub_section_id = $sub_section_id;
                                if ($form->is_title) {
                                    Forms::updateAll(['is_title' => 0]);
                                }
                                if (!in_array($form->type, ['select', 'input', 'checkbox', 'radio', 'textarea'])) {
                                    $flag = false;
                                    $form->addError('type', 'Type is required');
                                    $transaction->rollBack();
                                    break;
                                }
                                $form->scenario = $form->type;
                                if (!$form->save()) {
                                    $flag = false;
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $j = false;
                        $delFormIds = [];
                        foreach ($form_ids as $formId) {
                            foreach ($multiple_form_model as $newForm) {
                                if ($formId == $newForm->id) {
                                    $j = true;
                                }
                            }
                            if ($j !== true) {
                                $delFormIds[] = $formId;
                            }
                            $j = false;
                        }
                        if (!empty($delFormIds)) {
                            $f = Forms::deleteAll(['id' => $delFormIds]);
                        } else {
                            $f = true;
                        }
                        if (!$f) {
                            $transaction->rollBack();
                        } else {
                            \Yii::$app->session->destroySession('showSection');
                            \Yii::$app->session->destroySession('showSubSection');
                            $transaction->commit();
                            \Yii::$app->getSession()->addFlash('sub_section_success', 'The sub section added successfully!');
                            if ($isUpdate) {
                                return $this->redirect('/admins/user-settings');
                            }
                            return $this->refresh();
                        }
                    } else {
                        Yii::$app->session->writeSession('showSubSection', true);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } else {
                Yii::$app->session->writeSession('showSubSection', true);
            }
        }


        return $this->render('userSettings', [
                    'sections' => $sections,
                    'sections_array' => $sections_array,
                    'sub_sections' => $sub_sections,
                    'forms' => $forms,
                    'section_model' => $section_model,
                    'sub_section_model' => $sub_section_model,
                    'form_model' => $form_model,
                    'isUpdate' => $isUpdate,
                    'multiple_form_model' => (empty($multiple_form_model)) ? [new FormsForm()] : $multiple_form_model,
                    'modelsFormsForm' => (empty($modelsFormsForm)) ? [new \app\models\FormsFormOptions()] : $modelsFormsForm
        ]);
    }

    public function actionDeleteSection($id) {
        $id = (int) $id;
        if ($id) {
            $model = new Sections();
            $model->deleteAll('id = :id', [':id' => $id]);
            \Yii::$app->getSession()->setFlash('deleteSuccess', 'Section deleted successfully!');
            $this->redirect('/admins/user-settings');
        } else {
            $this->redirect('/admins/index');
        }
    }

    public function actionRedirectAndSetFlash($data = false) {
        if ($data === 'section') {
            Yii::$app->session->writeSession('showSection', true);
            $this->redirect('/admins/user-settings');
        }
        if ($data === 'sub-section') {
            Yii::$app->session->writeSession('showSubSection', true);
            $this->redirect('/admins/user-settings');
        }
    }

    public function actionDeleteSubSection($id) {
        $id = (int) $id;
        if ($id) {
            $model = new SubSections();
            $model->deleteAll('id = :id', [':id' => $id]);
            \Yii::$app->getSession()->setFlash('deleteSuccess', 'Sub section deleted successfully!');
            $this->redirect('/admins/user-settings');
        } else {
            $this->redirect('/admins/index');
        }
    }

    public function actionSiteSettings() {

        $model = \app\models\Upload::findOne(['key' => 'logo']);
        if (!isset($model->value) || !$model->value) {
            $image = '';
        } else {
            $image = $model->value;
        }
        return $this->render('siteSettings', ['logo' => $image]);
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

        $up = Yii::$app->FileUploader->upload(\Yii::getAlias('@web') . 'images/');

        if (isset($up['error'])) {
            $response['success'] = false;
            $response['error'] = $up['error'];
        } else {
            $ext = end(explode('.', $up['filename']));
            $newFileName = \Yii::$app->security->generateRandomString(32) . '.' . $ext;
            rename(\Yii::getAlias('@web') . 'images/' . $up['filename'], \Yii::getAlias('@web') . 'images/' . $newFileName);
            $up['filename'] = $newFileName;
            $model = \app\models\Upload::findOne(['key' => 'logo']);
            if (!$model) {
                $model = new \app\models\Upload();
                $model->key = 'logo';
                $oldLogo = '';
            } else {
                $oldLogo = $model->value;
            }
            $model->value = $up['filename'];
            if ($model->save()) {
                if (!empty($oldLogo) && is_file(\Yii::getAlias('@web') . 'images/' . $oldLogo)) {
                    unlink(\Yii::getAlias('@web') . 'images/' . $oldLogo);
                }
                $response['fileName'] = $up['filename'];
                $response['success'] = true;
            } else {
                if (is_file(\Yii::getAlias('@web') . 'images/' . $up['filename'])) {
                    unlink(\Yii::getAlias('@web') . 'images/' . $up['filename']);
                }
                $response['success'] = false;
                $response['error'] = 'The image isn\'t saved!';
            }
        }

        $response = json_encode($response);

        return $response;
    }

    public function actionIndex() {
        return $this->redirect('/admins/user-settings');
    }

    public function actionLogin() {
        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/admins/user-settings');
        } else {
            return $this->render('login', ['model' => $model]);
        }
    }

    public function actionLogout() {
        Yii::$app->admin->logout();
        $this->goHome();
    }

    public function actionAboutUs() {
        $aboutUsModel = AboutUs::findOne(['static_id' => '1']);
        if ($postData = Yii::$app->request->post("AboutUs")) {
            $aboutUsModel->attributes = $postData;
            if ($aboutUsModel->save()) {
                $this->redirect('/admins/index');
            }
        }
        return $this->render('about-us', ['model' => $aboutUsModel]);
    }

    public function actionNews($edit = false, $delete = false, $id = false) {

        if ($id && $edit) {
            $newsModel = \app\models\NewsResources::findOne(['resource_id' => $id]);
            $image = $newsModel->resource_image;
            $newsModel->scenario = 'edit';
        } else {
            $newsModel = new \app\models\NewsResources();
            $newsModel->scenario = 'insert';
        }

        if ($id && $delete) {
            $news = \app\models\NewsResources::findOne(['resource_id' => $id]);
            if($news){
                if (is_file(\Yii::getAlias('@web') . 'images/news/' . $news->resource_image)) {
                    unlink(\Yii::getAlias('@web') . 'images/news/' . $news->resource_image);
                }
            }
            $newsModel->deleteAll(['resource_id' => $id]);
        }

        $newsList = \app\models\NewsResources::find()->all();

        if (\Yii::$app->request->isPost) {
            if ($newsModel->load(\Yii::$app->request->post()) && (!empty($_FILES['NewsResources']['name']['resource_image']) || $edit)) {
                if (empty($_FILES['NewsResources']['name']['resource_image']) && $edit) {
                    if (is_file(\Yii::getAlias('@web') . 'images/news/' . $image)) {
                        unlink(\Yii::getAlias('@web') . 'images/news/' . $image);
                    }
                    if ($newsModel->save()) {
                        \Yii::$app->session->setFlash('newsSuccess', 'Url saved.');
                        $this->redirect('/admins/news');
                    }
                } else {
                    $fileName = $_FILES['NewsResources']['name']['resource_image'];
                    $fileSize = $_FILES['NewsResources']['size']['resource_image'];
                    $fileType = strtolower(end(explode('.', $fileName)));
                    $newFileName = \Yii::$app->security->generateRandomString(32) . '.' . $fileType;
                    $allowedTypes = ['jpg', 'jpeg', 'png'];
                    if (in_array($fileType, $allowedTypes)) {
                        if ($fileSize <= 10000000) {
                            $fileTmp = $_FILES['NewsResources']['tmp_name']['resource_image'];
                            move_uploaded_file($fileTmp, \Yii::getAlias('@web') . 'images/news/' . $newFileName);
                            $newsModel->resource_image = $newFileName;
                            if ($newsModel->save()) {
                                \Yii::$app->session->setFlash('newsSuccess', 'Url saved.');
                                $this->redirect('/admins/news');
                            }
                        } else {
                            $newsModel->addError('resource_image', 'Allowed image size is 10mb.');
                        }
                    } else {
                        $newsModel->addError('resource_image', 'Invalid image format.');
                    }
                }
            }
        }

        return $this->render('news', [
                    'model' => $newsModel,
                    'newsList' => $newsList
        ]);
    }


    public function actionUsers(){
        $usersList = \app\models\Users::find();
        $countQuery = clone $usersList;
        $pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize' => '10']);
        $usersList = $usersList->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('users', [
            'usersList' => $usersList,
            'pages' => $pages
        ]);
    }

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
