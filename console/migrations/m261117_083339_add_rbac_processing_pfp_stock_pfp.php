<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m261117_083339_add_rbac_processing_pfp_stock_pfp
 */
class m261117_083339_add_rbac_processing_pfp_stock_pfp extends Migration
{
    private $route = '/processing-pfp/stock-pfp';
    private $parents = ['Melihat Processing Pfp', 'Viewer'];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();
        $exists = (new Query())->from('auth_item')->where(['name' => $this->route])->exists($this->db);
        if (!$exists) {
            $this->insert('auth_item', [
                'name' => $this->route,
                'type' => 2,
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }

        foreach ($this->parents as $parent) {
            $parentExists = (new Query())->from('auth_item')->where(['name' => $parent])->exists($this->db);
            if ($parentExists) {
                $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $parent, 'child' => $this->route])->exists($this->db);
                if (!$linkExists) {
                    $this->insert('auth_item_child', ['parent' => $parent, 'child' => $this->route]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->parents as $parent) {
            $this->delete('auth_item_child', ['parent' => $parent, 'child' => $this->route]);
        }
        $this->delete('auth_item', ['name' => $this->route]);
    }
}
