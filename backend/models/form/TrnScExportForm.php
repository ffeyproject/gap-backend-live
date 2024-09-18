<?php
namespace backend\models\form;

use backend\modules\user\models\User;
use common\models\ar\MstBankAccount;
use common\models\ar\MstCurrency;
use common\models\ar\MstCustomer;
use common\models\ar\MstJenisOrder;
use common\models\ar\TrnSc;

class TrnScExportForm extends TrnSc
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'date', 'cust_id', 'currency', 'jenis_order', 'ongkos_angkut', 'pmt_method', 'destination', 'pmt_term', 'disc_grade_b',
                    'no_po', 'direktur_id', 'manager_id', 'marketing_id', 'disc_piece_kecil', 'delivery_date', 'due_date', 'bank_acct_id',
                    'consignee_name', 'notify_party', 'buyer_name_in_invoice'
                ],
                'required'
            ],

            [['date', 'due_date', 'delivery_date'], 'date', 'format'=>'php:Y-m-d'],

            [['cust_id', 'tipe_kontrak', 'jenis_order', 'currency', 'bank_acct_id', 'direktur_id', 'manager_id', 'marketing_id', 'pmt_term', 'ongkos_angkut', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['destination', 'notify_party', 'note', 'pmt_method'], 'string'],
            [['jet_black'], 'boolean'],
            [['disc_grade_b', 'disc_piece_kecil'], 'number'],
            [['packing', 'no_po', 'consignee_name', 'buyer_name_in_invoice'], 'string', 'max' => 255],
            [['cust_id'], 'exist', 'skipOnError' => true, 'targetClass' => MstCustomer::class, 'targetAttribute' => ['cust_id' => 'id']],
            [['direktur_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['direktur_id' => 'id']],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['manager_id' => 'id']],
            [['marketing_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['marketing_id' => 'id']],
            ['tipe_kontrak', 'in', 'range' => [self::TIPE_KONTRAK_LOKAL, self::TIPE_KONTRAK_EXPORT]],
            ['currency', 'in', 'range' => [self::CURRENCY_IDR, self::CURRENCY_USD]],
            ['jenis_order', 'in', 'range' => [self::JENIS_ORDER_FRESH_ORDER, self::JENIS_ORDER_MAKLOON, self::JENIS_ORDER_BARANG_JADI, self::JENIS_ORDER_STOK]],
            [['status'], 'default', 'value'=>1]
        ];
    }
}