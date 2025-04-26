<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use \App\Models\Chapter;
use \App\Models\Subject;
use App\Models\Course;
use Illuminate\Support\Str;
use DB;

class SubjectController extends Controller
{
	public function add(Request $request)
	{
		if ($request->isMethod('post')) {
			$rules['name'] = 'string|required|unique:subjects,name,' . $request->post('id') . ',id,deleted_at,NULL';
			$rules['description'] = 'required';
			$rules['course_id'] = 'string|required';

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

						$subject = Subject::find($request->post('id'));
					} else {

						$subject = new Subject();

					}
					$filename = $request->post('old_image');

					if ($request->hasFile('image')) {
						$filename = \App\Helpers\commonHelper::uploadFile($request->file('image'), 'subject');

					}

					$subject->image = $filename;
					$subject->course_id = $request->post('course_id');
					$subject->name = $request->post('name');
					$subject->description = $request->post('description');
					$subject->slug = Str::slug($subject->name);
					$subject->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Subject updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Subject added successfully.', 'reset' => true, 'script' => false), 200);

					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);

				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];

		$courses = \App\Models\Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.subject.add', compact('result', 'courses'));
	}

	public function subjectList(Request $request)
	{
		$result = Subject::orderBy('id', 'DESC');
		if ($request->get('course_id') > 0) {
			$result->where('course_id', $request->course_id);
		}
		$result = $result->get();
		$course = Course::orderBy('id', 'DESC')->get();
		return view('admin.subject.list', compact('result', 'course'));
	}

	public function viewChapter(Request $request, $id)
	{
		$result = Chapter::where('course_id', $id)->get();
		return view('admin.subject.chapters', compact('result'));
	}

	public function changeStatus(Request $request)
	{
		Subject::where('id', $request->post('id'))->update(['status' => $request->post('status')]);
		return response(array('message' => 'Subject status changed successfully.'), 200);
	}

	public function updateSubject(Request $request, $id)
	{

		$result = Subject::find($id);

		if ($result) {
			$courses = Course::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.subject.add', compact('result', 'courses'));

		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}

	}

	public function deleteSubject(Request $request, $id)
	{

		$result = Subject::find($id);

		if ($result) {

			DB::table('subjects')->where('id', $id)->delete();
			DB::table('chapters')->where('subject_id', $id)->delete();
			DB::table('topic_materials')->where('course_id', $id)->delete();
			DB::table('questions')->where('course_id', $id)->delete();

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
			Subject::where('id', $value)->update(array('sort_order' => $i));
			$i++;
		}

	}
}
