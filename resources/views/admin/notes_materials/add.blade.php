@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Notes Materials
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
                            <a class="btn-primary" href="{{ url('admin/notes-materials/list')}}">
                                <i class="fa fa-list"></i> Notes Materials List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Notes Materials</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.notes-materials.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
						<div class="row clearfix">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Course <label class="text-danger">*</label></label>
										<select class="form-control course_id fSelect" name="course_id" required>
											<option  selected value="">--Select Course--</option>
											@if(!empty($course))
											@foreach($course as $raw)
											<option value="{{ $raw['id'] }}" @if(!empty($result) && $raw['id']==$result['course_id']) {{ 'selected' }} @endif>{{ ucfirst($raw['name']) }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Batch <label class="text-danger">*</label></label>
										<select class="form-control batchIdHtml batch_id" name="batch_id" required id="batch_id" data-batch_id="@if(!empty($result)){{ $result['batch_id'] }}@else{{ '0' }}@endif" >
											<option   value="0">--Select Batch--</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Topic <label class="text-danger">*</label></label>
										<select class="form-control topicHtml fSelect" name="topic_id" required id="topic_id" data-topic_id="@if(!empty($result)){{ $result['topic_id'] }}@else{{ '0' }}@endif">
											<option selected value="0">--Select Topic--</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">File Type <label class="text-danger">*</label></label>
										<select class="form-control fSelect" name="file_type" required id="file_type">
											<option   value="0">--Select File Type--</option>
											<option value="image" @if(!empty($result) && $result['file_type']=='image') {{ 'selected' }}@endif>Image</option>
											<option value="pdf" @if(!empty($result) && $result['file_type']=='pdf') {{ 'selected' }}@endif>PDF</option>
											<option value="video" @if(!empty($result) && $result['file_type']=='video') {{ 'selected' }}@endif>Video</option>
											<!-- <option value="notes" @if(!empty($result) && $result['file_type']=='notes') {{ 'selected' }}@endif>Notes</option> -->
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Title <label class="text-danger">*</label></label>
										<input type="text" name="title"  class="form-control" placeholder="Enter Title" value="@if(!empty($result)){{ $result['title'] }}@endif"/>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputimg">Upload File <label class="text-danger">*</label></label>
										<input type="file" id="inputimg" class="form-control"  name="file" @if(!$result) required @endif  data-type="single" data-image-preview="image_preview">
									</div>
								</div>
								
								<div class="form-group previewimages image col-md-6" id="image_preview">
									@if($result)
										@if(!empty($result) && $result['file_type']=='image')
											<img src="{{ asset('uploads/file/'.$result->file) }}" style="width: 100px;border:1px solid #222;margin-right: 13px" />
											<input type="hidden" name="old_file" value="{{ $result->file }}" />
										@endif
										@if(!empty($result) && $result['file_type']=='pdf')
											<a target="blank" href="{{ asset('uploads/file/'.$result->file) }}">ViewPDF</a>
											<input type="hidden" name="old_file" value="{{ $result->file }}" />
										@endif
										@if(!empty($result) && $result->file_type == 'video')
											<video controls style="width: 20%;">
												<source src="{{ asset('uploads/file/'.$result->file) }}" type="video/mp4">
												Your browser does not support the video tag.
											</video>
											<input type="hidden" name="old_file" value="{{ $result->file }}" />
										@endif
									@endif
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

<script>

	function resetFormData(){
		location.reload();
	}
	

	$('.course_id').on('change', function() {
		var course_id = this.value;
		$.ajax({
			url: "{{ url('admin/notes-materials/get-batch-name') }}",
			type: "POST",
			data: {
				course_id: course_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			cache: false,
			success: function (response) {
				var batchSelect = $(".batchIdHtml");
				batchSelect.empty();
				batchSelect.append('<option value="0">--Select Batch--</option>');

				if (response.batchData) {
					$.each(response.batchData, function (index, batch) {
						
						batchSelect.append('<option value="' + batch.id + '">' + batch.name + '</option>');
					});
				}
				var batchIdValue = batchSelect.data('batch_id');
				if (batchIdValue) {
					batchSelect.val(batchIdValue); 
				}
				batchSelect.fSelect('destroy');
				batchSelect.fSelect('create');
				
			},
		});
	});

	$('.batch_id').on('change', function() {
		var batch_id = this.value;

		$.ajax({
			url: "{{ url('admin/notes-materials/get-topic') }}",
			type: "POST",
			data: {
				batch_id: batch_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			cache: false,
			success: function (response) {

				var topicSelect = $(".topicHtml");
				topicSelect.empty();
				topicSelect.append('<option value="0">--Select Topic--</option>');

				if (response.topicData) {
					$.each(response.topicData, function (index, topic) {
						topicSelect.append('<option value="' + topic.id + '">' + topic.name + '</option>');
					});

					$("#topic_id").html(topicSelect.html());

					var topicIdValue = topicSelect.data('topic_id');

					if (topicIdValue) {
						topicSelect.val(topicIdValue);
					}
				}

				topicSelect.fSelect('destroy');
				topicSelect.fSelect('create');

				
			},
		});
	});

	$(document).ready(function () {
		$('.fSelect').fSelect({
			placeholder: '-- Select Topic --',
		});

		$('.batchIdHtml').fSelect({
			placeholder: '-- Select Batch --',
		});
	
	});

</script>
<script>
	$(document).ready(function() {

		updateFileInputAccept();

		$('#file_type').on('change', function() {
			updateFileInputAccept();
		});

		function updateFileInputAccept() {
			var fileType = $('#file_type').val();
			var acceptValue = '';

			switch (fileType) {
				case 'image':
					acceptValue = 'image/*';
					break;
				case 'pdf':
					acceptValue = '.pdf';
					break;
				case 'video':
					acceptValue = 'video/*';
					break;
				default:
					acceptValue = ''; 
					break;
			}

			$('#inputimg').attr('accept', acceptValue);
		}
	});
</script>
@endpush
