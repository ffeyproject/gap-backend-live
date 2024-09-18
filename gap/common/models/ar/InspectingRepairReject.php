<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "inspecting_repair_reject".
 *
 * @property int $id
 * @property int $memo_repair_id
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $untuk_bagian
 * @property string|null $pcs
 * @property string|null $keterangan
 * @property string|null $penerima
 * @property string|null $mengetahui
 * @property string|null $pengirim
 * @property int $created_at
 * @property int $created_by
 *
 * @property TrnMemoRepair $memoRepair
 */
class InspectingRepairReject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspecting_repair_reject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['memo_repair_id', 'date', 'created_at', 'created_by'], 'required'],
            [['memo_repair_id', 'no_urut', 'created_at', 'created_by'], 'default', 'value' => null],
            [['memo_repair_id', 'no_urut', 'created_at', 'created_by'], 'integer'],
            [['date'], 'safe'],
            [['no', 'untuk_bagian', 'pcs', 'keterangan', 'penerima', 'mengetahui', 'pengirim'], 'string', 'max' => 255],
            [['memo_repair_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMemoRepair::className(), 'targetAttribute' => ['memo_repair_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'memo_repair_id' => 'Memo Repair ID',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'untuk_bagian' => 'Untuk Bagian',
            'pcs' => 'Pcs',
            'keterangan' => 'Keterangan',
            'penerima' => 'Penerima',
            'mengetahui' => 'Mengetahui',
            'pengirim' => 'Pengirim',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[MemoRepair]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemoRepair()
    {
        return $this->hasOne(TrnMemoRepair::className(), ['id' => 'memo_repair_id']);
    }
}
