<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\NotesMaterials;
use DB;

class NoteMaterialsController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			$rules=[				
				'course_id'=>'required',
				'batch_id'=>'required',
				'file_type'=>'required',			
				'topic_id'=>'required',	
				'file' => ((int)$request->post('id') == 0) ? 'required' : '',		
			];
	
			if ($request->file_type == "image") {
				$rules['file'] .= '|mimes:jpg,jpeg,png,gif';
			}elseif($request->file_type == "pdf"){
				$rules['file'] .= '|mimes:pdf';
			}elseif ($request->file_type == "video") {
				$rules['file'] .= '|mimes:mp4,avi,mov|max:550000';
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


					if((int) $request->post('id')>0){

						$notesMaterial=NotesMaterials::find($request->post('id'));

					}else{
						
						$notesMaterial=new NotesMaterials();

					
					}
					$topicId = $request->post('topic_id');
					$fileType = $request->post('file_type');

					$existingFilesCount = NotesMaterials::where('topic_id', $topicId)
					->where('file_type', $fileType)
					->where('id','!=', $request->post('id'))
					->count();

					if ($fileType == 'video') {
						$videoCount = $existingFilesCount >= 3;
						if ($videoCount) {
							return response(array('message'=>'Maximum Three Videos are allowed in a Topic.'),403);
						}	
					}elseif ($fileType == 'pdf') {
						$pdfCount = $existingFilesCount >= 1;
						if ($pdfCount) {
							return response(array('message'=>'Maximum One PDF are allowed in a Topic.'),403);
						}	
					}

					if($request->hasFile('file')){
						$imageData = $request->file('file');
						$file = strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
						$destinationPath = public_path('/uploads/file');
						$imageData->move($destinationPath, $file);
	
						$notesMaterial->file=$file;
					} 

					$notesMaterial->title=$request->post('title');
					$notesMaterial->batch_id=$request->post('batch_id');
					$notesMaterial->course_id=$request->post('course_id');
					$notesMaterial->topic_id=$request->post('topic_id');
					$notesMaterial->file_type=$request->post('file_type');
                    				
					
					$notesMaterial->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Notes Materials updated successfully.','reset'=>false,'script'=>true),200);
					}else{
						
						return response(array('message'=>'Notes Materials added successfully.','reset'=>true,'script'=>false),200);
					
					}

				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        $course = \App\Models\Course::where('status','1')->orderBy('id','DESC')->get();
        return view('admin.notes_materials.add',compact('result','course'));
    }
	
	public function notesMaterialList(){
		
		$result=\App\Models\NotesMaterials::orderBy('id','DESC')->get();		
		return view('admin.notes_materials.list',compact('result'));
	}
	
	public function changeStatus(Request $request){
		
		NotesMaterials::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Notes Materials status changed successfully.'),200);
	}
	
	public function updateNotesMaterial(Request $request,$id){
		
		$result=NotesMaterials::find($id);
		
		if($result){
			$course = \App\Models\Course::where('status','1')->orderBy('id','DESC')->get();
			$batchName = \App\Models\Batch::where('name',$request->post('batch_id'))->where('status', '1')->get();
			return view('admin.notes_materials.add',compact('result','course','batchName'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteNotesMaterial(Request $request,$id){
		
		$result=NotesMaterials::find($id);
		
		if($result){
			
			NotesMaterials::where('id',$id)->delete();;
			
			return redirect()->back()->with('5fernsadminsuccess','Notes Materials deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function getBatchName(Request $request)
	{
		$batchData = \App\Models\Batch::where('course_id', $request->post('course_id'))
			->where('status', '1')
			->orderBy('id', 'DESC')
			->get();
	
		if ($batchData->isNotEmpty()) {
			$batchNames = $batchData->map(function ($batch) {
				return [
					'id' => $batch->id,
					'name' => ucfirst($batch->name),
				];
			})->toArray();
	

			return response(['message' => 'Batch data fetched successfully.', 'batchData' => $batchNames], 200);
		} else {
			return response(['message' => 'No batch data found.'], 200);
		}
	}
    
    public function getTopicName(Request $request){

		$topicData = \App\Models\TopicMaterials::where('batch_id', $request->post('batch_id'))
            ->where('status', '1')
            ->orderBy('id', 'DESC')
            ->get();
    
		if ($topicData->isNotEmpty()) {
			$topicsNames = $topicData->map(function ($topic) {
				return [
					'id' => $topic->id,
					'name' => ucfirst($topic->topic),
				];
			})->toArray();

			return response(['message' => 'Topic data fetched successfully.', 'topicData' => $topicsNames ], 200);
        } else {
            return response(['message' => 'No topic data found.'], 200);
        }
    }
}
