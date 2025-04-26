<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseMaster;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Validator;

class SubscriptionController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			
			$rules=[
				'name'=>'required',
				'id'=>'numeric|required',
				'duration_type'=>'numeric|required',
				'duration'=>'numeric|required',
				'price'=>'numeric|required',
				'course_master_id' => 'required'
			];
			 
			// if((int) $request->post('id')==0){
						
			// 	$rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg';
			// }
					
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()){
				$message = "";
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				}
				
				return response(array('message'=>$message),403);
				
			}else{
				
				try{
					if((int) $request->post('id')>0){
						
						$subscription=Subscription::find($request->post('id'));
					}else{
						
						$subscription=new Subscription();
					
					}
					
					// $image=$request->post('old_image');
					
					// if($request->hasFile('image')){
					// 	$imageData = $request->file('image');
					// 	$image = strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
					// 	$destinationPath = public_path('/uploads/banner');
					// 	$imageData->move($destinationPath, $image);
					// } 

					$subscription->course_master_id=$request->post('course_master_id');
					$subscription->name=$request->post('name');
					$subscription->duration_type=$request->post('duration_type');
					$subscription->duration=$request->post('duration');
					$subscription->price=$request->post('price');
					$subscription->description=$request->post('description');
					//$subscription->image=$image;
					
					$subscription->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Subscription updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'Subscription added successfully.','reset'=>true,'script'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
		$courses = \App\Models\CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
        return view('admin.subscription.add',compact('result', 'courses'));
    }

    public function subscriptionList(){
		
		$result=Subscription::orderBy('id','DESC')->get();

		$permission = session('permission') ?? [];
		$permission = isset($permission['53']) ? $permission['53'] : null;
		
		
		return view('admin.subscription.list',compact('result','permission'));
	}
	
	public function changeStatus(Request $request){
		
		Subscription::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Subscription status changed successfully.'),200);
	}
	
	public function updateSubscription(Request $request,$id){
		
		$result=Subscription::find($id);
		
		if($result){
			$courses = \App\Models\CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.subscription.add',compact('result', 'courses'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteSubscription(Request $request,$id){
		
		$result=Subscription::find($id);
		
		if($result){
			
			Subscription::where('id',$id)->delete();
			
			return redirect()->back()->with('5fernsadminsuccess','Subscription deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
}
