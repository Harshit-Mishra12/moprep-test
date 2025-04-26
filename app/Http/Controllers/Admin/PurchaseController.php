<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\Purchasedhistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\DataTables;
use DB;
use Carbon\Carbon;
use App\Models\CourseMaster;

class PurchaseController extends Controller
{
    public function purchasedHistory()
    {
        // $query = \App\Models\Transaction::orderBy('id','DESC');
  
        // if(isset($_GET['startDate']) && $_GET['startDate'] != ''){
        //     $query->whereDate('created_at','>=',$_GET['startDate']);
        // }

        // if(isset($_GET['toDate']) && $_GET['toDate'] != ''){
        //     $query->whereDate('created_at','<=',$_GET['toDate']);
        // }
        
        // $result = $query->get();

        $course = CourseMaster::orderBy('id', 'DESC')->get();
        return view('admin.purchase_history', compact('course'));
    }

    function purchasedHistoryData(Request $request)
    {
        $permission = session('permission') ?? [];
		$permission = isset($permission['13']) ? $permission['13'] : null;
// 		dd($permission0)
        if ($request->ajax()) {
            $query = \App\Models\Transaction::orderBy('id', 'DESC');

            // Apply filters based on the request
            if ($request->get('course_id') > 0) {
                $query->where('course_master_id', $request->course_id);
            }

            if (isset($_GET['startDate']) && $_GET['startDate'] != '') {
                $query->whereDate('created_at', '>=', $_GET['startDate']);
            }

                if(isset($_GET['toDate']) && $_GET['toDate'] != ''){
                    $query->whereDate('created_at','<=',$_GET['toDate']);
                }
                
                $result = $query->get();
		

              return Datatables::of($result)

                ->addIndexColumn()
                ->addColumn('coursemaster', function ($row) {

					return  \App\Helpers\commonHelper::getCourseMasterName($row->course_master_id);
				})
                ->addColumn('created_at', function ($row) {

                    return date('d-m-Y h:i A', strtotime($row['created_at']));
                })
                ->addColumn('start_date', function ($row) {

                    return $row['start_date'] ? date('d-m-Y h:i A', strtotime($row['start_date'])) : 'NA';
                })
                ->addColumn('end_date', function ($row) {

                    return $row['end_date'] ? date('d-m-Y h:i A', strtotime($row['end_date'])) : 'NA';
                })
                ->addColumn('status', function ($row)  {

                    if ($row['status'] == 0) {
                        $status = "<span class='text-warning'>Pending</span>";
                    } elseif ($row['status'] == '1') {
                        $status = "<span class='text-success'>Success</span>";
                    } elseif ($row['status'] == '2') {
                        $status = "<span class='text-danger'>Failed</span>";
                    } else {
                        $status = "NA";
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) use ($permission) {
                    $acceptUrl = url('admin/accept-purchase-history/' . $row['id']);
                    $rejectUrl = url('admin/reject-purchase-history/' . $row['id']);
                    $deleteUrl = url('admin/delete-purchase-history/' . $row['id']);

                $acceptBtn = '<a href="'.$acceptUrl.'" title="Accept Purchase" onclick="return confirm(\'Are you sure? You want to Accept this purchase.\')" class="btn btn-tbl-edit btn_change"><i class="fas fa-check"></i></a>';
                $rejectBtn = '<a href="'.$rejectUrl.'" title="Reject Purchase" onclick="return confirm(\'Are you sure? You want to Reject this purchase.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-times"></i></a>';
                	$deleteBtn = '';
                  if(isset($permission) && $permission['delete'] == 'true'){
                    $deleteBtn = '<a href="'.$deleteUrl.'" title="Remove Transaction history" onclick="return confirm(\'Are you sure? You want to delete this transaction history.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
                  }
                return $deleteBtn;
            })
			->rawColumns(['status','action'])
            ->make(true);


        }else{

            return view('admin.purchase_history');
        } 
    }


    public function addAssignBatch(Request $request){

        if ($request->isMethod('post')) {

            $rules = [
                'batch_id' => 'required',
                'user_id' => 'required',
                'course_id' => 'required',
            ];
    
            $validator = \Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                $message = "";
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message = $msg[0];
                    break;
                }
    
                return response(array('message' => $message), 403);

            } else {

                try {

                    $batchName = \App\Models\Batch::where('name',$request->post('batch_id'))->where('status', '1')->first();
                    
                    $purchasedHistory = \App\Models\Purchasedhistory::where('user_id', $request->post('user_id'))
                                                                        ->where('batch_id', $batchName->id)
                                                                        ->first();
                                                    
                    $id = $request->post('user_id');
                    $getPurchaseHistory = \App\Models\User::whereHas('PurchaseHistory', function($query) use($id){
                        $query->where('user_id',$id);
                    })->with(['PurchaseHistory'=> function($query) use($id){
                        $query->where('user_id',$id);
                    }])->get();

                    if ((int)$request->post('id') > 0) {

                        $batch = \App\Models\Purchasedhistory::find($request->post('id'));
                    } else {
                        $batch = new Purchasedhistory();

                        if ($purchasedHistory) {

                            return response(array('message' => 'Batch already purchased.'), 403);
    
                        } 
                    }

                    $batch->batch_id = $batchName->id;
                    $batch->course_id = $request->post('course_id');
                    $batch->user_id = $request->post('user_id');
                    $batch->save();

                    if ((int)$request->post('id') > 0) {
                        return response(array('message' => 'Batch updated successfully.', 'reset' => false), 200);
                    } else {
                        return response(array('message' => 'Batch added successfully.', 'reset' => true, 'script' => false), 200);
                    }

                } catch (\Exception $e) {
                    return response(array("message" => $e->getMessage()), 403);
                }
            }
    
            return response(array('message' => 'Data not found.'), 403);
        }
    
