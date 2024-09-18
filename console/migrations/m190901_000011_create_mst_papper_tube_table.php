<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000011_create_mst_papper_tube_table extends Migration
{
    const TABLE_NAME = "mst_papper_tube";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id'=>$this->primaryKey()->unsigned(),
            'name'=>$this->string()->notNull(),
            'created_at' => $this->integer()->unsigned(),
            'created_by' => $this->integer()->unsigned(),
            'updated_at' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
        ]);

        $ts = time();
        $this->insert(self::TABLE_NAME, ['name'=>'133x38', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'133x50', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'150x38', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'150x50', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'160x32', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'122x32', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
        $this->insert(self::TABLE_NAME, ['name'=>'122x32', 'created_at' => $ts, 'created_by' => 1, 'updated_at' => $ts, 'updated_by' => 1,]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(self::TABLE_NAME);
    }
}
