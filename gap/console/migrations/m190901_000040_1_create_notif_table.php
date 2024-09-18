<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%member}}`.
 */
class m190901_000040_1_create_notif_table extends Migration
{
    const TABLE_NAME = "trn_notif";

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(self::TABLE_NAME, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'message' => $this->text()->notNull(),
            'link' => $this->text(),
            'type' => $this->tinyInteger(1)->notNull()->comment('1=Notification, 2=Message, 3=Task'),
            'read' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ]);

        $this->addForeignKey('fk_'.self::TABLE_NAME.'_user', self::TABLE_NAME, 'user_id', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_'.self::TABLE_NAME.'_user', self::TABLE_NAME);
        $this->dropTable(self::TABLE_NAME);
    }
}
