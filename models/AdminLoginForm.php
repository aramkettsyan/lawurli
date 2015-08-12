<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class AdminLoginForm extends Model {

    public $username;
    public $password;
    public $rememberMe = true;
    private $_admin = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getAdmin();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if (\Yii::$app->user->isGuest) {
            if ($this->validate()) {
                return Yii::$app->admin->login($this->getAdmin(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            }else{
                return false;
            }
        } else {
            $this->addError('username', '');
            $this->addError('password', 'You are already logged in as '.\Yii::$app->user->identity->username.' !');
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getAdmin() {
        if ($this->_admin === false) {
            $this->_admin = Admins::findByUsername($this->username);
        }

        return $this->_admin;
    }

}
