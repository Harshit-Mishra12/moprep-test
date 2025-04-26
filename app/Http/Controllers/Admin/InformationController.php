<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Information;
use Validator;

class InformationController extends Controller
{
    public function termCondition(){
		$result=Information::where('id','1')->first();
		return view('admin.information.add',compact('result'));
	}

    public function privacyPolicy(){
		$result=Information::where('id','2')->first();
		return view('admin.information.add',compact('result'));
	}

    public function UpdateDetail(Request $request){

		if($request->isMethod('post')){
			
			$rules=[
				'id'=>'numeric|required|in:1,2',
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
							
							$Information=Information::find($request->post('id'));
						}else{
							
							$Information=new Information();
						
						}
						
						$Information->description=$request->post('description');
						
						$Information->save();
						
						if((int) $request->post('id')>0){
							
							return response(array('message'=>'Information updated successfully.','reset'=>false),200);
						}
					}catch (\Exception $e){
				
						return response(array("message" => $e->getMessage()),403); 
					
					}
			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        return view('admin.information.add');
    }
}
