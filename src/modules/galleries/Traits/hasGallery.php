<?php

/**
 *  HasGallery
 *
 *  provides hooks to link galleries to the model without hardcoding a dependency
 *
 *  Client code must implement getGalleryName()
 */

namespace P3in\Traits;

use Auth;
use P3in\Models\Gallery;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasGallery
{

    /**
     * Client code provides means for storing the gallery name
     */
    abstract function getGalleryName();

    /**
     *  galleries
     */
    public function gallery()
    {
        $rel = $this->morphOne(Gallery::class, 'galleryable');

        if (!$rel->get()->count()) {

            $this->getOrCreateGallery($this->getGalleryName());

            $this->fresh();

        }

        return $rel;
    }

    /**
     * getOrCreateGallery
     */
    public function getOrCreateGallery($name)
    {
        try {

            if (!\Auth::check()) {

                throw new \Exception('User must be logged in order to create a gallery.');

            }

            return Gallery::where('name', '=', $name)->firstOrFail();

        } catch (ModelNotFoundException $e) {

            return Gallery::create([
                'name' => $name,
                'description' => '',
                'user_id' => \Auth::user()->id,
                'galleryable_id' => $this->id,
                'galleryable_type' => get_class($this)
            ]);

        }
    }


    /**
     * make, for whichever reason
     */
    private function make($attributes = [])
    {
        return Gallery::create($attributes);
    }
}