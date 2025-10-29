<?php

use yii\db\Migration;

/**
 * Class m251027_093247_fix_item_log_fk
 */
class m251027_093247_fix_item_log_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Drop FK lama yang menyebabkan item_id jadi NULL saat item dihapus
        $this->dropForeignKey('fk_itemlog_item', '{{%trn_kartu_proses_dyeing_item_log}}');

        // Buat index biasa untuk pencarian cepat
        $this->createIndex(
            'idx_itemlog_item_id',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'item_id'
        );
    }

    /**
     * {@inheritdoc}
     */
     public function safeDown()
    {
        // Rollback ke kondisi awal (tidak disarankan)
        $this->dropIndex('idx_itemlog_item_id', '{{%trn_kartu_proses_dyeing_item_log}}');
        $this->addForeignKey(
            'fk_itemlog_item',
            '{{%trn_kartu_proses_dyeing_item_log}}',
            'item_id',
            '{{%trn_kartu_proses_dyeing_item}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251027_093247_fix_item_log_fk cannot be reverted.\n";

        return false;
    }
    */
}