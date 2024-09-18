<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%trn_buy_php}}`.
 */
class m210405_031721_add_more_columns_to_trn_buy_php_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%trn_buy_pfp}}', 'jenis', $this->smallInteger()->notNull()->defaultValue(1)->comment('1=Beli, 2=Hasil Makloon'));
        $this->addColumn('{{%trn_buy_pfp}}', 'no_referensi', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
