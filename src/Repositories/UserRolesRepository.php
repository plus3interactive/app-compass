<?php

namespace P3in\Repositories;

use P3in\Models\Role;
use P3in\Models\User;

class UserRolesRepository extends AbstractChildRepository
{
    protected $view_types = ['MultiSelect'];
    const REQUIRES_PERMISSION = 1;

    public function __construct(Role $model, User $parent)
    {
        $this->model = $model;

        $this->parent = $parent;

        $this->relationName = 'users';

        $this->parentToChild = 'roles';
    }
}
