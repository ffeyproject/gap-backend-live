<?php

use yii\db\Migration;

/**
 * Class m260620_045539_create_trn_kartu_proses_dyeing_planning_tables_new
 */
class m260620_045539_create_trn_kartu_proses_dyeing_planning_tables_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Table 1: Planning Options (10 slots globally per process)
        $this->createTable('mst_process_dyeing_planning_option', [
            'id' => $this->primaryKey(),
            'process_id' => $this->integer()->notNull(),
            'slot' => $this->integer()->notNull(),
            'label' => $this->string(255),
            'color' => $this->string(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-mst_process_dyeing_planning_option-process_id', 'mst_process_dyeing_planning_option', 'process_id');
        $this->addForeignKey('fk-mst_process_dyeing_planning_option-process_id', 'mst_process_dyeing_planning_option', 'process_id', 'mst_process_dyeing', 'id', 'CASCADE');

        // Table 2: Kartu Planning (Transaction data per kartu and process)
        $this->createTable('trn_kartu_proses_dyeing_planning', [
            'kartu_process_id' => $this->integer()->notNull(),
            'process_id' => $this->integer()->notNull(),
            'is_siap' => $this->boolean()->defaultValue(false),
            'option_id' => $this->integer(),
            'catatan' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-trn_kartu_proses_dyeing_planning', 'trn_kartu_proses_dyeing_planning', ['kartu_process_id', 'process_id']);
        
        $this->addForeignKey('fk-trn_kartu_proses_dyeing_planning-kartu_process_id', 'trn_kartu_proses_dyeing_planning', 'kartu_process_id', 'trn_kartu_proses_dyeing', 'id', 'CASCADE');
        $this->addForeignKey('fk-trn_kartu_proses_dyeing_planning-process_id', 'trn_kartu_proses_dyeing_planning', 'process_id', 'mst_process_dyeing', 'id', 'CASCADE');
        $this->addForeignKey('fk-trn_kartu_proses_dyeing_planning-option_id', 'trn_kartu_proses_dyeing_planning', 'option_id', 'mst_process_dyeing_planning_option', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-trn_kartu_proses_dyeing_planning-option_id', 'trn_kartu_proses_dyeing_planning');
        $this->dropForeignKey('fk-trn_kartu_proses_dyeing_planning-process_id', 'trn_kartu_proses_dyeing_planning');
        $this->dropForeignKey('fk-trn_kartu_proses_dyeing_planning-kartu_process_id', 'trn_kartu_proses_dyeing_planning');
        
        $this->dropTable('trn_kartu_proses_dyeing_planning');

        $this->dropForeignKey('fk-mst_process_dyeing_planning_option-process_id', 'mst_process_dyeing_planning_option');
        $this->dropTable('mst_process_dyeing_planning_option');
    }
}
