<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Forms;


class UserFormsForm extends Model {

    public $user_id;
    public $form_id;
    public $index;
    public $value;
    public $created;
    public $modified;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'form_id', 'index', 'value', 'created', 'modified'], 'required'],
            [['user_id', 'form_id', 'index'], 'integer'],
            [['value'], 'string'],
            [['created', 'modified'], 'safe']
        ];
    }
}
