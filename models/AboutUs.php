<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "about_us".
 *
 * @property string $about_us
 * @property string $contact_us_email
 * @property string $contact_us_text
 * @property string $contact_us_visibility
 */
class AboutUs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'about_us';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['about_us', 'contact_us_email'], 'required',],
            [['contact_us_visibility'], 'required', 'message'=>'Select visibility'],
            [['about_us', 'contact_us_text', 'contact_us_visibility'], 'string'],
            [['contact_us_email'], 'string', 'max' => 256],
            [['contact_us_email'], 'email']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'about_us' => 'About Us',
            'contact_us_email' => 'Contact Us Email',
            'contact_us_text' => 'Contact Us Text',
            'contact_us_visibility' => 'Contact Form Visibility',
        ];
    }
}
