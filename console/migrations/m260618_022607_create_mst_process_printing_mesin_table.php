<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_process_printing_mesin}}`.
 */
class m260618_022607_create_mst_process_printing_mesin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%mst_process_printing_mesin}}', true) === null) {
            $this->createTable('{{%mst_process_printing_mesin}}', [
                'mst_process_printing_id' => $this->integer()->notNull(),
                'mst_mesin_proses_id' => $this->integer()->notNull(),
            ]);
            
            $this->addPrimaryKey(
                'pk-mst_process_printing_mesin',
                '{{%mst_process_printing_mesin}}',
                ['mst_process_printing_id', 'mst_mesin_proses_id']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mst_process_printing_mesin}}');
    }
}
