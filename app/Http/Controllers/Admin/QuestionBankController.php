<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Chapter;
use \App\Models\Question;
use App\Models\Course;
use App\Models\CourseMaster;
use App\Models\Mockup;
use \App\Models\TopicMaterials;
use Validator;
use DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;


class QuestionBankController extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules = [
				'id' => 'numeric|required',
			//	'course_master_id' => 'numeric|required',
				'name' => 'required|unique:chapters,name,' . $request->post('id') ,
			];
			$validator = Validator::make($request->all(), $rules);

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
					if ((int) $request->post('id') > 0) {

						$chapter = Chapter::find($request->post('id'));
					} else {

						$chapter = new Chapter();
					}

					// $chapter->course_master_id = $request->post('course_master_id');
					$chapter->course_id = $request->post('course_id');
					$chapter->name = $request->post('name');

					$chapter->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Chapter updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Chapter added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		$subjects = Course::where('status', '1')->orderBy('id', 'DESC')->get();

		return view('admin.question_bank.chapter.add', compact('result', 'courses', 'subjects'));
	}
	public function addQuestionExcel(Request $request)
	{

		if ($request->isMethod('post')) {

			// Check if file was uploaded
			if ($request->hasFile('excel_file')) {
				$file = $request->file('excel_file');

				// Check if the file is a valid Excel file
				if ($file->getClientOriginalExtension() == 'xls' || $file->getClientOriginalExtension() == 'xlsx') {
					// Load the file
					$data = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
					$sheet = $data->getActiveSheet();

					$insertArray = [];
					$finalData = [];
					// Iterate over rows and save data to database
					foreach ($sheet->getRowIterator() as $row) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false);
						// $rowData = $row->getCells();

						foreach ($cellIterator as $cell) {
							$value = $cell->getValue();
							// Do something with $value
							$rowData[] = $value;
						}

						$finalData[] = $rowData;
						$rowData = [];
					}


					unset($finalData[0]);
					if (!empty($finalData)) {
						foreach ($finalData as $insertData) {
							$insertArray = [
								'course_id' => '21',
								'chapter_id' => '8',
								'topic_id' => '20',
								'question' => $insertData[1],
								'option1' => $insertData[2],
								'option2' => $insertData[3],
								'option3' => $insertData[4],
								'option4' => $insertData[5],
								'right_option' => $insertData[6],
								'explaination' => $insertData[7],
								'exam_type' => 'Revision/Exam'
							];
							DB::table('questions')->insert($insertArray);
						}
					}



					return response(array('message' => 'File uploaded successfully.', 'reset' => false), 200);
				} else {
					return response(array('message' => 'Invalid file format.'), 403);
				}
			} else {
				return response(array('message' => 'No file upload.'), 403);
			}
		}

		$result = [];
		return view('admin.question_bank.question.excel', compact('result'));
	}

	public function addQuestionWord(Request $request)
	{

		if ($request->isMethod('post')) {

			// Check if file was uploaded
			if ($request->hasFile('word_file')) {
				$file = $request->file('word_file');

				// Check if the file is a valid Excel file
				if ($file->getClientOriginalExtension() == 'htm' || $file->getClientOriginalExtension() == 'html') {



					$wordFile = $request->file('word_file');
					$tempPath = $wordFile->storeAs('temp', $wordFile->getClientOriginalName());
					$htmlContent = file_get_contents(storage_path('app/' . $tempPath));

					// Initialize arrays to store questions
					$questions = [];
					$question = [
						'question' => '',
						'options' => [],
						'correct_answer' => '',
						'solution' => ''
					];

					// Flag to indicate if we are currently capturing question text
					$capturingQuestion = false;

					// Flag to indicate if we are currently capturing solution text
					$capturingSolution = false;

					// Parse HTML content
					$dom = new \DOMDocument();
					@$dom->loadHTML($htmlContent);

					// Iterate through each paragraph in the HTML document
					foreach ($dom->getElementsByTagName('p') as $paragraph) {
						$text = $paragraph->textContent;

						// Extract paragraph style
						$styleString = '';
						if ($paragraph->hasAttribute('style')) {
							$styleString = $paragraph->getAttribute('style');
						}

						$hasStrongTag = false;

						foreach ($paragraph->getElementsByTagName('strong') as $strongTag) {
							$hasStrongTag = true;
						}

						// Check if the paragraph contains a question statement
						if (strpos($text, '[Q]') !== false) {
							// Save the previous question if it exists
							if (!empty($question['question'])) {
								$questions[] = $question;
							}

							// Start a new question
							$question = [
								'question' => '',
								'options' => [],
								'correct_answer' => '',
								'solution' => ''
							];

							// Start capturing question text
							$capturingQuestion = true;

							// Reset capturing solution flag
							$capturingSolution = false;
						} elseif (strpos($text, '[ANS]') !== false) {
							// Extract the correct answer
							$question['correct_answer'] = substr($text, strpos($text, '[ANS]') + 5);
						} elseif (strpos($text, '[SOL]') !== false) {
							// Start capturing solution text
							$capturingSolution = true;

							// Stop capturing question text
							$capturingQuestion = false;
						} elseif (preg_match('/\([a-zA-Z]\)/', $text)) {
							// If an option tag is encountered, stop capturing question text
							$capturingQuestion = false;
							$text = str_replace(['(a)', '(b)', '(c)', '(d)'], '', $text);
							// Extract and store the option
							$question['options'][] = "<p style=\"{$styleString}\">" . $text;
						}

						// If capturing question text, append to question field
						if ($capturingQuestion) {
							// Remove tags like [Q]
							// $text = str_replace(['[Q]', '[ANS]', '[SOL]'], '', $text);
							$question['question'] .= "<p style=\"{$styleString}\">" . preg_replace('/^\d+\[Q\]/', '', $text);
						}

						// If capturing solution text, append to solution field
						if ($capturingSolution) {
							// Remove tags like [SOL]
							$text = str_replace(['[ANS]', '[SOL]'], '', $text);

							if (strpos($text, '·') !== false) {
								$text = "<ul><li>{$text}</li></ul>";
							}

							$text = str_replace('·', '', $text);

							if ($hasStrongTag) {
								$styleString = 'font-weight: bold';
							}

							$question['solution'] .= "<p style=\"{$styleString}\">" . $text;
						}
					}

					// Add the last question to the questions array
					if (!empty($question['question'])) {
						$questions[] = $question;
					}

					//echo '<pre>'; print_r($questions); die;

					$yearData = [
						'year' => $request->post('year'),
						'part' => $request->post('part'),
						'course_master_id' => $request->post('course_id')
					];

					$checkYarData = DB::table('bulk_question_years')->where($yearData)->first();

					if (!empty($checkYarData)) {
						$insertYear = $checkYarData->id;
					} else {
						$insertYear = DB::table('bulk_question_years')->insertGetId($yearData);
					}


					//echo '<pre>'; print_r($questions); die;
					// Process and save data in the database
					foreach ($questions as $questionData) {
						$question = "";
						$option1 = "";
						$option2 = "";
						$option3 = "";
						$option4 = "";
						$right_option = "";
						$explaination = "";


						if (isset($questionData['question']) && !empty($questionData['question'])) {
							$question = $questionData['question'];
						}

						if (isset($questionData['options'][0]) && !empty($questionData['options'][0])) {
							$option1 = $questionData['options'][0];
						}

						if (isset($questionData['options'][1]) && !empty($questionData['options'][1])) {
							$option2 = $questionData['options'][1];
						}

						if (isset($questionData['options'][2]) && !empty($questionData['options'][2])) {
							$option3 = $questionData['options'][2];
						}

						if (isset($questionData['options'][3]) && !empty($questionData['options'][3])) {
							$option4 = $questionData['options'][3];
						}

						if (isset($questionData['correct_answer']) && !empty($questionData['correct_answer'])) {
							$right_option = $questionData['correct_answer'];
						}

						if (isset($questionData['solution']) && !empty($questionData['solution'])) {
							$explaination = $questionData['solution'];
						}

						$bulkQuestionData = [
							'year_id' => $insertYear,
							'question' => $question,
							'option1' => $option1,
							'option2' => $option2,
							'option3' => $option3,
							'option4' => $option4,
							'right_option' => $right_option,
							'explaination' => $explaination,
						];

						DB::table('bulk_questions')->insert($bulkQuestionData);
					}

					// Cleanup: Delete the temporary file
					unlink(storage_path('app/' . $tempPath));

					return response(array('message' => 'File uploaded successfully.', 'reset' => false), 200);
				} else {
					return response(array('message' => 'Invalid file format.'), 403);
				}
			} else {
				return response(array('message' => 'No file upload.'), 403);
			}
		}

		$result = [];
		$courses = \App\Models\CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
		$subjects = \App\Models\Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.question_bank.bulkquestion.wordUpload', compact('result', 'courses', 'subjects'));
	}

	public function deleteChapter(Request $request, $id)
	{
		DB::table('chapters')->where('id', $id)->delete();
		DB::table('topic_materials')->where('batch_id', $id)->delete();
		DB::table('questions')->where('chapter_id', $id)->delete();
		return redirect()->back()->with('5fernsadminsuccess', 'Chapter deleted successfully.');
	}

	public function yearPartUpdate(Request $request, $id)
	{
		$result = DB::table('bulk_question_years')->where('id', $id)->first();
		$result = (array) $result;


		if ($result) {
			$courses = \App\Models\CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.question_bank.bulkquestion.yearEdit', compact('result', 'courses'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function updateYearSubmit(Request $request)
	{
		if ($request->isMethod('post')) {

			$id = $request->post('id');
			$yearData = [
				'year' => $request->post('year'),
				'part' => $request->post('part'),
				'course_id' => $request->post('course_id')
			];

			$insertYear = DB::table('bulk_question_years')->where('id', $id)->update($yearData);
			return response(array('message' => 'Year Upadated successfully.', 'reset' => false), 200);
		}
	}

	public function deleteYearQuestion(Request $request, $id)
	{

		$getYearQuestionId = DB::table('bulk_questions')->where('year_id', $id)->get();

		if (!empty($getYearQuestionId)) {
			$idArray = [];
			foreach ($getYearQuestionId as $questionId) {
				$idArray[] = $questionId->id;
			}

			DB::table('questions')->whereIn('question_id', $idArray)->delete();
		}

		DB::table('bulk_question_years')->where('id', $id)->delete();
		DB::table('bulk_questions')->where('year_id', $id)->delete();
		return redirect()->back()->with('5fernsadminsuccess', 'Year deleted successfully.');
	}

	public function addQuestionSingle(Request $request)
	{
		if ($request->isMethod('post')) {
			$rules = [
				'id' => 'numeric|required',
				'year' => 'required',
				'course_master_id' => 'required',
				'part' => 'required',
				'right_option' => 'required',
				'question_text' => 'required',
				'option1_text' => 'required',
				'option2_text' => 'required',
				'option3_text' => 'required',
				'option4_text' => 'required',
				'explaination' => 'required',
			];

			$validator = Validator::make($request->all(), $rules);

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

					$id = (int) $request->post('id');

					$yearData = [
						'year' => $request->post('year'),
						'part' => $request->post('part'),
						'course_master_id' => $request->post('course_master_id')
					];

					$checkYarData = DB::table('bulk_question_years')->where($yearData)->first();

					if (!empty($checkYarData)) {
						$insertYear = $checkYarData->id;
					} else {
						$insertYear = DB::table('bulk_question_years')->insertGetId($yearData);
					}

					$question['year_id'] = $insertYear;
					$question['right_option'] = $request->post('right_option');
					$question['question'] = $request->post('question_text');
					$question['option1'] = $request->post('option1_text');
					$question['option2'] = $request->post('option2_text');
					$question['option3'] = $request->post('option3_text');
					$question['option4'] = $request->post('option4_text');
					$question['explaination'] = $request->post('explaination');

					DB::table('bulk_questions')->insert($question);

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Question updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Question added successfully.', 'reset' => true, 'script' => true), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$courses = CourseMaster::orderBy('id', 'DESC')->where('status',1)->get();
		return view('admin.question_bank.bulkquestion.questionAdd', compact('result','courses'));
	}

	public function questionYearList()
	{
		//$result = DB::table('bulk_question_years')->where('status','1')->orderBy('id','DESC')->get();	
		// $result = DB::table('bulk_question_years')
		// ->select('bulk_question_years.*', DB::raw('(SELECT COUNT(*) FROM bulk_questions WHERE bulk_questions.year_id = bulk_question_years.id) as question_count'))
		// ->where('bulk_question_years.status', '1')
		// ->orderBy('bulk_question_years.id', 'DESC')
		// ->get();	
		$course = CourseMaster::orderBy('id', 'DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['50']) ? $permission['50'] : null;
		return view('admin.question_bank.bulkquestion.yearlist', compact('course','permission'));
	}

	public function questionYearListData(Request $request)
	{


		$permission = session('permission') ?? [];
		$permission = isset($permission['50']) ? $permission['50'] : null;

		if ($request->ajax()) {

			// $result = Mockup::orderBy('id', 'DESC');
			//  // Apply filters based on the request
			//  if ($request->has('course_id')) {
			// 	$result->where('course_id', $request->course_id);
			// }
			// $result = $result->get();

			$result = DB::table('bulk_question_years')
				->select('bulk_question_years.*', DB::raw('(SELECT COUNT(*) FROM bulk_questions WHERE bulk_questions.year_id = bulk_question_years.id) as question_count'))
				->where('bulk_question_years.status', '1')
				->orderBy('bulk_question_years.id', 'DESC');

			if ($request->post('course_id') > 0) {
				$result->where('course_master_id', $request->course_id);
			}
			$result = $result->get();

			return Datatables::of($result)

				->addIndexColumn()
				->addColumn('total_question', function ($row)  {

					return $row->question_count;
				})
				->addColumn('is_live', function ($row) {
					$is_live = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-live" data-id="' . $row->id . '"' . ($row->is_live == '1' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $is_live;
				})
->addColumn('is_lock', function ($row) {
					$is_lock = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-lock" data-id="' . $row->id . '"' . ($row->is_lock == '1' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $is_lock;
				})
				->addColumn('action', function ($row)  use ($permission) {

					$viewBtn = '';
					$editBtn = '';
					$deleteBtn = '';

					$viewUrl = url('admin/questionBank/questionBulkList/' . $row->id);
					$editUrl = url('admin/questionBank/yearPartUpdate/' . $row->id);
					$deleteUrl = url('admin/questionBank/deleteYearQuestion/' . $row->id);
					
					$viewBtn = '<a href="' . $viewUrl . '" title="View Questions" class="btn btn-tbl-edit btn_change"><i class="fa fa-eye"></i></a>';
					if(isset($permission) && $permission['edit'] == 'true'){
						$editBtn = '<a href="' . $editUrl . '" title="Edit Year" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';
					}
					
					if(isset($permission) && $permission['delete'] == 'true'){
						$deleteBtn = '<a href="' . $deleteUrl . '" title="Delete Year and Its Questions" onclick="return confirm(\'Are you sure? You want to delete this Year and all its Questions.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
					}

					return $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn;
				})
				->rawColumns(['action','is_live','is_lock'])
				->make(true);
		} else {

			return view('admin.question_bank.bulkquestion.yearlist');
		}
	}

	// public function questionList(Request $request, $id){
	// 	$result = DB::table('bulk_questions')->where('year_id',$id)->get();
	// 	$yearName = DB::table('bulk_question_years')->where('id',$id)->first();
	// 	return view('admin.question_bank.bulkquestion.view',compact('result','yearName'));
	// }

	public function questionList()
	{
		// $result = \App\Models\Course::withCount(['question_data as otherexam_count'=>function($query){
		// 	//$query->where('exam_type','Revision/Exam');
		// }])->withCount(['question_data as mockexam_count'=>function($query){
		// 	//$query->where('exam_type','Mock Exam');
		// }])->where('status','1')->orderBy('id','DESC')->get();
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		$subjects = Course::where('status', '1')->orderBy('id', 'DESC')->get();

		$permission = session('permission') ?? [];
		$permission = isset($permission['34']) ? $permission['34'] : null;

		return view('admin.question_bank.question.list',compact('permission','courses','subjects'));
	}

	public function questionListData(Request $request)
	{
		if ($request->ajax()) {
			$query = Course::join('course_map_masters', 'courses.id', '=', 'course_map_masters.course_id')
				->select('courses.*', 'course_map_masters.course_master_id')
				->selectRaw("
					(SELECT COUNT(*) FROM questions 
					 WHERE (questions.course_id = courses.id 
					 AND questions.course_master_id = course_map_masters.course_master_id) 
					) as otherexam_count
				")
				->where('courses.is_live', 1);
	
			// Apply filters
			if ($request->filled('course_master_id')) {
				$query->where('course_map_masters.course_master_id', $request->course_master_id);
			}
	
			if ($request->filled('course_id') && $request->course_id != 0) {
				$query->where('courses.id', $request->course_id);
			}
	
			$result = $query->orderBy('courses.id', 'DESC')->get();
	
			return Datatables::of($result)
				->addIndexColumn()
				->addColumn('coursemaster', function ($row) {
					return \App\Helpers\commonHelper::getCourseMasterName($row->course_master_id);
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
				->addColumn('action', function ($row) {
					$viewUrl = url('admin/question_bank/question/view/' . $row->id . '/revisionexam/'.$row->course_master_id);
					return '<a href="' . $viewUrl . '" title="View Questions" target="_blank" class="btn btn-success btn_change d-inline-flex justify-content-center align-items-center">
								<i class="fa fa-eye" style="margin: 1px; font-size:14px;"> View Questions</i>
							</a>';
				})
				->rawColumns(['question', 'action'])
				->make(true);
		} else {
			return view('admin.question_bank.question.list');
		}
	}
	
	

	public function chapterList()
	{
		$result = Chapter::with('course_details','course_master_details')->orderBy('id', 'DESC')->get();
		$permission = session('permission') ?? [];
				// @dd($permission);
		$permission = isset($permission['35']) ? $permission['35'] : null;

		return view('admin.question_bank.chapter.list', compact('result','permission'));
	}

	public function viewTopics(Request $request, $id)
	{
		$result = TopicMaterials::where('batch_id', $id)->get();
		return view('admin.question_bank.chapter.topics', compact('result'));
	}

	public function changeStatus(Request $request)
	{

		Chapter::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Chapter status changed successfully.'), 200);
	}

	public function updateChapter(Request $request, $id)
	{

		$result = Chapter::find($id);

		if ($result) {
			$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
			$subjects = Course::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.question_bank.chapter.add', compact('result', 'subjects', 'courses'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function addQuestion(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules = [
				'id' => 'numeric|required',
				'course_master_id' => 'numeric|required',
				'course_id' => 'numeric|required',
				'chapter_id' => 'numeric|required',
				'topic_id' => 'numeric|required',
				'selectedQuestion' => 'required',
			];


			$validator = Validator::make($request->all(), $rules);

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

					$course_master_id = $request->post('course_master_id');
					$course_id = $request->post('course_id');
					$chapter_id = $request->post('chapter_id');
					$topic_id = $request->post('topic_id');
					$questions = $request->post('selectedQuestion');
					$question_is_live = $request->post('question_is_live');

					foreach ($questions as $question_id) {
						$data = [
							'course_master_id' => $course_master_id,
							'course_id' => $course_id,
							'chapter_id' => $chapter_id,
							'topic_id' => $topic_id,
							'question_id' => $question_id,
							'is_live' => $question_is_live[$question_id],

						];
                        // dd($data);
						$checkData = [
							'course_master_id' => $course_master_id,
							'question_id' => $question_id,
						];

						$check = DB::table('questions')->where($checkData)->count();
// dd($check);
						if ($check == 0) {
							DB::table('questions')->insert($data);
						}
					}

					return response(array('message' => 'Question added successfully.', 'reset' => true, 'script' => true), 200);
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$previousYear = DB::table('bulk_question_years')->where('status', '1')->orderBy('id', 'DESC')->get();
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		$subjects = Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.question_bank.question.add', compact('result', 'previousYear', 'courses', 'subjects'));
	}

	public function selectedYearQuestion(Request $request)
	{

		$id = $request->get('id');
		$course_master_id = $request->get('course_master_id');
		$subject_id = $request->get('subject_id');
		$chapter_id = $request->get('chapter_id');
		$topic_id = $request->get('topic_id');

		$result = DB::table('bulk_questions')->where('year_id', $id)->get();
		$html = View::make('admin.question_bank.question.loadQuestion', compact('result', 'subject_id', 'chapter_id', 'topic_id','course_master_id'))->render();

		// Return JSON response with the HTML
		return response()->json(['html' => $html]);

		//return view('admin.question_bank.question.loadQuestion',compact('result'));
	}

	public function questionBulkList(Request $request, $id)
	{
		$permission = session('permission') ?? [];
		$permission = isset($permission['50']) ? $permission['50'] : null;
		$result = DB::table('bulk_questions')->where('status', '1')->where('year_id', $id)->get();
		$yearName = DB::table('bulk_question_years')->where('id', $id)->first();
	
		return view('admin.question_bank.bulkquestion.viewQuestion', compact('result', 'yearName','permission'));
	}

	public function deleteBulkQuestion(Request $request, $id)
	{
		DB::table('bulk_questions')->where('id', $id)->delete();
		return redirect()->back()->with('5fernsadminsuccess', 'Question deleted successfully.');
	}

	public function updateBulkQuestion(Request $request, $id)
	{

		$result = DB::table('bulk_questions')->where('id', $id)->first();
		$result = (array) $result;

		if ($result) {

			return view('admin.question_bank.bulkquestion.questionEdit', compact('result'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function updateBulkQuestionSubmit(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules = [
				'id' => 'numeric|required',
				'right_option' => 'required',
				'question_text' => 'required',
				'option1_text' => 'required',
				'option2_text' => 'required',
				'option3_text' => 'required',
				'option4_text' => 'required',
				'explaination' => 'required',
			];

			$validator = Validator::make($request->all(), $rules);

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

					$id = (int) $request->post('id');

					$question['right_option'] = $request->post('right_option');
					$question['question'] = $request->post('question_text');
					$question['option1'] = $request->post('option1_text');
					$question['option2'] = $request->post('option2_text');
					$question['option3'] = $request->post('option3_text');
					$question['option4'] = $request->post('option4_text');
					$question['explaination'] = $request->post('explaination');

					DB::table('bulk_questions')->where('id', $id)->update($question);

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Question updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Question added successfully.', 'reset' => true, 'script' => true), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		$subjects = Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.question_bank.question.add', compact('result', 'courses', 'subjects'));
	}

	public function viewQuestion(Request $request, $course_id, $type,$course_master_id)
	{

		// $result = Question::with('chapter_data')->where('course_id',$course_id)->get();

		$courseName = \App\Models\Course::where('id', $course_id)->first();
		$topics=TopicMaterials::where('course_id',$course_id)->get();

		return view('admin.question_bank.question.view', compact('course_id', 'type', 'courseName','course_master_id','topics'));
	}

	public function viewCourseQuestions(Request $request)
	{
		$permission = session('permission') ?? [];
		$permission = isset($permission['38']) ? $permission['38'] : null;
		if ($request->ajax()) {
		
			$course_id = $_GET['course_id'];
			$course_master_id = $_GET['course_master_id'];
			$topic_id = $request->input('topic'); // Get topic filter value

			$query = Question::with('chapter_data')
				->where('course_id', $course_id)
				->where('course_master_id', $course_master_id);

			if (!empty($topic_id)) {
				$query->where('topic_id', $topic_id); // Apply topic filter
			}

        $result = $query->get();
			return Datatables::of($result)
				->addIndexColumn()
				->addColumn('year_part', function ($row) {

					$yearName = "NA";
					$getQuestionData = DB::table('bulk_questions')->where('id', $row->question_id)->first();

					if (!empty($getQuestionData)) {
						$yearData = DB::table('bulk_question_years')->where('id', $getQuestionData->year_id)->first();
						if (!empty($yearData)) {
							$yearName = $yearData->year . ' / ' . $yearData->part;
						}
					}

					return $yearName;
				})
				->addColumn('chapter_name', function ($row) {

					return \App\Helpers\commonHelper::getChapterName($row->chapter_id) ?? "NA";
				})
				->addColumn('topic', function ($row) {

					return \App\Helpers\commonHelper::getTopicName($row->topic_id) ?? "NA";
				})
				->addColumn('question', function ($row) {

					return \App\Helpers\commonHelper::getQuestionName($row->question_id) ?? "NA";
				})
				->addColumn('is_live', function ($row) {
					$is_live = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-live" data-id="' . $row->id . '"' . ($row->is_live == '1' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $is_live;
				})
				->addColumn('action', function ($row) use ($permission) {

					$editUrl = url('admin/questionBank/updateBulkQuestion/' . $row->question_id);
					$deleteUrl = url('admin/question_bank/question/delete/' . $row->id);
						$editBtn = '';
					$deleteBtn = '';
            	if(isset($permission) && $permission['edit'] == 'true'){
					$editBtn = '<a href="' . $editUrl . '" title="Edit Question" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';
            	}
            	if(isset($permission) && $permission['delete'] == 'true'){
					$deleteBtn = '<a href="' . $deleteUrl . '" title="Delete Question" onclick="return confirm(\'Are you sure? You want to delete this Question.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
            	}
					return $editBtn . ' ' . $deleteBtn;
				})
				->rawColumns(['question', 'action','is_live'])
				->make(true);
		} else {
			return view('admin.mockup_question.list');
		}
	}

	public function viewMockQuestion(Request $request, $course_id, $type)
	{

		$result = Question::with('chapter_data')->where('course_id', $course_id)->where('exam_type', 'Mock Exam')->get();

		$courseName = \App\Models\Course::where('id', $course_id)->first();

		return view('admin.question_bank.question.view', compact('result', 'type', 'courseName'));
	}

	public function updateQuestion(Request $request, $id)
	{

		$result = Question::find($id);

		if ($result) {
			$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
			return view('admin.question_bank.question.add', compact('result', 'courses'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function deleteQuestion(Request $request, $id)
	{

		$result = Question::with('user_exam_data')->where('id', $id)->first();

		if ($result->user_exam_data) {

			return redirect()->back()->with('5fernsadminerror', 'Cannot delete. Associated Exam exists.');
		} else {

			Question::where('id', $id)->delete();
			return redirect()->back()->with('5fernsadminsuccess', 'Question deleted successfully.');
		}
	}
	public function changeRecall(Request $request)
	{

		Question::where('id', $request->post('id'))->update(['recall' => $request->post('recall')]);

		return response(array('message' => 'Question Recall changed successfully.'), 200);
	}

	public function getSubjectName(Request $request)
	{
		// Validate the request to ensure course_master_id is provided
		$request->validate([
			'course_master_id' => 'required|integer'
		]);
	
		// Fetch subjects (courses) linked to the given course_master_id via course_map_masters
		$subjectData = DB::table('course_map_masters')
			->join('courses', 'course_map_masters.course_id', '=', 'courses.id') // Join to get subject details
			->where('course_map_masters.course_master_id', $request->post('course_master_id')) // Filter by course_master_id
			->where('courses.status', '1') // Ensure active subjects
			->orderBy('courses.id', 'DESC') // Order by subject ID
			->get(['courses.id', 'courses.name']); // Fetch only required columns
	
		// Check if subjects exist
		if ($subjectData->isNotEmpty()) {
			return response()->json([
				'message' => 'Subject data fetched successfully.',
				'subjectData' => $subjectData
			], 200);
		} else {
			return response()->json([
				'message' => 'No subjects found for the given course master ID.',
			], 200);
		}
	}
	public function changeLivePreYearList(Request $request)
	{
		DB::table('bulk_question_years')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Question Year List Live changed successfully.'], 200);
		
	}

	public function changeLiveChapterList(Request $request)
	{
		DB::table('chapters')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Chapters Live changed successfully.'], 200);
		
	}

	public function changeLive(Request $request)
	{
		// Question::where('id', $request->post('id'))->update(['is_live' => $request->post('is_live')]);
		// return response(array('message' => 'Question live changed successfully.'), 200);
		DB::table('questions')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Question Live changed successfully.'], 200);

	}
	public function changeLivePrevYear(Request $request)
	{
		DB::table('bulk_questions')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Question Live Pre changed successfully.'], 200);

	}
	public function changeLockPrevYear(Request $request)
	{
		DB::table('bulk_question_years')
        ->where('id', $request->post('id'))
        ->update(['is_lock' => $request->post('is_lock')]);
		return response(['message' => 'Previous Year Question lock changed successfully.'], 200);

	}
		public function bulkUpdateStatus(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'status' => 'required|in:0,1'
		]);
		DB::table('bulk_question_years')->whereIn('id', $request->ids)->update(['is_live' => $request->status]);
		return response(['message' => 'Question Lock changed successfully.'], 200);
	}
		public function bulkUpdateQuestion(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'status' => 'required|in:0,1'
		]);
		DB::table('questions')->whereIn('id', $request->ids)->update(['is_live' => $request->status]);
		return response(['message' => 'Question Lock changed successfully.'], 200);
	}
    
    	public function bulkUpdatePYQQuestion(Request $request)
	{
		$request->validate([
			'ids' => 'required|array',
			'status' => 'required|in:0,1'
		]);
		DB::table('bulk_questions')->whereIn('id', $request->ids)->update(['is_live' => $request->status]);
		return response(['message' => 'Question Lock changed successfully.'], 200);
	}
}
