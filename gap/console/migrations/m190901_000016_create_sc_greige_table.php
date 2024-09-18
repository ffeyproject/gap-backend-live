<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000016_create_sc_greige_table extends Migration
{
    const TABLE_NAME = 'trn_sc_greige';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'sc_id' => $this->integer()->unsigned()->notNull(),
            'greige_group_id' => $this->integer()->unsigned()->notNull(),
            'process' => $this->tinyInteger(1)->notNull()->comment('1=DYEING, 2=PRINTING, 3=PFP'),
            'lebar_kain' => $this->tinyInteger(1)->notNull()->comment('1=44, 2=58, 3=64'),
            'merek' => $this->string(255)->notNull(),
            'grade' => $this->tinyInteger(50)->notNull()->comment('1=A, 2=B, 3=C, 4=ALL GRADE'),
            'piece_length' => $this->string(100)->notNull(),
            'unit_price' => $this->decimal(19,4)->unsigned()->notNull(),
            'price_param' => $this->string(100)->notNull()->comment('1=Per Unit, 2=Per Yard'),
            'qty' => $this->decimal(18,3)->unsigned()->notNull(),
            'woven_selvedge' => $this->text(),
            'note' => $this->text(),
            'closed' => $this->boolean()->defaultValue(false),
            'closing_note' => $this->text(),
            'no_order_greige' => $this->string(255),
            'no_urut_order_greige' => $this->integer()->unsigned(),
            'order_greige_note' => $this->text(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME, 'sc_id', 'trn_sc', 'id');
        $this->addForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME, 'greige_group_id', 'mst_greige_group', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_greige_group', self::TABLE_NAME);
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_sc', self::TABLE_NAME);

        $this->dropTable(self::TABLE_NAME);
    }
}
