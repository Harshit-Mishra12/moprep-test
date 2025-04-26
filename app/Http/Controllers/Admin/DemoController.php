<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\DemoVideo;
use Validator;

class DemoController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){			
            $rules=[
				'id'=>'numeric|required',	
                'course_id'	=>'numeric|required',
                'title' => 'required|unique:demo_videos,title,' . $request->post('id') . ',id,course_id,' . $request->post('course_id') . ',deleted_at,NULL',
                'video_link'=>'url|required'
			];

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

						$demo=DemoVideo::find($request->post('id'));
					}else{
						
						$demo=new DemoVideo();
					
					}
										
					$demo->course_id=$request->post('course_id');					
					$demo->title=$request->post('title');					
					$demo->video_link=$request->post('video_link');					

					$demo->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Demo Video updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'Demo Video added successfully.','reset'=>true,'script'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
        return view('admin.demo_video.add',compact('result','courses'));
    }
	
	public function demoList(){
		$result=DemoVideo::with('course_details')->get();		
		return view('admin.demo_video.list',compact('result'));
	}
	
	public function changeStatus(Request $request){
		
		DemoVideo::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Demo Video status changed successfully.'),200);
	}
	
	public function updateDemo(Request $request,$id){
		
		$result=DemoVideo::find($id);
		
		if($result){
            $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
			return view('admin.demo_video.add',compact('result','courses'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteDemo(Request $request,$id){
		
		$result=DemoVideo::find($id);
		
		if($result){
			
			DemoVideo::where('id',$id)->delete();;
			
			return redirect()->back()->with('5fernsadminsuccess','Demo Video deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	} 
}
