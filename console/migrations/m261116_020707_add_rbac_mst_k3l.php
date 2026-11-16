<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m261116_020707_add_rbac_mst_k3l
 */
class m261116_020707_add_rbac_mst_k3l extends Migration
{
    private $routes = [
        '/mst-k3l/index' => 'Melihat Master K3L',
        '/mst-k3l/view' => 'Melihat Master K3L',
        '/mst-k3l/create' => 'Membuat dan Update Master K3L',
        '/mst-k3l/update' => 'Membuat dan Update Master K3L',
        '/mst-k3l/delete' => 'Menghapus Master K3L',
        '/mst-k3l/*' => null,
    ];

    private $permissions = [
        'Melihat Master K3L' => 'Melihat Master Data K3L',
        'Membuat dan Update Master K3L' => 'Membuat dan Mengubah Master Data K3L',
        'Menghapus Master K3L' => 'Menghapus Master Data K3L',
    ];

    private $rolePermissions = [
        'Viewer' => ['Melihat Master K3L'],
        'Super Administrator' => ['Melihat Master K3L', 'Membuat dan Update Master K3L', 'Menghapus Master K3L'],
        'Inspecting' => ['Melihat Master K3L', 'Membuat dan Update Master K3L'],
        'Kepala Qc' => ['Melihat Master K3L', 'Membuat dan Update Master K3L'],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();

        // 1. Insert permissions
        foreach ($this->permissions as $permissionName => $description) {
            $exists = (new Query())->from('auth_item')->where(['name' => $permissionName])->exists($this->db);
            if (!$exists) {
                $this->insert('auth_item', [
                    'name' => $permissionName,
                    'type' => 2,
                    'description' => $description,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }

        // 2. Insert routes and bind to permissions
        foreach ($this->routes as $route => $permission) {
            $exists = (new Query())->from('auth_item')->where(['name' => $route])->exists($this->db);
            if (!$exists) {
                $this->insert('auth_item', [
                    'name' => $route,
                    'type' => 2,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
            if ($permission !== null) {
                $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $permission, 'child' => $route])->exists($this->db);
                if (!$linkExists) {
                    $this->insert('auth_item_child', ['parent' => $permission, 'child' => $route]);
                }
            }
        }

        // 3. Assign permissions to roles
        foreach ($this->rolePermissions as $role => $perms) {
            $roleExists = (new Query())->from('auth_item')->where(['name' => $role, 'type' => 1])->exists($this->db);
            if ($roleExists) {
                foreach ($perms as $perm) {
                    $linkExists = (new Query())->from('auth_item_child')->where(['parent' => $role, 'child' => $perm])->exists($this->db);
                    if (!$linkExists) {
                        $this->insert('auth_item_child', ['parent' => $role, 'child' => $perm]);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->rolePermissions as $role => $perms) {
            foreach ($perms as $perm) {
                $this->delete('auth_item_child', ['parent' => $role, 'child' => $perm]);
            }
        }

        foreach ($this->routes as $route => $permission) {
            if ($permission !== null) {
                $this->delete('auth_item_child', ['parent' => $permission, 'child' => $route]);
            }
            $this->delete('auth_item', ['name' => $route]);
        }

        foreach ($this->permissions as $permissionName => $description) {
            $this->delete('auth_item', ['name' => $permissionName]);
        }
    }
}
