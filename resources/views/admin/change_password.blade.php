@extends('layouts/master')

@section('title',__('Change Password'))

@section('Changepassword',__('active'))

@section('content')
<section class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Change Password</h2>
					</div>
                    
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="body">
						<form id="form" action="" method="post" enctype="multipart/form-data">
						@csrf
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Enter Old Password <label class="text-danger">*</label></label>
                                        <input required type="text"  style="width:50%" id="inputName" class="form-control" name="old_pass">
									</div>
								</div>
							</div>
						</div>
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Enter New Password<label class="text-danger">*</label></label>
                                        <input required type="text"  style="width:50%" id="inputName" class="form-control" name="password">

									</div>
								</div>
							</div>
						</div>
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Enter Confirm  Password<label class="text-danger">*</label></label>
                                        <input required type="text"  style="width:50%" id="inputName" class="form-control" name="confirm_password">
									</div>
								</div>
							</div>
						</div>
						<div class="row clearfix">
							<div class="col-lg-6 p-t-20 text-center">
								<input style="margin-right: 91%;" type="submit" class="btn btn-primary" value="Submit">
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
