<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;
use DB;
use Validator;
use App\Models\Event;
use Hash;
use Stripe;
use Carbon\Carbon;
use App\Models\CourseMaster;

class PostLoginControllerV2 extends Controller
{

	public function courseMasterList(Request $request)
	{
		$title = $request->post('title');
		$course_data = CourseMaster::where('name', 'like', '%' . $title . '%')->where('status', '1')->where('is_live', '1')->orderBy('id', 'DESC')->get();

		$courseResult = [];
		if (!empty($course_data)) {
			foreach ($course_data as $course) {
				$tResult = [
					'course_master_id' => $course->id,
					'name' => ucfirst($course->name),
					'image' => asset('uploads/coursemaster/' . $course->image),
					'description' => $course->description
				];
				$courseResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $courseResult], 200);
	}

	public function questionBankAnswers(Request $request)
	{
		$user_id = $request->user()->id;
		$question_id = $request->post('question_id');
		$course_id = $request->post('course_id');
		$course_master_id = $request->post('course_master_id',);
		$chapter_id = $request->post('chapter_id');
		$topic_id = $request->post('topic_id');
		$user_answer = $request->post('user_answer');


		$questionData = \App\Models\Question::where([
			'questions.id' => $question_id,
			'course_master_id' => $course_master_id,
			'course_id' => $course_id,
			'chapter_id' => $chapter_id,
			'topic_id' => $topic_id,
			'questions.is_live' => 1
		])
			->join('bulk_questions', 'questions.question_id', '=', 'bulk_questions.id')
			->select('questions.*', 'bulk_questions.id as bulk_id', 'bulk_questions.right_option', 'bulk_questions.explaination')
			->first();

		if ($questionData) {
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
				"course_master_id" => (int) $course_master_id,
				"course_id" => (int) $course_id,
				"chapter_id" => (int) $chapter_id,
				"topic_id" => (int) $topic_id,
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
					'course_id' => $course_id,
					'course_master_id' => $course_master_id,
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
		} else {
			return response(["error" => false, "message" => "Answer not found.", "result" => []], 200);
		}
	}

	public function uncompletedQuestionBank(Request $request)
	{
		$user_id = $request->user()->id;

		if ((int) $request->course_master_id <= 0) {
			return response([
				"error" => true,
				"message" => "No Course found.",
				"result" => []
			], 400);
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
			->where('course_map_masters.course_master_id', $request->course_master_id)
			->groupBy('courses.id', 'course_map_masters.course_master_id')
			->having('otherexam_count', '>', 0)
			->orderByRaw('CAST(courses.sort_order AS UNSIGNED) ASC') // Primary sort by sort_order (numeric)
			->orderBy('courses.id', 'ASC') // Secondary sort by ID
			->get();

		if ($questions->isEmpty()) {
			return response([
				"error" => true,
				"message" => "No data found.",
				"result" => []
			], 400);
		}

		$result = [];

		foreach ($questions as $question) {
			$completed_questions = DB::table('question_bank_answers')
				->where([
					"user_id" => $user_id,
					"subject_id" => $question->id,
					"course_master_id" => $request->course_master_id
				])
				->count();

			// Only include courses with incomplete questions
			if ($question->otherexam_count - $completed_questions > 0) {
				$result[] = [
					"id" => $question->id,
					"name" => ucfirst($question->name),
					"description" => ucfirst($question->description),
					"image" => asset('uploads/course/' . $question->image),
					"total_questions" => $question->otherexam_count,
					"completed_questions" => $completed_questions,
					"left_questions" => $question->otherexam_count - $completed_questions,
				];
			}
		}

		if (empty($result)) {
			return response([
				"error" => true,
				"message" => "No incomplete courses found.",
				"result" => []
			], 400);
		}

		return response([
			"error" => false,
			"message" => "Data fetched successfully.",
			"result" => $result
		], 200);
	}
	// 	public function uncompletedQuestionBank(Request $request)
	// 	{
	// 		$user_id = $request->user()->id;

	// 		if ((int) $request->course_master_id <= 0) {
	// 			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
	// 		}

	// 		$questions = Course::join('course_map_masters', 'courses.id', '=', 'course_map_masters.course_id')
	// 			->leftJoin('questions', function ($join) {
	// 				$join->on('courses.id', '=', 'questions.course_id')
	// 					->whereColumn('course_map_masters.course_master_id', '=', 'questions.course_master_id')
	// 					->where('questions.is_live', 1); // Only count live questions
	// 			})
	// 			->select(
	// 				'courses.*',
	// 				'course_map_masters.course_master_id',
	// 				DB::raw('COUNT(questions.id) as otherexam_count') // Count only live questions
	// 			)
	// 			->where('courses.status', '1')
	// 			->where('courses.is_live', '1')
	// 			->where('course_map_masters.course_master_id', $request->course_master_id)
	// 			->groupBy('courses.id', 'course_map_masters.course_master_id')
	// 			->having('otherexam_count', '>', 0)
	// 			->orderBy('courses.id', 'DESC')
	// 			->get();


	// 		if ($questions->count() == 0) {
	// 			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
	// 		}

	// 		$result = [];

	// 		foreach ($questions as $question) {
	// 			$completed_questions = DB::table('question_bank_answers')->where(["user_id" => $user_id, "subject_id" => $question->id, "course_master_id" => $request->course_master_id])->count();
	// 			if ($question->otherexam_count - $completed_questions > 0) {
	// 				$data = [
	// 					"id" => $question->id,
	// 					"name" => ucfirst($question->name),
	// 					"description" => ucfirst($question->description),
	// 					"image" => asset('uploads/course/' . $question->image),
	// 					"total_questions" => $question->otherexam_count,
	// 					"completed_questions" => $completed_questions,
	// 					"left_questions" => $question->otherexam_count - $completed_questions,
	// 				];

	// 				$result[] = $data;
	// 			}
	// 		}

	// 		if (count($result) == 0) {
	// 			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
	// 		}

	// 		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	// 	}

	public function upcomingMockTests(Request $request)
	{
		$user_id = $request->user()->id;
		$currentDateTime = now()->format('Y-m-d H:i');

		if ((int) $request->course_master_id <= 0) {
			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
		}

		//   $questions = \App\Models\Mockup::whereRaw("
		//     STR_TO_DATE(CONCAT(start_date, ' ', start_time), '%Y-%m-%d %H:%i:%s') > ?",
		//     [$currentDateTime]
		// )->where('status', 'Active')
		// ->where('course_master_id', $request->course_master_id)
		// ->orderBy('id', 'DESC')
		// ->get();

		$questions = \App\Models\Mockup::whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$currentDateTime])
			->where('status', 'Active')
			->where('course_master_id', $request->course_master_id)
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

