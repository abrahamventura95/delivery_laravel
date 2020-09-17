<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Service;

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
    	$service = Service::find($id);
    	return $service;
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
}
