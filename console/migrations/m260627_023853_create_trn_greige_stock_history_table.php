<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%trn_greige_stock_history}}`.
 */
class m260627_023853_create_trn_greige_stock_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%trn_greige_stock_history}}', [
            'id' => $this->primaryKey(),
            'greige_id' => $this->integer()->notNull(),
            'stock_old' => $this->double()->defaultValue(0),
            'stock_new' => $this->double()->defaultValue(0),
            'available_old' => $this->double()->defaultValue(0),
            'available_new' => $this->double()->defaultValue(0),
            'booked_wo_old' => $this->double()->defaultValue(0),
            'booked_wo_new' => $this->double()->defaultValue(0),
            'booked_pfp_old' => $this->double()->defaultValue(0),
            'booked_pfp_new' => $this->double()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'created_by' => $this->integer(),
            'context' => $this->string(255),
        ]);

        $this->addForeignKey(
            'fk_trn_greige_stock_history_greige',
            '{{%trn_greige_stock_history}}',
            'greige_id',
            '{{%mst_greige}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_trn_greige_stock_history_user',
            '{{%trn_greige_stock_history}}',
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Create PostgreSQL trigger function
        $sqlFunction = <<<SQL
CREATE OR REPLACE FUNCTION log_mst_greige_stock_changes()
RETURNS TRIGGER AS $$
DECLARE
    user_id_val INT;
    context_val VARCHAR(255);
BEGIN
    IF (OLD.stock IS DISTINCT FROM NEW.stock OR
        OLD.available IS DISTINCT FROM NEW.available OR
        OLD.booked_wo IS DISTINCT FROM NEW.booked_wo OR
        OLD.booked_pfp IS DISTINCT FROM NEW.booked_pfp) THEN
        
        BEGIN
            user_id_val := NULLIF(current_setting('app.user_id', true), '')::INT;
        EXCEPTION WHEN OTHERS THEN
            user_id_val := NULL;
        END;
        
        BEGIN
            context_val := NULLIF(current_setting('app.context', true), '');
        EXCEPTION WHEN OTHERS THEN
            context_val := NULL;
        END;

        INSERT INTO trn_greige_stock_history (
            greige_id,
            stock_old, stock_new,
            available_old, available_new,
            booked_wo_old, booked_wo_new,
            booked_pfp_old, booked_pfp_new,
            created_at,
            created_by,
            context
        ) VALUES (
            NEW.id,
            OLD.stock, NEW.stock,
            OLD.available, NEW.available,
            OLD.booked_wo, NEW.booked_wo,
            OLD.booked_pfp, NEW.booked_pfp,
            CURRENT_TIMESTAMP,
            user_id_val,
            context_val
        );
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
SQL;

        $this->execute($sqlFunction);

        // Create AFTER UPDATE trigger on mst_greige
        $sqlTrigger = <<<SQL
CREATE TRIGGER trigger_mst_greige_stock_changes
AFTER UPDATE ON mst_greige
FOR EACH ROW
EXECUTE PROCEDURE log_mst_greige_stock_changes();
SQL;

        $this->execute($sqlTrigger);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP TRIGGER IF EXISTS trigger_mst_greige_stock_changes ON mst_greige;');
        $this->execute('DROP FUNCTION IF EXISTS log_mst_greige_stock_changes();');
        $this->dropForeignKey('fk_trn_greige_stock_history_user', '{{%trn_greige_stock_history}}');
        $this->dropForeignKey('fk_trn_greige_stock_history_greige', '{{%trn_greige_stock_history}}');
        $this->dropTable('{{%trn_greige_stock_history}}');
    }
}
