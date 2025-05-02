<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Validator;
use \App\Models\TopicMaterials;
use \App\Models\CourseMaster;
use \App\Models\Course;
use DB;
use Yajra\DataTables\DataTables;

class TopicMaterialsController extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {
			$rules = [
				'course_id' => 'string|required',
				'batch_id' => 'required',
				'topic' => 'required',
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
					$alreadyExists = TopicMaterials::where('batch_id', $request->post('batch_id'))
						->where('topic', $request->post('topic'))
						->first();


					if ((int) $request->post('id') > 0) {

						$topicMaterial = TopicMaterials::find($request->post('id'));
					} else {

						$topicMaterial = new TopicMaterials();

						if ($alreadyExists) {

							return response(array('message' => 'Topic already exists.'), 403);
						}
					}

					$topicMaterial->batch_id = $request->post('batch_id');
					// $topicMaterial->course_master_id = $request->post('course_master_id');
					$topicMaterial->course_id = $request->post('course_id');
					$topicMaterial->topic = $request->post('topic');

					$topicMaterial->save();

					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Topic updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Topic added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		$courses = CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
		$subject = Course::where('status', '1')->orderBy('id', 'DESC')->get();
		return view('admin.topic_materials.add', compact('result', 'courses', 'subject'));
	}

	public function topicMaterialList()
	{
		//	$result = \App\Models\TopicMaterials::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
		$result = \App\Models\TopicMaterials::select([
			'id',
			'course_id',
			'batch_id',
			'topic',
			'sort_order',
			'status',
			'course_master_id',
			'is_live',
			'is_lock',
			'created_at',
			'updated_at'
		])
			->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')
			->get();
		//  		dd($result);
		$permission = session('permission') ?? [];
		$permission = isset($permission['15']) ? $permission['15'] : null;

		return view('admin.topic_materials.list', compact('result', 'permission'));
	}

	public function topicMaterialListData(Request $request)
	{

		$permission = session('permission') ?? [];
		$permission = isset($permission['15']) ? $permission['15'] : null;



		if ($request->ajax()) {
			//	$result = TopicMaterials::orderBy('id', 'DESC')->get();

			$result = TopicMaterials::orderByRaw('CAST(sort_order AS UNSIGNED) ASC')
				->orderBy('id', 'ASC') // Secondary sort
				->get();

			return Datatables::of($result)

				->addIndexColumn()
				// ->addColumn('coursemaster', function ($row) {

				// 	return  \App\Helpers\commonHelper::getCourseMasterName($row->course_master_id);
				// })
				->addColumn('subject', function ($row) {

					return  \App\Helpers\commonHelper::getCourseName($row->course_id);
				})
				->addColumn('chapter', function ($row) {

					return \App\Helpers\commonHelper::getChapterName($row->batch_id);
				})
				->addColumn('status', function ($row) {
					$status = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-change" data-id="' . $row->id . '"' . ($row->status == '1' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $status;
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
				->addColumn('action', function ($row) use ($permission) {

					$viewBtn = '';
					$editBtn = '';
					$deleteBtn = '';

					$viewUrl = url('admin/question_bank/question/view/' . $row->course_id . '/revisionexam');
					$editUrl = url('admin/topic-materials/update/' . $row->id);
					$deleteUrl = url('admin/topic-materials/delete/' . $row->id);

					$viewBtn = '<a href="' . $viewUrl . '" title="View Questions" target="_blank" style="line-height:1px;" class="btn btn-tbl-edit btn_change"><i style="margin-right: 0px;" class="fa fa-eye"></i></a>';
					if (isset($permission) && $permission['edit'] == 'true') {
						$editBtn = '<a href="' . $editUrl . '" title="Edit Topic Materials" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';
					}

					if (isset($permission) && $permission['delete'] == 'true') {
						$deleteBtn = '<a href="' . $deleteUrl . '" title="Delete Topic Materials" onclick="return confirm(\'Are you sure? You want to delete this topic materials.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
					}

					return $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn;
				})
				->rawColumns(['status', 'is_live', 'action', 'is_lock'])

				->make(true);
		} else {

			return view('admin.topic_materials.list');
		}
	}


	public function changeOrder(Request $request)
	{
		$allData = $request->allData;



		try {
			$request->validate(['allData' => 'required|array']);

			// Update sort orders for each topic
			foreach ($request->allData as $order => $id) {
				DB::table('topic_materials')
					->where('id', $id)
					->update(['sort_order' => $order + 1]);
			}

			// Get updated topics data with their new sort orders
			$result = DB::table('topic_materials')
				->whereIn('id', $allData)
				->select('id', 'sort_order')
				->orderBy('sort_order')
				->get();

			return response()->json([
				'success' => true,
				'data' => $result
			]);
		} catch (\Exception $e) {
			return response()->json([
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function changeStatus(Request $request)
	{

		TopicMaterials::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Topic status changed successfully.'), 200);
	}

	public function updateTopicMateria(Request $request, $id)
	{

		$result = TopicMaterials::find($id);

		if ($result) {
			// $courses = CourseMaster::where('status', '1')->orderBy('id', 'DESC')->get();
			$subject = Course::where('status', '1')->orderBy('id', 'DESC')->get();
			// 			dd($subject);
			$chapter = Chapter::where('status', '1')->where('course_id', $result->course_id)->orderBy('id', 'DESC')->get();
			return view('admin.topic_materials.add', compact('result', 'subject', 'chapter'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function deleteTopicMateria(Request $request, $id)
	{

		$result = TopicMaterials::find($id);

		if ($result) {

			DB::table('topic_materials')->where('id', $id)->delete();
			DB::table('questions')->where('topic_id', $id)->delete();

			return redirect()->back()->with('5fernsadminsuccess', 'Topic deleted successfully.');
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}
	public function getBatchName(Request $request)
	{
		$batchData = DB::table('chapters')->where('course_id', $request->post('course_id'))
			->where('status', 'Active')
			->orderBy('id', 'DESC')
			->get();


		if ($batchData->isNotEmpty()) {
			$batchNames = $batchData->map(function ($batch) {
				return [
					'id' => $batch->id,
					'name' => $batch->name,
				];
			})->toArray();


			return response(['message' => 'Chapter data fetched successfully.', 'batchData' => $batchNames], 200);
		} else {
			return response(['message' => 'No chapter data found.'], 200);
		}
	}
	public function changeLive(Request $request)
	{
		TopicMaterials::where('id', $request->post('id'))->update(['is_live' => $request->post('is_live')]);
		return response(array('message' => 'Topic live changed successfully.'), 200);
	}

	public function changeLock(Request $request)
	{
		TopicMaterials::where('id', $request->post('id'))->update(['is_lock' => $request->post('is_lock')]);
		return response(array('message' => 'Topic lock changed successfully.'), 200);
	}
}
