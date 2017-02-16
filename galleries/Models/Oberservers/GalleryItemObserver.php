<?php

namespace P3in\Models\Oberservers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GalleryItemObserver
{
    public function creating(Model $model)
    {
        $model->type = $model->getType();
    }

    public function deleting(Model $model)
    {
    }

    public function deleted(Model $model)
    {
        // we only delete the file if it's a force deletion.
        if ($model->isForceDeleting()) {
            $disk = $model->getDisk();
            $disk->delete($model->path);
            info('deleted gallery item file: '.$model->path);
        }
    }
}