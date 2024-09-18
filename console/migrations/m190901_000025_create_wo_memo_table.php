<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000025_create_wo_memo_table extends Migration
{
    const TABLE_NAME = "trn_wo_memo";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'wo_id' => $this->integer()->unsigned()->notNull(),
            'tahun' => $this->integer()->notNull()->defaultValue(0),
            'no_urut' => $this->bigInteger()->notNull()->defaultValue(0),
            'no' => $this->string()->notNull()->defaultValue(''),
            'memo' => $this->text()->notNull(),
            'created_at' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME, 'wo_id', 'trn_wo', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_wo', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
