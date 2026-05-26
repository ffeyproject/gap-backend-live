<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_mesin_proses_hambatan}}`.
 */
class m260520_085738_create_mst_mesin_proses_hambatan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mst_mesin_proses_hambatan}}', [
            'mst_mesin_proses_id' => $this->integer()->notNull(),
            'mst_jenis_hambatan_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-mst_mesin_proses_hambatan', '{{%mst_mesin_proses_hambatan}}', ['mst_mesin_proses_id', 'mst_jenis_hambatan_id']);

        $this->addForeignKey(
            'fk-mst_mesin_proses_hambatan-mesin',
            '{{%mst_mesin_proses_hambatan}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mst_mesin_proses_hambatan-hambatan',
            '{{%mst_mesin_proses_hambatan}}',
            'mst_jenis_hambatan_id',
            '{{%mst_jenis_hambatan}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-mst_mesin_proses_hambatan-hambatan', '{{%mst_mesin_proses_hambatan}}');
        $this->dropForeignKey('fk-mst_mesin_proses_hambatan-mesin', '{{%mst_mesin_proses_hambatan}}');
        $this->dropTable('{{%mst_mesin_proses_hambatan}}');
    }
}
