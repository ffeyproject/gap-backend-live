<?php
namespace backend\models\search;

use backend\components\Converter;
use common\models\ar\InspectingItem;
use common\models\ar\MstCustomer;
use common\models\ar\MstGreigeGroup;
use common\models\ar\TrnInspecting;
use common\models\ar\TrnWo;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\BaseVarDumper;

/**
 * @property string $no_kirim
 * @property string $tgl_kirim
 * @property string $wo_id
 * @property int $buyer_id
 * @property string $no_lot
 * @property string $motif
 * @property string $design
 * @property string $kombinasi
 * @property string $piece_length
 * @property string $jenis_order
 *
 * @property TrnWo $wo
 * @property MstCustomer $buyer
 * @property string $woNo
 * @property string $buyerName
*/
class
AnalisaPengirimanProduksi extends \yii\base\Model
{
    //CONSTANTS--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------CONSTANTS
    const JENIS_ORDER_DYEING = 1; const JENIS_ORDER_PRINTING = 2; const JENIS_ORDER_MKL_BJ = 3; const JENIS_ORDER_FRESH = 4;
    /**
     * @return array
     */
    public static function jenisOrderOptions(){
        return [
            self::JENIS_ORDER_DYEING => 'Dyeing',
            self::JENIS_ORDER_PRINTING => 'Printing',
            self::JENIS_ORDER_MKL_BJ => 'Makloon Dan Barang Jadi',
            self::JENIS_ORDER_FRESH => 'Fresh',
        ];
    }

    public $fromDate;
    public $toDate;

    public $no_kirim;
    public $tgl_kirim;
    public $wo_id;
    public $buyer_id;
    public $no_lot;
    public $motif;
    public $design;
    public $kombinasi;
    public $piece_length;
    public $jenis_order;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_kirim'], 'required'],
            ['tgl_kirim', 'date', 'format'=>'php:Y-m-d to Y-m-d'],
            [['wo_id', 'buyer_id'], 'integer'],
            [['no_kirim', 'no_lot', 'motif', 'design', 'kombinasi', 'piece_length', 'jenis_order', 'tgl_kirim'], 'string'],
            [['wo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrnWo::className(), 'targetAttribute' => ['wo_id' => 'id']],
            [['buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstCustomer::className(), 'targetAttribute' => ['buyer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_kirim' => 'Tanggal Kirim',
            'woNo' => 'No. WO',
            'buyerName' => 'Buyer'
        ];
    }

    public function search($params)
    {
        $data = [];
        $grandTotal = [
            'grade_a'=>0,
            'grade_b'=>0,
            'grade_c'=>0,
            'grade_pk'=>0,
            'contoh'=>0,
            'total'=>0,
        ];

        if($this->load($params)){
            //BaseVarDumper::dump($params, 10, true);\Yii::$app->end();
            if($this->validate()){
                $this->fromDate = substr($this->tgl_kirim, 0, 10);
                $this->toDate = substr($this->tgl_kirim, 14);

                $inspectings = TrnInspecting::find()
                    ->select([
                        'trn_inspecting.id',
                        'trn_inspecting.no',
                        'trn_inspecting.no_lot',
                        'trn_inspecting.unit',
                        'motif'=>'mst_greige.nama_kain',
                        'no_do'=>'trn_wo.no',
                        'trn_mo.design',
                        'trn_inspecting.kombinasi',
                        'trn_mo.piece_length',
                        'jenis'=>'trn_inspecting.jenis_process',
                        'cust_id'=>'mst_customer.id',
                        'cust_no'=>'mst_customer.cust_no',
                    ])
                    ->joinWith([
                        'wo.greige',
                        'mo.scGreige.sc.cust'
                    ], false)
                    ->where(['trn_inspecting.status'=>TrnInspecting::STATUS_DELIVERED])
                    ->andWhere(['between', 'trn_inspecting.date', $this->fromDate, $this->toDate])
                    ->andFilterWhere([
                        'trn_inspecting.no'=>$this->no_kirim,
                        'trn_inspecting.wo_id' => $this->wo_id,
                        'trn_inspecting.no_lot'=>$this->no_lot,
                        'mst_greige.nama_kain'=>$this->motif,
                        'trn_mo.design'=>$this->design,
                        'trn_inspecting.kombinasi'=>$this->kombinasi,
                        'trn_mo.piece_length'=>$this->piece_length,
                        'trn_inspecting.jenis_process' =>$this->jenis_order,
                    ])
                    ->asArray()
                    ->all()
                ;

                //BaseVarDumper::dump($inspectings, 10, true);\Yii::$app->end();

                if(!empty($inspectings)){
                    foreach ($inspectings as $inspecting) {
                        $inspecting['jenis'] = self::jenisOrderOptions()[$inspecting['jenis']];
                        $inspecting['design'] = empty($inspecting['design']) ? '-' : $inspecting['design'];

                        $q_a = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_A])->sum('qty');
                        $q_b = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_B])->sum('qty');
                        $q_c = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_C])->sum('qty');
                        $q_pk = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_PK])->sum('qty');
                        $q_contoh = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_SAMPLE])->sum('qty');

                        switch ($inspecting['unit']) {
                            case MstGreigeGroup::UNIT_METER:
                                $q_a = $q_a > 0 ? Converter::meterToYard($q_a) : 0;
                                $q_b = $q_b > 0 ? Converter::meterToYard($q_b) : 0;
                                $q_c = $q_c > 0 ? Converter::meterToYard($q_c) : 0;
                                $q_pk = $q_pk > 0 ? Converter::meterToYard($q_pk) : 0;
                                $q_contoh = $q_contoh > 0 ? Converter::meterToYard($q_contoh) : 0;
                                break;
                            case MstGreigeGroup::UNIT_YARD:
                                $q_a = $q_a > 0 ? $q_a : 0;
                                $q_b = $q_b > 0 ? $q_b : 0;
                                $q_c = $q_c > 0 ? $q_c : 0;
                                $q_pk = $q_pk > 0 ? $q_pk : 0;
                                $q_contoh = $q_contoh > 0 ? $q_contoh : 0;
                                break;
                            case MstGreigeGroup::UNIT_KILOGRAM:
                                $q_a = $q_a > 0 ? ($q_a*3) : 0;
                                $q_b = $q_b > 0 ? ($q_b*3) : 0;
                                $q_c = $q_c > 0 ? ($q_c*3) : 0;
                                $q_pk = $q_pk > 0 ? ($q_pk*3) : 0;
                                $q_contoh = $q_contoh > 0 ? $q_contoh : 0;
                                break;
                            default:
                                $q_a = $q_a > 0 ? $q_a : 0;
                                $q_b = $q_b > 0 ? $q_b : 0;
                                $q_c = $q_c > 0 ? $q_c : 0;
                                $q_pk = $q_pk > 0 ? $q_pk : 0;
                                $q_contoh = $q_contoh > 0 ? $q_contoh : 0;
                        }

                        $q_totoal = $q_a + $q_b + $q_c + $q_pk + $q_contoh;

                        $grandTotal['grade_a'] += $q_a;
                        $grandTotal['grade_b'] += $q_b;
                        $grandTotal['grade_c'] += $q_c;
                        $grandTotal['grade_pk'] += $q_pk;
                        $grandTotal['contoh'] += $q_contoh;
                        $grandTotal['total'] += $q_totoal;

                        if(isset($data[$inspecting['cust_id']])){
                            $data[$inspecting['cust_id']]['grade_a'] += $q_a;
                            $data[$inspecting['cust_id']]['grade_b'] += $q_b;
                            $data[$inspecting['cust_id']]['grade_c'] += $q_c;
                            $data[$inspecting['cust_id']]['grade_pk'] += $q_pk;
                            $data[$inspecting['cust_id']]['contoh'] += $q_contoh;
                            $data[$inspecting['cust_id']]['total'] += $q_totoal;

                            if(isset($data[$inspecting['cust_id']]['dos'][$inspecting['no_do']])){
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['grade_a'] += $q_a;
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['grade_b'] += $q_b;
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['grade_c'] += $q_c;
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['grade_pk'] += $q_pk;
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['contoh'] += $q_contoh;
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['total'] += $q_totoal;

                                if(isset($data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']])){
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['grade_a'] += $q_a;
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['grade_b'] += $q_b;
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['grade_c'] += $q_c;
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['grade_pk'] += $q_pk;
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['contoh'] += $q_contoh;
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']]['total'] += $q_totoal;
                                }else{
                                    $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']]['designs'][$inspecting['design']] = [
                                        'design'=>$inspecting['design'],
                                        'grade_a'=>$q_a,
                                        'grade_b'=>$q_b,
                                        'grade_c'=>$q_c,
                                        'grade_pk'=>$q_pk,
                                        'contoh'=>$q_contoh,
                                        'total'=>$q_totoal,
                                    ];
                                }
                            }else{
                                $data[$inspecting['cust_id']]['dos'][$inspecting['no_do']] = [
                                    'no_do' => $inspecting['no_do'],
                                    'motif'=>$inspecting['motif'],
                                    'designs'=>[
                                        $inspecting['design']=>[
                                            'design'=>$inspecting['design'],
                                            'grade_a'=>$q_a,
                                            'grade_b'=>$q_b,
                                            'grade_c'=>$q_c,
                                            'grade_pk'=>$q_pk,
                                            'contoh'=>$q_contoh,
                                            'total'=>$q_totoal,
                                        ]
                                    ],
                                    'jenis'=>$inspecting['jenis'],
                                    'grade_a'=>$q_a,
                                    'grade_b'=>$q_b,
                                    'grade_c'=>$q_c,
                                    'grade_pk'=>$q_pk,
                                    'contoh'=>$q_contoh,
                                    'total'=>$q_totoal,
                                ];
                            }
                        }else{
                            $data[$inspecting['cust_id']] = [
                                'cust_id' => $inspecting['cust_id'],
                                'cust_no' => $inspecting['cust_no'],
                                'dos' => [
                                    $inspecting['no_do'] => [
                                        'no_do' => $inspecting['no_do'],
                                        'motif'=>$inspecting['motif'],
                                        'designs'=>[
                                            $inspecting['design']=>[
                                                'design'=>$inspecting['design'],
                                                'grade_a'=>$q_a,
                                                'grade_b'=>$q_b,
                                                'grade_c'=>$q_c,
                                                'grade_pk'=>$q_pk,
                                                'contoh'=>$q_contoh,
                                                'total'=>$q_totoal,
                                            ]
                                        ],
                                        'jenis'=>$inspecting['jenis'],
                                        'grade_a'=>$q_a,
                                        'grade_b'=>$q_b,
                                        'grade_c'=>$q_c,
                                        'grade_pk'=>$q_pk,
                                        'contoh'=>$q_contoh,
                                        'total'=>$q_totoal,
                                    ]
                                ],
                                'grade_a'=>$q_a,
                                'grade_b'=>$q_b,
                                'grade_c'=>$q_c,
                                'grade_pk'=>$q_pk,
                                'contoh'=>$q_contoh,
                                'total'=>$q_totoal,
                            ];
                        }
                    }
                }
            }
        }

        return ['data'=>$data, 'grand_total'=>$grandTotal];
    }

    public function searchXxxx($params)
    {
        $data = [];

        if($this->load($params)){
            //BaseVarDumper::dump($params, 10, true);\Yii::$app->end();
            if($this->validate()){
                $this->fromDate = substr($this->tgl_kirim, 0, 10);
                $this->toDate = substr($this->tgl_kirim, 14);

                $custs = MstCustomer::find()->select('id, cust_no, name')->filterWhere(['id'=>$this->buyer_id])->asArray()->all();
                foreach ($custs as $cust) {
                    $r = [
                        'cust_id' => $cust['id'],
                        'cust_no' => $cust['cust_no'],
                        'cust_name' => $cust['name'],
                        'dos' => [],
                        'grade_a'=>0,
                        'grade_b'=>0,
                        'grade_c'=>0,
                        'grade_pk'=>0,
                        'contoh'=>0,
                        'total'=>0,
                    ];

                    $inspectings = TrnInspecting::find()
                        ->select([
                            'trn_inspecting.id',
                            'trn_inspecting.no',
                            'trn_inspecting.no_lot',
                            'motif'=>'mst_greige.nama_kain',
                            'no_do'=>'trn_wo.no',
                            'trn_mo.design',
                            'trn_inspecting.kombinasi',
                            'trn_mo.piece_length',
                            'jenis'=>'trn_inspecting.jenis_process',
                            'cust_no'=>'mst_customer.cust_no',
                        ])
                        ->joinWith([
                            //'kartuProcessDyeing',
                            //'kartuProcessPrinting',
                            //'memoRepair',
                            'wo.greige',
                            'mo.scGreige.sc.cust'
                        ], false)
                        ->where(['trn_sc.cust_id'=>$cust['id']])
                        ->andWhere(['trn_inspecting.status'=>TrnInspecting::STATUS_DELIVERED])
                        ->andFilterWhere([
                            'trn_inspecting.no'=>$this->no_kirim,
                            'trn_inspecting.wo_id' => $this->wo_id,
                            'trn_inspecting.no_lot'=>$this->no_lot,
                            'mst_greige.nama_kain'=>$this->motif,
                            'trn_mo.design'=>$this->design,
                            'trn_inspecting.kombinasi'=>$this->kombinasi,
                            'trn_mo.piece_length'=>$this->piece_length,
                            'trn_inspecting.jenis_process' =>$this->jenis_order,
                        ])
                        ->andFilterWhere(['between', 'trn_inspecting.date', $this->fromDate, $this->toDate])
                        ->asArray()
                        ->all()
                    ;

                    if(!empty($inspectings)){
                        $dos = [];
                        foreach ($inspectings as $inspecting) {
                            $inspecting['jenis'] = self::jenisOrderOptions()[$inspecting['jenis']];
                            $inspecting['design'] = empty($inspecting['design']) ? '-' : $inspecting['design'];

                            $q_a = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_A])->sum('qty'); $q_a = $q_a > 0 ? Converter::meterToYard($q_a) : 0;
                            $q_b = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_B])->sum('qty'); $q_b = $q_b > 0 ? Converter::meterToYard($q_b) : 0;
                            $q_c = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_C])->sum('qty'); $q_c = $q_c > 0 ? Converter::meterToYard($q_c) : 0;
                            $q_pk = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_PK])->sum('qty'); $q_pk = $q_pk > 0 ? Converter::meterToYard($q_pk) : 0;
                            $q_contoh = InspectingItem::find()->where(['inspecting_id'=>$inspecting['id'], 'grade'=>InspectingItem::GRADE_SAMPLE])->sum('qty'); $q_contoh = $q_contoh > 0 ? Converter::meterToYard($q_contoh) : 0;
                            $q_totoal = $q_a + $q_b + $q_c + $q_pk + $q_contoh;

                            $r['grade_a'] += $q_a;
                            $r['grade_b'] += $q_b;
                            $r['grade_c'] += $q_c;
                            $r['grade_pk'] += $q_pk;
                            $r['contoh'] += $q_contoh;
                            $r['total'] += $q_totoal;

                            if(isset($dos[$inspecting['no_do']])){
                                $dos[$inspecting['no_do']]['grade_a'] += $q_a;
                                $dos[$inspecting['no_do']]['grade_b'] += $q_b;
                                $dos[$inspecting['no_do']]['grade_c'] += $q_c;
                                $dos[$inspecting['no_do']]['grade_pk'] += $q_pk;
                                $dos[$inspecting['no_do']]['contoh'] += $q_contoh;
                                $dos[$inspecting['no_do']]['total'] += $q_totoal;

                                if(isset($dos[$inspecting['no_do']]['designs'][$inspecting['design']])){
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['grade_a'] += $q_a;
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['grade_b'] += $q_b;
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['grade_c'] += $q_c;
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['grade_pk'] += $q_pk;
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['contoh'] += $q_contoh;
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']]['total'] += $q_totoal;
                                }else{
                                    $dos[$inspecting['no_do']]['designs'][$inspecting['design']] = [
                                        'design'=>$inspecting['design'],
                                        'grade_a'=>$q_a,
                                        'grade_b'=>$q_b,
                                        'grade_c'=>$q_c,
                                        'grade_pk'=>$q_pk,
                                        'contoh'=>$q_contoh,
                                        'total'=>$q_totoal,
                                    ];
                                }
                            }else{
                                $dos[$inspecting['no_do']] = [
                                    'no_do' => $inspecting['no_do'],
                                    'motif'=>$inspecting['motif'],
                                    'designs'=>[
                                        $inspecting['design']=>[
                                            'design'=>$inspecting['design'],
                                            'grade_a'=>$q_a,
                                            'grade_b'=>$q_b,
                                            'grade_c'=>$q_c,
                                            'grade_pk'=>$q_pk,
                                            'contoh'=>$q_contoh,
                                            'total'=>$q_totoal,
                                        ]
                                    ],
                                    'jenis'=>$inspecting['jenis'],
                                    'grade_a'=>$q_a,
                                    'grade_b'=>$q_b,
                                    'grade_c'=>$q_c,
                                    'grade_pk'=>$q_pk,
                                    'contoh'=>$q_contoh,
                                    'total'=>$q_totoal,
                                ];
                            }
                        }

                        $r['dos'] = $dos;

                        $data[] = $r;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return \yii\db\Query
     */
    public function getWo()
    {
        return (new Query())->from(TrnWo::tableName())->filterWhere(['id' => $this->wo_id]);
    }

    /**
     * @return \yii\db\Query
     */
    public function getBuyer()
    {
        return (new Query())->from(MstCustomer::tableName())->filterWhere(['id' => $this->buyer_id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuyerName()
    {
        return $this->getBuyer()
            ->select(new Expression('concat(name, \' (\', cust_no, \')\') "buyer"'))
            ->one()
        ['buyer']
            ;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWoNo()
    {
        return $this->getWo()->select('no')->one()['no'];
    }
}