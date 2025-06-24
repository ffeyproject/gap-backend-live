<?php

use yii\db\Migration;

/**
 * Handles adding column `foto` to table `user`.
 */
class m250624_035948_add_foto_column_to_user_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'foto', $this->string()->null()->after('email'));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'foto');
    }
}