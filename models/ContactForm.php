<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $reCaptcha;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => '6Lc2-g0TAAAAAGAslMtVp6OJSHU1ElaXYF49VlId']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'body' => 'Message',
            'reCaptcha' => '',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string  $email the target email address
     * @return boolean whether the model passes validation
     */
    public function contact($email)
    {
        if ($this->validate()) {
            Yii::$app->mailer->compose()
                ->setTo(\Yii::$app->params['adminEmail'])
                ->setFrom([\Yii::$app->params['adminEmail'] => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body.'<br/><br/> Contact Email: '.$this->email)
                ->send();

            return true;
        } else {
            return false;
        }
    }
}
