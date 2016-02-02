<?php

namespace P3in\Models;

use P3in\Models\Gallery;
use P3in\Models\Photo;
use Exception;
use Cache;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Modular;
use P3in\Models\Group;
use P3in\Models\Permission;
use P3in\Profiles\BaseProfile;
use P3in\Traits\AlertableTrait as Alertable;
use P3in\Traits\OptionableTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

	use Authenticatable, CanResetPassword, Alertable, Authorizable, OptionableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'phone',
		'email',
		'password',
		'active'
	];

	/**
	 * 	The attributes excluded from the model's JSON form.
	 *
	 * 	@var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * Stuff to append to each request
	 *
	 *
	 */
	protected $appends = ['full_name'];

    public static $validator_rules = [
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|unique:users|max:255',
        'password' => 'required|max:255',
    ];

	/**
	*	Get all the permissions the user has
	*
	*
	*/
	public function permissions()
	{

		return $this->belongsToMany(Permission::class)->withTimestamps();

	}

	/**
	 * Return all the permissions of the user
	 *
	 * @return array permission owned by the user
	 */
	public function allPermissions()
	{

		return Cache::remember($this->id, 1, function() {

			// $this->load(['groups.permissions' => function($query) { $query->where('active', true); }])
			$this->load('groups.permissions')
				->load('permissions');

			$perms = collect($this->permissions->lists('type', 'id'));

			$this->groups
				->each(function($group) use($perms) {
					$group->permissions
						->lists('type', 'id')
						->each(function($perm, $key) use($perms) {
							$perms->push($perm);
						});
			});

			return $perms->unique();

		});

	}

	/**
	 *	Check if user has a single permission
	 *
	 *	@param string $permission Permission type.
	 *	@return bool
	 */
	public function hasPermission($permission)
	{

		if (is_array($permission)) {

			return $this->hasPermissions($permission);

		}

		return in_array($permission, $this->allPermissions()->toArray());

	}

	/**
	 *	Check if user has a group of permissions
	 *
	 *	@param array permissions
	 *	@return bool
	 */
	public function hasPermissions($permissions)
	{

		$permissions = explode(",", $permissions);

		if (count($permissions) == 0) {
			return true;
		}

		return (bool)count(array_intersect($this->allPermissions()->toArray(), $permissions)) == count($permissions);

	}

	/**
	 *  Get all the groups this user belongs to
	 *
	 *
	 */
	public function groups()
	{

		return $this->belongsToMany(Group::class)->withTimestamps();

	}

	/**
	 *	Get either all or a specific profile type of a user
	 *
	 *
	 *	TODO: this needs to be refactored a little
	 */
	public function profiles($type = null)
	{

	  $relation = $this->hasMany(BaseProfile::class);

	  if (! is_null($type)) {

	  	$profile_class = $relation->where('model', $type)->firstOrFail();

	  	return new $profile_class->model($profile_class->toArray());

	  }

	  return $relation;

	}

	public function photos()
	{
	  return $this->hasMany(Photo::class);
	}
	public function galleries()
	{
	  return $this->hasMany(Gallery::class);
	}

	/**
	 *	Get user's full name
	 *
	 */
	public function getFullNameAttribute()
	{

		return sprintf("%s %s", $this->first_name, $this->last_name);

	}

	/**
	 *	Get/Set user's Avatar
	 *
	 *
	 */
	public function avatar(Photo $photo = null)
	{

		if (! Modular::isDef('photos')) {

			$userEmail = \Auth::user()->email;
			return "//www.gravatar.com/avatar/".md5($userEmail)."?s={$size}";

		}

		if (! is_null($photo)) {

			if (! is_null($this->avatar)) {

				$this->avatar()
					->first()
					->unlink();

			}

			$this->avatar()->save($photo);

		}

		return $this->morphOne(Photo::class, 'photoable');

	}

}
