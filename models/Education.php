<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "education".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $organization
 * @property double $number_of_units
 * @property string $date
 * @property integer $ethics
 * @property string $certificate
 * @property string $created
 * @property string $modified
 */
class Education extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'education';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'organization', 'number_of_units', 'date', 'ethics', 'certificate', 'created', 'modified'], 'required'],
            [['user_id', 'ethics'], 'integer'],
            [['number_of_units'], 'number'],
            [['date', 'created', 'modified'], 'safe'],
            [['organization', 'certificate'], 'string', 'max' => 255]
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
            'organization' => 'Organization',
            'number_of_units' => 'Number Of Units',
            'date' => 'Date',
            'ethics' => 'Ethics',
            'certificate' => 'Certificate',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
