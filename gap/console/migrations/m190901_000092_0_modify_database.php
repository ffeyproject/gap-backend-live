<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000092_0_modify_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('trn_kirim_buyer_header', 'is_resmi', $this->boolean()->notNull()->defaultValue(true)->comment('Surat jalan nya resmi atau tidak.'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}
