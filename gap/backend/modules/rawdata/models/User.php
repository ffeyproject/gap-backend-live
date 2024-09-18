<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 * @property string|null $full_name
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnInspecting[] $trnInspectings0
 * @property TrnInspecting[] $trnInspectings1
 * @property TrnInspecting[] $trnInspectings2
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings0
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems0
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings0
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems0
 * @property TrnMo[] $trnMos
 * @property TrnMo[] $trnMos0
 * @property TrnMo[] $trnMos1
 * @property TrnMo[] $trnMos2
 * @property TrnMo[] $trnMos3
 * @property TrnSc[] $trnScs
 * @property TrnSc[] $trnScs0
 * @property TrnSc[] $trnScs1
 * @property TrnSc[] $trnScs2
 * @property TrnSc[] $trnScs3
 * @property TrnWo[] $trnWos
 * @property TrnWo[] $trnWos0
 * @property TrnWo[] $trnWos1
 * @property TrnWo[] $trnWos2
 * @property TrnWo[] $trnWos3
 * @property TrnWo[] $trnWos4
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'verification_token', 'full_name'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['username'], 'unique'],
        ];
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
            'full_name' => 'Full Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings0()
    {
        return $this->hasMany(TrnInspecting::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings1()
    {
        return $this->hasMany(TrnInspecting::className(), ['approved_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings2()
    {
        return $this->hasMany(TrnInspecting::className(), ['delivered_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings0()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems0()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings0()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems0()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['approval_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos0()
    {
        return $this->hasMany(TrnMo::className(), ['closed_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos1()
    {
        return $this->hasMany(TrnMo::className(), ['batal_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos2()
    {
        return $this->hasMany(TrnMo::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos3()
    {
        return $this->hasMany(TrnMo::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs()
    {
        return $this->hasMany(TrnSc::className(), ['direktur_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs0()
    {
        return $this->hasMany(TrnSc::className(), ['manager_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs1()
    {
        return $this->hasMany(TrnSc::className(), ['marketing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs2()
    {
        return $this->hasMany(TrnSc::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnScs3()
    {
        return $this->hasMany(TrnSc::className(), ['updated_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos()
    {
        return $this->hasMany(TrnWo::className(), ['mengetahui_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos0()
    {
        return $this->hasMany(TrnWo::className(), ['marketing_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos1()
    {
        return $this->hasMany(TrnWo::className(), ['closed_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos2()
    {
        return $this->hasMany(TrnWo::className(), ['batal_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos3()
    {
        return $this->hasMany(TrnWo::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWos4()
    {
        return $this->hasMany(TrnWo::className(), ['updated_by' => 'id']);
    }
}
