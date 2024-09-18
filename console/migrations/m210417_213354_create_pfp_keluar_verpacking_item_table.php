<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pfp_keluar_verpacking_item}}`.
 */
class m210417_213354_create_pfp_keluar_verpacking_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pfp_keluar_verpacking_item}}', [
            'id' => $this->primaryKey(),
            'pfp_keluar_verpacking_id' => $this->integer()->notNull(),
            'ukuran' => $this->float()->notNull(),
            'join_piece' => $this->string(),
            'keterangan' => $this->text()
        ]);

        $this->addForeignKey('fk_pfp_keluar_verpacking_item_pfp_keluar_verpacking', '{{%pfp_keluar_verpacking_item}}', 'pfp_keluar_verpacking_id', '{{%pfp_keluar_verpacking}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pfp_keluar_verpacking_item}}');
    }
}
