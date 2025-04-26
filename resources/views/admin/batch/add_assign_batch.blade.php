@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Assign Batch
@endsection

@push('custom_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<link href="{{ asset('admin-assets/dragimage/dist/image-uploader.min.css')}}" rel="stylesheet"> 
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
<style>
	.fs-wrap {
        display: block !important;
}
</style>
@section('content')
<section class="content">
	<div class="container-fluid">
		<!-- <div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i>  Go To</h2>
					</div>
					<div class="body">
						<div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/batch/list')}}">
                                <i class="fa fa-list"></i> Assign Batch List
							</a>
                        </div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Assign Batch</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.addAssignBatch') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
						<div class="row clearfix">
							<div class="col-sm-4">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Student <label class="text-danger">*</label></label>
										<select class="form-control fSelect" name="user_id" required >
											<option  selected value="">--Select Student--</option>
											@if(!empty($student))
											@foreach($student as $raw)
											<option value="{{ $raw['id'] }}" @if(!empty($result) && $raw['id']==$result['user_id']) {{ 'selected' }} @endif>{{ ucfirst($raw['name']) }} @if($raw['email'] ) ({{ ($raw['email']) }}) @endif</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Course <label class="text-danger">*</label></label>
										<select class="form-control course_id fSelect" name="course_id" required >
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
							<div class="col-sm-4">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Batch <label class="text-danger">*</label></label>
										<select class="form-control batchIdHtml fSelect" name="batch_id" required >
											<option  selected value="0">--Select Batch--</option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-12 p-t-20 text-center">
							<button style="background:#353c48;" type="submit" class="btn btn-primary" >@if(!empty($result)) Update @else Submit @endif</button> 
							@if(empty($result)) 
								<button type="reset" class="btn btn-danger waves-effect">Reset</button>
							@endif
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
	$('#summernote').summernote({
		placeholder: 'Enter Description',
		tabsize: 2,
		height: 200,
	});


	$('.course_id').on('change', function() {
    var course_id = this.value;
   
    $.ajax({
        url: "{{ url('admin/getbatch') }}",
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
			batchSelect.append('<option selected value="0">--Select Batch--</option>');
			var batchSelect = $('.batchIdHtml');
            batchSelect.fSelect('destroy');
			if (response.batchData) {
				$.each(response.batchData, function (index, batchName) {
					batchSelect.append('<option value="' + batchName + '">' + batchName + '</option>');
				});

				
			}
			batchSelect.fSelect('create');
		},
    });
});



  $(document).ready(function(){
	
       $('.fSelect').fSelect();
   			@if($result)
   				 $(".fSelect").trigger('change'); 
   			@endif
   		 });
</script>
@endpush
