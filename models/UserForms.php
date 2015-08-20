<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_forms".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $form_id
 * @property integer $index
 * @property string $value
 * @property string $created
 * @property string $modified
 *
 * @property Users $user
 * @property Forms $form
 */
class UserForms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_forms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'form_id', 'index', 'value', 'created', 'modified'], 'required'],
            [['user_id', 'form_id', 'index'], 'integer'],
            [['value'], 'string'],
            [['created', 'modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'form_id' => 'Form ID',
            'index' => 'Index',
            'value' => 'Value',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Forms::className(), ['id' => 'form_id']);
    }
}
