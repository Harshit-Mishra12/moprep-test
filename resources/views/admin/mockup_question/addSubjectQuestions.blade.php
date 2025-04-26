@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Mockup Question
@endsection

@section('content')
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i>  Go To</h2>
					</div>
					<div class="body">
						<div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/mockupquestion/list')}}">
                                <i class="fa fa-list"></i>Mockup Question List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Mockup Question By Subject</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.mockupquestion.addSubjectQuestion') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label for="inputName">Select Mockup <label class="text-danger">*</label></label>
                                            <select class="form-control" id="mock_id" onchange="resetYear()" name="mock_id" required >
                                                <option  selected value="">--Select--</option>
                                                @if(!empty($mockups))
                                                    @foreach($mockups as $mockup)
                                                        <option value="{{ $mockup->id }}" >{{ ucfirst($mockup->name) }} - {{$mockup->course_master_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
								<div class="col-sm-6">
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
								</div>

								<div class="col-sm-6">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Select Subject <label class="text-danger">*</label></label>
											<select class="form-control yearSelect subjectIdHtml" required name="year_id" id="subject_id">
												<option value="" disabled selected>-- Select --</option>
                                 
											</select>
										</div>
									</div>
								</div>
								<div class="col-sm-6" id="chapter_select">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Select Chapter <label class="text-danger">*</label></label>
											<select class="form-control chapterHtml" required id="chapter_id" name="chapter_id">
											<option value="0">--Select Chapter--</option>
											@if(!empty($chapter))
												@foreach($chapter as $c)
												<option value="{{ $c['id'] }}" @if(!empty($result) && $c['id']==$result['chapter_id']) {{ 'selected' }} @endif>{{ ucfirst($c['name']) }}</option>
												@endforeach
												@endif	
										</select>
										</div>
									</div>
								</div>

								<div class="col-sm-6" id="">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Select Topic <label class="text-danger">*</label></label>
											<select class="form-control topicHtml" required id="topic_id" onchange="resetYear()" name="topic_id">
											</select>
										</div>
									</div>
								</div>
                            </div>

							<div class="col-md-12">
								<span id="questionHtml">

								</span>
							</div>
							
							
							<div class="col-lg-12 p-t-20 text-center">
								@if(empty($result)) 
									<button type="reset" class="btn btn-danger waves-effect">Reset</button>
								@endif
								<button style="background:#353c48;" type="submit" class="btn btn-primary waves-effect m-r-15" >@if(!empty($result)) Update @else Submit @endif</button> 
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

	var result = {!! json_encode($result) !!};
 

	$('.yearSelect').change(function() {
		// var subjectId=$(this).val();

		// var mock_id = $('#mock_id').val();
		
		// if(mock_id==''){
		// 	alert('Please select Mock Test');
		// 	return false;
		// }

		

		// $.ajax({
		// 	url: baseUrl + '/admin/mockupquestion/loadQuestionSubject?id='+subjectId+'&mock_id='+mock_id+'&course_master_id='+$('#course_master_id').val(),
		// 	dataType: 'json',
		// 	type: 'get',
		// 	error: function(xhr, textStatus) {
		// 		if (xhr && xhr.responseJSON.message) {
		// 			showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
		// 		} else {
		// 			showMsg('error', xhr.status + ': ' + xhr.statusText);
		// 		}
		// 	},
			
		// 	success: function(data) {

		// 		$('#questionHtml').html(data.html);
 
		// 	},
		// 	cache: false,
		// 	timeout: 5000
		// });
	});

	
	if(result){
		
		$('.courseHtml').trigger('change'); 
		//$('.chapterHtml').trigger('change'); 
	}

	function resetFormData(){
		location.reload();
	}

	function resetYear(){
// 		$('.yearSelect').val('');
		$('#questionHtml').html('');
	}

	$(document).ready(function () {
		$('.fSelect').fSelect({
			placeholder: '-- Select --',
		});
	
	});

</script>
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
					$.each(response.subjectData, function(index, subject) {
						subjectSelect.append('<option value="' + subject.id + '">' + subject.name + '</option>');
					});
				}
				var subjectIdValue = subjectSelect.data('year_id');
				if (subjectIdValue) {
					subjectSelect.append('<option selected value="' + subjectIdValue + '"></option>');
				}
				//  alert('ss');
				// subjectSelect.fSelect('destroy');
				// subjectSelect.fSelect('create');
				
			},
		});
	});

	$(document).ready(function() {
		$('.fSelect').fSelect();
	});
	$('.chapterHtml').change(function() {
		var courseId=$(this).val();
		$.ajax({
			url: baseUrl + '/get-topic?chapter_id=' + courseId,
			dataType: 'json',
			type: 'get',
			error: function(xhr, textStatus) {
				if (xhr && xhr.responseJSON.message) {
					showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
				} else {
					showMsg('error', xhr.status + ': ' + xhr.statusText);
				}
			},
			
			success: function(data) {

				$('.topicHtml').html(data.html);
 
				if(result){

					$('.topicHtml').val(result.topic_id);
				}
				// $('.yearSelect').val('');
				$('#questionHtml').html('');
			},
			cache: false,
			timeout: 5000
		});
	});

	$('.subjectIdHtml').change(function() {
		var courseId = $(this).val();
		$.ajax({
			url: baseUrl + '/get-chapter?course_id=' + courseId,
			dataType: 'json',
			type: 'get',
			error: function(xhr, textStatus) {
				if (xhr && xhr.responseJSON.message) {
					showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
				} else {
					showMsg('error', xhr.status + ': ' + xhr.statusText);
				}
			},
			
			success: function(data) {

				$('.chapterHtml').html(data.html);
 
				if(result && result.course_id==courseId){

					$('.chapterHtml').val(result.chapter_id);
				}

				$('.chapterHtml').trigger('change');
				//$('.yearSelect').val('');
				$('#questionHtml').html('');
			},
			cache: false,
			timeout: 5000
		});
	});

	$('.topicHtml').change(function() {
		var chapter_id = $('#chapter_id').val();
		var topic_id = $('#topic_id').val();
		var subjectId=$('#subject_id').val();
	

		var mock_id = $('#mock_id').val();
		
		if(mock_id==''){
			alert('Please select Mock Test');
			return false;
		}
		if(chapter_id==''){
			alert('Please select Chapter');
			return false;
		}
		
		if(subjectId==''){
			alert('Please select subject');
			return false;
		}

		if(chapter_id==''){
			alert('Please select chapter');
			return false;
		}

		if(topic_id==''){
			alert('Please select topic');
			return false;
		}

		$.ajax({
			url: baseUrl + '/admin/mockupquestion/loadQuestionSubject?id='+subjectId+'&mock_id='+mock_id+'&course_master_id='+$('#course_master_id').val()+'&chapter_id='+chapter_id+'&topic_id='+topic_id,
			dataType: 'json',
			type: 'get',
			error: function(xhr, textStatus) {
				if (xhr && xhr.responseJSON.message) {
					showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
				} else {
					showMsg('error', xhr.status + ': ' + xhr.statusText);
				}
			},
			
			success: function(data) {

				$('#questionHtml').html(data.html);
 
			},
			cache: false,
			timeout: 5000
		});
	});


</script>
	
@endpush