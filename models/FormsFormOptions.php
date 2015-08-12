<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Forms;

/**
 * LoginForm is the model behind the login form.
 */
class FormsFormOptions extends Model {

    public $options;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
        [['options'], 'safe']
            ];
    }
    
}
