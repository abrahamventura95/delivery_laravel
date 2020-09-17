<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service;
use App\Permission;

class ServiceController extends Controller
{
    /**
     * Create a service
     */
    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'description' => 'string',
            'image' => 'string'
        ]);

        Service::create([
            'owner' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image
        ]);

        return response()->json([
            'message' => 'Successfully created service!'
        ], 201);
    }
    /**
     * Show all user`s services
     */
    public function getMine(Request $request){
    	return Service::where('owner','=',auth()->user()->id)
    			      ->orderBy('status','desc')
    			      ->orderBy('created_at','desc')
    			      ->get();
    }
    /**
     * Show all services
     */
    public function get(Request $request){
    	return Service::join('users','users.id','=','services.owner')
    				  ->select('services.*', 'users.name as user_name', 'users.email', 'users.tlfn', 'users.verified', 'users.custom')
    				  ->orderBy('status','desc')
    			      ->orderBy('created_at','desc')
    			      ->get();
    }
    /**
     * Show a service
     */
    public function show($id){
    	$service = Service::join('users','users.id','=','services.owner')
    				  ->select('services.*', 'users.name as user_name', 'users.email', 'users.tlfn', 'users.verified', 'users.custom')
    				  ->where('services.id','=',$id)
    				  ->orderBy('status','desc')
    			      ->orderBy('created_at','desc')
    			      ->get();
    	$users = Permission::join('users','users.id','=','permissions.user_id')
    					   ->select('users.*','permissions.status','permissions.id as permission_id')
    					   ->where('permissions.service_id','=',$id)
    					   ->get();
    	$resp = array('service' => $service[0], 'users' => $users);				   
    	return $resp;
    }
    /**
     * Edit a service
     */
    public function edit($id, Request $request){
    	$service = Service::find($id);
    	$request->validate([
            'name' => 'string',
            'description' => 'string',
            'image' => 'string',
			'status' => 'boolean'
        ]);
		if(isset($request->name)){
		    $service->name = $request->name; 
		}
		if(isset($request->description)){
		    $service->description = $request->description; 
		}
		if(isset($request->image)){
		    $service->image = $request->image; 
		}
		if(isset($request->status)){
		    $service->status = $request->status; 
		}
        if($service->owner = auth()->user()->id){
        	$service->save();
        }
        return $service;
    }

    /**
     * Delete a service
     */
    public function delete($id){
    	$service = Service::find($id);
        if(isset($service) && $service->owner == auth()->user()->id){
        	$service->delete();
	        return response()->json([
	            'message' => 'Successfully deleted!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted!'
	        ], 401);
    	}
    }
    //Permission
    /**
     * Create a permission
     */
    public function createPermission($id, Request $request){
    	$service = Service::find($id);

    	if(isset($service) && $service->owner === auth()->user()->id){
    		$request->validate([
	            'user' => 'required'
	        ]);

	        Permission::create([
	            'service_id' => $id,
	            'user_id' => $request->user
	        ]);

	        return response()->json([
	            'message' => 'Successfully created service!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized!'
	        ], 401);
    	}
        
    }
	/**
     * Edit a permission
     */
    public function editPermission($id, Request $request){
    	$permission = Permission::find($id);
    	$service = Service::find($permission->service_id);

    	$request->validate([
			'status' => 'boolean'
        ]);
		if(isset($request->status)){
		    $permission->status = $request->status; 
		}
        if($service->owner = auth()->user()->id){
        	$permission->save();
        }
        return $permission;
    }

    /**
     * Delete a permission
     */
    public function deletePermission($id){
    	$permission = Permission::find($id);
    	$service = Service::find($permission->service_id);
        if(isset($service) && $service->owner == auth()->user()->id && isset($permission)){
        	$permission->delete();
	        return response()->json([
	            'message' => 'Successfully deleted!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted!'
	        ], 401);
    	}
    }
}
