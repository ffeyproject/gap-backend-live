<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_memo_perubahan_data".
 *
 * @property int $id
 * @property string $description
 * @property string $date
 * @property int $status 1=Draft 2=Posted
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $no_urut
 * @property string|null $no
 *
 * @property User $createdBy
 */
class TrnMemoPerubahanData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_memo_perubahan_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'date', 'created_at', 'created_by'], 'required'],
            [['description'], 'string'],
            [['date'], 'safe'],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'default', 'value' => null],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'no_urut'], 'integer'],
            [['no'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'date' => 'Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'no_urut' => 'No Urut',
            'no' => 'No',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
