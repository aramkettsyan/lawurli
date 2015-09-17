<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $news_id
 * @property string $news_title
 * @property string $news_pub_date
 * @property string $news_url
 * @property string $created
 * @property string $modified
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_title', 'news_url'], 'required'],
            [['created', 'modified'], 'safe'],
            [['news_title', 'news_pub_date', 'news_url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => 'News ID',
            'news_title' => 'News Title',
            'news_pub_date' => 'News Pub Date',
            'news_url' => 'News Url',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
}
