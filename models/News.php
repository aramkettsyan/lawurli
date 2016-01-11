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
 * @property string $site_url
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
            [['news_title', 'news_url','news_resource_id'], 'required'],
            [['created', 'modified'], 'safe'],
            [['news_title', 'news_pub_date', 'news_url','site_url'], 'string', 'max' => 255]
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
            'news_resource_id' => 'News Resource ID',
            'news_pub_date' => 'News Pub Date',
            'news_url' => 'News Url',
            'site_url' => 'News Url',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }
    
    /**
     * @return object
     */
    public function getNews(){
        $query = (new \yii\db\Query())
                ->select('ns.*,nr.*')
                ->from('news AS ns')
                ->leftJoin('news_resources AS nr','nr.resource_id=ns.news_resource_id')
                ->all();
        return $query;
    }
}
