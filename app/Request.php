<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'from_lat','from_lng','to_lat','to_lng',
       'from_time','delivery_time','status',
       'qualification','msg','service_id',
       'user_id','manager_id'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'service_id', 'user_id','manager_id'
    ];

}
