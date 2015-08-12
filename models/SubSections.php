<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "sub_sections".
 *
 * @property integer $id
 * @property integer $section_id
 * @property string $name
 * @property integer $multiple
 * @property string $created
 * @property string $modified
 *
 * @property Forms[] $forms
 * @property Sections $section
 */
class SubSections extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sub_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section_id', 'name'], 'required'],
            [['section_id', 'multiple'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_id' => 'Section',
            'name' => 'Name',
            'multiple' => 'Multiple',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForms()
    {
        return $this->hasMany(Forms::className(), ['sub_section_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Sections::className(), ['id' => 'section_id']);
    }
    
    
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created = new Expression('NOW()');
            $this->modified = new Expression('NOW()');
        } else {
            $this->modified = new Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }
}
