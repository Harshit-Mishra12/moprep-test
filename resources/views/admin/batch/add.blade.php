@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	Batch
@endsection

@push('custom_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<link href="{{ asset('admin-assets/dragimage/dist/image-uploader.min.css')}}" rel="stylesheet"> 
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
<style>
    .note-btn[aria-label="Video"] {
        display: none !important;
    } 
	.note-btn[aria-label="Link (CTRL+K)"] {
        display: none !important;
    }
</style>

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
                            <a class="btn-primary" href="{{ url('admin/batch/list')}}">
                                <i class="fa fa-list"></i> Batch List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Batch</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.batch.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
						<div class="row clearfix">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Course <label class="text-danger">*</label></label>
										<select class="form-control" name="course_id" required >
											<option  selected value="">--Select--</option>
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
										<label for="inputName">Name <label class="text-danger">*</label></label>
										<input type="text" name="name" required  class="form-control" placeholder="Enter Name" value="@if(!empty($result)){{ $result['name'] }}@endif"/>
									</div>
								</div>
							</div>
						</div>

                        <div class="row clearfix">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Start Date<label class="text-danger">*</label></label>
										<input type="date" name="start_date" required  class="form-control" value="@if(!empty($result)){{ $result['start_date'] }}@endif"/>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">End Date<label class="text-danger">*</label></label>
										<input type="date" name="end_date" required  class="form-control" value="@if(!empty($result)){{ $result['end_date'] }}@endif"/>
									</div>
								</div>
							</div>
						</div>
                        <div class="row clearfix">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Price <label class="text-danger">*</label></label>
										<input type="text" name="price" required  class="form-control" onkeypress="return /[0-9. ]/i.test(event.key)" placeholder="Enter Price" value="@if(!empty($result)){{ $result['price'] }}@endif"/>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Dollar Price <label class="text-danger">*</label></label>
										<input type="text" name="dollar_price" required  class="form-control" onkeypress="return /[0-9. ]/i.test(event.key)" placeholder="Enter Dollar Price" value="@if(!empty($result)){{ $result['dollar_price'] }}@endif"/>
									</div>
								</div>
							</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputimg">Image <label class="text-danger">*</label></label>
										<input type="file" id="inputimg" class="form-control image"  name="image" @if(!$result) @endif  data-type="single" data-image-preview="image_preview" accept="image/*"  >
										<!-- <p style="color:red;width:100%">Size must be 178*108 px</p> -->
									</div>
								</div>
								
								<div class="form-group previewimages image col-md-6" id="image_preview">
									@if($result)
										<img src="{{ asset('uploads/batch/'.$result->image) }}" style="width: 100px;border:1px solid #222;margin-right: 13px" />
										<input type="hidden" name="old_image" value="{{ $result->image }}" />
									@endif
								</div>
							</div>

						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Short description <label class="text-danger">*</label></label>
										<input type="text" name="short_desc"  required  class="form-control" placeholder="Enter Short Description" maxlength="50" value="@if(!empty($result)){{ $result['short_desc'] }}@endif"/>
									</div>
								</div>
							</div>
						</div>

                        <div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName"> Description <span style="color:red;">(Enter every feature with separated by comma)</span><label class="text-danger">*</label></label>
										<textarea  type="text" name="desc" required placeholder="Enter every feature with separated by comma" class="form-control" >@if(!empty($result)){{ $result['desc'] }}@endif</textarea> 
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
	$('#summernote').summernote({
		placeholder: 'Enter Description',
		tabsize: 2,
		height: 200,
	});
</script>
@endpush
