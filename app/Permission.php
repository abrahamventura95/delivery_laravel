<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'service_id', 'user_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'service_id', 'user_id',
    ];


}
