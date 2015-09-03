<?php

namespace app\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $location
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
            [['first_name', 'last_name', 'email'], 'required', 'on' => 'update'],
//            [['conditions'], 'checkConditions', 'on' => 'create'],
            [['password', 'confirm_password'], 'required', 'on' => 'resetPassword'],
            [['image'], 'required', 'on' => 'updateImage'],
            [['active'], 'integer'],
            [['email'], 'email'],
            [['created', 'modified'], 'safe'],
            [['email', 'password', 'location', 'latlng', 'password_reset_token', 'activation_token', 'image'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'string', 'max' => 18],
            [['auth_key'], 'string', 'max' => 64],
            [['phone'], 'string', 'max' => 128],
            [['password'], 'string', 'min' => 6],
            [['first_name', 'last_name'], 'string', 'min' => 2],
            [['email', 'password_reset_token', 'activation_token'], 'unique', 'targetAttribute' => ['email', 'password_reset_token', 'activation_token'], 'message' => 'Email has already been taken.'],
            [['auth_key'], 'unique'],
            [['email'], 'unique', 'message' => 'Email has already been taken.'],
            [['confirm_password'], 'validateConfirmPassword'],
            [['phone'], 'validatePhoneNumber']
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
            'phone' => 'Phone',
            'location' => 'Location',
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
     * @return true|false
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
     * Validate phone number
     *
     * @param string $attribute
     * @param string $params
     * @return static|null
     */
    public function validatePhoneNumber($attribute, $params) {
        $phone = $this->phone;
        $phone = str_replace(' ', '', $phone);

        if (!preg_match('/^\+{0,1}[0-9]+$/', $phone)) {
            $this->addError($attribute, 'The phone number you entered is not valid');
            return false;
        }
        $this->phone = $phone;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserForms() {
        return $this->hasMany(UserForms::className(), ['user_id' => 'id']);
    }

    /**
     * 
     * @return array
     */
    public function GetContactIds() {
        $requests = (new Query())
                ->select('user_to_id,user_from_id,request_accepted')
                ->distinct()
                ->from('contact_requests')
                ->where('user_from_id =' . Yii::$app->user->identity->id)
                ->orWhere('user_to_id =' . Yii::$app->user->identity->id)
                ->all();

        $requestArr = [];
        foreach ($requests as $request) {
            if ($request['user_to_id'] <> Yii::$app->user->identity->id) {
                $requestArr[$request['user_to_id']] = $request;
            } elseif ($request['user_from_id'] <> Yii::$app->user->identity->id) {
                $requestArr[$request['user_from_id']] = $request;
            }
        }
        return $requestArr;
    }

    /**
     * $requestAccepted ENUM Y/N
     * @param string $requestAccepted
     * @param int $userId
     * @return object
     */
    public function getColleagues($requestAccepted, $userId = null) {
        $query = (new Query())
                ->select('id,first_name,last_name,location,image,request_created,request_id')
                ->distinct()
                ->from('contact_requests');
        if ($requestAccepted == "N") {
            $query->where("user_to_id =" . Yii::$app->user->identity->id . " AND request_accepted = '$requestAccepted'");
        } else {
            if ($userId) {
                $query->where("user_from_id =" . $userId . " AND request_accepted = '$requestAccepted'")
                        ->orWhere("user_to_id =" . $userId . " AND request_accepted = '$requestAccepted'");
            } else {
                $query->where("user_from_id =" . Yii::$app->user->identity->id . " AND request_accepted = '$requestAccepted'")
                        ->orWhere("user_to_id =" . Yii::$app->user->identity->id . " AND request_accepted = '$requestAccepted'");
            }
        }
        if ($userId) {
            $query->innerJoin('users', 'user_from_id = id AND id<>' . $userId .
                    ' OR user_to_id = id AND id<>' . $userId);
        } else {
            $query->innerJoin('users', 'user_from_id = id AND id<>' . Yii::$app->user->identity->id .
                    ' OR user_to_id = id AND id<>' . Yii::$app->user->identity->id);
        }

        $query = $query->orderBy('request_modified DESC');
        return $query;
    }

    /**
     * 
     * @return int
     */
    public function getNotificationCount() {
        return (new Query())
                        ->select('request_id')
                        ->from('contact_requests')
                        ->where("user_to_id =" . Yii::$app->user->identity->id . " AND request_seen = 'N'")
                        ->count();
    }

    /**
     * 
     * @param int $id
     * @return array
     */
    public function checkRelationship($id) {
        return (new Query())
                        ->select('request_id,request_accepted,request_seen,user_from_id,user_to_id')
                        ->from('contact_requests')
                        ->where("user_from_id =" . $id . " AND user_to_id = " . Yii::$app->user->identity->id)
                        ->orWhere("user_to_id =" . $id . " AND user_from_id = " . Yii::$app->user->identity->id)
                        ->one();
    }

    /**
     * void
     */
    public function updateSeenRows() {
        $seenIds = (new Query())
                ->select('GROUP_CONCAT(request_id) as requestIds')
                ->from('contact_requests')
                ->where("user_to_id =" . Yii::$app->user->identity->id)
                ->orderBy('request_created DESC')
                ->limit(6)
                ->one();
        if ($seenIds['requestIds']) {
            Request::updateAll(['request_seen' => "Y"], 'request_id IN(' . $seenIds['requestIds'] . ')');
        }
    }

    /**
     * @return array
     */
    public function getNotConnectedUsers($limit = 5, $existsIds = false) {
        $colleagues = self::getColleagues('Y');
        $colleagues = $colleagues->all();
        $ids = '';
        foreach ($colleagues as $colleague) {
            if (empty($ids)) {
                $comma = '';
            } else {
                $comma = ',';
            }
            $ids.=$comma . $colleague['id'];
        }
        if (!$existsIds) {
            $existsIds = '';
        } else {
            $existsIds = 'AND users.id NOT IN(' . $existsIds . ') ';
        }

        $users = (new Query())
                ->select('GROUP_CONCAT(DISTINCT not_colleagues_users.not_colleague_id) as not_colleagues,'
                        . 'GROUP_CONCAT(DISTINCT CONCAT(request_sent.user_from_id)) as request_sent,'
                        . 'users.id,'
                        . 'users.first_name,'
                        . 'users.last_name,'
                        . 'users.image,'
                        . 'users.location'
                )
                ->from('users')
                ->leftJoin('contact_requests', 'contact_requests.user_from_id IN(' . $ids . ') OR contact_requests.user_to_id IN(' . $ids . ') AND contact_requests.request_accepted = "Y"')
                ->leftJoin('not_colleagues_users', 'not_colleagues_users.user_id = ' . \Yii::$app->user->id . ' AND not_colleagues_users.not_colleague_id = users.id')
                ->leftJoin('contact_requests as request_sent', '(request_sent.user_from_id = ' . \Yii::$app->user->id . ' AND request_sent.user_to_id = users.id) OR (request_sent.user_to_id = ' . \Yii::$app->user->id.' AND request_sent.user_from_id = users.id)')
                ->where('(users.id = contact_requests.user_from_id OR users.id = contact_requests.user_to_id) AND users.id NOT IN(' . $ids . ') AND users.id <> ' . \Yii::$app->user->id . $existsIds)
                ->limit($limit)
                ->orderBy('RAND()')
                ->groupBy('users.id')
                ->having('not_colleagues IS NULL AND request_sent IS NULL')
                ->all();
        return $users;
    }

    /**
     * @return array
     */
    public function addNotColleaguesUser($id) {
        if (!$id) {
            return false;
        }
        $users = (new Query())->createCommand()
                ->insert('not_colleagues_users', [
                    'user_id' => \Yii::$app->user->id,
                    'not_colleague_id' => $id,
                    'created' => new Expression('NOW()'),
                    'modified' => new Expression('NOW()')
                ])
                ->execute();
        if ($users) {
            return true;
        } else {
            return false;
        }
    }

}
