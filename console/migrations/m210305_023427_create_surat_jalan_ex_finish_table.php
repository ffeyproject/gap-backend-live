<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%surat_jalan_ex_finish}}`.
 */
class m210305_023427_create_surat_jalan_ex_finish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%surat_jalan_ex_finish}}', [
            'id' => $this->bigPrimaryKey(),
            'memo_id' => $this->bigInteger()->notNull(),
            'no' => $this->string(255),
            'pengirim' => $this->string(),
            'penerima' => $this->string(),
            'kepala_gudang' => $this->string(),
            'plat_nomor' => $this->string(),
            'note' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_surat_jalan_ex_finish_memo', '{{%surat_jalan_ex_finish}}', 'memo_id', '{{%jual_ex_finish}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%surat_jalan_ex_finish}}');
    }
}
