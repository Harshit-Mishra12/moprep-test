@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Question
@endsection
@push('custom_css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
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
                            <a class="btn-primary" href="{{ url('admin/questionBank/yearList') }}">
                                <i class="fa fa-list"></i> Question List
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
						<form id="form" action="{{ route('admin.questionBank.addQuestionSingle') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
                            
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Course <label class="text-danger">*</label></label>
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
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Year <label class="text-danger">*</label></label>
											<input type="text" name="year" required  class="form-control" placeholder="Enter Year" value="@if(!empty($result)){{ $result['year'] }}@endif"/>
										</div>
									</div>
								</div>	
							</div>	
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Part <label class="text-danger">*</label></label>
											<input type="text" name="part" required  class="form-control" placeholder="Enter Part" value="@if(!empty($result)){{ $result['part'] }}@endif"/>
										</div>
									</div>
								</div>	
							</div>
							
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Question <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="question_text" rows="2" cols="50">@if(!empty($result)){{$result['question']}}@endif</textarea>
										</div>
									</div>
								</div>
                            </div>	
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Option 1 <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="option1_text" rows="2" cols="50">@if(!empty($result)){{$result['option1']}}@endif</textarea>
										</div>
									</div>
								</div>
                            </div>	
							
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Option 2 <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="option2_text" rows="2" cols="50">@if(!empty($result)){{$result['option2']}}@endif</textarea>
										</div>
									</div>
								</div>
                            </div>	
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Option 3 <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="option3_text" rows="2" cols="50">@if(!empty($result)){{$result['option3']}}@endif</textarea>
										</div>
									</div>
								</div>
                            </div>	
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Option 4 <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="option4_text" rows="2" cols="50">@if(!empty($result)){{$result['option4']}}@endif</textarea>
										</div>
									</div>
								</div>
                            </div>	
							
							<div class="row clearfix">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label for="inputName">Select Right Option <label class="text-danger">*</label></label>
                                            <select class="form-control" name="right_option" required >
                                                <option value="">--Select--</option>
												<option	@if(!empty($result) && trim($result['right_option'])=='a'){{'selected'}}@endif value="a">A</option>
												<option @if(!empty($result) && trim($result['right_option'])=='b'){{'selected'}}@endif value="b">B</option>
												<option @if(!empty($result) && trim($result['right_option'])=='c'){{'selected'}}@endif value="c">C</option>
												<option @if(!empty($result) && trim($result['right_option'])=='d'){{'selected'}}@endif value="d">D</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName"> Explaination <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="explaination" rows="2" cols="50">@if(!empty($result)){{$result['explaination']}}@endif</textarea>
										</div>
									</div>
								</div>
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
	<script>
		$('.summernote').summernote({
			placeholder: 'Enter Text',
			tabsize: 2,
			height: 150,
			toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['height', ['height']],
				['view', ['fullscreen', 'codeview']],
				['insert', ['picture']],
			],
			fontSizes: ['8', '10', '12', '14', '18', '24', '36'],
		});

	</script>
	<script>

	var result = {!! json_encode($result) !!};
 
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
			},
			cache: false,
			timeout: 5000
		});
	});


	$('.courseHtml').change(function() {
		var courseId=$(this).val();
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
			},
			cache: false,
			timeout: 5000
		});
	});

	
	if(result){
		
		$('.courseHtml').trigger('change'); 
		//$('.chapterHtml').trigger('change'); 
	}

	function resetFormData(){
		location.reload();
	}

	// function handleExamTypeChange() {
	// 	if ($('.examType').val() === 'Revision/Exam') {
	// 		$('#chapter_select').css('display', 'block');
	// 		$('select[name="chapter_id"]').prop('required', true);
	// 	} else {
	// 		$('#chapter_select').css('display', 'none');
	// 		$('select[name="chapter_id"]').prop('required', false);
	// 	}
	// }
	// $(document).ready(function() {
		
	// 	$('.examType').on('change', handleExamTypeChange);

	// 	if (result) {
	// 		handleExamTypeChange();
	// 	}
	// });

</script>
	
@endpush