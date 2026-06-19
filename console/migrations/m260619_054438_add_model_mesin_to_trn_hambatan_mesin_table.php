<?php

use yii\db\Migration;

/**
 * Class m260619_054438_add_model_mesin_to_trn_hambatan_mesin_table
 */
class m260619_054438_add_model_mesin_to_trn_hambatan_mesin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('trn_hambatan_mesin');
        if (!isset($table->columns['model_mesin'])) {
            $this->addColumn('trn_hambatan_mesin', 'model_mesin', $this->string(255)->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->schema->getTableSchema('trn_hambatan_mesin');
        if (isset($table->columns['model_mesin'])) {
            $this->dropColumn('trn_hambatan_mesin', 'model_mesin');
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260619_054438_add_model_mesin_to_trn_hambatan_mesin_table cannot be reverted.\n";

        return false;
    }
    */
}