	public function questionBank(Request $request)
	{
		$user_id = $request->user()->id;

		if ((int) $request->course_master_id <= 0) {
			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
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
			->where('course_map_masters.course_master_id', $request->course_master_id)
			->groupBy('courses.id', 'course_map_masters.course_master_id')
			->having('otherexam_count', '>', 0)
			->orderBy('courses.id', 'DESC')
			->get();





		if ($questions->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];

		foreach ($questions as $question) {

			$completed_questions = $completed_questions = DB::table('question_bank_answers')->where(["user_id" => $user_id, "subject_id" => $question->id, 'course_master_id' => $request->course_master_id])->count();
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

	public function questionBankSearch(Request $request)
	{
		$title = $request->post('title');
		$course_master_id = $request->post('course_master_id');

		if (!$course_master_id || (int) $course_master_id <= 0) {
			return response([
				"error" => true,
				"message" => "No Course found.",
				"result" => []
			], 400);
		}

		$topic_data = \App\Models\TopicMaterials::select(
			'topic_materials.id as topic_id',
			'topic_materials.course_id',
			'topic_materials.course_master_id',
			'topic_materials.topic',
			'chapters.id as chapter_id',
			'chapters.name as chapter_name'
		)
			->join('chapters', 'chapters.id', '=', 'topic_materials.batch_id')
			->where('topic_materials.status', '1')
			->where('topic_materials.is_live', '1')
			->where(function ($query) use ($title) {
				$query->where('topic_materials.topic', 'like', '%' . $title . '%')
					->orWhere('chapters.name', 'like', '%' . $title . '%');
			})
			->whereExists(function ($query) use ($course_master_id) {
				$query->select(DB::raw(1))
					->from('questions')
					->whereRaw('questions.topic_id = topic_materials.id')
					->where('questions.course_master_id', $course_master_id) // Ensure it's tagged correctly
					->where('questions.is_live', '1');
			});

		if ($request->post('subject_id')) {
			$topic_data->where('topic_materials.course_id', $request->post('subject_id'));
		}

		$topic_data = $topic_data->get();

		if ($topic_data->isEmpty()) {
			return response([
				"error" => true,
				"message" => "No data found.",
				"result" => []
			], 400);
		}

		$topicResult = [];
		foreach ($topic_data as $topic) {
			$total_questions = \App\Models\Question::where([
				'course_master_id' => $course_master_id, // Added condition
				'course_id' => $topic->course_id,
				'chapter_id' => $topic->chapter_id,
				'topic_id' => $topic->topic_id,
				'is_live' => 1
			])->count();

			$topicResult[] = [
				'course_master_id' => $topic->course_master_id,
				'subject_id' => $topic->course_id,
				'chapter_id' => $topic->chapter_id,
				'topic_id' => $topic->topic_id,
				'topic_name' => ucfirst($topic->chapter_name) . ' - ' . ucfirst($topic->topic),
				'questions' => $total_questions
			];
		}
	}


	public function mockTestUpcomming(Request $request)
	{
		$user_id = $request->user()->id;
		$course_master_id = $request->course_master_id;
		$currentDateTime = now()->format('Y-m-d H:i');


		$questions = \App\Models\Mockup::whereRaw("CONCAT(end_date, ' ', end_time) >= ?", [$currentDateTime])
			->where('status', 'Active')
			->where('course_master_id', $course_master_id)
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
				"is_lock" => $question->is_lock,
				"live_on" => date('d F', strtotime($question->start_date)) . '-' . date('h:i A', strtotime($question->start_time)),
				"end_on" => date('d F', strtotime($question->end_date)) . '-' . date('h:i A', strtotime($question->end_time)),
				"image" => asset('uploads/banner/' . $question->image),
				"active_status" => $active_status
			];

			$result[] = $data;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}



	public function subscriptionPlansList(Request $request)
	{
		$user_id = $request->user()->id;

		if ((int) $request->course_master_id <= 0) {
			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
		}

		$checkFreeTrial = DB::table('transactions')->where(['user_id' => $user_id, 'status' => '1', 'course_master_id' => $request->course_master_id])->count();

		// $subData = \App\Models\Subscription::orderBy('id','DESC')->where('status','Active');
		$subData = \App\Models\Subscription::orderBy('id', 'DESC')->where('status', 'Active')->where('course_master_id', $request->course_master_id);

		if ($checkFreeTrial > 0) {
			$subData->where('price', '!=', '0');
		}
		$subData = $subData->get();
		// dd($subData);
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


	public function previousYearTopics(Request $request)
	{
		$user_id = $request->user()->id;
		$course_master_id = $request->course_master_id;
		$yearData = DB::table('bulk_question_years')->where('status', '1')->where('course_master_id', $course_master_id)->orderBy('year', 'DESC')->groupBy('year')->get();

		$result = [];
		$i = 1;
		$z = 1;
		if (!empty($yearData)) {
			foreach ($yearData as $year) {

				$partData = DB::table('bulk_question_years')->where('status', '1')->where('is_live', 1)->where('course_master_id', $course_master_id)->where('year', $year->year)->get();
				// 	dd($partData);
				$yearResult = [];
				if (!empty($partData)) {
					foreach ($partData as $part) {
						$question_count = DB::table('bulk_questions')->where('is_live', 1)->where('year_id', $part->id)->count();
						$attempted_question = DB::table('previous_year_answers')->where('part_id', $part->id)->where('user_id', $user_id)->count();

						$pause_status = "start";
						if ($attempted_question == 0) {
							$pause_status = "start";
						} else if ($attempted_question > 0 && $attempted_question < $question_count) {
							$pause_status = "resume";
						} else if ($attempted_question == $question_count) {
							$pause_status = "complete";
						}
						if ($question_count > 0) {
							$yResult = [
								'part_id' => $part->id,
								'is_lock' => $part->is_lock,
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
				}
				if (!empty($yearResult)) {
					$data = [
						"year_id" => $year->id,
						"year_name" => sprintf('%02d', $i) . ' - ' . ucfirst($year->year),
						"year_data" => $yearResult
					];

					$result[] = $data;
					$i++;
				}
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}


	public function previousYearSearch(Request $request)
	{
		$title = $request->post('title');
		$course_master_id = $request->course_master_id;
		$topic_data = DB::table('bulk_question_years')
			->where(function ($query) use ($title) {
				$query->where('part', 'like', '%' . $title . '%')
					->orWhere('year', 'like', '%' . $title . '%');
			})
			->where('course_master_id', $course_master_id)
			->where('status', '1')
			->where('is_live', '1')
			->orderBy('id', 'DESC')
			->get();

		if ($topic_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$topicResult = [];
		if (!empty($topic_data)) {
			foreach ($topic_data as $topic) {
				$questions = DB::table('bulk_questions')->where('year_id', $topic->id)->count();
				$tResult = [
					'part_id' => $topic->id,
					'is_lock' => $topic->is_lock,
					'part_name' => ucfirst($topic->part) . " ( " . $topic->year . " )",
					'questions' => $questions
				];

				$topicResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $topicResult], 200);
	}

	public function subjectList(Request $request)
	{
		$title = $request->post('title');


		if ((int) $request->course_master_id <= 0) {
			return response(array("error" => true, "message" => "No Course found. ", "result" => array()), 400);
		}

		$subject_data = Course::where('name', 'like', '%' . $title . '%')->where('status', '1')->where('course_master_id', $request->course_master_id)->orderBy('id', 'DESC')->get();

		if ($subject_data->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$subjectResult = [];
		if (!empty($subject_data)) {
			foreach ($subject_data as $subject) {

				$tResult = [
					'course_id' => $subject->id,
					'name' => ucfirst($subject->name),
					// 'questions' => $subject->otherexam_count
				];

				$subjectResult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $subjectResult], 200);
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
				// dd($subscriptionData);
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
					'status' => $subscription_status,
					'course_master_id' => $request->course_master_id
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


	public function checkUserSubscription(Request $request)
	{
		$user_id = $request->user()->id;

		$currentDateTime = Carbon::now();

		$cond = [
			"user_id" => $user_id,
			"transactions.status" => "1"
		];

		$subscriptionStatus = DB::table('transactions')->where($cond)->where('start_date', '<=', $currentDateTime)->where('end_date', '>=', $currentDateTime)->count();
		$subscriptionData = DB::table('transactions')->select('c.name', 'transactions.*')->leftJoin('course_masters as c', 'c.id', '=', 'transactions.course_master_id')->where('user_id', $user_id)->where('start_date', '<=', $currentDateTime)->where('end_date', '>=', $currentDateTime)->get();
		$data = array();
		foreach ($subscriptionData as $k => $s) {
			$data[$k]['course_master_id'] = $s->course_master_id;
			$data[$k]['course_name'] = $s->name;
			$data[$k]['subscription_expiry'] = $s->end_date;
			$data[$k]['status'] = $s->status;
		}
		//dd($subscriptionData);

		if ($subscriptionStatus > 0) {
			return response(["error" => false, "subscription_status" => 1, "subscribed_courses" => $data, "message" => "Subscription is active."], 200);
		} else {
			return response(["error" => false, "subscription_status" => 0, "subscribed_courses" => $data, "message" => "Subscription is not active."], 200);
		}
	}



	public function mockTestResultData(Request $request)
	{
		$user_id = $request->user()->id;
		$currentDateTime = now()->format('Y-m-d H:i:s');
		$type = $request->type == 'attempted' ? 'attempted' : 'not_attempted';
		$course_master_id = $request->course_master_id ?? 0;

		if ($request->type == 'attempted') {
			$bookmarked = DB::table('mockup_test_result')
				->where('user_id', $user_id)
				->join('mockups', function ($join) use ($currentDateTime, $course_master_id) {
					$join->on('mockups.id', '=', 'mockup_test_result.mock_id')
						// 		->whereRaw("CONCAT(mockups.end_date, ' ', mockups.end_time, ':00') < ?", [$currentDateTime])
						->where('course_master_id', $course_master_id);
				})
				->select('mockups.*')
				->get();
		} else {
			$bookmarked = DB::table('mockups')
				->leftJoin('mockup_test_result', function ($join) use ($user_id) {
					$join->on('mockups.id', '=', 'mockup_test_result.mock_id')
						->where('mockup_test_result.user_id', '=', $user_id);
				})
				->whereNull('mockup_test_result.mock_id') // If null, means no entry in test result
				->where('course_master_id', $course_master_id)
				->whereRaw("CONCAT(mockups.end_date, ' ', mockups.end_time, ':00') < ?", [$currentDateTime])
				->select('mockups.*')
				->get();
		}

		if ($bookmarked->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$result = [];
		$i = 1;
		foreach ($bookmarked as $question) {

			$data = [
				"id" => $question->id,
				"end_date" => $question->end_date,
				"end_time" => $question->end_time,
				"s_no" => sprintf('%02d', $i),
				"name" => ucfirst($question->name)
			];

			$result[] = $data;
			$i++;
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $result], 200);
	}
}
