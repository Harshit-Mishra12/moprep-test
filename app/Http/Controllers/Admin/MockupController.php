<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Mockup;
use App\Models\CourseMaster;
use Validator;
use DB;
use Yajra\DataTables\DataTables;

class MockupController extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {

			$rules = [
				'name' => 'required|unique:mockups,name,' . $request->post('id') . ',id',
				'id' => 'numeric|required',
				'duration' => 'numeric|required',
				'start_date' => 'required',
				'start_time' => 'required',
				'end_date' => 'required',
				'end_time' => 'required',
				'positive_marks' => 'numeric|required',
				'negative_marks' => 'required',
			];

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
					$start_date = $request->post('start_date');
					$start_time = $request->post('start_time');
					$end_date = $request->post('end_date');
					$end_time = $request->post('end_time');

					if (strtotime($start_date . ' ' . $start_time) > strtotime($end_date . ' ' . $end_time)) {
						return response(array("message" => 'End date time must be greater then start date time'), 403);
					}

					if ((int) $request->post('id') > 0) {

						$mockup = Mockup::find($request->post('id'));
					} else {

						$mockup = new Mockup();

					}

					$image = $request->post('old_image');

					if ($request->hasFile('image')) {
						$imageData = $request->file('image');
						$image = strtotime(date('Y-m-d H:i:s')) . '.' . $imageData->getClientOriginalExtension();
						$destinationPath = public_path('/uploads/banner');
						$imageData->move($destinationPath, $image);
					}

					$mockup->course_master_id = $request->post('course_master_id');
					$mockup->name = $request->post('name');
					$mockup->duration = $request->post('duration');
					$mockup->start_date = $request->post('start_date');
					$mockup->start_time = $request->post('start_time');
					$mockup->end_date = $request->post('end_date');
					$mockup->end_time = $request->post('end_time');
					$mockup->positive_marks = $request->post('positive_marks');
					$mockup->negative_marks = $request->post('negative_marks');
					$mockup->is_lock = '1';
					$mockup->image = $image;

					$mockup->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Mockup updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Mockup added successfully.', 'reset' => true, 'script' => false), 200);

					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);

				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$courses = CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.mockup.add', compact('result', 'courses'));
	}

	public function mockupList()
	{
		$course = CourseMaster::orderBy('id', 'DESC')->get();
		$permission = session('permission') ?? [];
		$permission = isset($permission['56']) ? $permission['56'] : null;

		return view('admin.mockup.list',compact('course','permission'));
	}

	public function mockupListData(Request $request)
	{

		$permission = session('permission') ?? [];
		$permission = isset($permission['56']) ? $permission['56'] : null;

		if ($request->ajax()) {
		    $currentDateTime = now()->format('Y-m-d H:i');
			$result = Mockup::orderBy('id', 'DESC');
			
			 // Apply filters based on the request
			 if ($request->get('course_id') > 0) {
				$result->where('course_master_id', $request->course_id);
			}
			$result = $result->get();
			return Datatables::of($result)

				->addIndexColumn()
				->addColumn('coursemaster', function ($row) {

					return  \App\Helpers\commonHelper::getCourseMasterName($row->course_master_id);
				})
				->addColumn('start_date_time', function ($row) {

					return date('d-m-Y', strtotime($row->start_date)) . ' ' . date('h:i A', strtotime($row->start_time));
				})
				->addColumn('end_date_time', function ($row) {

					return date('d-m-Y', strtotime($row->end_date)) . ' ' . date('h:i A', strtotime($row->end_time));
				})
				->addColumn('banner', function ($row) {
					$banner = '<img src="' . asset('uploads/banner/' . $row->image) . '" width="65px"/>';
					return $banner;
				})
				->addColumn('created_at', function ($row) {

					return date('d-m-Y', strtotime($row->created_at));
				})
				->addColumn('status', function ($row) {
					$status = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-change" data-id="' . $row->id . '"' . ($row->status == 'Active' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $status;
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
				->addColumn('action', function ($row) use ($permission) {


					$editBtn = '';
					$deleteBtn = '';

					$editUrl = url('admin/mockup/update/' . $row->id);
					$deleteUrl = url('admin/mockup/delete/' . $row->id);
					$resultUrl = url('admin/mockup/detailMock/' . $row->id);

					if(isset($permission) && $permission['edit'] == 'true'){
						$editBtn = '<a href="' . $editUrl . '" title="Edit Mockup" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';
					}

					if(isset($permission) && $permission['delete'] == 'true'){
						$deleteBtn = '<a href="' . $deleteUrl . '" title="Delete Mockup" onclick="return confirm(\'Are you sure? You want to delete this Mockup.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
					}


					$resultBtn = '<a href="' . $resultUrl . '" title="Mockup Result"  class="btn btn-tbl-edit btn_change"><i class="fas fa-eye"></i></a>';

					return $resultBtn . ' ' . $editBtn . ' ' . $deleteBtn;
				})
				->rawColumns(['banner', 'status', 'action','is_lock'])
				->make(true);


		} else {

			return view('admin.mockup.list');
		}
	}

	public function changeStatus(Request $request)
	{

		Mockup::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Mockup status changed successfully.'), 200);
	}
	public function changeLock(Request $request)
	{

		Mockup::where('id', $request->post('id'))->update(['is_lock' => $request->post('is_lock')]);

		return response(array('message' => 'Mockup Lock changed successfully.'), 200);
	}

	public function updateMockup(Request $request, $id)
	{

		$result = Mockup::find($id);

		if ($result) {
			$courses = CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
			return view('admin.mockup.add', compact('result', 'courses'));

		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}

	}

	public function deleteMockup(Request $request, $id)
	{

		$result = Mockup::find($id);

		if ($result) {

			Mockup::where('id', $id)->delete();
			DB::table('mock_questions')->where('mock_id', $id)->delete();

			return redirect()->back()->with('5fernsadminsuccess', 'Mockup deleted successfully.');

		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}

	}


	public function detailUserMock(Request $request, $id)
	{
		$mockName = DB::table('mockups')->where('id', $id)->first();
		$result = DB::table('mockup_test_result')->where('mock_id', $id)->orderBy('marks', 'DESC')->get();
		return view('admin.mockup.detailMock', compact('result', 'mockName'));
	}

}
