<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_notif".
 *
 * @property int $id
 * @property int $user_id
 * @property string $message
 * @property string|null $link
 * @property int $type 1=Notification, 2=Message, 3=Task
 * @property bool $read
 * @property int $created_at
 *
 * @property User $user
 */
class TrnNotif extends \yii\db\ActiveRecord
{
    const TYPE_NOTIFICATION = 1;const TYPE_MESSAGE = 2;const TYPE_TASK = 3;
    /**
     * @return array
     */
    public static function asalGreigeOptions(){
        return [self::TYPE_NOTIFICATION => 'Notification', self::TYPE_MESSAGE => 'Message', self::TYPE_TASK => 'Task'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_notif';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'message', 'type', 'created_at'], 'required'],
            [['user_id', 'type', 'created_at'], 'default', 'value' => null],
            [['user_id', 'type', 'created_at'], 'integer'],
            [['message', 'link'], 'string'],
            [['read'], 'boolean'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'message' => 'Message',
            'link' => 'Link',
            'type' => 'Type',
            'read' => 'Read',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
