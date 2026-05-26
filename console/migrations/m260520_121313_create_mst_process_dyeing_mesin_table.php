<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_process_dyeing_mesin}}`.
 */
class m260520_121313_create_mst_process_dyeing_mesin_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%mst_process_dyeing_mesin}}', [
            'mst_process_dyeing_id' => $this->integer()->notNull(),
            'mst_mesin_proses_id' => $this->integer()->notNull(),
            'PRIMARY KEY (mst_process_dyeing_id, mst_mesin_proses_id)'
        ]);

        $this->addForeignKey(
            'fk-mst_process_dyeing_mesin-process',
            '{{%mst_process_dyeing_mesin}}',
            'mst_process_dyeing_id',
            '{{%mst_process_dyeing}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mst_process_dyeing_mesin-mesin',
            '{{%mst_process_dyeing_mesin}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-mst_process_dyeing_mesin-mesin', '{{%mst_process_dyeing_mesin}}');
        $this->dropForeignKey('fk-mst_process_dyeing_mesin-process', '{{%mst_process_dyeing_mesin}}');
        $this->dropTable('{{%mst_process_dyeing_mesin}}');
    }
}
