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
            [['resource_url'], 'required','on'=>'edit'],
            [['resource_url','resource_image'], 'required','on'=>'insert'],
            [['resource_url','resource_image'], 'string', 'max' => 500],
            [['resource_image'], 'file', 'extensions' => 'jpg,jpeg,png', 'maxSize' => 10000000, 'tooBig' => 'Allowed image size is 10mb.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resource_id' => 'Resource ID',
            'resource_image' => 'Resource Image',
            'resource_url' => 'Resource Url',
        ];
    }

    /**
     * @return array
     */
    public function findeResources($limit)
    {
        return  (new Query())
                    ->select('resource_id,resource_url,resource_image')
                    ->from('news_resources')
                    ->limit($limit)
                    ->orderBy('resource_id DESC')
                    ->all();
    }
}
