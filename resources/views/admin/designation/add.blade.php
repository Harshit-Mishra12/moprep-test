@extends('layouts/master')

@section('title')
	@if(!empty($result))
		Update 
	@else
		Add 
	@endif
	designation
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
                            <a class="btn-primary" href="{{ url('admin/designation/list')}}">
                                <i class="fa fa-list"></i> Designation List
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
						<h2><i class="fa fa-th"></i> @if(!empty($result)) Update @else Add @endif Designation</h2>
					</div>
					<div class="body">
						<form id="form" action="{{ route('admin.designation.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						
						<input type="hidden" name="id" value="@if(!empty($result)){{$result['id']}}@else{{ 0 }}@endif"  required />
						
						<div class="row clearfix">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Name<label class="text-danger">*</label></label>
										<input type="tel" name="designations" required  class="form-control" placeholder="Enter designation Name" value="@if(!empty($result)){{ $result['designations'] }}@endif"/>
									</div>
								</div>
							</div>
						</div>

						<table class="table ">
							<thead>
								<tr>
									<td class="center" >Module</td>
									<td class="center" > Read </td>	
									<td class="center" > Write </td>	
									<td class="center" > Edit </td>	
									<td class="center" > Delete </td>	
								</tr>
							<thead>

							@if(!empty($menuList))
								@foreach($menuList as $menu)
								<tr>
									<td class="center">{{ $menu->label }}
										<input type="hidden" name="permissions[{{ $menu->id }}][id]" value="{{ $menu->id }}">
									</td>
									
									@foreach(['read', 'write', 'edit', 'delete'] as $action)
									<td class="center">
										<div class="switch mt-3">
											<label>
												<input type="checkbox" class="-change" name="permissions[{{ $menu->id }}][{{ $action }}]" 
													{{  ($type == 'edit') ? (isset($storedPermissions[$menu->id][$action]) && $storedPermissions[$menu->id][$action] ? 'checked' : '') : ''; }}>
												<span class="lever switch-col-red layout-switch"></span>
											</label>
										</div>
									</td>
									@endforeach
								</tr>
								@endforeach
							@endif
						</table>						
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
			
			$('.previewimages').html('');
		}
	</script>
@endpush