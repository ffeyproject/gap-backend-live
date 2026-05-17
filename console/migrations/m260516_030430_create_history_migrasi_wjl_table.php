<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%history_migrasi_wjl}}`.
 */
class m260516_030430_create_history_migrasi_wjl_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%history_migrasi_wjl}}', [
            'id' => $this->primaryKey(),
            'greige_id' => $this->integer()->notNull(),
            'total_qty_out' => $this->float()->defaultValue(0),
            'jumlah_roll_out' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
        ]);
        
        $this->addForeignKey('fk_history_migrasi_wjl_greige', '{{%history_migrasi_wjl}}', 'greige_id', 'mst_greige', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_history_migrasi_wjl_greige', '{{%history_migrasi_wjl}}');
        $this->dropTable('{{%history_migrasi_wjl}}');
    }
}
