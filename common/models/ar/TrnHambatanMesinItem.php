<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "trn_hambatan_mesin_item".
 *
 * @property int $id
 * @property int $trn_hambatan_mesin_id
 * @property string $start_time
 * @property string $stop_time
 * @property string|null $no_kartu
 * @property string|null $keterangan
 * @property string|null $shift
 * @property string|null $no_wo
 *
 * @property TrnHambatanMesin $trnHambatanMesin
 * @property MstJenisHambatan[] $mstJenisHambatans
 */
class TrnHambatanMesinItem extends \yii\db\ActiveRecord
{
    /**
     * @var array Selected jenis hambatan IDs
     */
    public $jenis_hambatan_ids = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_hambatan_mesin_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trn_hambatan_mesin_id', 'start_time', 'stop_time'], 'required'],
            [['trn_hambatan_mesin_id'], 'integer'],
            [['keterangan'], 'string'],
            [['start_time', 'stop_time', 'shift'], 'string', 'max' => 50],
            [['no_kartu', 'no_wo'], 'string', 'max' => 100],
            [['jenis_hambatan_ids'], 'safe'],
            [['trn_hambatan_mesin_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnHambatanMesin::className(), 'targetAttribute' => ['trn_hambatan_mesin_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trn_hambatan_mesin_id' => 'Header ID',
            'start_time' => 'Start',
            'stop_time' => 'Stop',
            'jenis_hambatan_ids' => 'Jenis Hambatan',
            'no_kartu' => 'NK (jika ada)',
            'no_wo' => 'WO (jika ada)',
            'shift' => 'Shift',
            'keterangan' => 'Keterangan',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrnHambatanMesin()
    {
        return $this->hasOne(TrnHambatanMesin::className(), ['id' => 'trn_hambatan_mesin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMstJenisHambatans()
    {
        return $this->hasMany(MstJenisHambatan::className(), ['id' => 'mst_jenis_hambatan_id'])
            ->viaTable('trn_hambatan_mesin_item_hambatan', ['trn_hambatan_mesin_item_id' => 'id']);
    }

    /**
     * Populate `jenis_hambatan_ids` after find
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->jenis_hambatan_ids = \yii\helpers\ArrayHelper::getColumn($this->mstJenisHambatans, 'id');
    }
}
