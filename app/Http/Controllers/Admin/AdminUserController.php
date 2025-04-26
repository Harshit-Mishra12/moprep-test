<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Validator;
use \App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class AdminUserController extends Controller
{
    public function add(Request $request){

		if($request->isMethod('post')){			
            $rules['name'] = 'required';	            
            $rules['email'] = 'string|required|unique:users,email,'.$request->post('id');
			$rules['whatsapp_number'] = 'integer|required|unique:users,whatsapp_number,'.$request->post('id');	            
			
			$customMessages = [
				'whatsapp_number.required' => 'The mobile number field is required.',
				'whatsapp_number.unique' => 'The mobile number has already been taken.',
				'whatsapp_number.integer' => 'The mobile number field must be in number only.',
			];

			$validator = Validator::make($request->all(), $rules, $customMessages);
			
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
					if((int) $request->post('id')>0 && (int) $request->post('id') != 1){
						$user=User::find($request->post('id'));
					}else{
						$user=new User();
						$user->user_type= 'Admin';
						$user->password=Hash::make($request->post('password'));					
					}
										
					$user->name=$request->post('name');					
					$user->email=$request->post('email');
					// 12345678					
				// 	$user->password=Hash::make('12345678');					
				
					$user->whatsapp_number=$request->post('whatsapp_number');					
					$user->college=$request->post('college');					
					$user->state=$request->post('state');					
					$user->designation_id= $request->post('designation_id');
					$user->avatar= 'avatar.png';
					$user->save();
					
					if((int) $request->post('id')>0) {
						return response(array('message'=>'User updated successfully.','reset'=>false),200);
					} else {
						return response(array('message'=>'User added successfully.','reset'=>true,'script'=>false),200);
					}

				} catch (\Exception $e) {
					return response(array("message" => $e->getMessage()),403); 	
				}
			}

			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
		$designations = Designation::where('status', '1')->orderBy('id', 'DESC')->get();
        return view('admin.admin-user.add',compact('result','designations'));
    }

	public function updateUser(Request $request,$id){
		
		$result=User::find($id);
		
		if($result){

			$designations = Designation::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.admin-user.add',compact('result','designations'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

	public function examDetailUser(Request $request,$id){
		
		$result=User::find($id);
		$questionBankResult = DB::table('question_bank_result')->where('user_id',$id)->orderBy('id','desc')->get();
		$preResult = DB::table('previous_year_result')->where('user_id',$id)->orderBy('id','desc')->get();
		$mockResult = DB::table('mockup_test_result')->where('user_id',$id)->orderBy('id','desc')->get();
		$user_id = $id;
		if($result){
			
			return view('admin.admin-user.examdetail',compact('result','questionBankResult','preResult','user_id','mockResult'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

	public function detailUser(Request $request,$id){
		
		$result=DB::table('question_bank_result')->where('id',$id)->first();
		
		if($result){
			
			return view('admin.admin-user.detail',compact('result'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

	public function detailUserPre(Request $request,$id){
		
		$result=DB::table('previous_year_result')->where('id',$id)->first();
		
		if($result){
			
			return view('admin.admin-user.detailPre',compact('result'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

	public function detailUserMock(Request $request,$id){
		
		$result=DB::table('mockup_test_result')->where('id',$id)->first();
		
		if($result){
			
			return view('admin.admin-user.detailMock',compact('result'));
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}
	
	public function userList(){
		
        // if(isset($_GET['duration']) && $_GET['duration']!=''){

		// 	$duration = $_GET['duration'];

		// 	if($duration=='day'){
		// 		$yesterday = date('Y-m-d',strtotime(Carbon::yesterday()));
		// 		$result = User::orderBy('id', 'DESC')->whereDate('created_at',$yesterday)->get();
		// 	}elseif($duration=='week'){
		// 		$lastWeekStartDate = Carbon::now()->startOfWeek()->subWeek()->toDateString();
		// 		$lastWeekEndDate = Carbon::now()->startOfWeek()->subDay()->toDateString();
		// 		$result = User::orderBy('id', 'DESC')->whereDate('created_at','>=',$lastWeekStartDate)->whereDate('created_at','<=',$lastWeekEndDate)->get();
		// 	}elseif($duration=='month'){
		// 		$lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
		// 		$lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();

		// 		$result = User::orderBy('id', 'DESC')->whereDate('created_at','>=',$lastMonthStartDate)->whereDate('created_at','<=',$lastMonthEndDate)->get();
		// 	}else{
		// 		$result=User::orderBy('id','DESC')->get();
		// 	}

		// }else{
		// 	$duration = "";
		// 	$result=User::orderBy('id','DESC')->get();		
		// }




		$permission = session('permission') ?? [];
		$permission = isset($permission['75']) ? $permission['75'] : null;

		return view('admin.admin-user.list',compact('permission'));
	}

	public function userListData(Request $request){

			
		$permission = session('permission') ?? [];
		$permission = isset($permission['75']) ? $permission['75'] : null;

			if ($request->ajax()) {
				$result = User::orderBy('id','DESC')->where('user_type','Admin');
  
                if(isset($_GET['startDate']) && $_GET['startDate'] != ''){
                    $result->whereDate('created_at','>=',$_GET['startDate']);
                }

                if(isset($_GET['toDate']) && $_GET['toDate'] != ''){
                    $result->whereDate('created_at','<=',$_GET['toDate']);
                }
                
                $result = $result->get();
			
	
				return Datatables::of($result)
	
				->addIndexColumn()
				// ->addColumn('college', function($row){
					
				// 	return $row->college!='' ? $row->college : 'NA';
				// })
				->addColumn('state', function($row){
					
					return $row->state!='' ? $row->state : 'NA';
				})
				// ->addColumn('subscription_status', function($row){

				// 	$user_id = $row->id;

				// 	$currentDateTime = Carbon::now();

				// 	$cond = [
				// 		"user_id" => $user_id,
				// 		"status" => "1"
				// 	];

				// 	$subscriptionStatus = DB::table('transactions')->where($cond)->where('start_date','<=',$currentDateTime)->where('end_date','>=',$currentDateTime)->count();

				// 	if($subscriptionStatus>0){
				// 		$sStatus = "<span class='text-success'>Active</span>";
				// 	}else{
				// 		$sStatus = "<span class='text-danger'>Inactive</span>";
				// 	}
					
				// 	return $sStatus;
				// })
				->addColumn('created', function($row){
					
					return date('d-m-Y',strtotime($row->created_at));
				})
				->addColumn('status', function ($row) {
					$status = '<td class="center">
					<div class="switch mt-3">
					<label>
					<input type="checkbox" class="-change" data-id="' .$row->id .'"'.($row->status == 1 ? 'checked': '').'>
					<span class="lever switch-col-red layout-switch"></span>
					</label>
					</div>
					</td>';
					return $status;
				})
				->addColumn('action', function($row) use ($permission) {
					
					$editBtn = '';
					$deleteBtn = '';
					
					// $viewUrl = url('admin/user/examDetail/'.$row->id);
					$editUrl = url('admin/admin-user/update/'.$row->id );
					$deleteUrl = url('admin/admin-user/delete/'.$row->id );
	
					// $viewBtn = '<a href="'.$viewUrl.'" title="View Student Analysis" class="btn btn-tbl-edit btn_change"><i class="fa fa-eye"></i></a>';
					if(isset($permission) && $permission['edit'] == 'true' && $row->id != 1){
						$editBtn = '<a href="'.$editUrl.'" title="Edit User" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';

					}	

					if(isset($permission) && $permission['delete'] == 'true' && $row->id != 1){
						$deleteBtn = '<a href="'.$deleteUrl.'" title="Remove User" onclick="return confirm(\'Are you sure? You want to delete this User.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
					}
					
					// $viewBtn.' '.

					return $editBtn .' '. $deleteBtn;
				})
				->rawColumns(['subscription_status','status','action'])
				->make(true);
	
	
			}else{
	
				return view('admin.admin-user.list');
			} 


	}
	
	public function changeStatus(Request $request){
		
		User::where('id',$request->post('id'))->update(['status'=>$request->post('status')]);
		
		return response(array('message'=>'Student status changed successfully.'),200);
	}
	
	public function deleteUser(Request $request,$id){
		
		$result=User::find($id);
		
		if($result){
			
			User::where('id',$id)->delete();
			\App\Models\Purchasedhistory::where('user_id',$id)->delete();
			
			return redirect()->back()->with('5fernsadminsuccess','Student deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

}
