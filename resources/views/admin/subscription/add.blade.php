@extends('layouts/master')

@section('title')
@if(!empty($result))
Update
@else
Add
@endif
Subscription
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
						<h2><i class="fa fa-th"></i> Go To</h2>
					</div>
					<div class="body">
						<div class="btn-group top-head-btn">
							<a class="btn-primary" href="{{ url('admin/subscription/list')}}">
								<i class="fa fa-list"></i> Subscription List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Subscription</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.subscription.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
							@csrf

							<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif" required />

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Select Course <label class="text-danger">*</label></label>
										<select class="form-control" name="course_master_id" required>
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
											<label for="inputName">Name <label class="text-danger">*</label></label>
											<input type="text" name="name" required class="form-control" placeholder="Enter Subscription Name" value="@if(!empty($result)){{ $result['name'] }}@endif" />
										</div>
									</div>
								</div>
							</div>

							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Duration Type <label class="text-danger">*</label></label>
											<select class="form-control" onchange="changeName(this.value)" name="duration_type" required>
												<option disabled selected value="">--Select--</option>

												<option value="1" @if(!empty($result) && $result['duration_type']=='1' ) {{ 'selected' }} @endif>Months</option>
												<option value="2" @if(!empty($result) && $result['duration_type']=='2' ) {{ 'selected' }} @endif>Days</option>

											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Duration <span id="durationTitle"> <?php if (!empty($result) && $result['duration_type'] == '1') {
																											echo "( In Months )";
																										} else if (!empty($result) && $result['duration_type'] == '2') {
																											echo "( In Days )";
																										} ?> </span> <label class="text-danger">*</label></label>
											<input type="text" name="duration" onkeypress="return /[0-9 ]/i.test(event.key)" required class="form-control" placeholder="Enter Subscription Duration" value="@if(!empty($result)){{ $result['duration'] }}@endif" />
										</div>
									</div>
								</div>
							</div>

							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Price <label class="text-danger">*</label></label>
											<input type="text" name="price" onkeypress="return /[0-9 ]/i.test(event.key)" required class="form-control" placeholder="Enter Subscription Price (In Rs)" value="@if(!empty($result)){{ $result['price'] }}@endif" />
										</div>
									</div>
								</div>
							</div>

							<div class="row clearfix">
								<div class="col-sm-12">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Description <label class="text-danger">*</label></label>
											<textarea class="form-control summernote" name="description" rows="2" cols="50">@if(!empty($result)){{$result['description']}}@endif</textarea>
										</div>
									</div>
								</div>
							</div>

							<!-- <div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Image <label class="text-danger">*</label></label>
										<input type="file" id="inputimg" class="form-control"  name="image" @if(!$result) required @endif  data-type="single" data-image-preview="product" accept="image/*"   >
										 <p style="color:red;width:100%">Size must be 1920*750</p> -->
							<!-- </div>
								</div>
								
								<div class="form-group previewimages col-md-6" id="product">
									@if($result)
										<img src="{{ asset('uploads/banner/'.$result->image) }}" style="width: 100px;border:1px solid #222;margin-right: 13px" />
										<input type="hidden" name="old_image" value="{{ $result->image }}" />
									@endif
								</div>
							</div>
						</div> -->

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
	function changeName(type) {

		if (type == 1) {
			$('#durationTitle').html('( In Months )');
		} else if (type == 2) {
			$('#durationTitle').html('( In Days )');
		} else {
			$('#durationTitle').html('');
		}
	}
</script>

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
	function resetFormData() {

		$('.previewimages').html('');
	}
</script>
@endpush