<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Validator;

class BannerController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			
			$rules=[
				'name'=>'required',
				'id'=>'numeric|required',
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
					if((int) $request->post('id')>0){
						$banner=Banner::find($request->post('id'));
					}else{
						$banner=new Banner();
					}
					$image=$request->post('old_image');
					if($request->hasFile('image')){
						$imageData = $request->file('image');
						$image = strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
						$destinationPath = public_path('/uploads/banner');
						$imageData->move($destinationPath, $image);
					} 
					$banner->name=$request->post('name');
					$banner->redirect_to=$request->post('redirect_to');
					$banner->image=$image;
					$banner->save();
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Banner updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'Banner added successfully.','reset'=>true,'script'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        return view('admin.banner.add',compact('result'));
    }

    public function bannerList(){
		
		$result=Banner::orderBy('id','DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['47']) ? $permission['47'] : null;
		
		return view('admin.banner.list',compact('result','permission'));
	}
	
	public function changeStatus(Request $request){
		
		Banner::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Banner status changed successfully.'),200);
	}
	
	public function updateBanner(Request $request,$id){
		
		$result=Banner::find($id);
		
		if($result){
			
			return view('admin.banner.add',compact('result'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function deleteBanner(Request $request,$id){
		
		$result=Banner::find($id);
		
		if($result){
			
			Banner::where('id',$id)->delete();
			
			return redirect()->back()->with('5fernsadminsuccess','Banner deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
}
