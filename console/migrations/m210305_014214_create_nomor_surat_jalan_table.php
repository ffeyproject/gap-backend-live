<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%nomor_surat_jalan}}`.
 */
class m210305_014214_create_nomor_surat_jalan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%nomor_surat_jalan}}', [
            'id' => $this->bigPrimaryKey(),
            'no_urut' => $this->integer()->notNull(),
            'no' => $this->string(255)->notNull(),
            'date' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%nomor_surat_jalan}}');
    }
}
