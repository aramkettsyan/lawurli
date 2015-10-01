<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "forms".
 *
 * @property integer $id
 * @property integer $sub_section_id
 * @property string $label
 * @property string $type
 * @property string $placeholder
 * @property integer $numeric
 * @property integer $show_in_search
 * @property integer $is_title
 * @property string $options
 * @property string $created
 * @property string $modified
 *
 * @property SubSections $subSection
 */
class Forms extends \yii\db\ActiveRecord {

    public $forms = [];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'forms';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sub_section_id', 'numeric','show_in_search','is_title'], 'integer'],
            [['type', 'options'], 'string'],
            [['created', 'modified'], 'safe'],
            [['label', 'placeholder'], 'string', 'max' => 255],
            ['options', 'checkOptions', 'skipOnEmpty' => false],
            ['placeholder', 'required', 'on' => 'input'],
            ['placeholder', 'required', 'on' => 'textarea'],
            [['options', 'label'], 'required', 'on' => 'select'],
            [['label', 'options'], 'required', 'on' => 'radio'],
            [['label', 'options'], 'required', 'on' => 'checkbox'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sub_section_id' => 'Sub Section ID',
            'label' => 'Label',
            'type' => 'Type',
            'placeholder' => 'Placeholder',
            'numeric' => 'Numeric',
            'options' => 'Options',
            'show_in_search' => 'Show in search',
            'is_title' => 'As title',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubSection() {
        return $this->hasOne(SubSections::className(), ['id' => 'sub_section_id']);
    }

    public function checkOptions($attr, $param) {
        if ($this->type === 'select' || $this->type === 'checkbox' || $this->type === 'radio') {
            if (empty($this->options)) {

                $this->addError($attr, 'You must type options');
            }
        }
    }

    public function beforeSave($insert) {
        if (!empty($this->forms) && is_array($this->forms)) {
            $connection = \Yii::$app->db;
            $forms_array = [];
            foreach ($this->forms as $form) {
//                print_r($form);
//                die;
                if (!$form['show_in_search']) {
                    $form['show_in_search'] = 0;
                }
                if (!$form['is_title']) {
                    $form['is_title'] = 0;
                }
                $forms_array[] = [$this->sub_section_id, $form['label'], $form['numeric'], $form['type'], $form['options'], $form['placeholder'], $form['show_in_search'], $form['is_title']];
            }
            $connection->createCommand()->batchInsert('forms', ['sub_section_id', 'label', 'numeric', 'type', 'options', 'placeholder', 'show_in_search', 'is_title'], $forms_array)->execute();
        }
        if ($this->isNewRecord) {
            $this->created = new Expression('NOW()');
            $this->modified = new Expression('NOW()');
        } else {
            $this->modified = new Expression('NOW()');
        }


        return parent::beforeSave($insert);
    }

    public function afterValidate() {
        if ($this->hasErrors()) {
            $forms = Yii::$app->request->post('FormsForm');
            Yii::$app->getSession()->writeSession('FormsForm', $forms);
        }
        parent::afterValidate();
    }

}
