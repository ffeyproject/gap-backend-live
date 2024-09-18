<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000009_create_greige_table extends Migration
{
    const TABLE_NAME = "mst_greige";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'group_id' => $this->integer()->unsigned()->notNull(),
            'nama_kain' => $this->string(255)->notNull(),
            'alias' => $this->string(255),
            'no_dok_referensi' => $this->string(255),
            'gap' => $this->decimal(17,2)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'aktif' => $this->boolean()->defaultValue(true),
            'stock' => $this->float()->notNull()->defaultValue(0),
            'available' => $this->float()->notNull()->defaultValue(0),
            'booked_wo' => $this->float()->notNull()->defaultValue(0),
            'booked' => $this->float()->notNull()->defaultValue(0),
            'stock_pfp' => $this->float()->notNull()->defaultValue(0),
            'booked_pfp' => $this->float()->notNull()->defaultValue(0),
            'stock_wip' => $this->float()->notNull()->defaultValue(0),
            'booked_wip' => $this->float()->notNull()->defaultValue(0),
            'stock_ef' => $this->float()->notNull()->defaultValue(0),
            'booked_ef' => $this->float()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk_mst_greige_group',
            self::TABLE_NAME,
            'group_id',
            'mst_greige_group',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_mst_greige_group', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
