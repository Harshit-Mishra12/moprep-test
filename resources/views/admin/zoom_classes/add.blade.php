@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Zoom Classes
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
                            <a class="btn-primary" href="{{ url('admin/zoom-classes/list')}}">
                                <i class="fa fa-list"></i> Zoom Classes List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Zoom Classes</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.zoom-classes.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
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
										<select class="form-control batchIdHtml fSelect batch_id" name="batch_id" required id="batch_id" data-batch_id="@if(!empty($result)){{ $result['batch_id'] }}@else{{ '0' }}@endif" >
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
											<option   value="0">--Select Topic--</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Start Time <label class="text-danger">*</label></label>
										<input type="datetime-local" class="form-control" id="start_time"  name="start_time" value="@if(!empty($result)){{ $result['start_time'] }}@else{{ '0' }}@endif">
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Duration (In Minute) <label class="text-danger">*</label></label>
										<input type="text"  onkeypress="return event.key >= '0' && event.key <= '9' || event.key === 'Backspace'" maxlength="4" class="form-control" id="duration"  name="duration" placeholder="Enter Duration" value="@if(!empty($result)){{ $result['duration'] }}@else{{ '' }}@endif"> 
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

<script>
$('.course_id').on('change', function() {
    var course_id = this.value;
    $.ajax({
        url: "{{ url('admin/zoom-classes/get-batch-name') }}",
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
				batchSelect.append('<option selected value="' + batchIdValue + '"></option>');
			}
            batchSelect.fSelect('destroy');
            batchSelect.fSelect('create');
			
        },
    });
});

$('.batch_id').on('change', function() {
    var batch_id = this.value;

    $.ajax({
        url: "{{ url('admin/zoom-classes/get-topic') }}",
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
@endpush
