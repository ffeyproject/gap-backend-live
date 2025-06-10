<?php
namespace backend\modules\user\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use mdm\admin\components\Helper;
use yii\web\UrlManager;

/**
 * This is the model class for table "article".
 *
 */
class User extends \common\models\User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $newRules = [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],

            [['signature', 'full_name'], 'string'],

            [['status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['username'], 'unique'],
        ];

        return ArrayHelper::merge($rules, $newRules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'signature' => 'Signature'
        ];
    }

    /**
     * Get all available and assigned roles/permission
     * @return array
     */
    public function getRbacItems()
    {
        $manager = Yii::$app->authManager;
        $available = [];
        foreach (array_keys($manager->getRoles()) as $name) {
            if($name !== Yii::$app->params['rbac_roles']['developer']){
                $available[$name] = 'role';
            }
        }

        foreach (array_keys($manager->getPermissions()) as $name) {
            if ($name[0] != '/') {
                $available[$name] = 'permission';
            }
        }

        $assigned = [];
        foreach ($manager->getAssignments($this->id) as $item) {
            if($item->roleName !== Yii::$app->params['rbac_roles']['developer']){
                $assigned[$item->roleName] = $available[$item->roleName];
                unset($available[$item->roleName]);
            }
        }

        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    /**
     * Grands a roles from a user.
     * @param array $items
     * @return integer number of successful grand
     */
    public function assign($items)
    {
        $manager = Yii::$app->authManager;
        $success = 0;
        foreach ($items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ?: $manager->getPermission($name);
                $manager->assign($item, $this->id);
                $success++;
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
        return $success;
    }

    /**
     * Revokes a roles from a user.
     * @param array $items
     * @return integer number of successful revoke
     */
    public function revoke($items)
    {
        $manager = Yii::$app->authManager;
        $success = 0;
        foreach ($items as $name) {
            try {
                $item = $manager->getRole($name);
                $item = $item ?: $manager->getPermission($name);
                $manager->revoke($item, $this->id);
                $success++;
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Helper::invalidate();
        return $success;
    }

    /**
     * @return array
     */
    public static function getStatusOptions(){
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DELETED => 'Deleted'
        ];
    }

    /**
     * @param $role
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getUsersByRoles($role = null){
        $userIds = null;
        if($role !== null){
            $manager = Yii::$app->authManager;
            $userIds = $manager->getUserIdsByRole($role);
        }

        $query = new Query;
        $query->select(new Expression("id, concat(full_name, ' (', email, ')') \"text\""))
            ->from('user')
            ->where(['status'=>self::STATUS_ACTIVE])
            ->andFilterWhere(['id'=>$userIds])
        ;
        $command = $query->createCommand();
        $data = $command->queryAll();
        return ArrayHelper::map($data, 'id', 'text');
    }
}