<?php

use yii\db\Migration;

/**
 * Class m251126_163607_create_action_log_kartu_dyeing
 */
class m251126_163607_create_action_log_kartu_dyeing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // PostgreSQL table
        $this->createTable('action_log_kartu_dyeing', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'username' => $this->string(100)->null(),
            'kartu_proses_id' => $this->integer()->notNull(),
            'action_name' => $this->string(100)->notNull(),
            'description' => $this->text()->null(),
            'ip' => $this->string(50)->null(),
            'user_agent' => $this->text()->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        // Optional: index untuk pencarian cepat
        $this->createIndex(
            'idx-action_log_kartu_dyeing-kartu_proses_id',
            'action_log_kartu_dyeing',
            'kartu_proses_id'
        );

        $this->createIndex(
            'idx-action_log_kartu_dyeing-action_name',
            'action_log_kartu_dyeing',
            'action_name'
        );

        $this->createIndex(
            'idx-action_log_kartu_dyeing-user_id',
            'action_log_kartu_dyeing',
            'user_id'
        );

        // Optional FK jika ingin relasi
        // Sesuaikan nama tabel kartu proses dyeing Anda
        // $this->addForeignKey(
        //     'fk-action_log-kartu_proses',
        //     'action_log_kartu_dyeing',
        //     'kartu_proses_id',
        //     'trn_kartu_proses_dyeing', // sesuaikan tabel Anda
        //     'id',
        //     'CASCADE'
        // );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Optional FK drop
        // $this->dropForeignKey('fk-action_log-kartu_proses', 'action_log_kartu_dyeing');

        $this->dropTable('action_log_kartu_dyeing');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251126_163607_create_action_log_kartu_dyeing cannot be reverted.\n";

        return false;
    }
    */
}