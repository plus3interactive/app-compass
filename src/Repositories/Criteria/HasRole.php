<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;

class HasRole extends AbstractCriteria
{
    private $role_id;

    public function __construct($role_id)
    {
        $this->role_id = $role_id;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()
            ->whereHas('roles', function ($query) {
                $query->where('id', $this->role_id);
            })
        ;

        return $query;
    }
}