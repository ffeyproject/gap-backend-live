<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mst_handling".
 *
 * @property int $id
 * @property int $greige_id
 * @property string $name
 * @property string $lebar_preset
 * @property string $lebar_finish
 * @property string $berat_finish
 * @property string $densiti_lusi
 * @property string $densiti_pakan
 * @property string $buyer_ids
 * @property bool|null $ket_washing
 * @property bool|null $ket_wr
 * @property int $berat_persiapan
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $keterangan
 * @property string $no_hanger
 *
 * @property MstGreige $greige
 * @property TrnMo[] $trnMos
 */
class MstHandling extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mst_handling';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['greige_id', 'name', 'lebar_preset', 'lebar_finish', 'berat_finish', 'densiti_lusi', 'densiti_pakan'], 'required'],
            [['ket_washing', 'ket_wr', 'greige_id', 'buyer_ids', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            ['berat_persiapan', 'default', 'value'=>0],
            ['berat_persiapan', 'number'],
            [['ket_washing', 'ket_wr'], 'boolean'],
            [['greige_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['greige_id', 'name'], 'unique', 'targetAttribute' => ['greige_id', 'name']],
            [['name', 'lebar_preset', 'lebar_finish', 'berat_finish', 'densiti_lusi', 'densiti_pakan', 'no_hanger'], 'string', 'max' => 255],
            ['buyer_ids', 'match', 'pattern' => '/^([1-9]\d*)(,[1-9]\d*)*$/'],// 1,2,3,4,5,6,7,8 ....dst
            ['buyer_ids', function ($attribute, $params, $validator) {
                $ids = explode(',', $this->$attribute);
                foreach ($ids as $id) {
                    $count = (new \yii\db\Query())
                        ->select(['id'])
                        ->from(MstCustomer::tableName())
                        ->where(['id' => $id])
                        ->limit(1)
                        ->count('id');
                    if(!($count === 1)){
                        $this->addError($attribute, 'Buyer Id: '.$id.' Tidak valid.');
                    }
                }
            }],
            ['keterangan', 'string'],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'greige_id' => 'Greige ID',
            'name' => 'Name',
            'lebar_preset' => 'Lebar Preset',
            'lebar_finish' => 'Lebar Finish',
            'berat_finish' => 'Berat Finish',
            'densiti_lusi' => 'Densiti Lusi',
            'densiti_pakan' => 'Densiti Pakan',
            'buyer_ids' => 'Buyer Ids',
            'ket_washing' => 'Ket. Washing',
            'ket_wr' => 'Ket. WR',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'keterangan' => 'Keterangan',
            'no_hanger' => 'No Hanger'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'buyer_ids' => 'ID Buyer-buyer yang dikaitkan dengan Handling ini, dipisahkan dengan tanda koma(,). Contoh: 1,2,3,4',
        ];
    }

    /**
     * Gets query for [[Greige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGreige()
    {
        return $this->hasOne(MstGreige::className(), ['id' => 'greige_id']);
    }

    /**
     * Gets query for [[TrnMos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnMos()
    {
        return $this->hasMany(TrnMo::className(), ['handling_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function optionList(){
        $models = self::find()->asArray()->all();
        return ArrayHelper::map($models, 'id', 'name');
    }
}
