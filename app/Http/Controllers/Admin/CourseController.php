<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Validator;
use \App\Models\Course;
use \App\Models\CourseMaster;
use \App\Models\Chapter;
use App\Models\CourseMapMaster;
use Illuminate\Support\Str;
use DB;

class CourseController extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules['name'] = 'string|required|unique:courses,name,' . $request->post('id') . ',id,deleted_at,NULL';
			$rules['description'] = 'required';
			// $rules['course_master_id'] = 'required';

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

						$course = Course::find($request->post('id'));
					} else {

						$course = new Course();
					}
					$filename = $request->post('old_image');

					if ($request->hasFile('image')) {
						$filename = \App\Helpers\commonHelper::uploadFile($request->file('image'), 'course');
					}

					$course->image = $filename;
					// $course->course_master_id = $request->post('course_master_id');
					$course->name = $request->post('name');
					$course->description = $request->post('description');
					$course->slug = Str::slug($course->name);
					$course->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Course updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Course added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}
		
		$result=[];
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		return view('admin.course.add',compact('result', 'courses'));
    }
	public function map(Request $request)
	{
		
		if ($request->isMethod('post')) {
			$rules['course_master_id'] = 'required';
			$rules['course_id'] = 'required';

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

						$course_map = CourseMapMaster::find($request->post('id'));
					} else {

						$course = new CourseMapMaster();
					}
					$course->course_master_id = $request->post('course_master_id');
					$course->course_id = $request->post('course_id');
					$course->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Course Mapping updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Course Mapping added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}
		
			
		$result=[];
		$courses = Course::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		$coursesMaster = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		return view('admin.course.map',compact('result', 'courses','coursesMaster'));
    }
	public function subjectList()
	{
		$result = Course::with('CourseMaster')->orderBy('id', 'DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['4']) ? $permission['4'] : null;
		
		return view('admin.course.list', compact('result','permission'));
	}
	public function mapList()
	{
		$result = CourseMapMaster::with(['course:id,name', 'courseMaster:id,name'])
        ->orderBy('id', 'DESC')
        ->get();

		return view('admin.course.map_list', compact('result'));
	}
	
	public function viewSubject(Request $request,$id){		
		$result=Chapter::where('course_id',$id)->get();	
		return view('admin.course.chapters',compact('result'));
	}

	public function changeStatus(Request $request)
	{

		Course::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Course status changed successfully.'), 200);
	}

	public function updateSubject(Request $request, $id)
	{

		$result = Course::find($id);
		$courses = CourseMaster::where('status', '1')->where('deleted_at', NULL)->orderBy('sort_order', 'ASC')->get();
		if ($result) {

			return view('admin.course.add', compact('result','courses'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function deleteSubject(Request $request, $id)
	{

		$result = Course::find($id);

		if ($result) {

			DB::table('courses')->where('id', $id)->delete();
			DB::table('chapters')->where('course_id', $id)->delete();
			DB::table('topic_materials')->where('course_id', $id)->delete();
			DB::table('questions')->where('course_id', $id)->delete();

			return redirect()->back()->with('5fernsadminsuccess', 'Course deleted successfully.');
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function changeOrder(Request $request)
	{

		$allData = $request->allData;
		$i = 1;
		foreach ($allData as $key => $value) {
			Course::where('id', $value)->update(array('sort_order' => $i));
			$i++;
		}
	}

	public function changeLive(Request $request)
	{
		DB::table('courses')
        ->where('id', $request->post('id'))
        ->update(['is_live' => $request->post('is_live')]);
		return response(['message' => 'Subject Live changed successfully.'], 200);
		
	}
}
