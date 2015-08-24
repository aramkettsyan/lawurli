<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact_requests".
 *
 * @property integer $request_id
 * @property integer $user_from_id
 * @property integer $user_to_id
 * @property string $request_seen
 * @property string $request_accepted
 * @property string $request_created
 * @property string $request_modified
 *
 * @property Users $userTo
 * @property Users $userFrom
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_from_id', 'user_to_id', 'request_created', 'request_modified'], 'required'],
            [['user_from_id', 'user_to_id'], 'integer'],
            [['request_seen', 'request_accepted'], 'string'],
            [['request_created', 'request_modified'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'request_id' => 'Request ID',
            'user_from_id' => 'User From ID',
            'user_to_id' => 'User To ID',
            'request_seen' => 'Request Seen',
            'request_accepted' => 'Request Accepted',
            'request_created' => 'Request Created',
            'request_modified' => 'Request Modified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTo()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_to_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_from_id']);
    }
}
