<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%defect_inspecting_items}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%inspecting_item}}`
 * - `{{%mst_kode_defect}}`
 */
class m230110_123457_create_defect_inspecting_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%defect_inspecting_items}}', [
            'id' => $this->bigPrimaryKey(),
            'inspecting_item_id' => $this->bigInteger()->notNull(),
            'mst_kode_defect_id' => $this->bigInteger()->notNull(),
            'meterage' => $this->double()->notNull(),
            'point' => $this->double()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        
        $this->createIndex(
            '{{%idx-defect_inspecting_items-inspecting_item_id}}',
            '{{%defect_inspecting_items}}',
            'inspecting_item_id'
        );

        
        $this->addForeignKey(
            '{{%fk-defect_inspecting_items-inspecting_item_id}}',
            '{{%defect_inspecting_items}}',
            'inspecting_item_id',
            '{{%inspecting_item}}',
            'id',
            'CASCADE'
        );

        
        $this->createIndex(
            '{{%idx-defect_inspecting_items-mst_kode_defect_id}}',
            '{{%defect_inspecting_items}}',
            'mst_kode_defect_id'
        );

        
        $this->addForeignKey(
            '{{%fk-defect_inspecting_items-mst_kode_defect_id}}',
            '{{%defect_inspecting_items}}',
            'mst_kode_defect_id',
            '{{%mst_kode_defect}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        $this->dropForeignKey(
            '{{%fk-defect_inspecting_items-inspecting_item_id}}',
            '{{%defect_inspecting_items}}'
        );

        
        $this->dropIndex(
            '{{%idx-defect_inspecting_items-inspecting_item_id}}',
            '{{%defect_inspecting_items}}'
        );

        
        $this->dropForeignKey(
            '{{%fk-defect_inspecting_items-mst_kode_defect_id}}',
            '{{%defect_inspecting_items}}'
        );

        
        $this->dropIndex(
            '{{%idx-defect_inspecting_items-mst_kode_defect_id}}',
            '{{%defect_inspecting_items}}'
        );

        $this->dropTable('{{%defect_inspecting_items}}');
    }
}