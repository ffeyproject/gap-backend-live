<?php

use yii\db\Migration;

/**
 * Class m261116_025953_add_indexes_for_trn_kirim_buyer
 * Adds indexes on trn_kirim_buyer_header, trn_kirim_buyer, and trn_kirim_buyer_item for fast MTD aggregation & queries.
 */
class m261116_025953_add_indexes_for_trn_kirim_buyer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_kirim_buyer_header_status_date ON {{%trn_kirim_buyer_header}} (status, date)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_kirim_buyer_header_id ON {{%trn_kirim_buyer}} (header_id)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_kirim_buyer_item_kirim_buyer_id ON {{%trn_kirim_buyer_item}} (kirim_buyer_id)');
        $this->execute('CREATE INDEX IF NOT EXISTS idx_trn_kirim_buyer_item_stock_id ON {{%trn_kirim_buyer_item}} (stock_id)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP INDEX IF EXISTS idx_trn_kirim_buyer_header_status_date');
        $this->execute('DROP INDEX IF EXISTS idx_trn_kirim_buyer_header_id');
        $this->execute('DROP INDEX IF EXISTS idx_trn_kirim_buyer_item_kirim_buyer_id');
        $this->execute('DROP INDEX IF EXISTS idx_trn_kirim_buyer_item_stock_id');
    }
}
