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
            
            'gap_old' => $this->double()->defaultValue(0),
            'gap_new' => $this->double()->defaultValue(0),
            
            'stock_old' => $this->double()->defaultValue(0),
            'stock_new' => $this->double()->defaultValue(0),
            
            'available_old' => $this->double()->defaultValue(0),
            'available_new' => $this->double()->defaultValue(0),
            
            'booked_wo_old' => $this->double()->defaultValue(0),
            'booked_wo_new' => $this->double()->defaultValue(0),
            
            'stock_pfp_old' => $this->double()->defaultValue(0),
            'stock_pfp_new' => $this->double()->defaultValue(0),
            
            'stock_wip_old' => $this->double()->defaultValue(0),
            'stock_wip_new' => $this->double()->defaultValue(0),
            
            'stock_ef_old' => $this->double()->defaultValue(0),
            'stock_ef_new' => $this->double()->defaultValue(0),
            
            'booked_old' => $this->double()->defaultValue(0),
            'booked_new' => $this->double()->defaultValue(0),
            
            'booked_pfp_old' => $this->double()->defaultValue(0),
            'booked_pfp_new' => $this->double()->defaultValue(0),
            
            'booked_wip_old' => $this->double()->defaultValue(0),
            'booked_wip_new' => $this->double()->defaultValue(0),
            
            'booked_ef_old' => $this->double()->defaultValue(0),
            'booked_ef_new' => $this->double()->defaultValue(0),
            
            'booked_opfp_old' => $this->double()->defaultValue(0),
            'booked_opfp_new' => $this->double()->defaultValue(0),
            
            'available_pfp_old' => $this->double()->defaultValue(0),
            'available_pfp_new' => $this->double()->defaultValue(0),
            
            'stock_opname_old' => $this->double()->defaultValue(0),
            'stock_opname_new' => $this->double()->defaultValue(0),
            
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
    IF (OLD.gap IS DISTINCT FROM NEW.gap OR
        OLD.stock IS DISTINCT FROM NEW.stock OR
        OLD.available IS DISTINCT FROM NEW.available OR
        OLD.booked_wo IS DISTINCT FROM NEW.booked_wo OR
        OLD.stock_pfp IS DISTINCT FROM NEW.stock_pfp OR
        OLD.stock_wip IS DISTINCT FROM NEW.stock_wip OR
        OLD.stock_ef IS DISTINCT FROM NEW.stock_ef OR
        OLD.booked IS DISTINCT FROM NEW.booked OR
        OLD.booked_pfp IS DISTINCT FROM NEW.booked_pfp OR
        OLD.booked_wip IS DISTINCT FROM NEW.booked_wip OR
        OLD.booked_ef IS DISTINCT FROM NEW.booked_ef OR
        OLD.booked_opfp IS DISTINCT FROM NEW.booked_opfp OR
        OLD.available_pfp IS DISTINCT FROM NEW.available_pfp OR
        OLD.stock_opname IS DISTINCT FROM NEW.stock_opname) THEN
        
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
            gap_old, gap_new,
            stock_old, stock_new,
            available_old, available_new,
            booked_wo_old, booked_wo_new,
            stock_pfp_old, stock_pfp_new,
            stock_wip_old, stock_wip_new,
            stock_ef_old, stock_ef_new,
            booked_old, booked_new,
            booked_pfp_old, booked_pfp_new,
            booked_wip_old, booked_wip_new,
            booked_ef_old, booked_ef_new,
            booked_opfp_old, booked_opfp_new,
            available_pfp_old, available_pfp_new,
            stock_opname_old, stock_opname_new,
            created_at,
            created_by,
            context
        ) VALUES (
            NEW.id,
            OLD.gap, NEW.gap,
            OLD.stock, NEW.stock,
            OLD.available, NEW.available,
            OLD.booked_wo, NEW.booked_wo,
            OLD.stock_pfp, NEW.stock_pfp,
            OLD.stock_wip, NEW.stock_wip,
            OLD.stock_ef, NEW.stock_ef,
            OLD.booked, NEW.booked,
            OLD.booked_pfp, NEW.booked_pfp,
            OLD.booked_wip, NEW.booked_wip,
            OLD.booked_ef, NEW.booked_ef,
            OLD.booked_opfp, NEW.booked_opfp,
            OLD.available_pfp, NEW.available_pfp,
            OLD.stock_opname, NEW.stock_opname,
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
