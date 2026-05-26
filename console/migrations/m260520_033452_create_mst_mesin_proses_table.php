<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_mesin_proses}}`.
 */
class m260520_033452_create_mst_mesin_proses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mst_mesin_proses}}', [
            'id' => $this->primaryKey(),
            'nama_mesin' => $this->string()->notNull(),
            'jenis_mesin' => $this->integer()->notNull(),
            'mst_jenis_hambatan_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk_mst_mesin_proses_jenis_hambatan',
            '{{%mst_mesin_proses}}',
            'mst_jenis_hambatan_id',
            'mst_jenis_hambatan',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_mst_mesin_proses_jenis_hambatan', '{{%mst_mesin_proses}}');
        $this->dropTable('{{%mst_mesin_proses}}');
    }
}
