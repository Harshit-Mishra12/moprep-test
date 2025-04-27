<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\ZoomClasses;
use DB;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use App\Library\Zoom_Api;

class ZoomClassesController extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules = [
				'course_id' => 'required',
				'batch_id' => 'required',
				'topic_id' => 'required',
				'start_time' => 'required',
				'duration' => 'required|numeric|min:1',
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

						$zoomClass = ZoomClasses::find($request->post('id'));
					} else {

						$zoomClass = new ZoomClasses();
					}

					$topicName = \App\Models\TopicMaterials::where('id', $request->post('topic_id'))->where('status', '1')->orderBy('id', 'DESC')->first();

					$zoom_meeting = new Zoom_Api();
					$date = date("Y-m-d\TH:i:s", strtotime($request->start_time));

					$data = array();
					$data['topic'] 		= $topicName->topic;
					$data['start_date'] = $date;
					$data['duration'] 	= $request->duration;
					$data['type'] 		= 2;

					$response = $zoom_meeting->createMeeting($data);

					$zoomClass->meeting_id = $response->id;
					$zoomClass->join_url = $response->join_url;
					$zoomClass->start_url = $response->start_url;
					$zoomClass->password = $response->password;

					$zoomClass->batch_id = $request->post('batch_id');
					$zoomClass->course_id = $request->post('course_id');
					$zoomClass->topic_id = $request->post('topic_id');
					$zoomClass->start_time = $request->post('start_time');
					$zoomClass->duration = $request->post('duration');


					$zoomClass->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Zoom Classes updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Zoom Classes added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$course = \App\Models\Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.zoom_classes.add', compact('result', 'course'));
	}

	public function zoomClassesList()
	{

		$result = \App\Models\ZoomClasses::orderBy('id', 'DESC')->get();
		return view('admin.zoom_classes.list', compact('result'));
	}

	public function changeStatus(Request $request)
	{

		ZoomClasses::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Zoom Classes status changed successfully.'), 200);
	}

	public function zoomClassesUpdate(Request $request, $id)
	{

		$result = ZoomClasses::find($id);

		if ($result) {
			$course = \App\Models\Course::where('status', '1')->orderBy('id', 'DESC')->get();
			$batchName = \App\Models\Batch::where('name', $request->post('batch_id'))->where('status', '1')->get();
			return view('admin.zoom_classes.add', compact('result', 'course', 'batchName'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	protected $zoomApi;

	public function __construct(Zoom_Api $zoomApi)
	{
		$this->zoomApi = $zoomApi;
	}
	public function zoomClassesDelete(Request $request, $id)
	{

		$result = ZoomClasses::find($id);
		if ($result) {

			$meetingID = $result->meeting_id;

			ZoomClasses::where('id', $id)->delete();
			$this->zoomApi->deleteMeeting($meetingID);

			return redirect()->back()->with('5fernsadminsuccess', 'Zoom Classes deleted successfully.');
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function zoomClassesGetBatchName(Request $request)
	{
		$batchData = \App\Models\Batch::where('course_id', $request->post('course_id'))
			->where('status', '1')
			->orderBy('id', 'DESC')
			->get();

		if ($batchData->isNotEmpty()) {
			$batchNames = $batchData->map(function ($batch) {
				return [
					'id' => $batch->id,
					'name' => $batch->name,
				];
			})->toArray();


			return response(['message' => 'Batch data fetched successfully.', 'batchData' => $batchNames], 200);
		} else {
			return response(['message' => 'No batch data found.'], 200);
		}
	}

	public function zoomClassesGetTopicName(Request $request)
	{

		$topicData = \App\Models\TopicMaterials::where('batch_id', $request->post('batch_id'))
			->where('status', '1')
			->orderBy('id', 'DESC')
			->get();

		if ($topicData->isNotEmpty()) {
			$topicsNames = $topicData->map(function ($topic) {
				return [
					'id' => $topic->id,
					'name' => $topic->topic,
				];
			})->toArray();

			return response(['message' => 'Topic data fetched successfully.', 'topicData' => $topicsNames], 200);
		} else {
			return response(['message' => 'No topic data found.'], 200);
		}
	}
}
