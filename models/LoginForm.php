<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    public $email;
    public $password;
    public $rememberMe = false;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['email', 'email'],
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
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('email', 'Incorrect email or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided email and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login() {
        if (\Yii::$app->admin->isGuest) {
            if ($this->validate()) {
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            }else{
                return false;
            }
        } else {
            $this->addError('email','');
            $this->addError('password','You are already logged in as '.\Yii::$app->admin->identity->username.'!');
            return false;
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $this->_user = Users::findByEmail($this->email);
            if(isset($this->_user->active ) && $this->_user->active === 0){
                $this->addError('email','');
                $this->addError('password','');
                Yii::$app->getSession()->setFlash('passwordResend','Your account is not activated. <a href="/users/resend-activation-message?email='.$this->_user->email.'">Resend email</a>');
                return false;
            }
        }

        return $this->_user;
    }

}
