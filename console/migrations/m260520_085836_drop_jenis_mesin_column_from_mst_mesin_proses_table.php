<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%mst_mesin_proses}}`.
 */
class m260520_085836_drop_jenis_mesin_column_from_mst_mesin_proses_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%mst_mesin_proses}}', 'jenis_mesin');
        $this->dropForeignKey('fk_mst_mesin_proses_jenis_hambatan', '{{%mst_mesin_proses}}');
        $this->dropColumn('{{%mst_mesin_proses}}', 'mst_jenis_hambatan_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%mst_mesin_proses}}', 'jenis_mesin', $this->integer()->notNull());
        $this->addColumn('{{%mst_mesin_proses}}', 'mst_jenis_hambatan_id', $this->integer());
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
}
