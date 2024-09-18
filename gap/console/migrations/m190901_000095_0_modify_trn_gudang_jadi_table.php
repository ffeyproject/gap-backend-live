<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000095_0_modify_trn_gudang_jadi_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_gudang_jadi', 'no_memo_repair', $this->string());
        $this->addColumn('trn_gudang_jadi', 'no_memo_ganti_greige', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}
