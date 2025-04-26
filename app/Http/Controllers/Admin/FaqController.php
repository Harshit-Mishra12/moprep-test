<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\FAQ;

class FaqController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){			
            $rules=[
				'id'=>'numeric|required',	
                'title' => 'required',
                'description'=>'required'
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

						$faq=FAQ::find($request->post('id'));
					}else{
						
						$faq=new FAQ();
					
					}
															
					$faq->title=$request->post('title');					
					$faq->description=$request->post('description');					

					$faq->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'FAQ updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'FAQ added successfully.','reset'=>true,'script'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
        return view('admin.faq.add',compact('result','courses'));
    }
	
	public function faqList(){
		$result=FAQ::with('course_details')->get();		
		return view('admin.faq.list',compact('result'));
	}
	
	public function changeStatus(Request $request){
		
		FAQ::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'FAQ status changed successfully.'),200);
	}
	
	public function updateFaq(Request $request,$id){
		
		$result=FAQ::find($id);
		
		if($result){
            $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
			return view('admin.faq.add',compact('result','courses'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteFaq(Request $request,$id){
		
		$result=FAQ::find($id);
		
		if($result){
			
			FAQ::where('id',$id)->delete();;
			
			return redirect()->back()->with('5fernsadminsuccess','FAQ deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	} 
}
