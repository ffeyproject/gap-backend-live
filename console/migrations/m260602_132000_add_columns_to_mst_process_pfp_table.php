<?php

use yii\db\Migration;

/**
 * Class m260602_132000_add_columns_to_mst_process_pfp_table
 */
class m260602_132000_add_columns_to_mst_process_pfp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%mst_process_pfp}}', 'panjang_jadi', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%mst_process_pfp}}', 'keterangan', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%mst_process_pfp}}', 'perbaikan', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%mst_process_pfp}}', 'use_jetblack', $this->boolean()->defaultValue(false));

        $this->createTable('{{%mst_process_pfp_mesin}}', [
            'mst_process_pfp_id' => $this->integer()->notNull(),
            'mst_mesin_proses_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-mst_process_pfp_mesin', '{{%mst_process_pfp_mesin}}', ['mst_process_pfp_id', 'mst_mesin_proses_id']);
        
        $this->addForeignKey(
            'fk-mst_process_pfp_mesin-process',
            '{{%mst_process_pfp_mesin}}',
            'mst_process_pfp_id',
            '{{%mst_process_pfp}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-mst_process_pfp_mesin-mesin',
            '{{%mst_process_pfp_mesin}}',
            'mst_mesin_proses_id',
            '{{%mst_mesin_proses}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-mst_process_pfp_mesin-mesin', '{{%mst_process_pfp_mesin}}');
        $this->dropForeignKey('fk-mst_process_pfp_mesin-process', '{{%mst_process_pfp_mesin}}');
        $this->dropTable('{{%mst_process_pfp_mesin}}');
        
        $this->dropColumn('{{%mst_process_pfp}}', 'use_jetblack');
        $this->dropColumn('{{%mst_process_pfp}}', 'perbaikan');
        $this->dropColumn('{{%mst_process_pfp}}', 'keterangan');
        $this->dropColumn('{{%mst_process_pfp}}', 'panjang_jadi');
    }
}
