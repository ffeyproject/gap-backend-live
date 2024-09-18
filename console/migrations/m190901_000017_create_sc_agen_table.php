<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000017_create_sc_agen_table extends Migration
{
    const TABLE_NAME = "trn_sc_agen";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'date' => $this->date()->notNull(),
            'nama_agen' => $this->string(255)->notNull(),
            'attention' => $this->string(255)->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(255),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);

        $this->dropTable(self::TABLE_NAME);
    }
}
