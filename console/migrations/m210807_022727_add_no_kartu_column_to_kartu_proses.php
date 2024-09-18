<?php

use yii\db\Migration;

/**
 * Class m210807_022727_add_no_kartu_column_to_kartu_proses
 */
class m210807_022727_add_no_kartu_column_to_kartu_proses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_kartu_proses_dyeing}}', 'nomor_kartu', $this->string()->notNull()->defaultValue(''));
        $this->addColumn('{{%trn_kartu_proses_printing}}', 'nomor_kartu', $this->string()->notNull()->defaultValue(''));
        $this->addColumn('{{%trn_kartu_proses_pfp}}', 'nomor_kartu', $this->string()->notNull()->defaultValue(''));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210807_022727_add_no_kartu_column_to_kartu_proses cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210807_022727_add_no_kartu_column_to_kartu_proses cannot be reverted.\n";

        return false;
    }
    */
}
