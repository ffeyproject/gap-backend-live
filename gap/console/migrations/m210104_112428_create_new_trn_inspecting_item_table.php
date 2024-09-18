<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%new_trn_inspecting_item}}`.
 */
class m210104_112428_create_new_trn_inspecting_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspecting_item}}', [
            'id' => $this->primaryKey(),
            'inspecting_id' => $this->integer()->notNull(),
            'grade' => $this->tinyInteger()->notNull()->comment('1=Grade A, 2=Grade B, 3=Grade C, 4=Piece Kecil, 5=Sample'),
            'join_piece' => $this->string(10),
            'qty' => $this->float()->notNull(),
            'note' => $this->text(),
        ]);

        $this->addForeignKey('fk_inspecting_item_inspecting', '{{%inspecting_item}}', 'inspecting_id', 'trn_inspecting', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%new_trn_inspecting_item}}');
    }
}
