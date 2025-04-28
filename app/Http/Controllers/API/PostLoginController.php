<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;
use DB;
use Validator;
use App\Models\Event;
use Hash;
use Stripe;
use Carbon\Carbon;
use App\Models\Course;
use Illuminate\Support\Facades\Log;


class PostLoginController extends Controller
{


	public function bannerList(Request $request)
	{
		$banners = \App\Models\Banner::where('status', 'Active')->orderBy('id', 'DESC')->get();
		if ($banners->count() == 0) {
			return response(array("error" => true, "message" => "Banners not found. ", "result" => array()), 400);
		}
		$result = [];

		foreach ($banners as $banner) {

			$data = [
				"id" => $banner->id,
				"name" => ucfirst($banner->name),
				"redirect_to" => $banner->redirect_to,
				"image" => asset('uploads/banner/' . $banner->image),
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Banners fetched successfully.", "result" => $result], 200);
	}

	public function uncompletedQuestionBank(Request $request)
	{



		$user_id = $request->user()->id;
		$questions = \App\Models\Course::withCount(['question_data as otherexam_count'])
			->where('status', '1')->where('is_live', '1')
			// ->whereNotIn('id', function($query) use ($user_id) {
			// 	$query->select('subject_id')
			// 		->from('question_bank_result')
			// 		->whereColumn('question_bank_result.subject_id', 'courses.id')
			// 		->where('question_bank_result.user_id', $user_id);
			// })
			->orderBy('id', 'DESC')->having('otherexam_count', '>', 0)->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {

			$completed_questions = DB::table('question_bank_answers')->where(["user_id" => $user_id, "subject_id" => $question->id])->count();
			if ($question->otherexam_count - $completed_questions > 0) {
				$data = [
					"id" => $question->id,
					"name" => ucfirst($question->name),
					"description" => ucfirst($question->description),
					"image" => asset('uploads/course/' . $question->image),
					"total_questions" => $question->otherexam_count,
					"completed_questions" => $completed_questions,
					"left_questions" => $question->otherexam_count - $completed_questions,
				];

				$result[] = $data;
			}
		}

		if (count($result) == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	public function upcomingMockTests(Request $request)
	{
		$currentDateTime = now()->format('Y-m-d H:i');

		$questions = \App\Models\Mockup::whereRaw("CONCAT(start_date, ' ', start_time) > ?", [$currentDateTime])
			->where('status', 'Active')
			->orderBy('id', 'DESC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {

			$completed_questions = 0;
			$data = [
				"id" => $question->id,
				"name" => ucfirst($question->name),
				"duration" => $question->duration,
				"live_on" => date('d F', strtotime($question->start_date)) . '-' . date('h:i A', strtotime($question->start_time)),
				"image" => asset('uploads/banner/' . $question->image),
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	// 	public function questionBank(Request $request){
	// 	    if ((int) $request->course_master_id <= 0) {
	// 			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
	// 		}
	//  		$user_id = $request->user()->id;
	// 		$questions = \App\Models\Course::withCount(['question_data as otherexam_count'])->where('status','1')->where('is_live','1')
	// 		// ->whereNotIn('id', function($query) use ($user_id) {
	// 		// 		$query->select('subject_id')
	// 		// 			->from('question_bank_result')
	// 		// 			->whereColumn('question_bank_result.subject_id', 'courses.id')
	// 		// 			->where('question_bank_result.user_id', $user_id);
	// 		// 	})
	// 		->orderBy('id','DESC')->having('otherexam_count', '>', 0)->get();

	// 		if ($questions->count() == 0) {
	// 			return response(array("error"=>true,"message" => "No data found. ", "result"=>array()), 400);
	// 		}

	// 		$result = [];

	// 		foreach ($questions as $question) {

	// 			$completed_questions = $completed_questions = DB::table('question_bank_answers')->where(["user_id"=>$user_id,"subject_id"=>$question->id])->count();
	// 			$data = [
	// 				"id" => $question->id,
	// 				"name" => ucfirst($question->name),
	// 				"description" => ucfirst($question->description),
	// 				"image" => asset('uploads/course/' . $question->image),
	// 				"total_questions" => $question->otherexam_count,
	// 				"completed_questions" => $completed_questions,
	// 				"left_questions" => $question->otherexam_count-$completed_questions,
	// 			];

	// 			$result[]=$data;
	// 		}

	// 		return response(["error"=>false,"message" => "Data fetched successfully.", "result" => $result], 200);
	// 	}


	public function questionBank(Request $request)
	{
		$user_id = $request->user()->id;
		//$user_id = 4646;

		// 		if ((int) $request->course_master_id <= 0) {
		// 		    $course_master_id = 1;
		// 		}
		// 		else {
		// 		    $course_master_id = $request->course_master_id;
		// 		}

		if ($request->has('course_master_id')) {
			$course_master_id = $request->course_master_id;
		} else {
			$course_master_id = 1;
		}

		$questions = Course::join('course_map_masters', 'courses.id', '=', 'course_map_masters.course_id')
			->leftJoin('questions', function ($join) {
				$join->on('courses.id', '=', 'questions.course_id')
					->whereColumn('course_map_masters.course_master_id', '=', 'questions.course_master_id')
					->where('questions.is_live', 1); // Only count live questions
			})
			->select(
				'courses.*',
				'course_map_masters.course_master_id',
				DB::raw('COUNT(questions.id) as otherexam_count') // Count only live questions
			)
			->where('courses.status', '1')
			->where('courses.is_live', '1')
			->where('course_map_masters.course_master_id', $course_master_id)
			->groupBy('courses.id', 'course_map_masters.course_master_id')
			->having('otherexam_count', '>', 0)
			->orderBy('courses.id', 'DESC')
			->get();


		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {

			$completed_questions  = DB::table('question_bank_answers')->where(["user_id" => $user_id, "subject_id" => $question->id, 'course_master_id' => $course_master_id])->count();
			$data = [
				"id" => $question->id,
				"name" => ucfirst($question->name),
				"description" => ucfirst($question->description),
				"image" => asset('uploads/course/' . $question->image),
				"total_questions" => $question->otherexam_count,
				"completed_questions" => $completed_questions,
				"left_questions" => $question->otherexam_count - $completed_questions,
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}
	public function chapterQuestionBank(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$course_master_id = $request->post('course_master_id'); // Nullable

		$subject_data = \App\Models\Course::where('id', $subject_id)->first();

		// If course_master_id is provided, filter based on it, else fetch all active chapters
		if (!empty($course_master_id)) {
			$chapter_data = \App\Models\Chapter::whereIn('id', function ($query) use ($subject_id, $course_master_id) {
				$query->select('chapter_id')
					->from('questions')
					->where('course_id', $subject_id)
					->where('course_master_id', $course_master_id)
					->where('is_live', '1')
					->distinct();
			})
				->where('course_id', $subject_id)
				->where('status', 'Active')
				->where('is_live', '1')
				->orderBy('name', 'ASC')
				->get();
		} else {
			$chapter_data = \App\Models\Chapter::where('course_id', $subject_id)
				->where('status', 'Active')
				->where('is_live', '1')
				->orderBy('name', 'ASC')
				->get();
		}

		$result = [];
		$i = 1;
		$z = 1;

		if (!empty($chapter_data)) {
			foreach ($chapter_data as $chapter) {
				// Fetch topics only for the filtered chapters
				$topic_data = \App\Models\TopicMaterials::where('course_id', $subject_id)
					->where('batch_id', $chapter->id)
					->where('status', '1')
					->where('is_live', '1')
					->orderBy('id', 'DESC')
					->get();

				$topicResult = [];
				if (!empty($topic_data)) {
					foreach ($topic_data as $topic) {
						$questionQuery = \App\Models\Question::where('course_id', $subject_id)
							->where('chapter_id', $chapter->id)
							->where('topic_id', $topic->id)
							->where('is_live', '1');

						// Apply course_master_id filter only if provided
						if (!empty($course_master_id)) {
							$questionQuery->where('course_master_id', $course_master_id);
						}

						$question_count = $questionQuery->count();

						// Fetch attempted questions for the user
						$attemptedQuery = DB::table('question_bank_answers')
							->where('subject_id', $subject_id)
							->where('chapter_id', $chapter->id)
							->where('topic_id', $topic->id)
							->where('user_id', $user_id);

						if (!empty($course_master_id)) {
							$attemptedQuery->where('course_master_id', $course_master_id);
						}

						$attempted_question = $attemptedQuery->count();

						// Determine pause status
						$pause_status = "start";
						if ($attempted_question == 0) {
							$pause_status = "start";
						} else if ($attempted_question > 0 && $attempted_question < $question_count) {
							$pause_status = "resume";
						} else if ($attempted_question == $question_count) {
							$pause_status = "complete";
						}

						// Prepare topic result
						$tResult = [
							'topic_id' => $topic->id,
							'topic_sequence' => sprintf('%02d', $z++),
							'topic_name' => ucfirst($topic->topic),
							'is_lock' => $topic->is_lock,
							'questions' => $question_count,
							'attempted_question' => $attempted_question,
							'unattempted_question' => $question_count - $attempted_question,
							'pause_status' => $pause_status
						];

						$topicResult[] = $tResult;
					}
				}

				// Prepare chapter result
				$data = [
					"chapter_id" => $chapter->id,
					"chapter_name" => 'Chapter ' . sprintf('%02d', $i) . ' - ' . ucfirst($chapter->name),
					"topic_data" => $topicResult
				];

				$result[] = $data;
				$i++;
			}
		}

		return response([
			"error" => false,
			"message" => "Data fetched successfully.",
			"subject_id" => $subject_data->id,
			"subject_name" => $subject_data->name,
			"result" => $result
		], 200);
	}
	public function questionBankSearch(Request $request)
	{
		// dd('ss');
		$title = $request->post('title');
		$topic_data = \App\Models\TopicMaterials::where('status', '1')
			->where('is_live', '1')
			->where(function ($query) use ($title, $request) {
				$query->where('topic', 'like', '%' . $title . '%')
					->orWhereExists(function ($subquery) use ($title) {
						$subquery->select(\DB::raw(1))
							->from('chapters')
							->whereRaw('chapters.id = topic_materials.batch_id')
							->where('chapters.name', 'like', '%' . $title . '%');
					});
			})
			->whereExists(function ($query) {
				$query->select(\DB::raw(1))
					->from('questions')
					->whereRaw('questions.topic_id = topic_materials.id');
			})
			->orderBy('id', 'DESC');

		if ($request->post('subject_id')) {
			$topic_data->where('topic_materials.course_id', $request->post('subject_id'));
		}
		$topic_data = $topic_data->get();

		if ($topic_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$topicResult = [];
		if (!empty($topic_data)) {
			foreach ($topic_data as $topic) {

				$chapterData = DB::table('chapters')->where('id', $topic->batch_id)->first();

				$total_questions = DB::table('questions')->where([
					'course_id' => $topic->course_id,
					'chapter_id' => $topic->batch_id,
					'topic_id' => $topic->id,
					'questions.is_live' => 1
				])->count();
				$tResult = [
					'subject_id' => $topic->course_id,
					'chapter_id' => $topic->batch_id,
					'topic_id' => $topic->id,
					'is_lock' => $topic->is_lock,
					'topic_name' => ucfirst($chapterData->name) . ' - ' . ucfirst($topic->topic),
					'questions' => $total_questions
				];

				$topicResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $topicResult], 200);
	}

	public function questionBankSearchSubject(Request $request)
	{
		$title = $request->post('title');
		$subject_data = \App\Models\Course::where('name', 'like', '%' . $title . '%')->where('status', '1')->where('is_live', '1')->orderBy('id', 'DESC')->get();


		$subject_data = $questions = \App\Models\Course::withCount(['question_data as otherexam_count'])->where('name', 'like', '%' . $title . '%')->where('status', '1')->where('is_live', '1')->orderBy('id', 'DESC')->having('otherexam_count', '>', 0)->get();

		//echo '<pre>'; print_r($subject_data->toArray()); die;

		if ($subject_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$subjectResult = [];
		if (!empty($subject_data)) {
			foreach ($subject_data as $subject) {

				$tResult = [
					'subject_id' => $subject->id,
					'name' => ucfirst($subject->name),
					'questions' => $subject->otherexam_count
				];

				$subjectResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $subjectResult], 200);
	}

	public function chapterQuestions(Request $request)
	{

		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');
		$course_master_id = $request->post('course_master_id', 1);

		$subject_data = \App\Models\Course::select('name')->where('id', $subject_id)->first();
		$topic_data = \App\Models\TopicMaterials::select('topic')->where('id', $topic_id)->first();

		$questions = \App\Models\Question::where([
			'questions.course_id' => $subject_id,
			'questions.chapter_id' => $chapter_id,
			'questions.topic_id' => $topic_id,
			'questions.is_live' => 1,
			'questions.course_master_id' => $course_master_id
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('question_bank_answers', function ($join) use ($user_id) {
				$join->on('questions.id', '=', 'question_bank_answers.question_id')
					->where('question_bank_answers.user_id', '=', $user_id);
			})
			->select('questions.*', 'bulk_questions.question', 'question_bank_answers.id as answer_id')
			->orderBy('questions.id', 'ASC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$checkAnswers = DB::table('question_bank_answers')->where(['user_id' => $user_id, 'subject_id' => $subject_id, 'chapter_id' => $chapter_id, 'topic_id' => $topic_id])->orderBy('modified_at', 'DESC')->first();

		$result = [];
		$i = 1;
		$attempted = 0;
		$qIndex = 0;
		$question_index = 0;
		foreach ($questions as $question) {
			$attempt_status = "Unattempted";
			if (!empty($question->answer_id)) {
				$attempt_status = "Attempted";
				$attempted++;
			}

			if (!empty($checkAnswers) && $checkAnswers->question_id == $question->id) {
				$question_index = $qIndex;
			}

			$data = [
				"id" => $question->id,
				"sno" => "Q." . $i,
				"question" => $question->question,
				"attempt_status" => $attempt_status,
				"subject_name" => ucfirst($subject_data->name)

			];

			$result[] = $data;
			$i++;
			$qIndex++;
		}

		$total_questions = count($questions);
		$unattempted_questions = $total_questions - $attempted;

		$pause_status = "start";
		if ($attempted == 0) {
			$pause_status = "start";
		} else if ($attempted > 0 && $attempted < $total_questions) {
			$pause_status = "resume";
		} else if ($attempted == $total_questions) {
			$pause_status = "complete";
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "subject_id" => $subject_id, "chapter_id" => $chapter_id, "topic_id" => $topic_id, "topic_name" => ucfirst($topic_data->topic), "total_questions" => $total_questions, "attempted" => $attempted, "unattempted_questions" => $unattempted_questions, "pause_status" => $pause_status, "question_index" => $question_index, "result" => $result], 200);
	}

	public function chapterQuestionsDelete(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');

		DB::table('question_bank_answers')->where([
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id
		])->delete();


		return response(["error" => false, "message" => "Data Deleted successfully."], 200);
	}

	public function getRightOption($option)
	{
		if (strstr($option, 'a')) {
			$right_option = "a";
		} else if (strstr($option, 'b')) {
			$right_option = "b";
		} else if (strstr($option, 'c')) {
			$right_option = "c";
		} else if (strstr($option, 'd')) {
			$right_option = "d";
		} else {
			$right_option = $option;
		}

		return $right_option;
	}

	public function questionBankQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');

		$questions = \App\Models\Question::where([
			'course_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'questions.id as question_id')
			->get();

		$attempted_question = DB::table('question_bank_answers')->where([
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id
		])->count();

		// if($attempted_question>=$questions->count()){
		// 	DB::table('question_bank_answers')->where(['user_id'=>$user_id,
		// 	'subject_id' => $subject_id,
		// 	'chapter_id' => $chapter_id,
		// 	'topic_id' => $topic_id
		// 	])->delete();
		// }

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {

			$user_answer = "";
			$answer_status = "";
			$rightPercentage = "";
			$wrongPercentage = "";
			$userAnsData = DB::table('question_bank_answers')->where([
				'user_id' => $user_id,
				'subject_id' => $subject_id,
				'chapter_id' => $chapter_id,
				'topic_id' => $topic_id,
				'question_id' => $question->question_id
			])->first();


			if (!empty($userAnsData)) {
				$user_answer = $userAnsData->user_answer;
				$answer_status = (int) $userAnsData->answer_status;

				$counts = DB::table('question_bank_answers')
					->where([
						'question_id' => $question->question_id,
						'subject_id' => $subject_id,
						'chapter_id' => $chapter_id,
						'topic_id' => $topic_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$user_answer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$user_answer]);

				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);
			}

			$yearName = "NA";
			$bookmarkStatus = 0;
			$yearData = DB::table('bulk_question_years')->where('id', $question->year_id)->first();

			if (!empty($yearData)) {
				$yearName = $yearData->year;
			}

			$checkBookmark = DB::table('question_bank_bookmarks')->where(
				[
					'user_id' => $user_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'topic_id' => $topic_id,
					'question_id' => $question->question_id
				]
			)->count();

			if ($checkBookmark > 0) {
				$bookmarkStatus = 1;
			}

			$data = [
				"id" => $question->id,
				"sno" => $i,
				"years" => $yearName,
				"question_id" => $question->question_id,
				"question" => $question->question,
				"option1" => $question->option1,
				"option2" => $question->option2,
				"option3" => $question->option3,
				"option4" => $question->option4,
				"right_option" => $this->getRightOption($question->right_option),
				"explaination" => $question->explaination,
				"bookmark_status" => $bookmarkStatus,
				"user_answer" => $user_answer,
				"answer_status" => $answer_status,
				"rightPercentage" => $rightPercentage,
				"wrongPercentage" => $wrongPercentage
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "subject_id" => $subject_id, "chapter_id" => $chapter_id, "topic_id" => $topic_id, "total_questions" => count($questions), "result" => $result], 200);
	}


	public function questionBankQuestionsResume(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');

		$questions = \App\Models\Question::where([
			'course_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'questions.id as question_id')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {

			$checkAnswerStatus = DB::table('question_bank_answers')->where(
				[
					'user_id' => $user_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'topic_id' => $topic_id,
					'question_id' => $question->question_id
				]
			)->count();

			if ($checkAnswerStatus == 0) {

				$yearName = "NA";
				$bookmarkStatus = 0;
				$yearData = DB::table('bulk_question_years')->where('id', $question->year_id)->first();

				if (!empty($yearData)) {
					$yearName = $yearData->year;
				}

				$checkBookmark = DB::table('question_bank_bookmarks')->where(
					[
						'user_id' => $user_id,
						'subject_id' => $subject_id,
						'chapter_id' => $chapter_id,
						'topic_id' => $topic_id,
						'question_id' => $question->question_id
					]
				)->count();

				if ($checkBookmark > 0) {
					$bookmarkStatus = 1;
				}

				$data = [
					"id" => $question->id,
					"sno" => $i,
					"years" => $yearName,
					"question_id" => $question->question_id,
					"question" => $question->question,
					"option1" => $question->option1,
					"option2" => $question->option2,
					"option3" => $question->option3,
					"option4" => $question->option4,
					"right_option" => $this->getRightOption($question->right_option),
					"explaination" => $question->explaination,
					"bookmark_status" => $bookmarkStatus
				];

				$result[] = $data;
			}
			$i++;
		}

		if (count($result) == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "subject_id" => $subject_id, "chapter_id" => $chapter_id, "topic_id" => $topic_id, "total_questions" => count($questions), "result" => $result], 200);
	}

	public function questionBankAnswers(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');
		$course_master_id = $request->post('course_master_id');
		$user_answer = $request->post('user_answer');


		$questionData = \App\Models\Question::where([
			'questions.id' => $question_id,
			'course_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->select('questions.*', 'bulk_questions.id as bulk_id', 'bulk_questions.right_option', 'bulk_questions.explaination')
			->first();

		if (strstr($questionData->right_option, $user_answer)) {
			$ans_status = "1"; //correct
		} else if ($user_answer == 'm') {
			$ans_status = "3"; //Missed
		} else {
			$ans_status = "2"; //incorrect
		}

		$checkData = [
			"user_id" => $user_id,
			"question_id" => $question_id
		];

		$checkDataQuestion = DB::table('question_bank_answers')->where($checkData)->first();


		$ansData = [
			"user_id" => $user_id,
			"bulk_table_id" => $questionData->bulk_id,
			"question_id" => $question_id,
			"subject_id" => (int) $subject_id,
			"chapter_id" => (int) $chapter_id,
			"topic_id" => (int) $topic_id,
			"course_master_id" => (int) $course_master_id,
			"user_answer" => $user_answer,
			"right_option" => $this->getRightOption($questionData->right_option),
			"answer_status" => (int) $ans_status,
			"modified_at" => date('Y-m-d H:i:s')
		];

		if (!empty($checkDataQuestion)) {
			DB::table('question_bank_answers')->where('id', $checkDataQuestion->id)->update($ansData);
		} else {
			DB::table('question_bank_answers')->insert($ansData);
		}



		$counts = DB::table('question_bank_answers')
			->where([
				'question_id' => $question_id,
				'subject_id' => $subject_id,
				'chapter_id' => $chapter_id,
				'topic_id' => $topic_id
			])
			->select(
				DB::raw('COUNT(*) as totalAnswerd'),
				DB::raw("SUM(CASE WHEN user_answer = '$user_answer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
				DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
			)
			->get([$user_answer]);

		$totalAnswerd = $counts->pluck('totalAnswerd')->first();
		$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
		$rightAnswerd = $counts->pluck('rightAnswerd')->first();

		if ($totalAnswerd == 0) {
			$totalAnswerd = 1;
		}

		$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
		$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);

		return response(["error" => false, "message" => "Answer submited successfully.", "explaination" => $questionData->explaination, "rightPercentage" => $rightPercentage, "wrongPercentage" => $wrongPercentage, "result" => $ansData], 200);
	}

	public function questionBankPause(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');

		$topic_data = \App\Models\TopicMaterials::select('topic')->where('id', $topic_id)->first();

		$total_questions = \App\Models\Question::where([
			'course_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])->get();

		$answered_questions = DB::table('question_bank_answers')
			->where([
				'user_id' => $user_id,
				'subject_id' => $subject_id,
				'chapter_id' => $chapter_id,
				'topic_id' => $topic_id
			])
			->select(DB::raw('COUNT(*) as attempted_questions'))
			->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as correct_questions')
			->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as incorrect_questions')
			->selectRaw('SUM(CASE WHEN answer_status = 3 THEN 1 ELSE 0 END) as missed_questions')
			->first();

		//echo $questionData; die;

		if (count($total_questions) == 0) {
			$correct_percentage = 0;
		} else {
			$correct_percentage = round((int) $answered_questions->correct_questions / count($total_questions) * 100);
		}

		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$checkStatus = DB::table('question_bank_answers')->where([
					'user_id' => $user_id,
					'subject_id' => $subject_id,
					'chapter_id' => $chapter_id,
					'topic_id' => $topic_id,
					'question_id' => $que->id,
					'bulk_table_id' => $que->question_id,
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
				}

				$userAnswer = [
					"serial_number" => sprintf('%02d', $i),
					"answer_status" => (int) $ans_status
				];

				$result[] = $userAnswer;

				$i++;
			}
		}

		return response(
			[
				"error" => false,
				"message" => "Result Fetch successfully.",
				"subject_id" => (int) $subject_id,
				"chapter_id" => (int) $chapter_id,
				"topic_id" => (int) $topic_id,
				"topic_name" => $topic_data->topic,
				"total_questions" => count($total_questions),
				"attempted_questions" => $answered_questions->attempted_questions,
				"correct_questions" => (int) $answered_questions->correct_questions,
				"incorrect_questions" => (int) $answered_questions->incorrect_questions,
				"missed_questions" => (int) $answered_questions->missed_questions,
				"unattempted_questions" => (int) count($total_questions) - $answered_questions->attempted_questions,
				"correct_percentage" => $correct_percentage,
				"result" => $result
			],
			200
		);
	}

	public function questionBankQuit(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');
		$topic_name = $request->post('topic_name');
		$total_questions = $request->post('total_questions');
		$attempted_questions = $request->post('attempted_questions');
		$correct_questions = $request->post('correct_questions');
		$incorrect_questions = $request->post('incorrect_questions');
		$missed_questions = $request->post('missed_questions');
		$unattempted_questions = $request->post('unattempted_questions');
		$correct_percentage = $request->post('correct_percentage');

		$check = [
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id
		];

		$checkStatus = DB::table('question_bank_result')->where($check)->count();

		$data = [
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'topic_name' => $topic_name,
			'total_questions' => $total_questions,
			'attempted_questions' => $attempted_questions,
			'correct_questions' => $correct_questions,
			'incorrect_questions' => $incorrect_questions,
			'missed_questions' => $missed_questions,
			'unattempted_questions' => $unattempted_questions,
			'correct_percentage' => $correct_percentage,
		];

		if ($checkStatus == 0) {
			DB::table('question_bank_result')->insert($data);
		} else {
			DB::table('question_bank_result')->where($check)->update($data);
		}

		return response(["error" => false, "message" => "Question Bank submited successfully."], 200);
	}


	public function questionBankBookmark(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$subject_id = $request->post('subject_id');
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');

		$questionData = \App\Models\Question::where([
			'questions.id' => $question_id,
			'course_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->select('questions.*', 'bulk_questions.id as bulk_id', 'bulk_questions.right_option', 'bulk_questions.explaination')
			->first();


		$user_selected_ans = "";
		$check = [
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'question_id' => $question_id
		];

		$checkStatus = DB::table('question_bank_answers')->where($check)->first();
		if (!empty($checkStatus) && !empty($checkStatus->user_answer)) {
			$user_selected_ans = $checkStatus->user_answer;
		}



		$checkData = [
			"user_id" => $user_id,
			"question_id" => $question_id
		];

		$checkDataQuestion = DB::table('question_bank_bookmarks')->where($checkData)->first();

		$ansData = [
			"user_id" => $user_id,
			"bulk_table_id" => $questionData->bulk_id,
			"question_id" => $question_id,
			"subject_id" => (int) $subject_id,
			"chapter_id" => (int) $chapter_id,
			"topic_id" => (int) $topic_id,
			"user_selected_ans" => $user_selected_ans
		];

		if (!empty($checkDataQuestion)) {
			DB::table('question_bank_bookmarks')->where('id', $checkDataQuestion->id)->delete();
			$msg = "Question removed from bookmark successfuly";
		} else {
			DB::table('question_bank_bookmarks')->insert($ansData);
			$msg = "Question added to bookmark successfuly";
		}

		return response(["error" => false, "message" => $msg], 200);
	}

	public function previousYearTopics(Request $request)
	{
		$user_id = $request->user()->id;
		$yearData = DB::table('bulk_question_years')->where('status', '1')->where('is_live', '1')->orderBy('year', 'DESC')->groupBy('year')->get();

		$result = [];
		$i = 1;
		$z = 1;
		if (!empty($yearData)) {
			foreach ($yearData as $year) {

				$partData = DB::table('bulk_question_years')->where('status', '1')->where('is_live', '1')->where('year', $year->year)->get();

				$yearResult = [];
				if (!empty($partData)) {
					foreach ($partData as $part) {
						$question_count = DB::table('bulk_questions')->where('is_live', '1')->where('year_id', $part->id)->count();
						$attempted_question = DB::table('previous_year_answers')->where('part_id', $part->id)->where('user_id', $user_id)->count();

						$pause_status = "start";
						if ($attempted_question == 0) {
							$pause_status = "start";
						} else if ($attempted_question > 0 && $attempted_question < $question_count) {
							$pause_status = "resume";
						} else if ($attempted_question == $question_count) {
							$pause_status = "complete";
						}

						$yResult = [
							'part_id' => $part->id,
							'part_sequence' => sprintf('%02d', $z++),
							'part_name' => ucfirst($part->part),
							'questions' => $question_count,
							'attempted_question' => $attempted_question,
							'unattempted_question' => $question_count - $attempted_question,
							'pause_status' => $pause_status
						];

						$yearResult[] = $yResult;
					}
				}
				$data = [
					"year_id" => $year->id,
					"year_name" => sprintf('%02d', $i) . ' - ' . ucfirst($year->year),
					"year_data" => $yearResult
				];

				$result[] = $data;
				$i++;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function previousYearSearch(Request $request)
	{
		$title = $request->post('title');
		$topic_data = DB::table('bulk_question_years')
			->where(function ($query) use ($title) {
				$query->where('part', 'like', '%' . $title . '%')
					->orWhere('year', 'like', '%' . $title . '%');
			})
			->where('status', '1')->where('is_live', '1')
			->orderBy('id', 'DESC')
			->get();

		if ($topic_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$topicResult = [];
		if (!empty($topic_data)) {
			foreach ($topic_data as $topic) {
				$questions = DB::table('bulk_questions')->where('is_live', '1')->where('year_id', $topic->id)->count();
				$tResult = [
					'part_id' => $topic->id,
					'part_name' => ucfirst($topic->part) . " ( " . $topic->year . " )",
					'questions' => $questions
				];

				$topicResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $topicResult], 200);
	}


	public function previousYearPartQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$part_id = $request->post('part_id');

		$partData = DB::table('bulk_question_years')->where('id', $part_id)->first();

		$questions = DB::table('bulk_questions')->where([
			'bulk_questions.year_id' => $part_id,
			'bulk_questions.is_live' => 1
		])
			->leftJoin('previous_year_answers', function ($join) use ($user_id) {
				$join->on('bulk_questions.id', '=', 'previous_year_answers.question_id')
					->where('previous_year_answers.user_id', '=', $user_id);
			})
			->select('bulk_questions.*', 'previous_year_answers.id as answer_id')
			->orderBy('bulk_questions.id', 'ASC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		$attempted = 0;
		$qIndex = 0;
		$question_index = 0;
		$checkAnswers = DB::table('previous_year_answers')->where(['user_id' => $user_id, 'part_id' => $part_id])->orderBy('modified_at', 'DESC')->first();

		foreach ($questions as $question) {
			$attempt_status = "Unattempted";
			if (!empty($question->answer_id)) {
				$attempt_status = "Attempted";
				$attempted++;
			}

			if (!empty($checkAnswers) && $checkAnswers->question_id == $question->id) {
				$question_index = $qIndex;
			}

			$data = [
				"id" => $question->id,
				"sno" => "Q." . $i,
				"question" => $question->question,
				"attempt_status" => $attempt_status,
				"year" => $partData->year

			];

			$result[] = $data;
			$i++;
			$qIndex++;
		}

		$total_questions = count($questions);
		$unattempted_questions = $total_questions - $attempted;

		$pause_status = "start";
		if ($attempted == 0) {
			$pause_status = "start";
		} else if ($attempted > 0 && $attempted < $total_questions) {
			$pause_status = "resume";
		} else if ($attempted == $total_questions) {
			$pause_status = "complete";
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "part_id" => (int) $part_id, "part_name" => $partData->part, "total_questions" => $total_questions, "attempted" => $attempted, "unattempted_questions" => $unattempted_questions, "pause_status" => $pause_status, "question_index" => $question_index, "result" => $result], 200);
	}

	public function previousYearPartQuestionsDelete(Request $request)
	{
		$user_id = $request->user()->id;
		$part_id = $request->post('part_id');

		DB::table('previous_year_answers')->where('part_id', $part_id)->where('user_id', $user_id)->delete();

		return response(["error" => false, "message" => "Data Deleted successfully."], 200);
	}


	public function previousYearQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$part_id = $request->post('part_id');

		$questions = DB::table('bulk_questions')->where('is_live', '1')->where('year_id', $part_id)->get();
		$attempted_question = DB::table('previous_year_answers')->where('part_id', $part_id)->where('user_id', $user_id)->count();

		// if($attempted_question>=$questions->count()){
		// 	DB::table('previous_year_answers')->where('part_id',$part_id)->where('user_id',$user_id)->delete();
		// }

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {
			$yearName = "NA";
			$bookmarkStatus = 0;
			$yearData = DB::table('bulk_question_years')->where('is_live', '1')->where('id', $question->year_id)->first();

			if (!empty($yearData)) {
				$yearName = $yearData->year;
			}

			$checkBookmark = DB::table('previous_year_bookmarks')->where(['user_id' => $user_id, 'part_id' => $part_id, 'question_id' => $question->id])->count();
			if ($checkBookmark > 0) {
				$bookmarkStatus = 1;
			}

			$user_answer = "";
			$answer_status = "";
			$rightPercentage = "";
			$wrongPercentage = "";
			$userAnsData = DB::table('previous_year_answers')->where(['user_id' => $user_id, 'part_id' => $part_id, 'question_id' => $question->id])->first();
			if (!empty($userAnsData)) {
				$user_answer = $userAnsData->user_answer;
				$answer_status = (int) $userAnsData->answer_status;

				$counts = DB::table('previous_year_answers')
					->where([
						'question_id' => $question->id,
						'part_id' => $part_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$user_answer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$user_answer]);



				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);
			}

			$data = [
				"id" => $question->id,
				"sno" => $i,
				"years" => $yearName,
				"question" => $question->question,
				"option1" => $question->option1,
				"option2" => $question->option2,
				"option3" => $question->option3,
				"option4" => $question->option4,
				"right_option" => $this->getRightOption($question->right_option),
				"explaination" => $question->explaination,
				"bookmark_status" => $bookmarkStatus,
				"user_answer" => $user_answer,
				"answer_status" => $answer_status,
				"rightPercentage" => $rightPercentage,
				"wrongPercentage" => $wrongPercentage

			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "part_id" => (int) $part_id, "total_questions" => count($questions), "result" => $result], 200);
	}


	public function previousYearAnswers(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$part_id = $request->post('part_id');
		$user_answer = $request->post('user_answer');


		$questionData = DB::table('bulk_questions')->where('id', $question_id)->where('year_id', $part_id)->first();

		if (strstr($questionData->right_option, $user_answer)) {
			$ans_status = "1"; //correct
		} else if ($user_answer == 'm') {
			$ans_status = "3"; //Missed
		} else {
			$ans_status = "2"; //incorrect
		}

		$checkData = [
			"user_id" => $user_id,
			"question_id" => $question_id
		];

		$checkDataQuestion = DB::table('previous_year_answers')->where($checkData)->first();


		$ansData = [
			"user_id" => $user_id,
			"question_id" => (int) $question_id,
			"part_id" => (int) $part_id,
			"user_answer" => $user_answer,
			"right_option" => $this->getRightOption($questionData->right_option),
			"answer_status" => (int) $ans_status,
			"modified_at" => date('Y-m-d H:i:s')
		];

		if (!empty($checkDataQuestion)) {
			DB::table('previous_year_answers')->where('id', $checkDataQuestion->id)->update($ansData);
		} else {
			DB::table('previous_year_answers')->insert($ansData);
		}



		$counts = DB::table('previous_year_answers')
			->where([
				'question_id' => $question_id,
				'part_id' => $part_id
			])
			->select(
				DB::raw('COUNT(*) as totalAnswerd'),
				DB::raw("SUM(CASE WHEN user_answer = '$user_answer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
				DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
			)
			->get([$user_answer]);

		$totalAnswerd = $counts->pluck('totalAnswerd')->first();
		$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
		$rightAnswerd = $counts->pluck('rightAnswerd')->first();

		if ($totalAnswerd == 0) {
			$totalAnswerd = 1;
		}

		$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
		$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);

		return response(["error" => false, "message" => "Answer submited successfully.", "explaination" => $questionData->explaination, "rightPercentage" => $rightPercentage, "wrongPercentage" => $wrongPercentage, "part_id" => (int) $part_id, "result" => $ansData], 200);
	}

	public function previousYearPause(Request $request)
	{
		$user_id = $request->user()->id;
		$part_id = $request->post('part_id');

		$partData = DB::table('bulk_question_years')->where('id', $part_id)->first();

		$total_questions = DB::table('bulk_questions')->where('year_id', $part_id)->get();

		$answered_questions = DB::table('previous_year_answers')
			->where([
				'user_id' => $user_id,
				'part_id' => $part_id
			])
			->select(DB::raw('COUNT(*) as attempted_questions'))
			->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as correct_questions')
			->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as incorrect_questions')
			->selectRaw('SUM(CASE WHEN answer_status = 3 THEN 1 ELSE 0 END) as missed_questions')
			->first();

		//echo $questionData; die;

		if (count($total_questions) == 0) {
			$correct_percentage = 0;
		} else {
			$correct_percentage = round((int) $answered_questions->correct_questions / count($total_questions) * 100);
		}

		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$checkStatus = DB::table('previous_year_answers')->where([
					'user_id' => $user_id,
					'part_id' => $part_id,
					'question_id' => $que->id
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
				}

				$userAnswer = [
					"serial_number" => sprintf('%02d', $i),
					"answer_status" => (int) $ans_status
				];

				$result[] = $userAnswer;

				$i++;
			}
		}

		return response(
			[
				"error" => false,
				"message" => "Result Fetch successfully.",
				"part_id" => $part_id,
				"topic_name" => $partData->part,
				"total_questions" => count($total_questions),
				"attempted_questions" => $answered_questions->attempted_questions,
				"correct_questions" => (int) $answered_questions->correct_questions,
				"incorrect_questions" => (int) $answered_questions->incorrect_questions,
				"missed_questions" => (int) $answered_questions->missed_questions,
				"unattempted_questions" => (int) count($total_questions) - $answered_questions->attempted_questions,
				"correct_percentage" => $correct_percentage,
				"result" => $result
			],
			200
		);
	}

	public function previousYearQuit(Request $request)
	{
		$user_id = $request->user()->id;
		$part_id = $request->post('part_id');
		$topic_name = $request->post('topic_name');
		$total_questions = $request->post('total_questions');
		$attempted_questions = $request->post('attempted_questions');
		$correct_questions = $request->post('correct_questions');
		$incorrect_questions = $request->post('incorrect_questions');
		$missed_questions = $request->post('missed_questions');
		$unattempted_questions = $request->post('unattempted_questions');
		$correct_percentage = $request->post('correct_percentage');

		$check = [
			'user_id' => $user_id,
			'part_id' => $part_id
		];

		$checkStatus = DB::table('previous_year_result')->where($check)->count();

		$data = [
			'user_id' => $user_id,
			'part_id' => $part_id,
			'topic_name' => $topic_name,
			'total_questions' => $total_questions,
			'attempted_questions' => $attempted_questions,
			'correct_questions' => $correct_questions,
			'incorrect_questions' => $incorrect_questions,
			'missed_questions' => $missed_questions,
			'unattempted_questions' => $unattempted_questions,
			'correct_percentage' => $correct_percentage,
		];

		if ($checkStatus == 0) {
			DB::table('previous_year_result')->insert($data);
		} else {
			DB::table('previous_year_result')->where($check)->update($data);
		}

		return response(["error" => false, "message" => "Previous year exam submited successfully."], 200);
	}

	public function previousYearBookmark(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$part_id = $request->post('part_id');


		$user_selected_ans = "";
		$check = [
			'user_id' => $user_id,
			'part_id' => $part_id,
			'question_id' => $question_id
		];

		$checkStatus = DB::table('previous_year_answers')->where($check)->first();
		if (!empty($checkStatus) && !empty($checkStatus->user_answer)) {
			$user_selected_ans = $checkStatus->user_answer;
		}


		$checkData = [
			"user_id" => $user_id,
			"part_id" => $part_id,
			"question_id" => $question_id
		];

		$checkDataInsert = [
			"user_id" => $user_id,
			"part_id" => $part_id,
			"question_id" => $question_id,
			"user_selected_ans" => $user_selected_ans
		];

		$checkDataQuestion = DB::table('previous_year_bookmarks')->where($checkData)->first();

		if (!empty($checkDataQuestion)) {
			DB::table('previous_year_bookmarks')->where('id', $checkDataQuestion->id)->delete();
			$msg = "Question removed from bookmark successfuly";
		} else {
			DB::table('previous_year_bookmarks')->insert($checkDataInsert);
			$msg = "Question added to bookmark successfuly";
		}

		return response(["error" => false, "message" => $msg], 200);
	}

	public function mockTestUpcomming(Request $request)
	{
		$user_id = $request->user()->id;
		$currentDateTime = now()->format('Y-m-d H:i');

		$questions = \App\Models\Mockup::whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$currentDateTime])
			->where('status', 'Active')
			->whereNotExists(function ($query) use ($user_id) {
				$query->select(\DB::raw(1))
					->from('mockup_test_result')
					->whereRaw('mockup_test_result.mock_id = mockups.id')
					->where('mockup_test_result.user_id', $user_id);
			})
			->orderBy('start_date', 'ASC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {

			$active_status = 0;
			if (($question['start_date'] . ' ' . $question['start_time'] <= date('Y-m-d H:i')) && ($question['end_date'] . ' ' . $question['end_time'] >= date('Y-m-d H:i'))) {
				$active_status = 1;
			}

			$totalQuestions = DB::table('mock_questions')->where('mock_id', $question->id)->count();
			$data = [
				"id" => $question->id,
				"name" => ucfirst($question->name),
				"questions" => $totalQuestions,
				"live_on" => date('d F', strtotime($question->start_date)) . '-' . date('h:i A', strtotime($question->start_time)),
				"end_on" => date('d F', strtotime($question->end_date)) . '-' . date('h:i A', strtotime($question->end_time)),
				"image" => asset('uploads/banner/' . $question->image),
				"active_status" => $active_status
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	public function mockTestPast(Request $request)
	{
		$currentDateTime = now()->format('Y-m-d H:i');

		$questions = \App\Models\Mockup::whereRaw("CONCAT(start_date, ' ', start_time) < ?", [$currentDateTime])
			->where('status', 'Active')
			->orderBy('id', 'DESC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {
			$totalQuestions = DB::table('mock_questions')->where('mock_id', $question->id)->count();
			$data = [
				"id" => $question->id,
				"name" => ucfirst($question->name),
				"questions" => $totalQuestions,
				"live_on" => date('d F', strtotime($question->start_date)) . '-' . date('h:i A', strtotime($question->start_time)),
				"image" => asset('uploads/banner/' . $question->image),
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	public function mockTestSearch(Request $request)
	{
		$user_id = $request->user()->id;
		$title = $request->post('title');
		$currentDateTime = now()->format('Y-m-d H:i');

		$mock_data = \App\Models\Mockup::where('name', 'like', '%' . $title . '%')->whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$currentDateTime])
			->where('status', 'Active')
			->whereNotExists(function ($query) use ($user_id) {
				$query->select(\DB::raw(1))
					->from('mockup_test_result')
					->whereRaw('mockup_test_result.mock_id = mockups.id')
					->where('mockup_test_result.user_id', $user_id);
			})
			->orderBy('start_date', 'ASC')
			->get();


		//$mock_data = \App\Models\Mockup::where('name', 'like', '%' . $title . '%')->where('status','Active')->orderBy('id','DESC')->get();

		//echo '<pre>'; print_r($mock_data->toArray()); die;

		if ($mock_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$mockResult = [];
		if (!empty($mock_data)) {
			foreach ($mock_data as $mock) {

				$active_status = 0;
				if (($mock['start_date'] . ' ' . $mock['start_time'] <= date('Y-m-d H:i')) && ($mock['end_date'] . ' ' . $mock['end_time'] >= date('Y-m-d H:i'))) {
					$active_status = 1;
				}

				$tResult = [
					'mock_id' => $mock->id,
					'name' => ucfirst($mock->name),
					'active_status' => $active_status
				];

				$mockResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $mockResult], 200);
	}

	public function mockTestInstructions(Request $request)
	{
		$currentDateTime = now()->format('Y-m-d H:i');
		$mock_id = $request->post('mock_id');

		// Fetch mock data from mockups table
		$data = \App\Models\Mockup::where('id', $mock_id)->first();

		// Get the course_master_id from the mockups table
		$courseMasterId = $data->course_master_id ?? null;

		// Fetch positive and negative marks from course_masters table
		$positiveMark = 2.08;  // Default value
		$negativeMark = 0.69;  // Default value

		if ($courseMasterId) {
			$courseMasterData = DB::table('course_masters')->where('id', $courseMasterId)->first();
			if ($courseMasterData) {
				$positiveMark = (float) $courseMasterData->positive_mark;
				$negativeMark = (float) $courseMasterData->negative_mark;
			}
		}

		// Calculate total questions
		$totalQuestions = DB::table('mock_questions')->where('mock_id', $mock_id)->count();

		// Calculate total marks based on the total questions and positive marks
		$totalMarks = round($totalQuestions * $positiveMark, 2);

		return response([
			"error" => false,
			"message" => "Data fetched successfully.",
			"mock_id" => $mock_id,
			"duration" => $data->duration . ' Mins',
			"total_questions" => $totalQuestions . ' Questions',
			"total_marks" => $totalMarks . ' Marks',
			"correct_marks" => "+" . $positiveMark,
			"incorrect_marks" => "-" . $negativeMark
		], 200);
	}


	// public function mockTestInstructions(Request $request)
	// {
	// 	$currentDateTime = now()->format('Y-m-d H:i');
	// 	$mock_id = $request->post('mock_id');
	// 	$data = \App\Models\Mockup::where('id', $mock_id)->first();
	// 	$totalQuestions = DB::table('mock_questions')->where('mock_id', $mock_id)->count();

	// 	return response(array(
	// 		"error" => false,
	// 		"message" => "Data fatched successfuly.",
	// 		"mock_id" => $mock_id,
	// 		"duration" => $data->duration . ' Mins',
	// 		"total_questions" => $totalQuestions . ' Questions',
	// 		"total_marks" => round($totalQuestions * '2.08', '2') . ' Marks',
	// 		"correct_marks" => "+2.08",
	// 		"incorrect_marks" => "-0.69"
	// 	), 200);
	// }


	public function mockTestQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');
		$mockData = \App\Models\Mockup::where('id', $mock_id)->first();

		$duration = (int) $mockData->duration;

		$checkDuration = DB::table('mockup_test_duration')->where(["user_id" => $user_id, "mock_id" => $mock_id])->first();

		if (empty($checkDuration)) {

			$currentDateTime = Carbon::now();
			$newDateTime = $currentDateTime->addMinutes($duration);
			$endDateTime = $newDateTime->format('Y-m-d H:i:s');

			$durationData = [
				"user_id" => $user_id,
				"mock_id" => $mock_id,
				"start_date" => date('Y-m-d'),
				"start_time" => date('H:i:s'),
				"start_date_time" => date('Y-m-d H:i:s'),
				"end_date_time" => $endDateTime
			];

			DB::table('mockup_test_duration')->insert($durationData);
		} else {
			$startDateTime = Carbon::parse($checkDuration->start_date_time);
			$currentDateTime = Carbon::now();

			// Calculate the difference in minutes
			$timeDifferenceInMinutes = $startDateTime->diffInMinutes($currentDateTime);
			$duration = (int) $mockData->duration - $timeDifferenceInMinutes;
		}

		if ($duration <= 0) {
			$duration = 0;
		}

		$questions = DB::table('mock_questions')->where([
			'mock_id' => $mock_id
		])
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'mock_questions.id as question_id')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$question_index = 0;
		$lastAnsData = $userAnsData = DB::table('mockup_test_answers')->where(['user_id' => $user_id, 'mock_id' => $mock_id])->orderBy('modified_at', 'DESC')->first();
		if (!empty($lastAnsData)) {
			$question_index = $lastAnsData->question_index;
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {
			$user_answer = "";
			$userAnsData = DB::table('mockup_test_answers')->where(['user_id' => $user_id, 'mock_id' => $mock_id, 'question_id' => $question->question_id])->first();
			if (!empty($userAnsData)) {
				$user_answer = $userAnsData->user_answer;
			}

			$bookmarkStatus = 0;
			$bookmarkStatusData = DB::table('mockup_test_question_bookmarks')->where(['user_id' => $user_id, 'mock_id' => $mock_id, 'question_id' => $question->question_id])->first();

			if (!empty($bookmarkStatusData)) {
				$bookmarkStatus = 1;
			}

			$data = [
				"id" => $question->id,
				"sno" => $i,
				"question_id" => $question->question_id,
				"question" => $question->question,
				"option1" => $question->option1,
				"option2" => $question->option2,
				"option3" => $question->option3,
				"option4" => $question->option4,
				"user_answer" => $user_answer,
				"bookmark_status" => $bookmarkStatus
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "mock_id" => (int) $mock_id, "duration" => $duration, "total_questions" => count($questions), "question_index" => (int) $question_index, "result" => $result], 200);
	}

	public function deleteMockTesting(Request $request)
	{
		$user_id = $request->post('user_id');
		$mock_id = $request->post('mock_id');

		DB::table('mockup_test_duration')->where(["user_id" => $user_id, "mock_id" => $mock_id])->delete();
		DB::table('mockup_test_result')->where(["user_id" => $user_id, "mock_id" => $mock_id])->delete();
		DB::table('mockup_test_answers')->where(["user_id" => $user_id, "mock_id" => $mock_id])->delete();

		echo "success";
		die;
	}

	// public function mockTestSubmitAuto(Request $request)
	// {
	// 	$user_id = $request->user()->id;
	// 	$user_name = $request->user()->name;
	// 	$currentDateTime = Carbon::now();

	// 	//$mockDurationData = DB::table('mockup_test_duration')->where("user_id",$user_id)->where('end_date_time','<=',$currentDateTime)->get();

	// 	$mockDurationData = DB::table('mockup_test_duration as d')
	// 		->leftJoin('mockup_test_result as r', function ($join) use ($user_id) {
	// 			$join->on('d.user_id', '=', 'r.user_id')
	// 				->on('d.mock_id', '=', 'r.mock_id')
	// 				->where('r.user_id', $user_id); // Added condition for user_id
	// 		})
	// 		->where('d.user_id', $user_id)
	// 		->where('d.end_date_time', '<=', $currentDateTime)
	// 		->where('r.mock_id', null)
	// 		->select('d.*')
	// 		->get();

	// 	if (!empty($mockDurationData)) {
	// 		foreach ($mockDurationData as $mock) {
	// 			$mock_id = $mock->mock_id;
	// 			$mockData = DB::table('mockups')->where('id', $mock_id)->first();

	// 			if (!empty($mockData)) {
	// 				$average_time = $mockData->duration;

	// 				$answered_questions = DB::table('mockup_test_answers')
	// 					->where([
	// 						'user_id' => $user_id,
	// 						'mock_id' => $mock_id
	// 					])
	// 					->select(DB::raw('COUNT(*) as attempted_questions'))
	// 					->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
	// 					->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
	// 					->first();

	// 				$total_questions = DB::table('mock_questions')->where([
	// 					'mock_id' => $mock_id
	// 				])->count();


	// 				$ansData = [
	// 					"user_id" => $user_id,
	// 					"mock_id" => (int) $mock_id,
	// 					"user_name" => $user_name,
	// 					"total_questions" => $total_questions,
	// 					"attempted_questions" => $answered_questions->attempted_questions,
	// 					"right_answers" => $answered_questions->right_answers,
	// 					"wrong_answers" => $answered_questions->wrong_answers,
	// 					"average_time" => (int) $average_time,
	// 					"marks" => (string) round(($answered_questions->right_answers * 2.08) - ($answered_questions->wrong_answers * 0.69), '2')
	// 				];

	// 				$checkMock = DB::table('mockup_test_result')->where(['user_id' => $user_id, "mock_id" => $mock_id])->first();
	// 				if (!empty($checkMock)) {
	// 					DB::table('mockup_test_result')->where(['id' => $checkMock->id])->update($ansData);
	// 				} else {
	// 					DB::table('mockup_test_result')->insert($ansData);
	// 				}
	// 			}
	// 		}
	// 	}

	// 	return response(["error" => false, "message" => "Exam submitted successfully."], 200);
	// }


	public function mockTestSubmitAuto(Request $request)
	{
		$user_id = $request->user()->id;
		$user_name = $request->user()->name;
		$currentDateTime = Carbon::now();



		$mockDurationData = DB::table('mockup_test_duration as d')
			->leftJoin('mockup_test_result as r', function ($join) use ($user_id) {
				$join->on('d.user_id', '=', 'r.user_id')
					->on('d.mock_id', '=', 'r.mock_id')
					->where('r.user_id', $user_id);
			})
			->where('d.user_id', $user_id)
			->where('d.end_date_time', '<=', $currentDateTime)
			->whereNull('r.mock_id')
			->select('d.*')
			->get();

		if (!empty($mockDurationData)) {
			foreach ($mockDurationData as $mock) {
				$mock_id = (int) $mock->mock_id;
				$mockData = DB::table('mockups')->where('id', $mock_id)->first();

				if (!empty($mockData)) {
					$average_time = $mockData->duration;

					// Get course master ID from mockData
					$courseMasterId = $mockData->course_master_id ?? null;

					$positiveMark = 2.08; // default
					$negativeMark = 0.69; // default

					// Fetch positive and negative marks if course_master_id is present
					if (!empty($courseMasterId)) {
						$courseMasterData = DB::table('course_masters')->where('id', $courseMasterId)->first();
						if (!empty($courseMasterData)) {
							$positiveMark = (float) $courseMasterData->positive_mark;
							$negativeMark = (float) $courseMasterData->negative_mark;
						}
					}

					// dd($positiveMark);

					// Log mock_id, positive mark, and negative mark
					//	dd("Mock ID: {$mock_id} | Positive Mark: {$positiveMark} | Negative Mark: {$negativeMark}");
					// \Log::info('Response dari API:', ['response' => $response]);

					$answered_questions = DB::table('mockup_test_answers')
						->where([
							'user_id' => $user_id,
							'mock_id' => $mock_id
						])
						->select(DB::raw('COUNT(*) as attempted_questions'))
						->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
						->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
						->first();

					$total_questions = DB::table('mock_questions')->where([
						'mock_id' => $mock_id
					])->count();

					$marks = round(
						($answered_questions->right_answers * $positiveMark) -
							($answered_questions->wrong_answers * $negativeMark),
						2
					);

					$ansData = [
						"user_id" => $user_id,
						"mock_id" => $mock_id,
						"user_name" => $user_name,
						"total_questions" => $total_questions,
						"attempted_questions" => $answered_questions->attempted_questions,
						"right_answers" => $answered_questions->right_answers,
						"wrong_answers" => $answered_questions->wrong_answers,
						"average_time" => (int) $average_time,
						"marks" => (string) $marks,
					];

					$checkMock = DB::table('mockup_test_result')->where([
						'user_id' => $user_id,
						"mock_id" => $mock_id
					])->first();

					if (!empty($checkMock)) {
						DB::table('mockup_test_result')->where('id', $checkMock->id)->update($ansData);
					} else {
						DB::table('mockup_test_result')->insert($ansData);
					}
				}
			}
		}

		return response(["error" => false, "message" => "Exam submitted successfully."], 200);
	}


	public function mockTestAnswers(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$mock_id = $request->post('mock_id');
		$user_answer = $request->post('user_answer');


		$questionData = DB::table('mock_questions')->where([
			'mock_questions.id' => $question_id,
			'mock_id' => $mock_id
		])
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->select('mock_questions.*', 'bulk_questions.id as bulk_id', 'bulk_questions.right_option')
			->first();

		if (strstr($questionData->right_option, $user_answer)) {
			$ans_status = "1"; //correct
		} else if ($user_answer == 'm') {
			$ans_status = "3"; //Missed
		} else {
			$ans_status = "2"; //incorrect
		}

		$checkData = [
			"user_id" => $user_id,
			"question_id" => $question_id
		];

		$question_index = 0;
		if ($request->post('question_index')) {
			$question_index = $request->post('question_index');
		}

		$checkDataQuestion = DB::table('mockup_test_answers')->where($checkData)->first();


		$ansData = [
			"user_id" => $user_id,
			"bulk_table_id" => $questionData->bulk_id,
			"question_id" => (int) $question_id,
			"mock_id" => (int) $mock_id,
			"user_answer" => $user_answer,
			"right_option" => $this->getRightOption($questionData->right_option),
			"answer_status" => (int) $ans_status,
			"question_index" => $question_index
		];

		if (!empty($checkDataQuestion)) {
			DB::table('mockup_test_answers')->where('id', $checkDataQuestion->id)->update($ansData);
		} else {
			DB::table('mockup_test_answers')->insert($ansData);
		}

		return response(["error" => false, "message" => "Answer submited successfully.", "mock_id" => (int) $mock_id, "result" => $ansData], 200);
	}


	public function mockTestPause(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$mockup_data = \App\Models\Mockup::select('name')->where('id', $mock_id)->first();

		$total_questions = DB::table('mock_questions')->where([
			'mock_id' => $mock_id
		])->get();

		$answered_questions = DB::table('mockup_test_answers')
			->where([
				'user_id' => $user_id,
				'mock_id' => $mock_id
			])
			->select(DB::raw('COUNT(*) as attempted_questions'))
			->selectRaw('SUM(CASE WHEN answer_status = 3 THEN 1 ELSE 0 END) as missed_questions')
			->first();

		//echo $questionData; die;


		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$checkStatus = DB::table('mockup_test_answers')->where([
					'user_id' => $user_id,
					'mock_id' => $mock_id,
					'question_id' => $que->id,
					'bulk_table_id' => $que->question_id,
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
				}

				if ($ans_status == 1 || $ans_status == 2) {
					$attempt_status = "attempted";
				} elseif ($ans_status == 3) {
					$attempt_status = "skipped";
				} else {
					$attempt_status = "not_visited";
				}

				$userAnswer = [
					"serial_number" => sprintf('%02d', $i),
					"attempt_status" => $attempt_status,
					"answer_status" => (int) $ans_status
				];

				$result[] = $userAnswer;

				$i++;
			}
		}

		return response(
			[
				"error" => false,
				"message" => "Result Fetch successfully.",
				"mockup_name" => $mockup_data->name,
				"total_questions" => count($total_questions),
				"attempted_questions" => $answered_questions->attempted_questions,
				"skipped_questions" => (int) $answered_questions->missed_questions,
				"notvisited_questions" => (int) count($total_questions) - $answered_questions->attempted_questions,
				"result" => $result
			],
			200
		);
	}
	public function mockTestEnd(Request $request)
	{
		$user_id = $request->user()->id;
		$user_name = $request->user()->name;
		$mock_id = $request->post('mock_id');
		$average_time = $request->post('average_time');

		// Fetch mock data
		$mockData = DB::table('mockups')->where('id', $mock_id)->first();

		$positiveMark = 2.08; // default
		$negativeMark = 0.69; // default

		// Fetch positive/negative marks from course_masters
		if (!empty($mockData) && !empty($mockData->course_master_id)) {
			$courseMasterData = DB::table('course_masters')->where('id', $mockData->course_master_id)->first();
			if (!empty($courseMasterData)) {
				$positiveMark = (float) $courseMasterData->positive_mark;
				$negativeMark = (float) $courseMasterData->negative_mark;
			}
		}

		// Fetch answered questions
		$answered_questions = DB::table('mockup_test_answers')
			->where([
				'user_id' => $user_id,
				'mock_id' => $mock_id
			])
			->select(DB::raw('COUNT(*) as attempted_questions'))
			->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
			->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
			->first();

		$total_questions = DB::table('mock_questions')->where([
			'mock_id' => $mock_id
		])->count();

		// Calculate marks dynamically
		$marks = round(
			($answered_questions->right_answers * $positiveMark) -
				($answered_questions->wrong_answers * $negativeMark),
			2
		);

		$ansData = [
			"user_id" => $user_id,
			"mock_id" => (int) $mock_id,
			"user_name" => $user_name,
			"total_questions" => $total_questions,
			"attempted_questions" => $answered_questions->attempted_questions,
			"right_answers" => $answered_questions->right_answers,
			"wrong_answers" => $answered_questions->wrong_answers,
			"average_time" => (int) $average_time,
			"marks" => (string) $marks,
		];

		$checkMock = DB::table('mockup_test_result')->where([
			'user_id' => $user_id,
			"mock_id" => $mock_id
		])->first();

		if (!empty($checkMock)) {
			DB::table('mockup_test_result')->where(['id' => $checkMock->id])->update($ansData);
		} else {
			DB::table('mockup_test_result')->insert($ansData);
		}

		return response([
			"error" => false,
			"message" => "Mock test submitted successfully.",
			"mock_id" => (int) $mock_id,
			"result" => $ansData
		], 200);
	}


	// public function mockTestEnd(Request $request)
	// {
	// 	$user_id = $request->user()->id;
	// 	$user_name = $request->user()->name;
	// 	$mock_id = $request->post('mock_id');
	// 	$average_time = $request->post('average_time');

	// 	$answered_questions = DB::table('mockup_test_answers')
	// 		->where([
	// 			'user_id' => $user_id,
	// 			'mock_id' => $mock_id
	// 		])
	// 		->select(DB::raw('COUNT(*) as attempted_questions'))
	// 		->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
	// 		->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
	// 		->first();

	// 	$total_questions = DB::table('mock_questions')->where([
	// 		'mock_id' => $mock_id
	// 	])->count();



	// 	$ansData = [
	// 		"user_id" => $user_id,
	// 		"mock_id" => (int) $mock_id,
	// 		"user_name" => $user_name,
	// 		"total_questions" => $total_questions,
	// 		"attempted_questions" => $answered_questions->attempted_questions,
	// 		"right_answers" => $answered_questions->right_answers,
	// 		"wrong_answers" => $answered_questions->wrong_answers,
	// 		"average_time" => (int) $average_time,
	// 		"marks" => (string) round(($answered_questions->right_answers * 2.08) - ($answered_questions->wrong_answers * 0.69), '2')
	// 	];

	// 	$checkMock = DB::table('mockup_test_result')->where(['user_id' => $user_id, "mock_id" => $mock_id])->first();
	// 	if (!empty($checkMock)) {
	// 		DB::table('mockup_test_result')->where(['id' => $checkMock->id])->update($ansData);
	// 	} else {
	// 		DB::table('mockup_test_result')->insert($ansData);
	// 	}

	// 	return response(["error" => false, "message" => "Mock test submited successfully.", "mock_id" => (int) $mock_id, "result" => $ansData], 200);
	// }

	// public function mockTestAnalysis(Request $request)
	// {
	// 	$user_id = $request->user()->id;
	// 	$mock_id = $request->post('mock_id');

	// 	$total_questions = DB::table('mock_questions')->where('mock_id', $mock_id)->count();
	// 	$mock_results = DB::table('mockup_test_result')
	// 		->select(DB::raw('AVG(average_time) as average_time'), DB::raw('COUNT(*) as total_result'))
	// 		->where('mock_id', $mock_id)
	// 		->first();

	// 	$rank = DB::table('mockup_test_result')
	// 		->where('mock_id', $mock_id)
	// 		->where('marks', '>', function ($query) use ($mock_id, $user_id) {
	// 			$query->select('marks')
	// 				->from('mockup_test_result')
	// 				->where('mock_id', $mock_id)
	// 				->where('user_id', $user_id); // Ensure the subquery returns a single value
	// 		})
	// 		->count();

	// 	$answered_questions = DB::table('mockup_test_answers')
	// 		->where([
	// 			'user_id' => $user_id,
	// 			'mock_id' => $mock_id
	// 		])
	// 		->select(DB::raw('COUNT(*) as attempted_questions'))
	// 		->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
	// 		->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
	// 		->first();

	// 	$ansData = [
	// 		"user_id" => $user_id,
	// 		"mock_id" => (int) $mock_id,
	// 		"total_questions" => $total_questions,
	// 		"marks" => round(($answered_questions->right_answers * 2.08) - ($answered_questions->wrong_answers * 0.69), '2')
	// 	];

	// 	$leaderBoard = DB::table('mockup_test_result')->where('mock_id', $mock_id)->orderBy('marks', 'DESC')->limit('10')->get();
	// 	$result = [];
	// 	$leaderData = [];
	// 	if (!empty($leaderBoard)) {
	// 		foreach ($leaderBoard as $leader) {
	// 			$avatar = asset('uploads/banner/avatar.png');
	// 			$userAvatar = DB::table('users')->where('id', $leader->user_id)->first();
	// 			if (!empty($userAvatar)) {
	// 				$avatar = asset('uploads/banner') . '/' . $userAvatar->avatar;
	// 			}

	// 			$leaderData[] = [
	// 				'avatar' => $avatar,
	// 				'student_name' => $leader->user_name,
	// 				'correct' => $leader->right_answers,
	// 				'marks' => (string) $leader->marks,
	// 			];
	// 		}
	// 		$result = $leaderData;
	// 	}


	// 	return response(
	// 		[
	// 			"error" => false,
	// 			"message" => "Mock analysis fetch successfuly.",
	// 			"user_id" => $user_id,
	// 			"mock_id" => (int) $mock_id,
	// 			"total_questions" => $total_questions,
	// 			"total_marks" => round($total_questions * 2.08, '2'),
	// 			"correct" => (int) $answered_questions->right_answers,
	// 			"incorrect" => (int) $answered_questions->wrong_answers,
	// 			"unattempted" => $total_questions - $answered_questions->attempted_questions,
	// 			"average_time" => round($mock_results->average_time),
	// 			"total_students" => $mock_results->total_result,
	// 			"student_rank" => $rank + 1,
	// 			"result" => $result
	// 		],
	// 		200
	// 	);
	// }


	public function mockTestAnalysis(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		// Fetch mock data
		$mockData = DB::table('mockups')->where('id', $mock_id)->first();

		$positiveMark = 2.08; // default
		$negativeMark = 0.69; // default

		// Fetch positive/negative marks from course_masters
		if (!empty($mockData) && !empty($mockData->course_master_id)) {
			$courseMasterData = DB::table('course_masters')->where('id', $mockData->course_master_id)->first();
			if (!empty($courseMasterData)) {
				$positiveMark = (float) $courseMasterData->positive_mark;
				$negativeMark = (float) $courseMasterData->negative_mark;
			}
		}

		$total_questions = DB::table('mock_questions')->where('mock_id', $mock_id)->count();

		$mock_results = DB::table('mockup_test_result')
			->select(DB::raw('AVG(average_time) as average_time'), DB::raw('COUNT(*) as total_result'))
			->where('mock_id', $mock_id)
			->first();

		$rank = DB::table('mockup_test_result')
			->where('mock_id', $mock_id)
			->where('marks', '>', function ($query) use ($mock_id, $user_id) {
				$query->select('marks')
					->from('mockup_test_result')
					->where('mock_id', $mock_id)
					->where('user_id', $user_id);
			})
			->count();

		$answered_questions = DB::table('mockup_test_answers')
			->where([
				'user_id' => $user_id,
				'mock_id' => $mock_id
			])
			->select(DB::raw('COUNT(*) as attempted_questions'))
			->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
			->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
			->first();

		// Calculate marks dynamically
		$marks = round(
			($answered_questions->right_answers * $positiveMark) -
				($answered_questions->wrong_answers * $negativeMark),
			2
		);

		$leaderBoard = DB::table('mockup_test_result')
			->where('mock_id', $mock_id)
			->orderBy('marks', 'DESC')
			->limit(10)
			->get();

		$result = [];
		if (!empty($leaderBoard)) {
			foreach ($leaderBoard as $leader) {
				$avatar = asset('uploads/banner/avatar.png');
				$userAvatar = DB::table('users')->where('id', $leader->user_id)->first();
				if (!empty($userAvatar) && !empty($userAvatar->avatar)) {
					$avatar = asset('uploads/banner') . '/' . $userAvatar->avatar;
				}

				$result[] = [
					'avatar' => $avatar,
					'student_name' => $leader->user_name,
					'correct' => $leader->right_answers,
					'marks' => (string) $leader->marks,
				];
			}
		}

		return response([
			"error" => false,
			"message" => "Mock analysis fetched successfully.",
			"user_id" => $user_id,
			"mock_id" => (int) $mock_id,
			"total_questions" => $total_questions,
			"total_marks" => round($total_questions * $positiveMark, 2), // Use positive mark here also!
			"correct" => (int) $answered_questions->right_answers,
			"incorrect" => (int) $answered_questions->wrong_answers,
			"unattempted" => $total_questions - $answered_questions->attempted_questions,
			"average_time" => round($mock_results->average_time),
			"total_students" => $mock_results->total_result,
			"student_rank" => $rank + 1,
			"marks" => (string) $marks,
			"result" => $result,
		], 200);
	}



	public function mockTestBookmark(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$checkData = [
			"user_id" => $user_id,
			"mock_id" => $mock_id
		];

		$checkDataQuestion = DB::table('mockup_test_bookmarks')->where($checkData)->first();

		if (!empty($checkDataQuestion)) {
			DB::table('mockup_test_bookmarks')->where('id', $checkDataQuestion->id)->delete();
			$msg = "Mockup test removed from bookmark successfuly";
		} else {
			DB::table('mockup_test_bookmarks')->insert($checkData);
			$msg = "Mockup test added to bookmark successfuly";
		}

		return response(["error" => false, "message" => $msg], 200);
	}

	public function mockTestQuestionBookmark(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');
		$question_id = $request->post('question_id');

		$checkData = [
			"user_id" => $user_id,
			"mock_id" => $mock_id,
			"question_id" => $question_id
		];

		$questionBulkData = DB::table('mock_questions')->where('id', $question_id)->first();

		$bulk_table_id = "0";
		if (!empty($questionBulkData)) {
			$bulk_table_id = $questionBulkData->question_id;
		}

		$checkDataInsert = [
			"user_id" => $user_id,
			"mock_id" => $mock_id,
			"question_id" => $question_id,
			"bulk_table_id" => $bulk_table_id
		];

		$checkDataQuestion = DB::table('mockup_test_question_bookmarks')->where($checkData)->first();

		if (!empty($checkDataQuestion)) {
			DB::table('mockup_test_question_bookmarks')->where('id', $checkDataQuestion->id)->delete();
			$msg = "Mockup test question removed from bookmark successfuly";
		} else {
			DB::table('mockup_test_question_bookmarks')->insert($checkDataInsert);
			$msg = "Mockup test question added to bookmark successfuly";
		}

		return response(["error" => false, "message" => $msg], 200);
	}


	public function mockTestQuestionList(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$mockData = \App\Models\Mockup::select('name')->where('id', $mock_id)->first();

		$questions = DB::table('mock_questions')->where([
			'mock_questions.mock_id' => $mock_id
		])
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('mockup_test_answers', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_answers.question_id')
					->where('mockup_test_answers.user_id', '=', $user_id);
			})
			->select('bulk_questions.*', 'bulk_questions.question', 'mockup_test_answers.id as answer_id', 'mock_questions.id as mock_id')
			->orderBy('bulk_questions.id', 'ASC')
			->get();


		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {
			$attempt_status = "Unattempted";
			if (!empty($question->answer_id)) {
				$attempt_status = "Attempted";
			}

			$data = [
				"id" => $question->id,
				"question_id" => $question->mock_id,
				"sno" => sprintf('%02d', $i),
				"question" => $question->question,
				"attempt_status" => $attempt_status,
				"mock_name" => ucfirst($mockData->name)
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function bookmarkedMockupList(Request $request)
	{
		$user_id = $request->user()->id;
		$bookmarkData = DB::table('mockup_test_question_bookmarks')->where('user_id', $user_id)->groupBy('mock_id')->get();

		if ($bookmarkData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];
		if (!empty($bookmarkData)) {
			foreach ($bookmarkData as $bookmark) {
				$result = [];

				$mockData = DB::table('mockups')->where('id', $bookmark->mock_id)->first();

				$data = [
					"mock_id" => $mockData->id,
					"mock_name" => ucfirst($mockData->name),
				];

				$fresult[] = $data;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}


	public function mockTestQuestionListBookmarked(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$mockData = \App\Models\Mockup::select('name')->where('id', $mock_id)->first();


		$questions = DB::table('mock_questions')
			->where('mock_questions.mock_id', $mock_id)
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('mockup_test_answers', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_answers.question_id')
					->where('mockup_test_answers.user_id', '=', $user_id);
			})
			->join('mockup_test_question_bookmarks', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_question_bookmarks.question_id')
					->where('mockup_test_question_bookmarks.user_id', '=', $user_id);
			})
			->select('bulk_questions.*', 'bulk_questions.question', 'mockup_test_answers.id as answer_id', 'mock_questions.id as mock_id')
			->orderBy('bulk_questions.id', 'ASC')
			->get();

		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($questions as $question) {
			$attempt_status = "Unattempted";
			if (!empty($question->answer_id)) {
				$attempt_status = "Attempted";
			}

			$data = [
				"id" => $question->id,
				"question_id" => $question->mock_id,
				"sno" => "Q." . $i,
				"question" => $question->question,
				"attempt_status" => $attempt_status,
				"mock_name" => ucfirst($mockData->name)
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "mock_id" => $mock_id, "result" => $result], 200);
	}


	public function mockTestSolution(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$total_questions = DB::table('mock_questions')->where([
			'mock_id' => $mock_id
		])
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'mock_questions.id as question_id')
			->get();

		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$user_answer = "";
				$right_option = $this->getRightOption($que->right_option);
				$checkStatus = DB::table('mockup_test_answers')->where([
					'user_id' => $user_id,
					'mock_id' => $mock_id,
					'question_id' => $que->question_id,
					'bulk_table_id' => $que->id,
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
					$user_answer = $checkStatus->user_answer;
					$right_option = $checkStatus->right_option;
				}

				$totals = DB::table('mockup_test_answers')
					->select(
						DB::raw('COUNT(*) as total_attended'),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as total_correct'),
						DB::raw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as total_incorrect')
					)
					->where('question_id', $que->question_id)
					->where('mock_id', $mock_id)
					->first();

				$total_attended = $totals->total_attended;
				$total_correct = $totals->total_correct;
				$total_incorrect = $totals->total_incorrect;

				$rightPercentage = 0;
				$wrongPercentage = 0;

				if ($total_attended != 0) {
					$rightPercentage = round($total_correct / $total_attended * 100);
					$wrongPercentage = round($total_incorrect / $total_attended * 100);
				}

				$bookmarkStatus = 0;
				$bookmarkStatusData = DB::table('mockup_test_question_bookmarks')->where(['user_id' => $user_id, 'mock_id' => $mock_id, 'question_id' => $que->question_id])->first();

				if (!empty($bookmarkStatusData)) {
					$bookmarkStatus = 1;
				}


				$userAnswer = [
					"id" => $que->question_id,
					"question_id" => $que->question_id,
					"serial_number" => sprintf('%02d', $i),
					"user_answer" =>  $user_answer,
					"right_option" =>  $right_option,
					"answer_status" => (int) $ans_status,
					"right_percentage" => $rightPercentage,
					"wrong_percentage" => $wrongPercentage,
					"question" => $que->question,
					"option1" => $que->option1,
					"option2" => $que->option2,
					"option3" => $que->option3,
					"option4" => $que->option4,
					"explaination" => $que->explaination,
					"bookmark_status" => $bookmarkStatus
				];

				$result[] = $userAnswer;

				$i++;
			}
		}

		return response(
			[
				"error" => false,
				"message" => "Result Fetch successfully.",
				"total_questions" => count($total_questions),
				"result" => $result
			],
			200
		);
	}

	public function mockTestSolutionBookmarked(Request $request)
	{
		$user_id = $request->user()->id;
		$mock_id = $request->post('mock_id');

		$total_questions = DB::table('mock_questions')
			->where('mock_questions.mock_id', $mock_id)
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('mockup_test_question_bookmarks', function ($join) use ($user_id, $mock_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_question_bookmarks.question_id')
					->where('mockup_test_question_bookmarks.user_id', '=', $user_id)
					->where('mockup_test_question_bookmarks.mock_id', '=', $mock_id);
			})
			->select('bulk_questions.*', 'mock_questions.id as question_id')
			->whereNotNull('mockup_test_question_bookmarks.question_id')
			->get();

		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$user_answer = "";
				$right_option = "";
				$checkStatus = DB::table('mockup_test_answers')->where([
					'user_id' => $user_id,
					'mock_id' => $mock_id,
					'question_id' => $que->question_id,
					'bulk_table_id' => $que->id,
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
					$user_answer = $checkStatus->user_answer;
					$right_option = $checkStatus->right_option;
				}

				$totals = DB::table('mockup_test_answers')
					->select(
						DB::raw('COUNT(*) as total_attended'),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as total_correct'),
						DB::raw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as total_incorrect')
					)
					->where('question_id', $que->question_id)
					->where('mock_id', $mock_id)
					->first();

				$total_attended = $totals->total_attended;
				$total_correct = $totals->total_correct;
				$total_incorrect = $totals->total_incorrect;

				$rightPercentage = 0;
				$wrongPercentage = 0;

				if ($total_attended != 0) {
					$rightPercentage = round($total_correct / $total_attended * 100);
					$wrongPercentage = round($total_incorrect / $total_attended * 100);
				}


				$userAnswer = [
					"id" => $que->question_id,
					"question_id" => $que->question_id,
					"serial_number" => sprintf('%02d', $i),
					"user_answer" =>  $user_answer,
					"right_option" =>  $right_option,
					"answer_status" => (int) $ans_status,
					"right_percentage" => $rightPercentage,
					"wrong_percentage" => $wrongPercentage,
					"question" => $que->question,
					"option1" => $que->option1,
					"option2" => $que->option2,
					"option3" => $que->option3,
					"option4" => $que->option4,
					"explaination" => $que->explaination
				];

				$result[] = $userAnswer;

				$i++;
			}
		}

		return response(
			[
				"error" => false,
				"message" => "Result Fetch successfully.",
				"total_questions" => count($total_questions),
				"result" => $result
			],
			200
		);
	}

	public function userProfile(Request $request)
	{
		$result = [
			"id" => $request->user()->id,
			"avatar" => asset('uploads/banner') . '/' . $request->user()->avatar,
			"name" => $request->user()->name,
			"email" => $request->user()->email,
			"college" => $request->user()->college,
			"state" => $request->user()->state,
		];

		return response(array("error" => false, 'message' => "Profile data fetched successfully.", "result" => $result), 200);
	}

	public function updateUserProfile(Request $request)
	{

		$rules = [
			'name' => 'required|string',
			'email' => 'email|required|unique:users,email,' . $request->user()->id,
			'college' => 'required',
			'state' => 'required',
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$message = "";
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array('error' => true, 'message' => $message, 'result' => []), 400);
		} else {

			$user = \App\Models\User::find($request->user()->id);

			$image = $request->user()->avatar;
			if (!empty($request->has('image'))) {
				$imageData = $request->file('image');
				$image = strtotime(date('Y-m-d H:i:s')) . '.' . $imageData->getClientOriginalExtension();
				$destinationPath = public_path('/uploads/banner');
				$imageData->move($destinationPath, $image);
			}
			$user->name = $request->post('name');
			$user->email = $request->post('email');
			$user->college = $request->post('college');
			$user->state = $request->post('state');
			$user->avatar = $image;
			$user->save();

			$userResult = \App\Models\User::where([
				['id', '=', $request->user()->id],
			])->first();

			$result = [
				"id" => $userResult->id,
				"avatar" => asset('uploads/banner') . '/' . $userResult->avatar,
				"name" => $userResult->name,
				"email" => $userResult->email,
				"college" => $userResult->college,
				"state" => $userResult->state,
			];

			return response(array("error" => false, 'message' => 'User profile updated successfully.', "result" => $result), 200);
		}
	}

	public function userProfileQbankCompleted(Request $request)
	{
		$user_id = $request->user()->id;
		$qBankResultData = DB::table('question_bank_result')->where('user_id', $user_id)->get();

		if ($qBankResultData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		foreach ($qBankResultData as $qdata) {

			$subjectData = \App\Models\Course::where('id', $qdata->subject_id)->first();

			$data = [
				"id" => $qdata->id,
				"name" => $subjectData->name,
				"total_questions" => $qdata->total_questions,
				"image" => asset('uploads/course/' . $subjectData->image),
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function userProfileMockCompleted(Request $request)
	{
		$user_id = $request->user()->id;
		$qBankResultData = DB::table('mockup_test_result')->where('user_id', $user_id)->get();

		if ($qBankResultData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		foreach ($qBankResultData as $qdata) {

			$data = [
				"id" => $qdata->id,
				"attempted_questions" => $qdata->attempted_questions,
				"correct_questions" => $qdata->right_answers,
				"incorrect_questions" => $qdata->wrong_answers,
				"unattempted_questions" => $qdata->total_questions - $qdata->attempted_questions,
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "test_completed" => $qBankResultData->count(), "result" => $result], 200);
	}


	public function subscriptionPlansList(Request $request)
	{
		$user_id = $request->user()->id;
		$checkFreeTrial = DB::table('transactions')->where(['user_id' => $user_id, 'status' => '1'])->count();
		$subData = \App\Models\Subscription::orderBy('id', 'DESC')->where('status', 'Active');
		if ($checkFreeTrial > 0) {
			$subData->where('price', '!=', '0');
		}
		$subData = $subData->get();


		if ($subData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$currentDateTime = Carbon::now();
		$result = [];
		foreach ($subData as $sdata) {
			$subscription_status = 0;
			if ($sdata->duration_type == '1') {
				$type = "Months";
			} else if ($sdata->duration_type == '2') {
				$type = "Days";
			} else {
				$type = "NA";
			}

			$checkPaidSubscription = DB::table('transactions')->where(['user_id' => $user_id, 'subscription_id' => $sdata->id, 'status' => '1'])->where('end_date', '>=', $currentDateTime)->first();

			$startDateTime = "";
			$endDateTime = "";
			if (!empty($checkPaidSubscription)) {
				$subscription_status = 1;
				$startDateTime = $checkPaidSubscription->start_date;
				$endDateTime = $checkPaidSubscription->end_date;
			}

			$data = [
				"id" => $sdata->id,
				"name" => $sdata->name,
				"duration" => $sdata->duration . ' ' . $type,
				"type_duration" => $sdata->duration,
				"type" => $type,
				"price" => $sdata->price,
				"description" => $sdata->description,
				"subscription_status" => $subscription_status,
				"start_date" => $startDateTime ? date('d-M-Y h:i A', strtotime($startDateTime)) : 'NA',
				"end_date" => $endDateTime ? date('d-M-Y h:i A', strtotime($endDateTime)) : 'NA'
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	public function transactionHistory(Request $request)
	{
		$user_id = $request->user()->id;
		$transactionData = \App\Models\Transaction::where('user_id', $user_id)->orderBy('id', 'DESC')->get();


		if ($transactionData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		foreach ($transactionData as $tdata) {

			if ($tdata->status == 0) {
				$paymentStatus = "Pending";
			} else if ($tdata->status == 1) {
				$paymentStatus = "Successful";
			} else if ($tdata->status == 2) {
				$paymentStatus = "Failed";
			} else {
				$paymentStatus = "NA";
			}

			$data = [
				"id" => $tdata->id,
				"transaction_id" => $tdata->transaction_id ?? 'NA',
				"method" => $tdata->method ?? 'NA',
				"order_id" => $tdata->paymentid ?? 'NA',
				"bank_transaction_id" => $tdata->bank_transaction_id ?? 'NA',
				"card_type" => $tdata->card_type ?? 'NA',
				"subscription_name" => $tdata->subscription_name ?? 'NA',
				"paid_amount" => $tdata->paid_amount,
				"start_date" => $tdata->start_date ? date('d-M-Y h:i A', strtotime($tdata->start_date)) : 'NA',
				"end_date" => $tdata->end_date ? date('d-M-Y h:i A', strtotime($tdata->end_date)) : 'NA',
				"status" => $paymentStatus,
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function subscriptionPlansDetail(Request $request)
	{
		$id = $request->post('subscription_id');
		$user_id = $request->user()->id;
		$subData = \App\Models\Subscription::where('id', $id)->where('status', 'Active')->first();

		if ($subData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		if ($subData->duration_type == '1') {
			$type = "Months";
		} else if ($subData->duration_type == '2') {
			$type = "Days";
		} else {
			$type = "NA";
		}

		$currentDateTime = Carbon::now();


		$checkPaidPlans = DB::table('transactions')->where('paid_amount', '!=', '0')->where(['user_id' => $user_id, 'status' => '1'])->where('end_date', '>=', $currentDateTime)->orderBy('id', 'DESC')->first();

		$transactionId = strtotime(date('d-m-y H:i:s')) . $user_id;
		$amount = $subData->price;


		if ($subData->duration_type == 1) {
			$duration_days = $subData->duration * 30;
		} else {
			$duration_days = $subData->duration;
		}


		$startDateTime = now()->format('Y-m-d H:i:s');
		$endDateTime = now()->addDays($duration_days)->format('Y-m-d H:i:s');

		if (!empty($checkPaidPlans)) {
			$startDateTime = $checkPaidPlans->end_date;
			$endDateTime = Carbon::parse($startDateTime)->addDays($duration_days)->format('Y-m-d H:i:s');
		}

		$data = [
			"id" => $subData->id,
			"name" => $subData->name,
			"duration" => $subData->duration . ' ' . $type,
			"type_duration" => $subData->duration,
			"type" => $type,
			"price" => $subData->price,
			"description" => $subData->description,
			"start_date" => $startDateTime ? date('d-M-Y h:i A', strtotime($startDateTime)) : 'NA',
			"end_date" => $endDateTime ? date('d-M-Y h:i A', strtotime($endDateTime)) : 'NA',
		];

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $data], 200);
	}


	public function notificaionList(Request $request)
	{

		$user_id = $request->user()->id;
		$yesterday = Carbon::yesterday();

		$removedNotification = DB::table('notification_removed')->where('user_id', $user_id)->pluck('notification_id')->toArray();

		$todayNotificationData = \App\Models\Notification::orderBy('id', 'DESC')->whereDate('created_at', date('Y-m-d'))->where('status', 'Active')->get();
		$yesterdayNotificationData = \App\Models\Notification::orderBy('id', 'DESC')->whereDate('created_at', date('Y-m-d', strtotime($yesterday)))->where('status', 'Active')->get();
		$notificationDataOld = \App\Models\Notification::select(\DB::raw('DATE(created_at) as created_date'))
			->orderBy('id', 'DESC')
			->where('status', 'Active')
			->whereDate('created_at', '<', date('Y-m-d', strtotime($yesterday)))
			->groupBy('created_date')
			->get();

		if ($todayNotificationData->count() == 0 && $yesterdayNotificationData->count() == 0 && $notificationDataOld->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		if (!empty($todayNotificationData)) {
			foreach ($todayNotificationData as $ndata) {

				if (!in_array($ndata->id, $removedNotification)) {

					$data = [
						"id" => $ndata->id,
						"title" => $ndata->title,
						"description" => $ndata->description,
						"created_at" => date('d-m-Y', strtotime($ndata->created_at))
					];

					$result['today'][] = $data;
				}
			}
		}

		if (!empty($yesterdayNotificationData)) {
			foreach ($yesterdayNotificationData as $ndata) {
				if (!in_array($ndata->id, $removedNotification)) {
					$data = [
						"id" => $ndata->id,
						"title" => $ndata->title,
						"description" => $ndata->description,
						"created_at" => date('d-m-Y', strtotime($ndata->created_at))
					];

					$result['yesterday'][] = $data;
				}
			}
		}


		foreach ($notificationDataOld as $ndata) {
			$alldata = [];
			$dateInnerData = \App\Models\Notification::orderBy('id', 'DESC')->where('status', 'Active')->whereDate('created_at', $ndata->created_date)->get();

			foreach ($dateInnerData as $idata) {
				if (!in_array($idata->id, $removedNotification)) {
					$alldata[] = [
						"id" => $idata->id,
						"title" => $idata->title,
						"description" => $idata->description,
						"created_at" => date('d-m-Y', strtotime($idata->created_at))
					];
				}
			}
			if (!empty($alldata)) {
				$result[date('M d, Y', strtotime($ndata->created_date))] = $alldata;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}

	public function removeNotification(Request $request)
	{

		$user_id = $request->user()->id;
		$notification_id = $request->post('notification_id');

		if (!empty($notification_id)) {
			$data = [
				'user_id' => $user_id,
				'notification_id' => $notification_id
			];

			DB::table('notification_removed')->insert($data);
		} else {

			$allNotifications = DB::table('notifications')->get();
			foreach ($allNotifications as $noti) {
				$data = [
					'user_id' => $user_id,
					'notification_id' => $noti->id
				];

				// Check if the record already exists
				$exists = DB::table('notification_removed')
					->where('user_id', $user_id)
					->where('notification_id', $noti->id)
					->exists();

				// Insert the record only if it doesn't exist
				if (!$exists) {
					DB::table('notification_removed')->insert($data);
				}
			}
		}




		return response(["error" => false, "message" => "Notification removed successfully."], 200);
	}

	public function contactUs(Request $request)
	{

		$email = "projectmoprep@gmail.com";
		$mobile = "+91-999999999";
		$telegram = "contact@moprepcms";

		$data = \App\Models\FAQ::orderBy('id', 'ASC')->where('status', 'Active')->get();

		$result = [];
		foreach ($data as $sdata) {

			$data = [
				"id" => $sdata->id,
				"title" => $sdata->title,
				"description" => $sdata->description,
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "email" => $email, "mobile" => $mobile, "telegram" => $telegram, "result" => $result], 200);
	}


	public function mockTestBookmarkedAnalysis(Request $request)
	{
		$user_id = $request->user()->id;
		$currentDateTime = now()->format('Y-m-d H:i:s');

		$bookmarked = DB::table('mockup_test_bookmarks')->where([
			'user_id' => $user_id
		])
			->join('mockups', 'mockups.id', '=', 'mockup_test_bookmarks.mock_id')
			->select('mockups.*')
			->get();

		if ($bookmarked->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($bookmarked as $question) {

			$data = [
				"id" => $question->id,
				"s_no" => sprintf('%02d', $i),
				"name" => ucfirst($question->name)
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function mockTestResultAnalysis(Request $request)
	{
		$user_id = $request->user()->id;
		$currentDateTime = now()->format('Y-m-d H:i:s');

		$bookmarked = DB::table('mockup_test_result')
			->where('user_id', $user_id)
			->join('mockups', function ($join) use ($currentDateTime) {
				$join->on('mockups.id', '=', 'mockup_test_result.mock_id')
					->whereRaw("CONCAT(mockups.end_date, ' ', mockups.end_time, ':00') < ?", [$currentDateTime]);
			})
			->select('mockups.*')
			->get();

		if ($bookmarked->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($bookmarked as $question) {

			$data = [
				"id" => $question->id,
				"s_no" => sprintf('%02d', $i),
				"name" => ucfirst($question->name)
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function bookmarkedSubjectList(Request $request)
	{
		$user_id = $request->user()->id;
		$bookmarkData = DB::table('question_bank_bookmarks')->where('user_id', $user_id)->groupBy('subject_id')->get();

		if ($bookmarkData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];
		if (!empty($bookmarkData)) {
			foreach ($bookmarkData as $bookmark) {
				$result = [];

				$subjectData = DB::table('courses')->where('id', $bookmark->subject_id)->first();

				$data = [
					"subject_id" => $subjectData->id,
					"subject_name" => ucfirst($subjectData->name),
				];

				$fresult[] = $data;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}


	public function bookmarkedSubjectQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->post('subject_id');
		$bookmarkData = DB::table('question_bank_bookmarks')->where('user_id', $user_id)->where('subject_id', $subject_id)->groupBy('subject_id')->get();

		if ($bookmarkData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];
		if (!empty($bookmarkData)) {
			foreach ($bookmarkData as $bookmark) {
				$result = [];

				$subjectData = DB::table('courses')->where('id', $bookmark->subject_id)->first();
				$bookmarkedQuestion = DB::table('question_bank_bookmarks')->where('user_id', $user_id)->where('subject_id', $bookmark->subject_id)->get();

				if (!empty($bookmarkedQuestion)) {
					$i = 1;
					foreach ($bookmarkedQuestion as $bQuestion) {

						$questions = \App\Models\Question::where([
							'questions.course_id' => $bQuestion->subject_id,
							'questions.chapter_id' => $bQuestion->chapter_id,
							'questions.topic_id' => $bQuestion->topic_id,
							'questions.id' => $bQuestion->question_id,
							'questions.is_live' => 1
						])
							->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
							->leftJoin('question_bank_answers', function ($join) use ($user_id) {
								$join->on('questions.id', '=', 'question_bank_answers.question_id')
									->where('question_bank_answers.user_id', '=', $user_id);
							})
							->select('questions.*', 'bulk_questions.question', 'question_bank_answers.id as answer_id')
							->orderBy('questions.id', 'ASC')
							->first();

						$attempt_status = "Unattempted";
						if (!empty($questions->answer_id)) {
							$attempt_status = "Attempted";
						}

						if (!empty($bQuestion->user_selected_ans)) {
							$attempt_status = "Attempted";
						}

						$data = [
							"id" => $questions->id,
							"subject_id" => $bQuestion->subject_id,
							"chapter_id" => $bQuestion->chapter_id,
							"topic_id" => $bQuestion->topic_id,
							"sno" => sprintf('%02d', $i),
							"question" => $questions->question,
							"attempt_status" => $attempt_status,
							"subject_name" => ucfirst($subjectData->name),
							"bookmark_status" => 1,
							"table_id" => $bQuestion->id,
							"table_name" => "question_bank_bookmarks"

						];

						$result[] = $data;
						$i++;
					}
				}
				$data = [
					"subject_id" => $subjectData->id,
					"subject_name" => ucfirst($subjectData->name),
					"question_data" => $result
				];

				$fresult[] = $data;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}


	public function bookmarkedSubjectQuestionsDetail(Request $request)
	{
		$user_id = $request->user()->id;
		$subject_id = $request->subject_id;
		// $chapter_id = $request->chapter_id;
		// $topic_id = $request->topic_id;

		$result = [];

		$bookmarkedQuestion = DB::table('question_bank_bookmarks')->where([
			'user_id' => $user_id,
			'subject_id' => $subject_id,
			// 'chapter_id' => $chapter_id,
			// 'topic_id' => $topic_id
		])
			->join('bulk_questions', 'question_bank_bookmarks.bulk_table_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'question_bank_bookmarks.question_id as question_id', 'question_bank_bookmarks.user_selected_ans as user_selected_ans')
			->get();


		if (!empty($bookmarkedQuestion)) {
			$i = 1;
			foreach ($bookmarkedQuestion as $questions) {

				$userAnswers = DB::table('question_bank_answers')->where([
					'user_id' => $user_id,
					'subject_id' => $subject_id,
					// 'chapter_id' => $chapter_id,
					// 'topic_id' => $topic_id,
					'question_id' => $questions->question_id
				])->first();

				$userAnswer = "m";
				if (!empty($userAnswers)) {
					$userAnswer = $userAnswers->user_answer;
				}

				if (!empty($questions->user_selected_ans)) {
					$userAnswer = $questions->user_selected_ans;
				}


				if (strstr($questions->right_option, $userAnswer)) {
					$ans_status = "1"; //correct
				} else if ($userAnswer == 'm') {
					$ans_status = "3"; //Missed
				} else {
					$ans_status = "2"; //incorrect
				}


				$counts = DB::table('question_bank_answers')
					->where([
						'question_id' => $questions->question_id,
						'subject_id' => $subject_id,
						// 'chapter_id' => $chapter_id,
						// 'topic_id' => $topic_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$userAnswer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$userAnswer]);

				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);


				$data = [
					"id" => $questions->id,
					'question_id' => $questions->question_id,
					"sno" => sprintf('%02d', $i),
					"question" => $questions->question,
					"option1" => $questions->option1,
					"option2" => $questions->option2,
					"option3" => $questions->option3,
					"option4" => $questions->option4,
					"explaination" => $questions->explaination,
					"right_option" => $this->getRightOption($questions->right_option),
					"user_answer" => $userAnswer,
					"answer_status" => (int) $ans_status,
					"rightPercentage" => $rightPercentage,
					"wrongPercentage" => $wrongPercentage
				];

				$result[] = $data;
				$i++;
			}
		}



		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function bookmarkedYearList(Request $request)
	{
		$user_id = $request->user()->id;
		$bookmarkData = DB::table('previous_year_bookmarks')->where('user_id', $user_id)->groupBy('part_id')->get();

		if ($bookmarkData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];

		if (!empty($bookmarkData)) {
			foreach ($bookmarkData as $bookmark) {
				$result = [];

				$yearData = DB::table('bulk_question_years')->where('id', $bookmark->part_id)->first();


				$data = [
					"year_id" => $yearData->id,
					"year_name" => ucfirst($yearData->year) . '-' . ucfirst($yearData->part)
				];

				$fresult[] = $data;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}


	public function bookmarkedYearQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$year_id = $request->post('year_id');
		$bookmarkData = DB::table('previous_year_bookmarks')->where('user_id', $user_id)->where('part_id', $year_id)->groupBy('part_id')->get();

		if ($bookmarkData->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];

		if (!empty($bookmarkData)) {
			foreach ($bookmarkData as $bookmark) {
				$result = [];

				$yearData = DB::table('bulk_question_years')->where('id', $bookmark->part_id)->first();
				$bookmarkedQuestion = DB::table('previous_year_bookmarks')->where('user_id', $user_id)->where('part_id', $bookmark->part_id)->get();

				if (!empty($bookmarkedQuestion)) {
					$i = 1;
					foreach ($bookmarkedQuestion as $bQuestion) {

						$question = DB::table('bulk_questions')->where([
							'bulk_questions.year_id' => $bQuestion->part_id,
							'bulk_questions.id' => $bQuestion->question_id
						])
							->leftJoin('previous_year_answers', function ($join) use ($user_id) {
								$join->on('bulk_questions.id', '=', 'previous_year_answers.question_id')
									->where('previous_year_answers.user_id', '=', $user_id);
							})
							->select('bulk_questions.*', 'previous_year_answers.id as answer_id')
							->orderBy('bulk_questions.id', 'ASC')
							->first();

						$attempt_status = "Unattempted";
						if (!empty($question->answer_id)) {
							$attempt_status = "Attempted";
						}

						if (!empty($bQuestion->user_selected_ans)) {
							$attempt_status = "Attempted";
						}

						$data = [
							"id" => $question->id,
							"year_id" => $yearData->id,
							"sno" => sprintf('%02d', $i),
							"question" => $question->question,
							"attempt_status" => $attempt_status,
							"part_name" => $yearData->year . '-' . $yearData->part,
							"bookmark_status" => 1,
							"table_id" => $bQuestion->id,
							"table_name" => "previous_year_bookmarks"
						];

						$result[] = $data;
						$i++;
					}
				}
				$data = [
					"year_id" => $yearData->id,
					"year_name" => ucfirst($yearData->year),
					"year_data" => $result
				];

				$fresult[] = $data;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}


	public function bookmarkedYearQuestionsDetail(Request $request)
	{
		$user_id = $request->user()->id;
		$year_id = $request->year_id;

		$result = [];

		$bookmarkedQuestion = DB::table('previous_year_bookmarks')->where([
			'user_id' => $user_id,
			'part_id' => $year_id
		])
			->join('bulk_questions', 'previous_year_bookmarks.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'previous_year_bookmarks.question_id as question_id', 'previous_year_bookmarks.user_selected_ans as user_selected_ans')
			->get();


		if (!empty($bookmarkedQuestion)) {
			$i = 1;
			foreach ($bookmarkedQuestion as $questions) {

				$userAnswers = DB::table('previous_year_answers')->where([
					'user_id' => $user_id,
					'part_id' => $year_id,
					'question_id' => $questions->question_id
				])->first();

				$userAnswer = "m";
				if (!empty($userAnswers)) {
					$userAnswer = $userAnswers->user_answer;
				}

				if (!empty($questions->user_selected_ans)) {
					$userAnswer = $questions->user_selected_ans;
				}


				if (strstr($questions->right_option, $userAnswer)) {
					$ans_status = "1"; //correct
				} else if ($userAnswer == 'm') {
					$ans_status = "3"; //Missed
				} else {
					$ans_status = "2"; //incorrect
				}


				$counts = DB::table('previous_year_answers')
					->where([
						'question_id' => $questions->question_id,
						'part_id' => $year_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$userAnswer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$userAnswer]);



				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);

				$data = [
					"id" => $questions->id,
					'question_id' => $questions->question_id,
					"sno" => sprintf('%02d', $i),
					"question" => $questions->question,
					"option1" => $questions->option1,
					"option2" => $questions->option2,
					"option3" => $questions->option3,
					"option4" => $questions->option4,
					"explaination" => $questions->explaination,
					"right_option" => $this->getRightOption($questions->right_option),
					"user_answer" => $userAnswer,
					"rightPercentage" => $rightPercentage,
					"wrongPercentage" => $wrongPercentage,
					"answer_status" => (int) $ans_status,
				];

				$result[] = $data;
				$i++;
			}
		}



		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function deleteAccount(Request $request)
	{
		$user_id = $request->user()->id;
		DB::table('mockup_test_answers')->where('user_id', $user_id)->delete();
		DB::table('mockup_test_bookmarks')->where('user_id', $user_id)->delete();
		DB::table('mockup_test_duration')->where('user_id', $user_id)->delete();
		DB::table('mockup_test_question_bookmarks')->where('user_id', $user_id)->delete();
		DB::table('mockup_test_result')->where('user_id', $user_id)->delete();


		DB::table('previous_year_answers')->where('user_id', $user_id)->delete();
		DB::table('previous_year_bookmarks')->where('user_id', $user_id)->delete();
		DB::table('previous_year_result')->where('user_id', $user_id)->delete();


		DB::table('question_bank_answers')->where('user_id', $user_id)->delete();
		DB::table('question_bank_bookmarks')->where('user_id', $user_id)->delete();
		DB::table('question_bank_result')->where('user_id', $user_id)->delete();


		return response(["error" => false, "message" => "User Account Deleted Successfully."], 200);
	}


	public function allBookmarkedQuestions(Request $request)
	{

		$user_id = $request->user()->id;

		$questions = DB::table('mock_questions')
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('mockup_test_answers', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_answers.question_id')
					->where('mockup_test_answers.user_id', '=', $user_id);
			})
			->join('mockup_test_question_bookmarks', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_question_bookmarks.question_id')
					->where('mockup_test_question_bookmarks.user_id', '=', $user_id);
			})
			->select('bulk_questions.id', 'bulk_questions.question', 'mockup_test_answers.id as answer_id', 'mock_questions.mock_id as mock_id', 'mockup_test_question_bookmarks.id as bookmark_id')
			->orderBy('bulk_questions.id', 'ASC')
			->get();


		$result = [];
		$i = 1;
		if (!empty($questions)) {
			foreach ($questions as $question) {
				$attempt_status = "Unattempted";
				if (!empty($question->answer_id)) {
					$attempt_status = "Attempted";
				}

				$mockDataName = DB::table('mockups')->where('id', $question->mock_id)->first();

				$mockName = "";
				if ($mockDataName) {
					$mockName = $mockDataName->name;
				}

				$data = [
					"id" => $question->id,
					"sno" => sprintf('%02d', $i),
					"question" => $question->question,
					"attempt_status" => $attempt_status,
					"type" => "all",
					"bookmark_status" => 1,
					"table_id" => $question->bookmark_id,
					"part_name" => $mockName,
					"table_name" => "mockup_test_question_bookmarks"
				];

				$result[] = $data;
				$i++;
			}
		}


		$bookmarkedQuestion = DB::table('question_bank_bookmarks')->where('user_id', $user_id)->get();

		if (!empty($bookmarkedQuestion)) {
			foreach ($bookmarkedQuestion as $bQuestion) {

				$questions = \App\Models\Question::where([
					'questions.course_id' => $bQuestion->subject_id,
					'questions.chapter_id' => $bQuestion->chapter_id,
					'questions.topic_id' => $bQuestion->topic_id,
					'questions.id' => $bQuestion->question_id,
					'questions.is_live' => 1
				])
					->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
					->leftJoin('question_bank_answers', function ($join) use ($user_id) {
						$join->on('questions.id', '=', 'question_bank_answers.question_id')
							->where('question_bank_answers.user_id', '=', $user_id);
					})
					->select('bulk_questions.id', 'bulk_questions.question', 'question_bank_answers.id as answer_id')
					->orderBy('questions.id', 'ASC')
					->first();



				$attempt_status = "Unattempted";
				if (!empty($questions->answer_id)) {
					$attempt_status = "Attempted";
				}

				if (!empty($bQuestion->user_selected_ans)) {
					$attempt_status = "Attempted";
				}

				$subjectDataName = DB::table('courses')->where('id', $bQuestion->subject_id)->first();

				$subjectName = "";
				if (!empty($subjectDataName)) {
					$subjectName = $subjectDataName->name;
				}

				$data = [
					"id" => $questions->id,
					"sno" => sprintf('%02d', $i),
					"question" => $questions->question,
					"attempt_status" => $attempt_status,
					"type" => "all",
					"bookmark_status" => 1,
					"table_id" => $bQuestion->id,
					"part_name" => $subjectName,
					"table_name" => "question_bank_bookmarks"

				];

				$result[] = $data;
				$i++;
			}
		}




		$bookmarkedQuestionYear = DB::table('previous_year_bookmarks')->where('user_id', $user_id)->get();

		if (!empty($bookmarkedQuestionYear)) {
			foreach ($bookmarkedQuestionYear as $bQuestion) {

				$question = DB::table('bulk_questions')->where([
					'bulk_questions.year_id' => $bQuestion->part_id,
					'bulk_questions.id' => $bQuestion->question_id
				])
					->leftJoin('previous_year_answers', function ($join) use ($user_id) {
						$join->on('bulk_questions.id', '=', 'previous_year_answers.question_id')
							->where('previous_year_answers.user_id', '=', $user_id);
					})
					->select('bulk_questions.id', 'bulk_questions.question', 'previous_year_answers.id as answer_id')
					->orderBy('bulk_questions.id', 'ASC')
					->first();

				$attempt_status = "Unattempted";
				if (!empty($question->answer_id)) {
					$attempt_status = "Attempted";
				}

				if (!empty($bQuestion->user_selected_ans)) {
					$attempt_status = "Attempted";
				}

				$partDataName = DB::table('bulk_question_years')->where('id', $bQuestion->part_id)->first();

				$partName = "";
				if (!empty($partDataName)) {
					$partName = $partDataName->year . '-' . $partDataName->part;
				}

				$data = [
					"id" => $question->id,
					"sno" => sprintf('%02d', $i),
					"question" => $question->question,
					"attempt_status" => $attempt_status,
					"type" => "all",
					"bookmark_status" => 1,
					"table_id" => $bQuestion->id,
					"part_name" => $partName,
					"table_name" => "previous_year_bookmarks"
				];

				$result[] = $data;
				$i++;
			}
		}

		if (count($result) == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		return response(["error" => false, "message" => "All Bookmarked Question Fatched Successfully.", "result" => $result]);
	}


	public function allBookmarkedQuestionsDetails(Request $request)
	{

		$user_id = $request->user()->id;


		$total_questions = DB::table('mock_questions')
			->join('bulk_questions', 'mock_questions.question_id', '=', 'bulk_questions.id')
			->leftJoin('mockup_test_question_bookmarks', function ($join) use ($user_id) {
				$join->on('mock_questions.id', '=', 'mockup_test_question_bookmarks.question_id')
					->where('mockup_test_question_bookmarks.user_id', '=', $user_id);
			})
			->select('bulk_questions.*', 'mock_questions.id as question_id')
			->whereNotNull('mockup_test_question_bookmarks.question_id')
			->get();

		$result = [];
		if (!empty($total_questions)) {
			$i = 1;
			foreach ($total_questions as $que) {
				$ans_status = 4;
				$user_answer = "";
				$right_option = "";
				$checkStatus = DB::table('mockup_test_answers')->where([
					'user_id' => $user_id,
					'question_id' => $que->question_id,
					'bulk_table_id' => $que->id,
				])->first();

				if (!empty($checkStatus)) {
					$ans_status = $checkStatus->answer_status;
					$user_answer = $checkStatus->user_answer;
					$right_option = $checkStatus->right_option;
				}

				$totals = DB::table('mockup_test_answers')
					->select(
						DB::raw('COUNT(*) as total_attended'),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as total_correct'),
						DB::raw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as total_incorrect')
					)
					->where('question_id', $que->question_id)
					->first();

				$total_attended = $totals->total_attended;
				$total_correct = $totals->total_correct;
				$total_incorrect = $totals->total_incorrect;

				$rightPercentage = 0;
				$wrongPercentage = 0;

				if ($total_attended != 0) {
					$rightPercentage = round($total_correct / $total_attended * 100);
					$wrongPercentage = round($total_incorrect / $total_attended * 100);
				}


				$userAnswer = [
					"id" => $que->question_id,
					"question_id" => $que->question_id,
					"sno" => sprintf('%02d', $i),
					"user_answer" =>  $user_answer,
					"right_option" =>  $right_option,
					"answer_status" => (int) $ans_status,
					"right_percentage" => $rightPercentage,
					"wrong_percentage" => $wrongPercentage,
					"question" => $que->question,
					"option1" => $que->option1,
					"option2" => $que->option2,
					"option3" => $que->option3,
					"option4" => $que->option4,
					"explaination" => $que->explaination
				];

				$result[] = $userAnswer;

				$i++;
			}
		}


		$bookmarkedQuestion = DB::table('question_bank_bookmarks')->where([
			'user_id' => $user_id
		])
			->join('bulk_questions', 'question_bank_bookmarks.bulk_table_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'question_bank_bookmarks.question_id as question_id', 'question_bank_bookmarks.user_selected_ans as user_selected_ans')
			->get();


		if (!empty($bookmarkedQuestion)) {
			foreach ($bookmarkedQuestion as $questions) {

				$userAnswers = DB::table('question_bank_answers')->where([
					'user_id' => $user_id,
					'question_id' => $questions->question_id
				])->first();

				$userAnswer = "m";
				if (!empty($userAnswers)) {
					$userAnswer = $userAnswers->user_answer;
				}

				if (!empty($questions->user_selected_ans)) {
					$userAnswer = $questions->user_selected_ans;
				}


				if (strstr($questions->right_option, $userAnswer)) {
					$ans_status = "1"; //correct
				} else if ($userAnswer == 'm') {
					$ans_status = "3"; //Missed
				} else {
					$ans_status = "2"; //incorrect
				}


				$counts = DB::table('question_bank_answers')
					->where([
						'question_id' => $questions->question_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$userAnswer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$userAnswer]);

				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);


				$data = [
					"id" => $questions->id,
					'question_id' => $questions->question_id,
					"sno" => sprintf('%02d', $i),
					"user_answer" => $userAnswer,
					"right_option" => $this->getRightOption($questions->right_option),
					"answer_status" => (int) $ans_status,
					"rightPercentage" => $rightPercentage,
					"wrongPercentage" => $wrongPercentage,
					"question" => $questions->question,
					"option1" => $questions->option1,
					"option2" => $questions->option2,
					"option3" => $questions->option3,
					"option4" => $questions->option4,
					"explaination" => $questions->explaination,
				];

				$result[] = $data;
				$i++;
			}
		}



		$bookmarkedQuestionYear = DB::table('previous_year_bookmarks')->where([
			'user_id' => $user_id
		])
			->join('bulk_questions', 'previous_year_bookmarks.question_id', '=', 'bulk_questions.id')
			->select('bulk_questions.*', 'previous_year_bookmarks.question_id as question_id', 'previous_year_bookmarks.user_selected_ans as user_selected_ans')
			->get();


		if (!empty($bookmarkedQuestionYear)) {
			foreach ($bookmarkedQuestionYear as $questions) {

				$userAnswers = DB::table('previous_year_answers')->where([
					'user_id' => $user_id,
					'question_id' => $questions->question_id
				])->first();

				$userAnswer = "m";
				if (!empty($userAnswers)) {
					$userAnswer = $userAnswers->user_answer;
				}

				if (!empty($questions->user_selected_ans)) {
					$userAnswer = $questions->user_selected_ans;
				}


				if (strstr($questions->right_option, $userAnswer)) {
					$ans_status = "1"; //correct
				} else if ($userAnswer == 'm') {
					$ans_status = "3"; //Missed
				} else {
					$ans_status = "2"; //incorrect
				}


				$counts = DB::table('previous_year_answers')
					->where([
						'question_id' => $questions->question_id
					])
					->select(
						DB::raw('COUNT(*) as totalAnswerd'),
						DB::raw("SUM(CASE WHEN user_answer = '$userAnswer' THEN 1 ELSE 0 END) as userSelectedAnswerd"),
						DB::raw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as rightAnswerd')
					)
					->get([$userAnswer]);



				$totalAnswerd = $counts->pluck('totalAnswerd')->first();
				$userSelectedAnswerd = $counts->pluck('userSelectedAnswerd')->first();
				$rightAnswerd = $counts->pluck('rightAnswerd')->first();

				if ($totalAnswerd == 0) {
					$totalAnswerd = 1;
				}

				$rightPercentage = round($rightAnswerd / $totalAnswerd * 100);
				$wrongPercentage = round($userSelectedAnswerd / $totalAnswerd * 100);

				$data = [
					"id" => $questions->id,
					'question_id' => $questions->question_id,
					"sno" => sprintf('%02d', $i),
					"user_answer" => $userAnswer,
					"right_option" => $this->getRightOption($questions->right_option),
					"answer_status" => (int) $ans_status,
					"rightPercentage" => $rightPercentage,
					"wrongPercentage" => $wrongPercentage,
					"question" => $questions->question,
					"option1" => $questions->option1,
					"option2" => $questions->option2,
					"option3" => $questions->option3,
					"option4" => $questions->option4,
					"explaination" => $questions->explaination,
				];

				$result[] = $data;
				$i++;
			}
		}

		if (count($result) == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		return response(["error" => false, "message" => "All Bookmarked Question Detail Fatched Successfully.", "result" => $result]);
	}

	public function deleteBookmarkedQuestions(Request $request)
	{
		$user_id = $request->user()->id;
		$table_id = $request->post('table_id');
		$table_name = $request->post('table_name');

		DB::table($table_name)->where('id', $table_id)->where('user_id', $user_id)->delete();

		return response(["error" => false, "message" => "Bookmarked Question Deleted Successfuly"]);
	}


	public function requestPackageSubscription(Request $request)
	{

		try {

			$user_id = $request->user()->id;
			$subscription_id = $request->post('subscription_id');
			$subscriptionData = DB::table('subscriptions')->where('id', $subscription_id)->first();

			if ($user_id) {

				$user = \App\Models\User::where('id', $user_id)->first();

				$transactionId = strtotime(date('d-m-y H:i:s')) . $user_id;
				$amount = $subscriptionData->price;
				$mobile = $user->whatsapp_number;



				if ($subscriptionData->duration_type == 1) {
					$duration_days = $subscriptionData->duration * 30;
				} else {
					$duration_days = $subscriptionData->duration;
				}


				$startDateTime = now()->format('Y-m-d H:i:s');
				$endDateTime = now()->addDays($duration_days)->format('Y-m-d H:i:s');

				DB::table('transactions')->insert([
					'user_id' => $user_id,
					'subscription_id' => $subscription_id,
					'student_name' => $user->name,
					'student_mobile' => $mobile,
					'paid_amount' => $amount,
					'transaction_id' => $transactionId,
					'subscription_name' => $subscriptionData->name,
					'start_date' => $startDateTime,
					'end_date' => $endDateTime,
					'status' => 0
				]);

				$callbackUrl = 'https://admin.moprep.in/api/webhookResponse';
				$merchant_id = env('MERCHANT_ID');
				$redirectUrl = url('/redirect');

				$jsonString = '{
						"merchantId": "' . $merchant_id . '" ,
						"merchantTransactionId":"' . 'MT' . $transactionId . '",
						"merchantUserId":"MU' . $user_id . '",
						"amount":' . ($amount * 100) . ',
						"callbackUrl":"' . $callbackUrl . '",
						"redirectUrl": "' . $redirectUrl . '",
						"redirectMode": "POST",
						"mobileNumber":"' . $mobile . '",
						"deviceContext": {
							"deviceOS": "ANDROID"
						  },
						"paymentInstrument": {
							"type": "UPI_INTENT",
						  	"targetApp": "com.phonepe.app"
						}
					}';

				$base64Encoded = base64_encode($jsonString);

				$base64Body = $base64Encoded;
				$apiEndPoint = '/pg/v1/pay';
				$phonepeAppId = env('SALT_KEY');
				$saltIndex = '1';

				$sha_body = $base64Body . $apiEndPoint . $phonepeAppId;
				$checksum = hash('sha256', $sha_body) . "###" . $saltIndex;
				$curl = curl_init();

				curl_setopt_array($curl, [
					CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => json_encode([
						'request' => $base64Body,
					]),
					CURLOPT_HTTPHEADER => [
						"Content-Type: application/json",
						"X-VERIFY: " . $checksum,
						"accept: application/json",
					],
				]);

				$response = curl_exec($curl);

				curl_close($curl);
				$response = json_decode($response, true);

				if (isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
					$intentUrl = $response['data']['instrumentResponse']['redirectInfo']['url'];

					$responseArray = [
						'txnId' => $transactionId,
						'intentUrls' => [$intentUrl],
					];

					return response()->json(['success' => true, 'data' => $responseArray]);
				} else {

					return response()->json(['success' => false, 'message' => 'Payment Gateway Not Working']);
				}
			} else {

				return response()->json(['success' => false, 'message' => 'User not found']);
			}
		} catch (\Exception $e) {

			return response()->json(['success' => false, 'message' => $e->getMessage()]);
		}
	}

	public function requestPackageSubscriptionRazor(Request $request)
	{

		try {

			$user_id = $request->user()->id;
			$subscription_id = $request->post('subscription_id');
			$subscriptionData = DB::table('subscriptions')->where('id', $subscription_id)->first();
			$currentDateTime = Carbon::now();
			if ($user_id) {

				// $key = env('RAZORPAY_KEY');
				// $secret = env('RAZORPAY_SECRET');

				$key = env('RAZORPAY_KEY') ? env('RAZORPAY_KEY') : 'rzp_live_033pnpxG3wyQXv';
				$secret = env('RAZORPAY_SECRET') ? env('RAZORPAY_SECRET') : 'pMYZf5TGfhm9BtNI0felf28U';

				$user = \App\Models\User::where('id', $user_id)->first();
				$checkPaidPlans = DB::table('transactions')->where('paid_amount', '!=', '0')->where(['user_id' => $user_id, 'status' => '1'])->where('end_date', '>=', $currentDateTime)->orderBy('id', 'DESC')->first();

				$transactionId = strtotime(date('d-m-y H:i:s')) . $user_id;
				$amount = $subscriptionData->price;
				$mobile = $user->whatsapp_number;

				if ($amount == 0) {
					$subscription_status = "1";
				} else {
					$subscription_status = "0";
				}



				if ($subscriptionData->duration_type == 1) {
					$duration_days = $subscriptionData->duration * 30;
				} else {
					$duration_days = $subscriptionData->duration;
				}


				$startDateTime = now()->format('Y-m-d H:i:s');
				$endDateTime = now()->addDays($duration_days)->format('Y-m-d H:i:s');

				if (!empty($checkPaidPlans)) {
					$startDateTime = $checkPaidPlans->end_date;
					$endDateTime = Carbon::parse($startDateTime)->addDays($duration_days)->format('Y-m-d H:i:s');
				}


				DB::table('transactions')->insert([
					'user_id' => $user_id,
					'subscription_id' => $subscription_id,
					'student_name' => $user->name,
					'student_mobile' => $mobile,
					'paid_amount' => $amount,
					'transaction_id' => $transactionId,
					'subscription_name' => $subscriptionData->name,
					'start_date' => $startDateTime,
					'end_date' => $endDateTime,
					'status' => $subscription_status
				]);

				if ($amount == 0) {
					return response()->json(['error' => false, 'data' => [], 'trial_period' => 1]);
				}

				$url = "https://api.razorpay.com/v1/orders";
				$fields = [
					'amount' => $amount * 100, // amount in paise
					'currency' => 'INR',
					'receipt' => $transactionId,
				];

				$fields_string = json_encode($fields);

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $secret);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

				$result = curl_exec($ch);

				curl_close($ch);
				$response = json_decode($result, true);
				// dd($response);
				if (isset($response['status']) && $response['status'] == 'created') {

					DB::table('transactions')->where('transaction_id', $response['receipt'])->where('user_id', $user_id)->update(['paymentid' => $response['id']]);

					return response()->json(['error' => false, 'data' => $response, 'trial_period' => 0]);
				} else {

					return response()->json(['error' => true, 'data' => $response, 'trial_period' => 0]);
				}
			} else {

				return response()->json(['error' => true, 'data' => 'User not found', 'trial_period' => 0]);
			}
		} catch (\Exception $e) {

			return response()->json(['error' => true, 'data' => $e->getMessage(), 'trial_period' => 0]);
		}
	}


	public function responsePackageSubscriptionRazor(Request $request)
	{
		$razorpayPaymentId = $request->input('razorpay_payment_id');
		$razorpayOrderId = $request->input('razorpay_order_id');
		$razorpaySignature = $request->input('razorpay_signature');
		$user_id = $request->user()->id;
		$apiKey = env('RAZORPAY_KEY') ? env('RAZORPAY_KEY') : 'rzp_live_033pnpxG3wyQXv';
		$apiSecret = env('RAZORPAY_SECRET') ? env('RAZORPAY_SECRET') : 'pMYZf5TGfhm9BtNI0felf28U';
		// Verify signature
		$generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $apiSecret);

		if ($generatedSignature == $razorpaySignature) {
			// Payment is successful, now verify with cURL
			// 			$apiKey = env('RAZORPAY_KEY');
			// 			$apiSecret = env('RAZORPAY_SECRET');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/payments/' . $razorpayPaymentId);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);

			$response = curl_exec($ch);
			\Log::info('Response dari API:', ['response' => $response]);

			$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($httpStatus === 200) {
				$responseArray = json_decode($response, true);
				if ($responseArray['status'] == 'captured') {
					DB::table('transactions')->where('paymentid', $razorpayOrderId)->where('user_id', $user_id)->update(['status' => '1']);
					return response()->json(['error' => false, 'message' => 'Payment Successful']);
				} else {
					DB::table('transactions')->where('paymentid', $razorpayOrderId)->where('user_id', $user_id)->update(['status' => '2']);
					return response()->json(['error' => true, 'message' => 'Payment Failed']);
				}
			} else {
				DB::table('transactions')->where('paymentid', $razorpayOrderId)->where('user_id', $user_id)->update(['status' => '2']);
				return response()->json(['error' => true, 'message' => 'Payment Failed']);
			}
		} else {
			// Payment failed
			DB::table('transactions')->where('paymentid', $razorpayOrderId)->where('user_id', $user_id)->update(['status' => '2']);
			return response()->json(['error' => true, 'message' => 'Payment Failed']);
		}
	}


	public function checkUserSubscription(Request $request)
	{
		$user_id = $request->user()->id;

		$currentDateTime = Carbon::now();

		$cond = [
			"user_id" => $user_id,
			"status" => "1"
		];

		$subscriptionStatus = DB::table('transactions')->where($cond)->where('start_date', '<=', $currentDateTime)->where('end_date', '>=', $currentDateTime)->count();

		if ($subscriptionStatus > 0) {
			return response(["error" => false, "subscription_status" => 1, "message" => "Subscription is active."], 200);
		} else {
			return response(["error" => false, "subscription_status" => 0, "message" => "Subscription is not active."], 200);
		}
	}


	public function logout(Request $request)
	{

		$request->user()->token()->revoke();

		return response(array('message' => 'Logout successfully.'), 200);
	}

	// old api

	// public function userProfile(Request $request){


	// 		$result = [
	// 			"id"=>$request->user()->id,
	// 			"name"=>$request->user()->name,
	// 			"email"=>$request->user()->email,
	// 			"mobile"=>$request->user()->mobile,
	//             "telegram_number"=>$request->user()->telegram_number,
	//             "whatsapp_number"=>$request->user()->whatsapp_number
	// 		];

	// 		return response(array('message'=>"Profile data fetched successfully.","result"=>$result),200);



	// }

	// public function courseList(Request $request){

	// 	$userCourses = \App\Models\Purchasedhistory::where('user_id', $request->user()->id)
	// 		->pluck('batch_id')
	// 		->toArray();

	// 	$courses = \App\Models\Course::with(['batches' => function ($query) use($userCourses){
	// 		$query->where('end_date', '>=', now());
	// 		$query->where('status', '1');
	// 		$query->whereIn('id', $userCourses);
	// 	}])->where('status', '1')->orderBy('id', 'DESC')->get();

	// 	$result = [];

	// 	foreach ($courses as $course) {

	// 		$data = [
	// 			"id" => $course->id,
	// 			"name" => ucfirst($course->name),
	// 			"image" => asset('uploads/course/' . $course->image),
	// 		];


	// 		$data['is_purchased']=false;
	// 		$data['batch_id']='0';
	// 		if(!empty($course['batches']) && !empty($course['batches'][0])){
	// 			$data['is_purchased']=true;
	// 			$data['batch_id']=$course['batches'][0]['id'];
	// 			$data['batch_name']=$course['batches'][0]['name'];
	// 			$data['unique_id']=$course['batches'][0]['unique_id'];
	// 		}

	// 		$result[]=$data;
	// 	}

	// 	return response(["message" => "Course fetched successfully.", "result" => $result], 200);
	// }



	// public function batchList(Request $request){

	// 	$rules['course_id'] = 'exists:courses,id|nullable';

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$query = \App\Models\Batch::with(['courseData','purchased_data'=> function($query1) use($request){
	// 			$query1->where('user_id',$request->user()->id);
	// 		}])->where('status','1')->where('end_date','>=',now()->format('Y-m-d'))->orderBy('id','DESC');

	//         if($request->json()->get('course_id')){
	//             $query->where('course_id',$request->json()->get('course_id'));
	//         }

	//         $batchResult=$query->get();

	// 		$result = [];

	// 		if($batchResult->count()>0){

	// 			foreach($batchResult as $batch){

	// 				$result[] = [
	// 					"id" => $batch->id,
	// 					"course_id" => $batch->course_id,
	// 					"unique_id" => $batch->unique_id,
	// 					"course_name" => $batch->courseData->name ?? "NA",
	// 					"name" => ucfirst($batch->name),
	// 					"slug" => $batch->slug,
	// 					"start_date" => date("d-M-Y",strtotime($batch->start_date)),
	// 					"end_date" => date("d-M-Y",strtotime($batch->end_date)),
	// 					"price" => $batch->price,
	//                     "dollar_price"=>$batch->dollar_price,
	// 					"image" => asset('uploads/batch/'.$batch->image),
	// 					"subject" => $batch->short_desc,
	// 					"description" => $batch->desc,
	// 					'status'=> isset($batch->purchased_data) ? 'Purchased' : 'No'
	// 				];
	// 			}
	// 		}

	// 		return response(array("message" => "Batch fetched successfully. ","result" => $result ),200);

	// 	}
	// }

	// public function purchasedBatchList(Request $request){

	// 	$batchResult = \App\Models\Batch::with(['courseData', 'purchased_data' => function ($query1) use ($request) {
	// 		$query1->where('user_id', $request->user()->id);
	// 	}])->whereHas('purchased_data', function ($query1) use ($request) {
	// 		$query1->where('user_id', $request->user()->id);
	// 	})->where('status', '1')
	// 		->where('end_date', '>=', now()->format('Y-m-d'))
	// 		->orderBy('id', 'DESC')
	// 		->get();


	// 	$result = [];

	// 	if($batchResult->count()>0){

	// 		foreach($batchResult as $batch){

	// 			$result[] = [

	// 				"id" => $batch->id,
	// 				"course_id" => $batch->course_id,
	// 				"unique_id" => $batch->unique_id,
	// 				"course_name" => $batch->courseData->name,
	// 				"name" => ucfirst($batch->name),
	// 				"slug" => $batch->slug,
	// 				"start_date" => date("d-M-Y",strtotime($batch->start_date)),
	// 				"end_date" => date("d-M-Y",strtotime($batch->end_date)),
	// 				"price" => $batch->price,
	//                 "dollar_price"=>$batch->dollar_price,
	// 				"image" => asset('uploads/batch/'.$batch->image),
	// 				"subject" => $batch->short_desc,
	// 				"description" => $batch->desc,
	// 				'status'=> isset($batch->purchased_data) ? 'Purchased' : 'No'
	// 			];
	// 		}
	// 	}

	// 	return response(array("message" => "Batch fetched successfully. ","result" => $result ),200);
	// }

	// public function paymentInitiate(Request $request){

	// 	$rules['batch_id'] = 'required';

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{
	// 			// Check if the user has already purchased the batch
	// 		$purchasedHistory = \App\Models\Purchasedhistory::where('user_id', $request->user()->id)
	// 														->where('batch_id', $request->json()->get('batch_id'))
	// 														->first();

	// 		if ($purchasedHistory) {
	// 			return response(array("message" => "You have already purchased this batch."), 400);
	// 		}

	// 		$batch = \App\Models\Batch::where('id',$request->json()->get('batch_id'))->whereDate('end_date','>=',date('Y-m-d'))->first();
	// 		if(!$batch){
	// 			return response(array( "message" => "Batch Not found."), 400);
	// 		}

	// 		$stripeDataArr = [
	// 			"line_items[0][price_data]" => [
	// 				"unit_amount" => $batch['price']*100,
	// 				"currency" => 'INR',
	// 				"product_data" => [
	// 					"name" => ($batch->name) ? $batch->name : "Order", // dynamic product name
	// 				]
	// 			],
	// 			"line_items[0][quantity]" => 1,
	// 			"mode" => "payment",
	// 			"success_url" => "https://demo.myalwar.in/mrcem_courseapp/test",
	// 			"cancel_url" => "http://192.168.1.11:8000/payment1",
	// 			"payment_intent_data[capture_method]" => "automatic",
	// 			"metadata" => [
	// 				'user_id'=> $request->user()->id,
	// 				'batch_id'=>$batch->id,
	// 				'course_id'=>$batch->course_id,
	// 			],
	// 			"customer_email" => $request->user()->email,
	// 			"billing_address_collection" => "required", // Include billing address
	// 		];

	// 		$config = [
	// 			'client_id' => env('STRIPE_PUBLISHABLE_KEY'),
	// 			'api_key' => env('STRIPE_SECRET_KEY')
	// 		];

	// 		$stripe = new \Stripe\StripeClient($config);

	// 		$checkout_session = $stripe->checkout->sessions->create($stripeDataArr)->toArray();

	// 		return response(array("message" => "CheckOut Successfully.","payment_url" => $checkout_session['url'] ),200);
	// 	}


	// }



	// public function topicBatchList(Request $request){

	// 	$rules = [
	// 		'batch_id' => 'exists:batches,id|nullable',
	// 	];

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = $validator->errors()->first();
	// 		return response(['error' => true, 'message' => $message], 200);
	// 	}

	// 	$topicResult = \App\Models\TopicMaterials::where('batch_id', $request->json()->get('batch_id'))
	// 		->where('status', '1')
	// 		->orderByDesc('id')
	// 		->get();

	// 	$result = [];

	// 	if ($topicResult->isEmpty()) {
	// 		return response(['message' => 'Topic materials not found.', 'result' => $result], 200);
	// 	}

	// 	foreach ($topicResult as $topic) {
	// 		$result[] = [
	// 			"id" => $topic->id,
	// 			"course_id" => ucfirst(\App\Helpers\commonHelper::getCourseName($topic->course_id)),
	// 			"batch_id" => ucfirst(\App\Helpers\commonHelper::getBatchName($topic->batch_id)),
	// 			"topic" => ucfirst($topic->topic),
	// 		];
	// 	}

	// 	return response(['message' => 'Topic Materials fetched successfully.', 'result' => $result], 200);
	// }


	// public function notesMaterialsList(Request $request){

	// 	$rules['batch_id'] = 'exists:batches,id|nullable';
	// 	$rules['file_type'] = 'required';

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$notesResult = \App\Models\TopicMaterials::where('batch_id',$request->json()->get('batch_id'))->whereHas('topicData', function ($data) use ($request) {
	// 			$data->where('status', '1');
	// 			if($request->json()->get('file_type')=='notes'){
	// 				$data->where('file_type', 'image')->orWhere('file_type', 'pdf');
	// 			}elseif($request->json()->get('file_type')=='video'){
	// 				$data->where('file_type', 'video');
	// 			}
	// 		})->with('topicData',function ($data) use ($request) {
	// 			$data->where('status', '1');
	// 			if($request->json()->get('file_type')=='notes'){
	// 				$data->where('file_type', 'image')->orWhere('file_type', 'pdf');
	// 			}elseif($request->json()->get('file_type')=='video'){
	// 				$data->where('file_type', 'video');
	// 			}
	// 		})->get();

	// 		if ($notesResult->count() == 0) {

	// 			return response(array("message" => "Notes materials not found. ", "result"=>array()), 200);
	// 		}

	// 		$baseurl = asset('uploads/file/');

	// 		return response(array("message" => "Notes Materials fetched successfully. ", "result" => $notesResult, "baseurl" => $baseurl, "userId" => $request->user()->id), 200);
	// 	}

	// }

	// public function chaptersList(Request $request){

	// 	$rules['course_id'] = 'exists:chapters,course_id|required';
	// 	$rules['type'] = 'required|in:all,unanswered,flagged,recall';
	// 	$rules['exam_type'] = 'required|in:revision,exam,mock';

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$query = \App\Models\Chapter::with('course_details')->where('course_id',$request->json()->get('course_id'))->where('status','Active')->whereHas('question_data',function($query) use($request){

	// 			if($request->json()->get('type') == 'recall'){
	// 				$query->where('recall','1');
	// 			}

	// 			if ($request->json()->get('type') == 'unanswered') {

	// 				$userUnansweredQuestion=\App\Models\User_unanswered_question_data::where('user_id',$request->user()->id)->where('course_id',$request->json()->get('course_id'))->where('exam_type',$request->json()->get('exam_type'))->first();

	// 				$questionIds=[];
	// 				if($userUnansweredQuestion && $userUnansweredQuestion->question_data){

	// 					$questionIdsData=json_decode($userUnansweredQuestion->question_data, true);

	// 					if(!empty($questionIdsData)){

	// 						$questionIds=$questionIdsData;
	// 					}
	// 				}

	// 				if(!empty($questionIds)){
	// 					$query->whereIn('id',$questionIds);
	// 				}else{

	// 					$query->where('id','0');
	// 				}
	// 			}


	// 			if ($request->json()->get('type') == 'flagged') {

	// 				$query->whereHas('flagQuestions', function($query1) use ($request) {
	// 					$query1->where('user_id', $request->user()->id);
	// 				});

	// 			}

	// 		});

	// 		$chapterResult = $query->get();

	// 		$result = [];

	// 		if($chapterResult->count()>0){

	// 			foreach($chapterResult as $chapter){

	// 				$result[] = [
	// 					"id" => $chapter->id,
	// 					"course_name" => $chapter->course_details->name ?? "NA",
	// 					"chapter_name"=> ucfirst($chapter->name)
	// 				];
	// 			}

	// 		}
	// 		return response(array( "message" => "Chapter fetched successfully.","result" => $result), 200);
	// 	}

	// }

	// public function zoomList(Request $request){

	// 	$rules['course_id'] = 'exists:zoom_classes,course_id|required';
	// 	$rules['batch_id'] = 'exists:zoom_classes,batch_id|required';
	// 	$rules['topic_id'] = 'exists:zoom_classes,topic_id|required';

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$query = \App\Models\ZoomClasses::with('course_details','batch_data','topic_data')->where('course_id',$request->json()->get('course_id'))->where('batch_id',$request->json()->get('batch_id'))->where('topic_id',$request->json()->get('topic_id'))->orderBy('sort_order','DESC');

	// 		$zoomResult=$query->get();

	// 		$result = [];

	// 		if($zoomResult->count()>0){

	// 			foreach($zoomResult as $zoom){

	// 				$result[] = [
	// 					"id" => $zoom->id,
	// 					"course_name" => $zoom->course_details->name ?? "NA",
	// 					"batch_name"=> $zoom->batch_data->name ?? "NA",
	// 					"topic_name"=> $zoom->topic_data->name ?? "NA",
	// 					"start_time" => $zoom->start_time,
	// 					"duration" => $zoom->duration,
	// 					"meeting_id" => $zoom->meeting_id,
	// 					"join_url" => $zoom->join_url,
	// 					"start_url" => $zoom->start_url,
	// 					"password" => $zoom->password,
	// 				];
	// 			}

	// 		}
	// 		return response(array( "message" => "Zoom List fetched successfully.","result" => $result), 200);

	// 	}
	// }

	// public function createExam(Request $request){

	// 	$rules['course_id'] = 'exists:questions,course_id|required';
	//     $rules['type'] = 'required|in:revision,exam,mock';
	//     $rules['question_type'] = 'required|in:all,unanswered,flagged,recall';
	//     $rules['pause_countinue'] = 'required|in:0,1';

	// 	if($request->json()->get('pause_countinue')=='1'){

	// 		$rules['exam_id'] = 'exists:user_exams,id|required';
	// 	}

	// 	if($request->json()->get('type')=='exam'){
	// 		$rules['limit'] = 'required|min:1';
	// 	}

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{
	// 		$offset=0;

	// 		if($request->json()->get('pause_countinue')=='1'){

	// 			$pauseData = \App\Models\UserExam::where('id',$request->json()->get('exam_id'))->where('pause_count','>','0')->first();

	// 			if (!$pauseData) {
	// 				return response(array( "message" => "Not found."), 400);
	// 			}
	// 			$offset = $pauseData->pause_question;
	// 		}

	// 		$query = \App\Models\Question::with(['course_details', 'chapter_data','flagQuestions'=>function($query) use($request){
	// 			$query->where('user_id',$request->user()->id);
	// 		}]);

	// 		if($request->json()->get('type')=='mock'){

	// 			$query->where('exam_type','Mock Exam');

	// 		}else{

	// 			$query->where('exam_type','Revision/Exam');

	// 		}

	// 		$query->where('course_id', $request->json()->get('course_id'));

	// 		if($request->json()->get('chapter_id') && count($request->json()->get('chapter_id'))){
	// 			$query->whereIn('chapter_id',$request->json()->get('chapter_id'));
	// 		}

	// 		$query->offset($offset);

	// 		$limit=null;
	// 		if ($request->json()->get('type') == 'revision') {

	// 			$query->limit('90');
	// 			$limit='90';

	// 		} elseif ($request->json()->get('type') == 'exam') {

	// 			$query->limit($request->json()->get('limit'));
	// 			$limit=$request->json()->get('limit');

	// 		} elseif ($request->json()->get('type') == 'mock') {

	// 			$query->limit(120);
	// 			$limit=120;
	// 		}

	// 		if ($request->json()->get('question_type') == 'recall') {
	// 			$query->where('recall', '1');
	// 		}

	// 		if ($request->json()->get('question_type') == 'flagged') {

	// 			$query->whereHas('flagQuestions', function($query) use ($request) {
	// 				$query->where('user_id', $request->user()->id);
	// 			});

	// 		}

	// 		if ($request->json()->get('question_type') == 'unanswered') {

	// 			$userUnansweredQuestion=\App\Models\User_unanswered_question_data::where('user_id',$request->user()->id)->where('course_id',$request->json()->get('course_id'))->where('exam_type',$request->json()->get('type'))->first();

	// 			$questionIds=[];
	// 			if($userUnansweredQuestion && $userUnansweredQuestion->question_data){

	// 				$questionIdsData=json_decode($userUnansweredQuestion->question_data, true);

	// 				if(!empty($questionIdsData)){

	// 					$questionIds=$questionIdsData;
	// 				}
	// 			}

	// 			if(!empty($questionIds)){
	// 				$query->whereIn('id',$questionIds);
	// 			}else{

	// 				$query->where('id','0');
	// 			}
	// 		}

	// 		$questionResult = $query->get();

	// 		$result = [];

	// 		if($questionResult->count()>0){

	// 			// create User Exam
	// 			$initateExamId=0;
	// 			if($request->json()->get('pause_countinue')=='0') {

	// 				$userExam=new \App\Models\UserExam();
	// 				$userExam->user_id=$request->user()->id;
	// 				$userExam->exam_type=$request->json()->get('type');
	// 				$userExam->exam_question_type=$request->json()->get('question_type');
	// 				$userExam->course_id=$request->json()->get('course_id');
	// 				if($request->json()->get('chapter_id') && count($request->json()->get('chapter_id'))){
	// 					$userExam->chapter_id=implode(',',$request->json()->get('chapter_id'));
	// 				}
	// 				$userExam->question_limit=$limit;

	// 			}else if($request->json()->get('pause_countinue')=='1'){

	// 				$initateExamId=(int) $request->json()->get('exam_id');

	// 			}
	// 			$ids =[];

	// 			foreach ($questionResult as $question) {
	// 				$ids[] = $question->id;

	// 				$result[] = [
	// 					"id" => $question->id,
	// 					"course_name" => $question->course_details->name ?? "NA",
	// 					"chapter_name" => $question->chapter_data->name ?? "NA",
	// 					"question" => $question->question,
	// 					"option1" => $question->option1,
	// 					"option2" => $question->option2,
	// 					"option3" => $question->option3,
	// 					"option4" => $question->option4,
	// 					"option5" => $question->option5,
	// 					"right_option" => $question->right_option,
	// 					"explaination" => $question->explaination,
	// 					"flag_marked" => $question['flag_questions'] ? 'Yes' : 'No'
	// 				];
	// 			}
	// 			$userExam->questions=implode(',', $ids);
	// 			$userExam->save();
	// 			$initateExamId=$userExam->id;


	// 			return response(array( "message" => "Questions fetched successfully.","result" => $result,"initateExamId"=>$initateExamId), 200);

	// 		}else{

	// 			return response(array( "message" => "Questions not found.","result" => []), 400);
	// 		}

	// 	}
	// }

	// public function submitQuestion(Request $request){

	// 	if($request->json()->get('exam_id')){

	// 		$rules['exam_id']='required|numeric';

	// 	}else{

	// 		$rules = [
	// 			'*.question_id' => 'required|numeric|exists:questions,id',
	// 			'*.given_answer' => 'required|numeric|in:1,2,3,4,5',
	// 			'*.exam_id' => 'required|numeric',
	// 		];
	// 	}

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$data = $request->json()->all();

	// 		if($request->json()->get('exam_id')){

	// 			$data[0]['exam_id']=$request->json()->get('exam_id');

	// 		}

	// 		$userExamResult=\App\Models\UserExam::where('id',$data[0]['exam_id'])->first();

	// 		if(!$userExamResult){

	// 			return response(array( "message" => "Exam not found."), 400);
	// 		}

	// 		if($data[0]['exam_id']>0){

	// 			\App\Models\UserExamData::where('user_id',$request->user()->id)->where('exam_id', $data[0]['exam_id'])->delete();
	// 		}

	// 		if(!$request->json()->get('exam_id')){
	// 			$uniqueQuestionIds = collect($data)->pluck('question_id')->unique();
	// 			if (count($uniqueQuestionIds) !== count($data)) {
	// 				return response(['error' => true, 'message' => 'Duplicate question_ids found.'], 200);
	// 			}
	// 		}

	// 		$checkUserCourseUnAns=\App\Models\User_unanswered_question_data::where('user_id',$request->user()->id)->where('course_id',$userExamResult->course_id)->where('exam_type',$userExamResult->exam_type)->first();

	// 		$userExamQuestionsArray=[];

	// 		if($checkUserCourseUnAns && $userExamResult->exam_question_type=='unanswered'){

	// 			$userExamQuestions=json_decode($checkUserCourseUnAns->question_data, true);

	// 		}else{

	// 			$userExamQuestions=explode(',',$userExamResult->questions);
	// 		}

	// 		if(!empty($userExamQuestions)){
	// 			foreach($userExamQuestions as $question){
	// 				$userExamQuestionsArray[$question]=$question;
	// 			}
	// 		}

	// 		if(!$request->json()->get('exam_id')){

	// 			foreach ($data as $item) {

	// 				$rightAnswer=\App\Models\Question::where('id',$item['question_id'])->first();

	// 				\App\Models\UserExamData::create([
	// 					'user_id' => $request->user()->id,
	// 					'exam_id' => $item['exam_id'] ? $item['exam_id'] : null,
	// 					'question_id' => $item['question_id'],
	// 					'given_answer' => $item['given_answer'],
	// 					'right_answer' => $rightAnswer['right_option'],
	// 				]);

	// 				if(isset($userExamQuestionsArray[$item['question_id']])){

	// 					unset($userExamQuestionsArray[$item['question_id']]);
	// 				}
	// 			}
	// 		}

	// 		if(!$checkUserCourseUnAns){
	// 			$checkUserCourseUnAns=new \App\Models\User_unanswered_question_data();
	// 			$checkUserCourseUnAns->exam_type=$userExamResult->exam_type;
	// 			$checkUserCourseUnAns->user_id=$request->user()->id;
	// 			$checkUserCourseUnAns->course_id=$userExamResult->course_id;

	// 		}else{

	// 			if($checkUserCourseUnAns->question_data && $userExamResult->exam_question_type!='unanswered'){

	// 				$userExamQuestionsArray=array_merge($userExamQuestionsArray, json_decode($checkUserCourseUnAns->question_data, true));

	// 			}
	// 		}

	// 		$checkUserCourseUnAns->question_data=json_encode(array_unique($userExamQuestionsArray));
	// 		$checkUserCourseUnAns->save();


	// 		$chapterId=explode(',',$userExamResult->chapter_id);
	// 		$questionId=explode(',',$userExamResult->questions);

	// 		\App\Models\UserExam::where('id',$data[0]['exam_id'])->update(['status'=>'Completed']);

	// 		$getQuestionQuery=\App\Models\Question::with(['userExamData'=>function($query) use($request, $data){
	// 			$query->where('user_id',$request->user()->id);
	// 			$query->where('exam_id',$data[0]['exam_id']);
	// 		},'flagQuestions'=>function($query) use($request){
	// 			$query->where('user_id',$request->user()->id);
	// 		}]);

	// 		if($userExamResult->exam_type=='mock'){

	// 			$getQuestionQuery->where('exam_type','Mock Exam');

	// 		}else{

	// 			$getQuestionQuery->where('exam_type','Revision/Exam');

	// 		}

	// 		if(!empty($chapterId) && $chapterId[0]!=''){

	// 			$getQuestionQuery->whereIn('chapter_id',$chapterId);
	// 		}
	// 		if(!empty($questionId) && $questionId[0]!=''){

	// 			$getQuestionQuery->whereIn('id',$questionId);
	// 		}

	// 		$getQuestionQuery->where('course_id',$userExamResult['course_id'])->orderBy('id','ASC');

	// 		if($data[0]['exam_id']>0){

	// 			$chapterIds=$userExamResult->chapter_id;
	// 			$chapterIdArray=[];
	// 			if($chapterIds){
	// 				$chapterIdArray=explode(',',$chapterIds);
	// 			}

	// 			if(!empty($chapterIdArray) && $chapterIdArray[0]!=''){

	// 				$getQuestionQuery->whereIn('chapter_id',$chapterIdArray);
	// 			}
	// 		}

	// 		$getQuestionData=$getQuestionQuery->get();

	// 		return response(array( "message" => "Question Answer submitted successfully.",'userreport'=>$getQuestionData->toArray()), 200);

	// 	}
	// }

	// public function submitFlagUserQuestion(Request $request){

	// 	$rules = [
	// 		'*.question_id' => 'required|numeric|exists:questions,id',
	// 		'*.course_id' => 'required|numeric|exists:courses,id',
	// 	];

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {

	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$data = $request->json()->all();

	// 		if(empty($data)){
	// 			return response(array("message"=>'No flag question found.'),200);
	// 		}

	// 		\App\Models\Userflagquestion::where('user_id',$request->user()->id)->where('course_id', $data[0]['course_id'])->delete();


	// 		foreach ($data as $item) {

	// 			\App\Models\Userflagquestion::create([
	// 				'user_id' => $request->user()->id,
	// 				'course_id' => $item['course_id'],
	// 				'question_id' => $item['question_id']
	// 			]);
	// 		}

	// 		return response(array( "message" => "Question Flagged submit successfully."), 200);

	// 	}
	// }

	// public function demoVideo(Request $request){

	// 	$demoVideo = \App\Models\DemoVideo::where('status','Active');

	// 	if($request->json()->get('course_id')){
	// 		$demoVideo->where('course_id',$request->json()->get('course_id'));
	// 	}

	// 	$video = $demoVideo->get();

	// 	$result = [];

	// 	if($video->count()>0){

	// 		foreach($video as $course){

	// 			$result[] = [
	// 				"id" => $course->id,
	// 				"course_id" => $course->course_id,
	// 				"title"=> $course->title,
	// 				"video"=> $course->video_link
	// 			];
	// 		}

	// 	}
	// 	return response(array( "message" => "Course fetched successfully.","result" => $result), 200);


	// }

	// public function examPauseContinue(Request $request){
	// 	$rules = [
	// 		'exam_id' => 'required|numeric|exists:user_exams,id',
	// 		'answer_data' => 'required|array',
	//         'pause_timing' => 'required',
	// 		'pause_question' => 'required|numeric',
	// 	];

	// 	$validator = Validator::make($request->json()->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$message = [];
	// 		$messages_l = json_decode(json_encode($validator->messages()), true);
	// 		foreach ($messages_l as $msg) {
	// 			$message= $msg[0];
	// 			break;
	// 		}

	// 		return response(array("error"=> true, "message"=>$message),200);

	// 	}else{

	// 		$exam_data = \App\Models\UserExam::where('id',$request->json()->get('exam_id'))->first();

	// 		if (!$exam_data) {

	// 			return response(array( "message" => "Exam not found."), 400);
	// 		}

	// 		if ($exam_data->pause_count === 4) {

	// 			return response(array("error"=>true,"message" => 'You cannot pause the exam more than 4 times'), 400);
	// 		}

	// 		$encodedAnswerData = json_encode($request->json()->get('answer_data'));

	// 		$exam_data->answer_data=$encodedAnswerData;
	// 		$exam_data->pause_timing=$request->json()->get('pause_timing');
	// 		$exam_data->pause_question=$request->json()->get('pause_question');
	// 		$exam_data->pause_count += 1;
	// 		$exam_data->save();

	// 		return response(array( "error"=>false, "message" => "Exam paused successfully.",'data'=>$exam_data->toArray()), 200);
	// 	}
	// }

	// public function pauseContinueExamList(Request $request){

	// 	$examList = \App\Models\UserExam::with('user_data')->where('pause_count','>','0')->where('status','Pending')->get();

	// 	$result = [];

	// 	if($examList->count()>0){

	// 		foreach($examList as $exam){
	// 			$chapterNames = '';
	// 			$chapterIds = explode(',', $exam->chapter_id);

	// 			$chapters = \App\Models\Chapter::whereIn('id', $chapterIds)->pluck('name')->toArray();

	// 			if ($chapters) {
	// 				$chapterNames=implode(',' , $chapters);
	// 			}
	// 			$result[] = [
	// 				"exam_id" => $exam->id,
	// 				"user_name"=>$exam->user_data->name ?? "NA",
	// 				"exam_type"=>$exam->exam_type,
	// 				"chapter"=>$chapterNames,
	// 				"question_limit"=>$exam->question_limit,
	// 				"pause_count"=>$exam->pause_count,
	// 				"answer_data"=>$exam->answer_data,
	// 				"pause_timing"=>$exam->pause_timing,
	// 				"pause_question"=>$exam->pause_question,

	// 			];
	// 		}

	// 	}
	// 	return response(array( "message" => "Pause Exam List fetched successfully.","result" => $result), 200);


	// }
}
