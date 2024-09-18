<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "pfp_keluar_verpacking_item".
 *
 * @property int $id
 * @property int $pfp_keluar_verpacking_id
 * @property float $ukuran
 * @property string|null $join_piece
 * @property string|null $keterangan
 * @property int $status 1=Stock, 2=Dijual
 *
 * @property PfpKeluarVerpacking $pfpKeluarVerpacking
 */
class PfpKeluarVerpackingItem extends \yii\db\ActiveRecord
{
    const STATUS_STOCK = 1;
    const STATUS_DIJUAL = 2;

    /**
     * @return array
     */
    public static function statusOptions(){
        return [
            self::STATUS_STOCK => 'Stock',
            self::STATUS_DIJUAL => 'Dijual',
        ];
    }

    /**
     * @return string
     */
    public function getStatusName(){
        return self::statusOptions()[$this->status];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pfp_keluar_verpacking_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ukuran'], 'required'],
            [['pfp_keluar_verpacking_id'], 'default', 'value' => null],
            [['pfp_keluar_verpacking_id'], 'integer'],
            [['ukuran'], 'number'],
            [['keterangan', 'join_piece'], 'string'],
            [['pfp_keluar_verpacking_id'], 'exist', 'skipOnError' => true, 'targetClass' => PfpKeluarVerpacking::className(), 'targetAttribute' => ['pfp_keluar_verpacking_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pfp_keluar_verpacking_id' => 'Pfp Keluar Verpacking ID',
            'ukuran' => 'Ukuran',
            'join_piece' => 'Join Piece',
            'keterangan' => 'Keterangan',
            'statusName' => 'Status',
        ];
    }

    /**
     * Gets query for [[PfpKeluarVerpacking]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPfpKeluarVerpacking()
    {
        return $this->hasOne(PfpKeluarVerpacking::className(), ['id' => 'pfp_keluar_verpacking_id']);
    }
}
