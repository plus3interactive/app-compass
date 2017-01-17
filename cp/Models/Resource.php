<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{

	protected $fillable = [
		'form_id',
		'resource',
	];

	/**
	 *
	 */
	public function form() {

		return $this->belongsTo(Form::class);

	}

    public function setForm(Form $form)
    {
        return $this->associate($form);
    }

}