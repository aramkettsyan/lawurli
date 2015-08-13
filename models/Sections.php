<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "sections".
 *
 * @property integer $id
 * @property string $name
 * @property string $created
 * @property string $modified
 *
 * @property SubSections[] $subSections
 */
class Sections extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Section name',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubSections()
    {
        return $this->hasMany(SubSections::className(), ['section_id' => 'id']);
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
