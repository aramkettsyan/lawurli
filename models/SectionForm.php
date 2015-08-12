<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SectionForm is the model behind the login form.
 */
class SectionForm extends Model {

    public $name;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

}
