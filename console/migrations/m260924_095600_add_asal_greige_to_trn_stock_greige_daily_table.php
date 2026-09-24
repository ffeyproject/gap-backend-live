<?php

use yii\db\Migration;

/**
 * Handles adding `asal_greige` column to table `trn_stock_greige_daily`.
 */
class m260924_095600_add_asal_greige_to_trn_stock_greige_daily_table extends Migration
{
    const TABLE_NAME = "trn_stock_greige_daily";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn(
            self::TABLE_NAME,
            'asal_greige',
            $this->tinyInteger(2)->notNull()->defaultValue(1)->comment('1=Water Jet Loom, 2=Beli Lokal, 3=Rapier, 4=Beli Import, 5=Lain-lain, 6=Retur, 7=Mutasi, 8=Pemotongan, 9=Hasil Makloon')
        );

        $this->dropIndex('uq_trn_stock_greige_daily_date_greige', self::TABLE_NAME);

        $this->createIndex(
            'uq_trn_stock_greige_daily_date_greige_asal',
            self::TABLE_NAME,
            ['date', 'greige_id', 'asal_greige'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropIndex('uq_trn_stock_greige_daily_date_greige_asal', self::TABLE_NAME);

        $this->createIndex(
            'uq_trn_stock_greige_daily_date_greige',
            self::TABLE_NAME,
            ['date', 'greige_id'],
            true
        );

        $this->dropColumn(self::TABLE_NAME, 'asal_greige');
    }
}
