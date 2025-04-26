@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
		User
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
                            <a class="btn-primary" href="{{ url('admin/admin-user/list')}}">
                                <i class="fa fa-list"></i> User List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif User</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.admin-user.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />												
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Name <label class="text-danger">*</label></label>
										<input type="text" name="name" required class="form-control" placeholder="Enter User Name" value="@if(!empty($result)){{ $result['name'] }}@endif"/>
									</div>
								</div>
							</div>	
							@if(empty($result))
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Password <label class="text-danger">*</label></label>
										<input type="password" name="password" required class="form-control" placeholder="Enter Password" value="@if(!empty($result)){{ $result['password'] }}@endif"/>
									</div>
								</div>
							</div>	
							@endif
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Email <label class="text-danger">*</label></label>
										<input type="email" name="email" required  class="form-control" placeholder="Enter User Email" value="@if(!empty($result)){{ $result['email'] }}@endif"/>
									</div>
								</div>
							</div>	

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Mobile Number <label class="text-danger">*</label></label>
										<input type="tel" onkeypress="return /[0-9 .]/i.test(event.key)" name="whatsapp_number" required  class="form-control" placeholder="Enter User Mobile Number" value="@if(!empty($result)){{ $result['whatsapp_number'] }}@endif"/>
									</div>
								</div>
							</div>


							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Select Designations <label class="text-danger">*</label></label>
										<select class="form-control" name="designation_id" required>
											<option selected value="">--Select--</option>
											@if(!empty($designations))
											@foreach($designations as $designation)
											<option value="{{ $designation['id'] }}" @if(!empty($result) && $designation['id']==$result['designation_id']) {{ 'selected' }} @endif>{{ ucfirst($designation['designations']) }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">State</label>
										<input type="text" name="state" required class="form-control" placeholder="Enter State Name" value="@if(!empty($result)){{ $result['state'] }}@endif"/>
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