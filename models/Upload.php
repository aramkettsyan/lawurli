<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * LoginForm is the model behind the login form.
 */
class Upload extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'site_settings';
    }

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            ['key', 'required'],
            ['value', 'string', 'max' => 255],
            ['key', 'string', 'max' => 255],
        ];
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
