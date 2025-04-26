@extends('layouts/master')

@section('title')
@if(!empty($result))
Update
@else
Add
@endif
Chapter
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
							<a class="btn-primary" href="{{ url('admin/question_bank/chapter/list')}}">
								<i class="fa fa-list"></i> Chapter List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Chapter</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.question_bank.chapter.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
							@csrf
							<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif" required />
							<div class="row clearfix">

								{{-- <div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Select Course <label class="text-danger">*</label></label>
										<select class="form-control courseHtml" id="course_master_id" name="course_master_id" required>
											<option selected value="">--Select--</option>
											@if(!empty($courses))
											@foreach($courses as $course)
											<option value="{{ $course['id'] }}" @if(!empty($result) && $course['id']==$result['course_master_id']) {{ 'selected' }} @endif>{{ ucfirst($course['name']) }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
							</div> --}}

								<div class="col-sm-6">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Subject <label class="text-danger">*</label></label>
											<select class="form-control subjectIdHtml fSelect" name="course_id" required id="course_id" data-course_id="@if(!empty($result)){{ $result['course_id'] }}@else{{ '0' }}@endif">
												<option value="0">--Select Subject--</option>
												@if(!empty($subjects))
													@foreach($subjects as $subject)
													<option value="{{ $subject['id'] }}" @if(!empty($result) && $subject['id']==$result['course_id']) {{ 'selected' }} @endif>{{ ucfirst($subject['name']) }}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>
								</div>

								<!-- <div class="col-sm-6">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Select Subject <label class="text-danger">*</label></label>
											<select class="form-control" name="subject_id" required>
												<option selected value="">--Select--</option>
												@if(!empty($subjects))
												@foreach($subjects as $subject)
												<option value="{{ $subject['id'] }}" @if(!empty($result) && $subject['id']==$result['subject_id']) {{ 'selected' }} @endif>{{ ucfirst($subject['name']) }}</option>
												@endforeach
												@endif
											</select>
										</div>
									</div>
								</div> -->

								<div class="col-sm-6">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Enter Name</label>
											<input value="@if(!empty($result)){{ $result['name'] }}@endif" type="text" required class="form-control" placeholder="Enter Name" name="name">
										</div>
									</div>
								</div>
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

@push('custom_js')
<script>
	$('#course_master_id').on('change', function() {
		var course_master_id = this.value;

		$.ajax({
			url: "{{ url('admin/question_bank/question/get-subject-name') }}",
			type: "POST",
			data: {
				course_master_id: course_master_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			cache: false,
			success: function(response) {
				var subjectSelect = $(".subjectIdHtml");
				subjectSelect.empty();
				subjectSelect.append('<option value="0">--Select Subject--</option>');

				if (response.subjectData) {
					$.each(response.subjectData, function(index, course) {
						subjectSelect.append('<option value="' + course.id + '">' + course.name + '</option>');
					});
				}
				var subjectIdValue = subjectSelect.data('course_id');
				if (subjectIdValue) {
					subjectSelect.append('<option selected value="' + subjectIdValue + '"></option>');
				}
				subjectSelect.fSelect('destroy');
				subjectSelect.fSelect('create');

			},
		});
	});

	$(document).ready(function() {
		$('.fSelect').fSelect();

	});
</script>
@endpush