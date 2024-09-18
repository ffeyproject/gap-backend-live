<?php

namespace backend\models\form;

use common\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class VerifyEmailForm extends Model
{
    public $token;
    public $full_name;
    public $username;
    public $password;
    public $password_repeat;

    /**
     * @var User
     */
    private $_user;


    /**
     * Creates a form model with given token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Verify email token cannot be blank.');
        }
        $this->_user = User::findByVerificationToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong verify email token.');
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['full_name', 'required'],
            ['full_name', 'string'],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u'],

            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * Verify email
     *
     * @return User|null the saved model or null if saving fails
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function verifyEmail()
    {
        if(!$this->validate()){
            return null;
        }

        $user = $this->_user;
        $user->full_name = $this->full_name;
        $user->username = $this->username;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($this->password);

        if($user->save(false)){
            $auth = Yii::$app->authManager;
            $assignedRole = $auth->getRole('Registered');
            $auth->assign($assignedRole, $user->id);
            return $user;
        }

        return null;
    }
}
