<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "news_resources".
 *
 * @property integer $resource_id
 * @property string $resource_url
 */
class NewsResources extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_resources';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resource_url'], 'required'],
            [['resource_url'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resource_id' => 'Resource ID',
            'resource_url' => 'Resource Url',
        ];
    }

    /**
     * @return array
     */
    public function findeResources($limit)
    {
        return  (new Query())
                    ->select('resource_id,resource_url')
                    ->from('news_resources')
                    ->limit($limit)
                    ->orderBy('resource_id DESC')
                    ->all();
    }
}
