<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $password_reset_token
 * @property integer $active
 * @property string $activation_token
 * @property string $auth_key
 * @property string $created
 * @property string $modified
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface {

    public $confirm_password;
    public $conditions;
    public $default_image = 'default.jpg';
    public $old_password;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['first_name', 'last_name', 'email', 'password', 'confirm_password'], 'required', 'on' => 'create'],
            [['first_name', 'last_name', 'email', 'password'], 'required', 'on' => 'update'],
            [['conditions'], 'checkConditions', 'on' => 'create'],
            [['password', 'confirm_password'], 'required', 'on' => 'resetPassword'],
            [['image'], 'required', 'on' => 'updateImage'],
            [['active'], 'integer'],
            [['email'], 'email'],
            [['created', 'modified'], 'safe'],
            [['first_name', 'last_name', 'email', 'password', 'password_reset_token', 'activation_token', 'image'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 64],
            [['password'], 'string', 'min' => 6],
            [['first_name', 'last_name'], 'string', 'min' => 2],
            [['email', 'password_reset_token', 'activation_token'], 'unique', 'targetAttribute' => ['email', 'password_reset_token', 'activation_token'], 'message' => 'Email has already been taken.'],
            [['auth_key'], 'unique'],
            [['email'], 'unique','message'=>'Email has already been taken.'],
            [['confirm_password'], 'validateConfirmPassword']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Retype password',
            'password_reset_token' => 'Password Reset Token',
            'active' => 'Active',
            'activation_token' => 'Activation Token',
            'auth_key' => 'Auth Key',
            'created' => 'Created',
            'modified' => 'Modified',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
                $this->setPassword($this->password);
                $this->generateEmailActivationToken();
                $this->created = new Expression('NOW()');
                $this->modified = new Expression('NOW()');
                $this->image = $this->default_image;
            } else {
                if ($this->scenario === 'resetPassword') {
                    $this->setPassword($this->password);
                }
                if ($this->scenario === 'update') {
                    if ($this->old_password !== $this->password) {
                        if ($this->password !== $this->confirm_password) {
                            $this->addError('password', '');
                            $this->addError($attribute, 'Passwords do  not match');
                            return false;
                        } else {
                            $this->setPassword($this->password);
                        }
                    }
                }
                $this->modified = new Expression('NOW()');
            }
            return true;
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'active' => 1]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        throw new NotSupportedException('findByUsername is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds validate passwords
     *
     * @param string $attribute
     * @param string $params
     * @return static|null
     */
    public function validateConfirmPassword($attribute, $params) {
        if ($this->password !== $this->confirm_password) {
            $this->addError('password', '');
            $this->addError($attribute, 'Passwords do  not match');
            return false;
        }
        return true;
    }

    /**
     * Finds validate passwords
     *
     * @param string $attribute
     * @param string $params
     * @return static|null
     */
    public function checkConditions($attribute, $params) {
        if ($this->conditions == 0) {
            $this->addError($attribute, 'You must agree terms');
            return false;
        }
        return true;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
                    'password_reset_token' => $token,
                    'active' => true,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['users.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * @param string $activation_token
     * @return static|null
     */
    public static function findByActivationToken($activation_token) {
        return static::findOne(['activation_token' => $activation_token, 'active' => 0]);
    }

    /**
     * Generates email confirmation token
     */
    public function generateEmailActivationToken() {
        $this->activation_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailActivationToken() {
        $this->email_confirm_token = null;
    }

}
