@extends('layouts/master')

@section('title')
@if(!empty($result))
Update
@else
Add
@endif
Question Bank
@endsection

@section('content')
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Go To</h2>
					</div>
					<div class="body">
						<div class="btn-group top-head-btn">
							<a class="btn-primary" href="{{ url('admin/questionBank/yearList')}}">
								<i class="fa fa-list"></i> Question List
							</a>
						</div>

						<div class="btn-group top-head-btn">
							<a class="btn-primary" href="{{ url('admin/questionBank/addQuestionSingle')}}">
								<i class="fa fa-list"></i> Add Manual Question
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Question</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.questionBank.add_question_word') }}" method="post" enctype="multipart/form-data" autocomplete="off">
							@csrf

							<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif" required />

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Select Course <label class="text-danger">*</label></label>
										<select class="form-control" name="course_id" required>
											<option selected value="">--Select--</option>
											@if(!empty($courses))
											@foreach($courses as $course)
											<option value="{{ $course['id'] }}" @if(!empty($result) && $course['id']==$result['course_id']) {{ 'selected' }} @endif>{{ ucfirst($course['name']) }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
							</div>

							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Year <label class="text-danger">*</label></label>
											<input type="text" name="year" required class="form-control" placeholder="Enter Year" value="@if(!empty($result)){{ $result['year'] }}@endif" />
										</div>
									</div>
								</div>
							</div>
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Part <label class="text-danger">*</label></label>
											<input type="text" name="part" required class="form-control" placeholder="Enter Part" value="@if(!empty($result)){{ $result['part'] }}@endif" />
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputimg">Word File <label class="text-danger">*</label></label>
										<input type="file" id="inputimg" required class="form-control" name="word_file">
										<span style="color:red">Save .docx file As .htm file. Then upload.</span>
									</div>
								</div>
								<a download href="{{ asset('admin-assets/sample_file.htm') }}">Download sample file</a>
							</div>

							<div class="col-lg-12 p-t-20 text-center">
								@if(empty($result))
								<button type="reset" class="btn btn-danger waves-effect">Reset</button>
								@endif
								<button style="background:#353c48;" type="submit" class="btn btn-primary waves-effect m-r-15">@if(!empty($result)) Update @else Submit @endif</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection