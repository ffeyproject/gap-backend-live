<?php
namespace backend\modules\user\models\form;

use common\models\ar\User;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 *
 * @property string $email
 * @property User $user
 */
class AddUserForm extends Model
{

    public $email;
    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\ar\User', 'message' => 'This email address has already been taken.'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     * @throws \yii\base\Exception
     */
    public function addUser()
    {
        if ($this->validate()) {
            $user = new User([
                'username' => $this->email,
                'email' => $this->email,
                'status' => User::STATUS_INACTIVE
            ]);
            $user->setPassword('123456');
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();

            $this->user = $user;

            if(!$this->user->validate()){
                $this->addError('email', Json::encode($user->firstErrors));
                return false;
            }

            /*$mailSent = Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $this->user]
                )
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($this->user->email)
                ->setSubject('Account registration at ' . Yii::$app->name)
                ->send();*/
                $mailSent = Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $this->user]
                )
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($this->user->email)
                ->setSubject('Account registration at ' . Yii::$app->name)
                ->send();
            $mailSent = true;

            if($mailSent){
                if(!$this->user->save(false)){
                    $this->addError('email', 'Gagal, coba lagi.');
                    return false;
                }

                return true;
            }
        }

        return false;
    }
}