        $result = [];
        $course = \App\Models\Course::where('status', '1')->orderBy('id', 'DESC')->get();
        $student = \App\Models\User::where('status', '1')->where('user_type', 'User')->orderBy('id', 'DESC')->get();
    
        return view('admin.batch.add_assign_batch', compact('result', 'course', 'student'));
    }
    

    public function getBatch(Request $request){
        
        $batchData = \App\Models\Batch::where('course_id', $request->post('course_id'))
            ->where('status', '1')
            ->orderBy('id', 'DESC')
            ->get();
    
        if ($batchData->isNotEmpty()) {
            $batchNames = $batchData->pluck('name')->toArray();
    
            return response(['message' => 'Batch data fetched successfully.', 'batchData' => $batchNames], 200);
        } else {
            return response(['message' => 'No batch data found.'], 200);
        }
    }
    
    public function deletePurchasedHistory(Request $request,$id){
		
		$result=\App\Models\Transaction::find($id);
		
		if($result){
			
			\App\Models\Transaction::where('id',$id)->delete();;
			
			return redirect()->back()->with('5fernsadminsuccess','Transaction history deleted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

    public function acceptPurchasedHistory(Request $request,$id){
		
		$result=\App\Models\Transaction::find($id);
		
		if($result){
			
			DB::table('transactions')->where('id',$id)->update(['status'=>'1']);
			
			return redirect()->back()->with('5fernsadminsuccess','Transaction accepted successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

    public function rejectPurchasedHistory(Request $request,$id){
		
		$result=\App\Models\Transaction::find($id);
		
		if($result){
			DB::table('transactions')->where('id',$id)->update(['status'=>'2']);
			
			return redirect()->back()->with('5fernsadminsuccess','Transaction rejected successfully.');
			
		}else{
			
			return redirect()->back()->with('5fernsadminerror','Something went wrong. Please try again.');
		}
		
	}

    public function exportToExcel()
    {
        // Fetch your data from the database or wherever it's stored
        $data = \App\Models\Transaction::orderBy('id','DESC')->get(); // Replace YourModel with your actual model

        // Create new PhpSpreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Student Name');
        $sheet->setCellValue('C1', 'Student Mobile');
        $sheet->setCellValue('D1', 'Paid Amount');
        $sheet->setCellValue('E1', 'Transaction ID');
        $sheet->setCellValue('F1', 'Subscription Plan Purchased');
        $sheet->setCellValue('G1', 'Transaction Date');

        // Add data
        $row = 2;
        $i=1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $i++);
            $sheet->setCellValue('B' . $row, $item->student_name);
            $sheet->setCellValue('C' . $row, $item->student_mobile);
            $sheet->setCellValue('D' . $row, $item->paid_amount);
            $sheet->setCellValue('E' . $row, $item->transaction_id);
            $sheet->setCellValue('F' . $row, $item->subscription_name);
            $sheet->setCellValue('G' . $row, $item->created_at->format('d-m-Y h:i A'));

            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Save Excel file to storage
        $fileName = 'transactions.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer->save($filePath);

        // Return download response
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportUserToExcel()
    {
        // Fetch your data from the database or wherever it's stored
        $data = \App\Models\User::orderBy('id','DESC')->where('id','!=','1')->get(); // Replace YourModel with your actual model

        // Create new PhpSpreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Mobile');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'College');
        $sheet->setCellValue('F1', 'State');
        $sheet->setCellValue('G1', 'Subscription Status');
        $sheet->setCellValue('H1', 'Added On');

        // Add data
        $row = 2;
        $i=1;
        foreach ($data as $item) {

            $user_id = $item->id;

					$currentDateTime = Carbon::now();

					$cond = [
						"user_id" => $user_id,
						"status" => "1"
					];

					$subscriptionStatus = DB::table('transactions')->where($cond)->where('start_date','<=',$currentDateTime)->where('end_date','>=',$currentDateTime)->count();

					if($subscriptionStatus>0){
						$sStatus = "Active";
					}else{
						$sStatus = "Inactive";
					}

            $sheet->setCellValue('A' . $row, $i++);
            $sheet->setCellValue('B' . $row, $item->name);
            $sheet->setCellValue('C' . $row, $item->whatsapp_number);
            $sheet->setCellValue('D' . $row, $item->email);
            $sheet->setCellValue('E' . $row, $item->college);
            $sheet->setCellValue('F' . $row, $item->state);
            $sheet->setCellValue('G' . $row, $sStatus);
            $sheet->setCellValue('H' . $row, $item->created_at->format('d-m-Y h:i A'));

            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Save Excel file to storage
        $fileName = 'registeredUsers.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer->save($filePath);

        // Return download response
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportMockToExcel(Request $request, $id)
    {
        // Fetch your data from the database or wherever it's stored
        $data=DB::table('mockup_test_result')->where('mock_id',$id)->orderBy('marks','DESC')->get();

        // Create new PhpSpreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Marks');
        $sheet->setCellValue('D1', 'Attempted');
        $sheet->setCellValue('E1', 'Correct');
        $sheet->setCellValue('F1', 'Incorrect');
        $sheet->setCellValue('G1', 'Duration');

        // Add data
        $row = 2;
        $i=1;
        foreach ($data as $item) {

            $sheet->setCellValue('A' . $row, $i++);
            $sheet->setCellValue('B' . $row, $item->user_name);
            $sheet->setCellValue('C' . $row, $item->marks);
            $sheet->setCellValue('D' . $row, $item->attempted_questions);
            $sheet->setCellValue('E' . $row, $item->right_answers);
            $sheet->setCellValue('F' . $row, $item->wrong_answers);
            $sheet->setCellValue('G' . $row, $item->average_time.' Min');

            $row++;
        }

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Save Excel file to storage
        $fileName = 'MockResultExcel.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer->save($filePath);

        // Return download response
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

}
