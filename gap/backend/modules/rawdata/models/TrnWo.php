<?php

namespace backend\modules\rawdata\models;

use Yii;

/**
 * This is the model class for table "trn_wo".
 *
 * @property int $id
 * @property int $sc_id
 * @property int $sc_greige_id
 * @property int $mo_id
 * @property int $jenis_order Pilihan jenis order sama dengan (mereferensi) jenis order pada SC
 * @property int $greige_id Greige yang digunakan berdasarkan greige_group pada tabel sc_greige
 * @property int $mengetahui_id
 * @property int|null $apv_mengetahui_at
 * @property string|null $reject_note_mengetahui
 * @property int|null $no_urut
 * @property string|null $no
 * @property string $date
 * @property string|null $plastic_size
 * @property string|null $shipping_mark
 * @property string|null $note
 * @property string|null $note_two
 * @property int $marketing_id
 * @property int|null $apv_marketing_at
 * @property string|null $reject_note_marketing
 * @property int|null $posted_at
 * @property int|null $closed_at
 * @property int|null $closed_by
 * @property string|null $closed_note
 * @property int|null $batal_at
 * @property int|null $batal_by
 * @property string|null $batal_note
 * @property int $status 1=draft, 2=posted, 3=approved by mengetahui, 4=approved by marketing, 5=approved, 6=rejected, 7=closed, 8=batal
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int $handling_id
 * @property int $papper_tube_id
 *
 * @property TrnInspecting[] $trnInspectings
 * @property TrnKartuProsesDyeing[] $trnKartuProsesDyeings
 * @property TrnKartuProsesDyeingItem[] $trnKartuProsesDyeingItems
 * @property TrnKartuProsesMaklon[] $trnKartuProsesMaklons
 * @property TrnKartuProsesMaklonItem[] $trnKartuProsesMaklonItems
 * @property TrnKartuProsesPrinting[] $trnKartuProsesPrintings
 * @property TrnKartuProsesPrintingItem[] $trnKartuProsesPrintingItems
 * @property MstGreige $greige
 * @property MstHandling $handling
 * @property MstPapperTube $papperTube
 * @property TrnMo $mo
 * @property TrnSc $sc
 * @property TrnScGreige $scGreige
 * @property User $mengetahui
 * @property User $marketing
 * @property User $closedBy
 * @property User $batalBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property TrnWoColor[] $trnWoColors
 * @property TrnWoMemo[] $trnWoMemos
 */
class TrnWo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trn_wo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'date', 'marketing_id', 'status', 'handling_id'], 'required'],
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'papper_tube_id'], 'default', 'value' => null],
            [['sc_id', 'sc_greige_id', 'mo_id', 'jenis_order', 'greige_id', 'mengetahui_id', 'apv_mengetahui_at', 'no_urut', 'marketing_id', 'apv_marketing_at', 'posted_at', 'closed_at', 'closed_by', 'batal_at', 'batal_by', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'handling_id', 'papper_tube_id'], 'integer'],
            [['reject_note_mengetahui', 'shipping_mark', 'note', 'note_two', 'reject_note_marketing', 'closed_note', 'batal_note'], 'string'],
            [['date'], 'safe'],
            [['no', 'plastic_size'], 'string', 'max' => 255],
            [['greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstGreige::className(), 'targetAttribute' => ['greige_id' => 'id']],
            [['handling_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstHandling::className(), 'targetAttribute' => ['handling_id' => 'id']],
            [['papper_tube_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstPapperTube::className(), 'targetAttribute' => ['papper_tube_id' => 'id']],
            [['mo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnMo::className(), 'targetAttribute' => ['mo_id' => 'id']],
            [['sc_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnSc::className(), 'targetAttribute' => ['sc_id' => 'id']],
            [['sc_greige_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnScGreige::className(), 'targetAttribute' => ['sc_greige_id' => 'id']],
            [['mengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['mengetahui_id' => 'id']],
            [['marketing_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['marketing_id' => 'id']],
            [['closed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['closed_by' => 'id']],
            [['batal_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['batal_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sc_id' => 'Sc ID',
            'sc_greige_id' => 'Sc Greige ID',
            'mo_id' => 'Mo ID',
            'jenis_order' => 'Jenis Order',
            'greige_id' => 'Greige ID',
            'mengetahui_id' => 'Mengetahui ID',
            'apv_mengetahui_at' => 'Apv Mengetahui At',
            'reject_note_mengetahui' => 'Reject Note Mengetahui',
            'no_urut' => 'No Urut',
            'no' => 'No',
            'date' => 'Date',
            'plastic_size' => 'Plastic Size',
            'shipping_mark' => 'Shipping Mark',
            'note' => 'Note',
            'note_two' => 'Note Two',
            'marketing_id' => 'Marketing ID',
            'apv_marketing_at' => 'Apv Marketing At',
            'reject_note_marketing' => 'Reject Note Marketing',
            'posted_at' => 'Posted At',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'closed_note' => 'Closed Note',
            'batal_at' => 'Batal At',
            'batal_by' => 'Batal By',
            'batal_note' => 'Batal Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'handling_id' => 'Handling ID',
            'papper_tube_id' => 'Papper Tube ID',
        ];
    }

    /**
     * Gets query for [[TrnInspectings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnInspectings()
    {
        return $this->hasMany(TrnInspecting::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeings()
    {
        return $this->hasMany(TrnKartuProsesDyeing::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesDyeingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesDyeingItems()
    {
        return $this->hasMany(TrnKartuProsesDyeingItem::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesMaklons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklons()
    {
        return $this->hasMany(TrnKartuProsesMaklon::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesMaklonItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesMaklonItems()
    {
        return $this->hasMany(TrnKartuProsesMaklonItem::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPrintings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintings()
    {
        return $this->hasMany(TrnKartuProsesPrinting::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnKartuProsesPrintingItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnKartuProsesPrintingItems()
    {
        return $this->hasMany(TrnKartuProsesPrintingItem::className(), ['wo_id' => 'id']);
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
     * Gets query for [[Handling]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHandling()
    {
        return $this->hasOne(MstHandling::className(), ['id' => 'handling_id']);
    }

    /**
     * Gets query for [[PapperTube]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPapperTube()
    {
        return $this->hasOne(MstPapperTube::className(), ['id' => 'papper_tube_id']);
    }

    /**
     * Gets query for [[Mo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMo()
    {
        return $this->hasOne(TrnMo::className(), ['id' => 'mo_id']);
    }

    /**
     * Gets query for [[Sc]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSc()
    {
        return $this->hasOne(TrnSc::className(), ['id' => 'sc_id']);
    }

    /**
     * Gets query for [[ScGreige]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScGreige()
    {
        return $this->hasOne(TrnScGreige::className(), ['id' => 'sc_greige_id']);
    }

    /**
     * Gets query for [[Mengetahui]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMengetahui()
    {
        return $this->hasOne(User::className(), ['id' => 'mengetahui_id']);
    }

    /**
     * Gets query for [[Marketing]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMarketing()
    {
        return $this->hasOne(User::className(), ['id' => 'marketing_id']);
    }

    /**
     * Gets query for [[ClosedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClosedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'closed_by']);
    }

    /**
     * Gets query for [[BatalBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatalBy()
    {
        return $this->hasOne(User::className(), ['id' => 'batal_by']);
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

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[TrnWoColors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoColors()
    {
        return $this->hasMany(TrnWoColor::className(), ['wo_id' => 'id']);
    }

    /**
     * Gets query for [[TrnWoMemos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrnWoMemos()
    {
        return $this->hasMany(TrnWoMemo::className(), ['wo_id' => 'id']);
    }
}
