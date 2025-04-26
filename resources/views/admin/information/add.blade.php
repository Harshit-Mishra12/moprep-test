@extends('layouts/master')

@section('title')
{{ ucfirst($result['title']) }}
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
						<h2><i class="fa fa-th"></i> {{ ucfirst($result['title']) }} </h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.information.update') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@endif"  required />
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Description <label class="text-danger">*</label></label>
										<textarea class="form-control" name="description" rows="4"  id="summernote" cols="50">@if(!empty($result)){{$result['description']}}@endif</textarea>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-lg-12 p-t-20 text-center">
							
							<button style="background:#353c48;" type="submit" class="btn btn-success waves-effect m-r-15" >Update</button> 
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