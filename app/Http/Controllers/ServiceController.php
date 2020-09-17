<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service;
use App\Permission;
use App\Request as Req;

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
	            'message' => 'Successfully created permission!'
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
    //Request
    /**
     * Create a request
     */
    public function createRequest($id, Request $request){
    	$service = Service::find($id);

    	if(isset($service)){
    		$request->validate([
	            'from_lat' =>'required',
	            'from_lng' =>'required',
	            'to_lat' =>'required',
	            'to_lng' =>'required',
	            'from_time' =>'required',
	            'user' => 'required'
	        ]);
    		$time = new \DateTime($request->from_time);
	        Req::create([
	            'service_id' => $id,
	            'user_id' => auth()->user()->id,
	            'manager_id' => $request->user,
	            'from_lat' => $request->from_lat,
				'from_lng' => $request->from_lng,
				'to_lat' => $request->to_lat,
				'to_lng' => $request->to_lng,
				'from_time' => $time
	        ]);

	        return response()->json([
	            'message' => 'Successfully created request!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized!'
	        ], 401);
    	}
        
    }
	/**
     * Edit a request
     */
    public function editRequest($id, Request $request){
    	$req = Req::find($id);
    	$service = Service::find($req->service_id);

    	$request->validate([
			'status' => 'boolean',
			'from_time' => 'datetime',
	        'delivery_time' => 'datetime',
	        'qualification' => 'integer',
	        'msg' => 'string'

        ]);
		if(isset($request->status) && 
			(auth()->user()->id === $req->user_id    || 
		     auth()->user()->id === $req->manager_id ||
		     auth()->user()->id === $service->owner)){
		    $req->status = $request->status; 
		}
		if(isset($request->from_lat) && auth()->user()->id === $req->user_id ){
		    $req->from_lat = $request->from_lat; 
		}
		if(isset($request->from_lng) && auth()->user()->id === $req->user_id ){
		    $req->from_lng = $request->from_lng; 
		}
		if(isset($request->to_lat) && auth()->user()->id === $req->user_id ){
		    $req->to_lat = $request->to_lat; 
		}
		if(isset($request->to_lng) && auth()->user()->id === $req->user_id ){
		    $req->to_lng = $request->to_lng; 
		}
		if(isset($request->delivery_time) && auth()->user()->id === $req->user_id ){
		    $req->delivery_time = $request->delivery_time; 
		}
		if(isset($request->from_time) && auth()->user()->id === $req->manager_id ){
		    $req->from_time = $request->from_time; 
		}
		if(isset($request->qualification) && auth()->user()->id === $req->user_id ){
		    $req->qualification = $request->qualification; 
		}
		if(isset($request->msg) && auth()->user()->id === $req->user_id ){
		    $req->msg = $request->msg; 
		}
        	$req->save();
		return $req;
    }

    /**
     * Delete a request
     */
    public function deleteRequest($id){
    	$req = Req::find($id);
    	$service = Service::find($req->service_id);
        if(isset($service) && $service->owner == auth()->user()->id && isset($req)){
        	$req->delete();
	        return response()->json([
	            'message' => 'Successfully deleted!'
	        ], 201);
    	}else{
    		return response()->json([
	            'message' => 'Unauthorized to deleted!'
	        ], 401);
    	}
    }
    /**
     * Show all user's requests
     */
    public function getUserRequest($id, Request $request){
    	return Req::join('users','users.id','=','requests.manager_id')
    				  ->join('services','services.id','=','requests.service_id')
    				  ->where('requests.user_id','=',$id)
    				  ->select('requests.*', 'users.name as user_name', 'users.email', 'users.tlfn', 'users.verified', 'users.custom', 'services.name as service')
    				  ->orderBy('requests.status','desc')
    			      ->orderBy('requests.created_at','desc')
    			      ->get();
    }
    /**
     * Show all manager's requests
     */
    public function getManagerRequest($id, Request $request){
    	return Req::join('users','users.id','=','requests.user_id')
    				  ->join('services','services.id','=','requests.service_id')
    				  ->where('requests.manager_id','=',$id)
    				  ->select('requests.*', 'users.name as user_name', 'users.email', 'users.tlfn', 'users.verified', 'users.custom', 'services.name as service')
    				  ->orderBy('requests.status','desc')
    			      ->orderBy('requests.created_at','desc')
    			      ->get();
    }
    /**
     * Show all services's requests
     */
    public function getServiceRequest($id, Request $request){
    	return Req::join('users','users.id','=','requests.manager_id')
    			  ->join('services','services.id','=','requests.service_id')
    			  ->where('requests.service_id','=',$id)
    			  ->select('requests.*', 'users.name as user_name', 'users.email', 'users.tlfn', 'users.verified', 'users.custom', 'services.name as service')
    			  ->orderBy('requests.status','desc')
    			  ->orderBy('requests.created_at','desc')
    			  ->get();
    }
}
