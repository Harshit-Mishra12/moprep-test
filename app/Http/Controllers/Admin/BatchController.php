<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\Batch;
use DB;

class BatchController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			$rules=[				
				'name'=>'string|required',
				'start_date'=>'required',
				'end_date'=>'required',
				'price'=>'numeric|required',			
				'dollar_price'=>'numeric|required',			
				'short_desc'=>'string|required|max:50',
				'desc'=>'string|required',				
			];

			if((int) $request->post('id')==0){
						
				$rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg';
			}
					
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

					if($request->post('end_date') != $request->post('start_date') && $request->post('end_date') > $request->post('start_date')) {

						if((int) $request->post('id')>0){
							
							$batch=Batch::find($request->post('id'));
						}else{
							
							$batch=new Batch();
							$pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
							$uniqueId = substr(str_shuffle(str_repeat($pool, 2)), 0, 5) . date("s");
							$batch->unique_id=$uniqueId; 
						}

						$filename=$request->post('old_image');
						
						if($request->hasFile('image')){
							$filename = \App\Helpers\commonHelper::uploadFile($request->file('image'),'batch');
								
						} 

						
						$batch->name=$request->post('name');
						$batch->slug=\Str::slug($request->post('name'));
						$batch->course_id=$request->post('course_id');
						$batch->start_date=$request->post('start_date');
						$batch->end_date=$request->post('end_date');
						$batch->price=$request->post('price');				
						$batch->dollar_price=$request->post('dollar_price');				
						$batch->image=$filename;								
						$batch->short_desc=$request->post('short_desc');				
						$batch->desc=$request->post('desc');					
						
						$batch->save();
						
						if((int) $request->post('id')>0){
							
							return response(array('message'=>'Batch updated successfully.','reset'=>false),200);
						}else{
							
							return response(array('message'=>'Batch added successfully.','reset'=>true,'script'=>false),200);
						
						}

					}else{

						return response(array('message'=>'End date need to be bigger then start date.'),403);
					}
					
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        $course = \App\Models\Course::where('status','1')->orderBy('id','DESC')->get();
        return view('admin.batch.add',compact('result','course'));
    }
	
	public function batchList(){
		
		$result=\App\Models\Batch::where('deleted_at',NULL)->orderBy('id','DESC')->get();		
		return view('admin.batch.list',compact('result'));
	}
	
	public function changeStatus(Request $request){
		
		Batch::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Batch status changed successfully.'),200);
	}
	
	public function updateBatch(Request $request,$id){
		
		$result=Batch::find($id);
		
		if($result){
			$course = \App\Models\Course::where('status','1')->orderBy('id','DESC')->get();
			return view('admin.batch.add',compact('result','course'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteBatch(Request $request,$id){
		
		$result=Batch::find($id);
		
		if($result){
			
			Batch::where('id',$id)->delete();;
			
			return redirect()->back()->with('5fernsadminsuccess','Batch deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

	public function changeOrder(Request $request){
		
		$allData = $request->allData;
		$i = 1;
		foreach ($allData as $key => $value) {
			Batch::where('id',$value)->update(array('sort_order'=>$i));
			$i++;
		}
		
	}
}
