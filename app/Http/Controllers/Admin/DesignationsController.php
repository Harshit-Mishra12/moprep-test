<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Designation;
use App\Models\Menu;
use App\Models\User_role;
use Validator;

class DesignationsController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){
			
			$rules=[
				'designations'=>'required',
				'id'=>'numeric|required',
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
						
						$designation=Designation::find($request->post('id'));
						$designationDetails = $designation;
					}else{
						
						$designation=new Designation();
						$designation->status=1;
					} 
					
					$permissions = [];
						foreach ($request->input('permissions') as $menu_id => $actions) {
							$permissions[$menu_id] = [
								'read' => isset($actions['read']),
								'write' => isset($actions['write']),
								'edit' => isset($actions['edit']),
								'delete' => isset($actions['delete']),
							];
						}

					$designation->permissions= json_encode($permissions);
					$designation->designations=$request->post('designations');
					
					$designation->save();
				 	$id = $designation->id;

					$menuList = Menu::all();
					foreach($menuList as $menu){
						$menuDt = User_role::where(['designation_id' =>  $id, 'menu_id' =>  $menu->id])->first();
                
						if(empty($menuDt)) {
							$userRole=new User_role();
							$userRole->designation_id = $id;
							$userRole->menu_id = $menu->id;
							$userRole->status = true;
							$userRole->save();
						}
					}
					
					if((int) $request->post('id')>0){
						
						return response(array('message'=>'Designation updated successfully.','reset'=>false),200);
					}else{
						
						return response(array('message'=>'Designation added successfully.','reset'=>true,'script'=>false),200);
					
					}
				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
		$menuList =	Menu::where('is_permission','1')->where('status','1')->orderBy('sort','ASC')->get();
// 		dd($menuList);
		$type = 'add';
        return view('admin.designation.add',compact('result','menuList','type'));
    }

    public function roleList(){

		$result = Designation::orderBy('id','DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['72']) ? $permission['72'] : null;

		return view('admin.designation.list',compact('result','permission'));
	}
	
	public function changeStatus(Request $request){

		Designation::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Designation status changed successfully.'),200);
	}
	
	public function updateDesignation(Request $request,$id){
		
		$result = Designation::find($id);
		if($result) {	
			$menuList =	Menu::where('is_permission','1')->where('status','1')->orderBy('sort','ASC')->get();
			$storedPermissions = Designation::where('id', $id)->value('permissions');
			$storedPermissions = json_decode($storedPermissions, true);
			$type = 'edit';

			return view('admin.designation.add',compact('result','menuList','storedPermissions','type'));
			
		} else {	
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}	
	}
	
	public function deleteDesignation(Request $request,$id){
		
		$result = Designation::find($id);
		
		if($result){
			
			Designation::where('id',$id)->delete();
			
			return redirect()->back()->with('5fernsadminsuccess','Designation deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
}
