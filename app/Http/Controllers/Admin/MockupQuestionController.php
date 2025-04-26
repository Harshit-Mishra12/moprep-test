<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Chapter;
use App\Models\CourseMaster;
use \App\Models\Question;
use \App\Models\TopicMaterials;
use Validator;
use DB;
use App\Models\Mockup;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;


class MockupQuestionController extends Controller
{
    

	public function add(Request $request){

		if($request->isMethod('post')){		
            $rules=[
				'id'=>'numeric|required',	
                'mock_id'=>'numeric|required',
                'selectedQuestion'=>'required',
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
	
					$mock_id=$request->post('mock_id');
					$questions=$request->post('selectedQuestion');

					foreach($questions as $question_id){
						$data = [
                          'mock_id' => $mock_id,
                          'question_id' => $question_id,
						];

						$checkData = [
							'mock_id' => $mock_id,
							'question_id' => $question_id,
						  ];

						$check = DB::table('mock_questions')->where($checkData)->count();

						if($check==0){
							DB::table('mock_questions')->insert($data);
						}

					}
	
					return response(array('message'=>'Question added successfully.','reset'=>true,'script'=>true),200);

				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		
		$result=[];
        $previousYear = DB::table('bulk_question_years')->where('status','1')->orderBy('id','DESC')->get();
       // $mockups = DB::table('mockups')->where('status','Active')->orderBy('id','DESC')->get();
        $currentDateTime = now()->format('Y-m-d H:i');
$mockups = DB::table('mockups')
    ->join('course_masters', 'mockups.course_master_id', '=', 'course_masters.id')
    ->where('mockups.status', 'Active')
    ->whereRaw("STR_TO_DATE(CONCAT(mockups.end_date, ' ', mockups.end_time), '%Y-%m-%d %H:%i') > ?", [$currentDateTime])
    ->orderBy('mockups.id', 'DESC')
    ->select('mockups.*', 'course_masters.name as course_master_name')
    ->get();
        return view('admin.mockup_question.add',compact('result','previousYear','mockups'));
    }

	public function addSubjectQuestion(Request $request){

		if($request->isMethod('post')){		
            $rules=[
				'id'=>'numeric|required',	
                'mock_id'=>'numeric|required',
                'selectedQuestion'=>'required',
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
	
					$mock_id=$request->post('mock_id');
					$questions=$request->post('selectedQuestion');

					foreach($questions as $question_id){
						$data = [
                          'mock_id' => $mock_id,
                          'question_id' => $question_id,
						];

						$checkData = [
							'mock_id' => $mock_id,
							'question_id' => $question_id,
						  ];

						$check = DB::table('mock_questions')->where($checkData)->count();

						if($check==0){
							DB::table('mock_questions')->insert($data);
						}

					}
	
					return response(array('message'=>'Question added successfully.','reset'=>true,'script'=>true),200);

				}catch (\Exception $e){
			
					return response(array("message" => $e->getMessage()),403); 
				
				}
			}
			
			return response(array('message'=>'Data not found.'),403);
		}
		 $currentDateTime = now()->format('Y-m-d H:i');
		$result=[];
		$courses = CourseMaster::where('status','1')->orderBy('id','DESC')->get();
        $subjects = DB::table('courses')->where('status','1')->orderBy('id','DESC')->get();
  $mockups = DB::table('mockups')
    ->join('course_masters', 'mockups.course_master_id', '=', 'course_masters.id')
    ->where('mockups.status', 'Active')
    ->whereRaw("STR_TO_DATE(CONCAT(mockups.end_date, ' ', mockups.end_time), '%Y-%m-%d %H:%i') > ?", [$currentDateTime])
    ->orderBy('mockups.id', 'DESC')
    ->select('mockups.*', 'course_masters.name as course_master_name')
    ->get();
        return view('admin.mockup_question.addSubjectQuestions',compact('result','subjects','mockups','courses'));
    }

	public function selectedYearQuestion(Request $request){

		$id = $request->get('id');
		$mock_id = $request->get('mock_id');
		
		$result = DB::table('bulk_questions')->where('year_id',$id)->get();	
		$html = View::make('admin.mockup_question.loadQuestion', compact('result','mock_id'))->render();

        // Return JSON response with the HTML
        return response()->json(['html' => $html]);
	}


	public function selectedSubjectQuestion(Request $request) {
		$id = $request->get('id');
		$mock_id = $request->get('mock_id');
		$course_master_id = $request->get('course_master_id');
		$chapter_id = $request->get('chapter_id');
		$topic_id = $request->get('topic_id');
	
		// Build the query
		$query = DB::table('questions')->where('course_id', $id);
	
		// Apply course_master_id filter if provided
		if (!empty($course_master_id)) {
			$query->where('course_master_id', $course_master_id);
		}
		if (!empty($chapter_id)) {
			$query->where('chapter_id', $chapter_id);
		}
		if (!empty($topic_id)) {
			$query->where('topic_id', $topic_id);
		}
	
	
		$result = $query->get(); 
	
		$html = View::make('admin.mockup_question.loadQuestionSubject', compact('result', 'mock_id'))->render();
	
		// Return JSON response with the HTML
		return response()->json(['html' => $html]);
	}
	


	public function mockupList(){
		
		//$result=Mockup::orderBy('id','DESC')->where('status','Active')->get();

		$permission = session('permission') ?? [];
		$permission = isset($permission['59']) ? $permission['59'] : null;
		$courses = CourseMaster::where('status','1')->orderBy('id','DESC')->get();
		
		return view('admin.mockup_question.list',compact('permission','courses'));
	}

	public function mockupListData(Request $request) {
		if ($request->ajax()) {
			
			$query = Mockup::orderBy('id', 'DESC')->where('status', 'Active');
	
			// Apply course_master_id filter if provided
			if ($request->has('course_master_id') && !empty($request->course_master_id)) {
				$query->where('course_master_id', $request->course_master_id);
			}
	
			$result = $query->get();
	
			return Datatables::of($result)
				->addIndexColumn()
				->addColumn('total_question', function($row) {
					return DB::table('mock_questions')->where('mock_id', $row->id)->count();
				})
				->addColumn('is_live', function ($row) {
					return '<td class="center">
								<div class="switch mt-3">
									<label>
										<input type="checkbox" class="-live" data-id="' . $row->id . '"' . ($row->is_live == '1' ? 'checked' : '') . '>
										<span class="lever switch-col-red layout-switch"></span>
									</label>
								</div>
							</td>';
				})
				->addColumn('action', function($row) {
					$viewUrl = url('admin/mockupquestion/view-mock-question/' . $row->id);
					return '<a href="' . $viewUrl . '" title="View Questions" target="_blank" class="btn btn-success btn_change d-inline-flex justify-content-center align-items-center">
								<i class="fa fa-eye" style="margin: 1px; font-size:14px;"> View Questions</i>
							</a>';
				})
				->rawColumns(['is_live', 'action'])
				->make(true);
		} else {
			return view('admin.mockup_question.list');
		}
	}
	

	public function viewMockQuestion(Request $request,$mock_id){	

		$result = DB::table('mock_questions')->where('mock_id',$mock_id)->get();

		$mockName = DB::table('mockups')->where('id',$mock_id)->first();

		$permission = session('permission') ?? [];
		$permission = isset($permission['59']) ? $permission['59'] : null;
		

		return view('admin.mockup_question.view',compact('result','mockName', 'permission'));
	}
	
	public function changeLive(Request $request)
	{
		DB::table('mock_questions')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Question Live changed successfully.'], 200);
		
	}
	
	public function deleteMockup(Request $request,$id){
		DB::table('mock_questions')->where('id', $id)->delete();
		return redirect()->back()->with('5fernsadminsuccess', 'Question deleted successfully.');
	} 

	
}
