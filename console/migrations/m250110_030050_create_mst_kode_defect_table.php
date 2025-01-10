<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_kode_defect}}`.
 */
class m250110_030050_create_mst_kode_defect_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mst_kode_defect}}', [
            'id' => $this->primaryKey(),
            'no_urut' => $this->integer()->notNull(),
            'kode' => $this->string(50)->notNull(),
            'nama_defect' => $this->string(255)->notNull(),
            'asal_defect' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mst_kode_defect}}');
    }
}