<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000060_0_create_trn_potong_greige_table extends Migration
{
    const TABLE_NAME = "trn_potong_greige";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'stock_greige_id' => $this->integer()->unsigned()->notNull(),
            'no_urut' => $this->integer()->unsigned(),
            'no' => $this->string(),
            'note' => $this->text(),
            'date' => $this->date()->notNull(),
            'posted_at' => $this->integer()->unsigned(),
            'approved_at' => $this->integer()->unsigned(),
            'approved_by' => $this->integer()->unsigned()->comment('yang memerintahkan pemotongan jika ada'),
            'status' => $this->tinyInteger(1)->notNull()->comment('1=draft, 2=posted, 3=approved 4=rejected'),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'created_by' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_stock_greige', self::TABLE_NAME, 'stock_greige_id', 'trn_stock_greige', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
