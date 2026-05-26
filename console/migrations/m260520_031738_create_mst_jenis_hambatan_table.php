<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mst_jenis_hambatan}}`.
 */
class m260520_031738_create_mst_jenis_hambatan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mst_jenis_hambatan}}', [
            'id' => $this->primaryKey(),
            'nama' => $this->string()->notNull(),
            'keterangan' => $this->text(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mst_jenis_hambatan}}');
    }
}
