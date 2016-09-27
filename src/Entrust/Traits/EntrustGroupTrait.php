<?php

namespace Entrust\Traits;

/**
 * This file is part of Entrust,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Entrust
 */

use Illuminate\Support\Facades\Config;

trait EntrustGroupTrait
{
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Config::get('entrust.role'),
            Config::get('entrust.role_user_table'),
            Config::get('entrust.group_foreign_key'),
            Config::get('entrust.role_foreign_key')
        );
    }

    /**
     * Many-to-Many relations with user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(
            Config::get('entrust.user'),
            Config::get('entrust.role_user_table'),
            Config::get('entrust.group_foreign_key'),
            Config::get('entrust.user_foreign_key')
        );
    }

    /**
     * Boot the permission model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the group model uses soft deletes.
     *
     * @return void|bool
     */
    public static function bootEntrustGroupTrait()
    {
        static::deleting(function ($group) {
            if (!method_exists(Config::get('entrust.group'), 'bootSoftDeletes')) {
                $group->roles()->sync([]);
                $group->users()->sync([]);
            }

            return true;
        });
    }
}
