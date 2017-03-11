<?php

namespace P3in\Seeders;

use Illuminate\Database\Seeder;
use P3in\Builders\FormBuilder;
use P3in\Models\User;
use P3in\Models\Permission;
use P3in\Models\Role;
use DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE users CASCADE');
        DB::statement("DELETE FROM forms WHERE name = 'users'");

        FormBuilder::new('users', function (FormBuilder $builder) {
            $builder->string('First Name', 'first_name')->list()->validation(['required'])->sortable()->searchable();
            $builder->string('Last Name', 'last_name')->list()->validation(['required'])->sortable()->searchable();
            $builder->string('Email', 'email')->list()->validation(['required', 'email'])->sortable()->searchable();
            $builder->string('Phone Number', 'phone')->list()->validation(['required'])->sortable()->searchable();
            $builder->boolean('Active', 'active')->list(false);
            $builder->string('Date Added', 'created_at')->list()->edit(false)->sortable();
            $builder->secret('Password', 'password')->validation(['required']);
        })->linkToResources(['users.index', 'users.show', 'users.create', 'users.update', 'users.store']);

        FormBuilder::new('user-roles', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->validation(['required'])->sortable()->searchable();
        })->linkToResources(['users.roles.index']);

        // DB::statement('TRUNCATE permissions CASCADE');
        // DB::statement("DELETE FROM forms WHERE name = 'permissions'");

        FormBuilder::new('permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->validation(['required'])->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->validation(['required'])->sortable()->searchable();
            $builder->string('Created', 'created_at')->list()->edit(false)->validation(['required'])->sortable()->searchable();
        })->linkToResources(['permissions.index', 'permissions.show', 'permissions.create', 'permissions.store', 'permissions.update']);

        // DB::statement('TRUNCATE roles CASCADE');
        // DB::statement("DELETE FROM forms WHERE name = 'roles'");

        FormBuilder::new('roles', function (FormBuilder $builder) {
            $builder->string('Role Name', 'name')->list()->validation(['required'])->sortable()->searchable();
            $builder->string('Role Label', 'label')->list()->validation(['required'])->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->validation(['required'])->sortable()->searchable();
            $builder->string('Date Added', 'created_at')->list()->edit(false)->sortable();
        })->linkToResources(['roles.index', 'roles.show', 'roles.store', 'roles.update']);

        // @TODO there is no mention of 'locked' in the entire codebase. removing.
        Permission::create(['type' => 'logged-user', 'label' => 'User', 'description' => 'The user can log into the application frontend (websites)']);
        Permission::create(['type' => 'guest', 'label' => 'Guest', 'description' => 'Guest Permission']);

        Role::create(['name' => 'user', 'label' => 'User', 'description' => 'User exists', 'active' => true]);
        Role::create(['name' => 'admin', 'label' => 'Admin', 'description' => 'Administrators', 'active' => true]);
        Role::create(['name' => 'system', 'label' => 'System', 'description' => 'System users', 'active' => true]);

        User::create(['first_name' => 'System', 'last_name' => 'User', 'email' => 'system@p3in.com', 'phone' => '', 'password' => ''])->assignRole('system');
    }
}
