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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Mockup Question By Year</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.mockupquestion.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
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
                                                        <option value="{{ $mockup->id }}" >{{ ucfirst($mockup->name) }} {{$mockup->course_master_name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
								

								<div class="col-sm-6" id="">
									<div class="form-group">
										<div class="form-line">
											<label for="inputName">Select Year & Part <label class="text-danger">*</label></label>
											<select class="form-control yearSelect" required name="year_id">
												<option value="" disabled selected>-- Select --</option>
                                                @foreach($previousYear as $previous)
													<option value="{{ $previous->id }}" >( {{ $previous->year }} ) ( {{ $previous->part }} )</value>
                                                @endforeach
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
		var yearId=$(this).val();

		var mock_id = $('#mock_id').val();
		
		if(mock_id==''){
			alert('Please select Mock Test');
			return false;
		}

		

		$.ajax({
			url: baseUrl + '/admin/mockupquestion/loadQuestion?id='+yearId+'&mock_id='+mock_id,
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

	
	if(result){
		
		$('.courseHtml').trigger('change'); 
		//$('.chapterHtml').trigger('change'); 
	}

	function resetFormData(){
		location.reload();
	}

	function resetYear(){
		$('.yearSelect').val('');
		$('#questionHtml').html('');
	}

	$(document).ready(function () {
		$('.fSelect').fSelect({
			placeholder: '-- Select --',
		});
	
	});

</script>
	
@endpush