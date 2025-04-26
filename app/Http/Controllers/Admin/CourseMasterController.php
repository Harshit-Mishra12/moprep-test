<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\Chapter;
use \App\Models\Subject;
use App\Models\Course;
use App\Models\CourseMaster;
use Illuminate\Support\Str;
use DB;

class CourseMasterController extends Controller
{
	public function add(Request $request)
	{
		if ($request->isMethod('post')) {
			$rules['name'] = 'string|required|unique:course_masters,name,' . $request->post('id') . ',id,deleted_at,NULL';
			$rules['description'] = 'required';
			$rules['positive_mark'] = 'required|numeric|min:0';
			$rules['negative_mark'] = 'required|numeric|min:0';

			if ((int) $request->post('id') == 0) {
				$rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg';
			}

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
						$subject = CourseMaster::find($request->post('id'));
					} else {
						$subject = new CourseMaster();
						$subject->status = 0;
					}

					$filename = $request->post('old_image');

					if ($request->hasFile('image')) {
						$filename = \App\Helpers\commonHelper::uploadFile($request->file('image'), 'coursemaster');
					}

					$subject->image = $filename;
					$subject->name = $request->post('name');
					$subject->description = $request->post('description');
					$subject->slug = Str::slug($subject->name);
					$subject->positive_mark = $request->post('positive_mark');
					$subject->negative_mark = $request->post('negative_mark');

					$subject->save();

					if ((int) $request->post('id') > 0) {
						return response(array('message' => 'Course updated successfully.', 'reset' => false), 200);
					} else {
						return response(array('message' => 'Course added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {
					return response(array("message" => $e->getMessage()), 403);
				}
			}
		}

		$result = [];
		$courses = Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.coursemaster.add', compact('result', 'courses'));
	}


	public function courseList(Request $request)
	{
		$result = CourseMaster::orderBy('id', 'DESC');
		// if ($request->get('course_id') > 0) {
		// 	$result->where('course_id', $request->course_id);
		// }
		$result = $result->get();
		$course = CourseMaster::orderBy('id', 'DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['66']) ? $permission['66'] : null;
		return view('admin.coursemaster.list', compact('result', 'course', 'permission'));

		// $result = CourseMaster::orderBy('id', 'DESC')->get();
		// return view('admin.coursemaster.list', compact('result'));
	}

	public function viewCourse(Request $request, $id)
	{
		$result = Course::where('course_master_id', $id)->get();
		return view('admin.coursemaster.subject', compact('result'));
	}

	public function changeStatus(Request $request)
	{
		CourseMaster::where('id', $request->post('id'))->update(['status' => $request->post('status')]);
		return response(array('message' => 'Subject status changed successfully.'), 200);
	}

	public function updateCourse(Request $request, $id)
	{
		$result = CourseMaster::find($id);

		if ($result) {
			$courses = CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.coursemaster.add', compact('result', 'courses'));
		} else {
			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function deleteCourse(Request $request, $id)
	{
		$result = CourseMaster::find($id);

		if ($result) {
			DB::table('course_masters')->where('id', $id)->delete();
			DB::table('chapters')->where('course_master_id', $id)->delete();
			DB::table('topic_materials')->where('course_master_id', $id)->delete();
			DB::table('questions')->where('course_master_id', $id)->delete();

			return redirect()->back()->with('5fernsadminsuccess', 'Subject deleted successfully.');
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function changeOrder(Request $request)
	{
		$allData = $request->allData;
		$i = 1;
		foreach ($allData as $key => $value) {
			CourseMaster::where('id', $value)->update(array('sort_order' => $i));
			$i++;
		}
	}

	public function changeLive(Request $request)
	{
		DB::table('course_masters')
			->where('id', $request->post('id'))
			->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Course Live changed successfully.'], 200);
	}
}
