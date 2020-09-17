<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name','description','status','image','owner'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'owner'
    ];
}
