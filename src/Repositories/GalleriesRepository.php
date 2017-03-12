<?php

namespace P3in\Repositories;

use P3in\Interfaces\GalleriesRepositoryInterface;
use P3in\Models\Gallery;

class GalleriesRepository extends AbstractRepository implements GalleriesRepositoryInterface
{
    protected $with = ['user', 'videos.user', 'galleryable.storage'];

    // List view
    protected $view = 'Card';

    protected $requires = [
        'user' => ['from' => 'id', 'to' => 'user_id']
    ];

    public function __construct(Gallery $model)
    {
        $this->model = $model;

    }

}
