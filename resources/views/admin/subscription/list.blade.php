@extends('layouts/master')

@section('title',__('Subscription List'))

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
                    @if(isset($permission) && $permission['write'] == 'true')
						<div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/subscription/add') }}">
                                <i class="fa fa-plus"></i> Add Subscription 
							</a>
                        </div>
                    @endIf
					</div>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Subscription List</h2>
					</div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover js-basic-example contact_list dataTable"
                                            id="DataTables_Table_0" role="grid"
                                            aria-describedby="DataTables_Table_0_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"># ID</th>

                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Course
                                                    </th>

                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Name
                                                    </th>   
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Duration
                                                    </th> 
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Price ( Rs. )
                                                    </th>  
                                                    <!-- <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Image
                                                    </th> -->
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Created Date
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Status
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($result))
                                                @foreach($result as $key=>$value)
                                                
                                                    <tr class="gradeX odd">
                                                    <td class="center">{{ $key+1}}</td>
                                                    <td class="center">{{ $value->CourseMaster->name ?? "NA"}}</td>
                                                    <td class="center">{{ $value->name }}</td>
                                                    <td class="center">{{ $value->duration }} <?php if ($value->duration_type == '1') {
                                                                                                    echo " Months";
                                                                                                } else if ($value->duration_type == '2') {
                                                                                                    echo " Days";
                                                                                                } ?>
                                                    </td>
                                                    <td class="center">{{ $value->price }}</td>
                                                    <!-- <td class="center"><img src="{{ asset('uploads/banner/'.$value->image) }} " width="65px	"/></td>  -->
                                                    <td class="center">{{ date('d-m-Y',strtotime($value->created_at)) }}</td>
                                                    <td class="center">
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change" data-id="{{ $value['id'] }}" @if($value['status']=='Active' ){{ 'checked' }} @endif>
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <td class="center">

                                                        <a href="{{ url('admin/subscription/update/'.$value['id'] )}}" title="Edit Subscription" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a title="Delete Subscription" onclick="return confirm('Are you sure? You want to delete this Subscription.')" href="{{ url('admin/subscription/delete/'.$value['id'] )}}" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1"># ID</th>
                                                    <th class="center" rowspan="1" colspan="1"> Course</th>
                                                    <th class="center" rowspan="1" colspan="1"> Name</th>
                                                    <th class="center" rowspan="1" colspan="1"> Duration</th>
                                                    <th class="center" rowspan="1" colspan="1"> Price ( Rs. )</th>
                                                    <!-- <th class="center" rowspan="1" colspan="1"> Image </th> -->
                                                    <th class="center" rowspan="1" colspan="1"> Created Date </th>
                                                    <th class="center" rowspan="1" colspan="1"> Status </th>
                                                    <th class="center" rowspan="1" colspan="1"> Action </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('custom_js')
    <script>
	
        $('.-change').change(function() {

            var status = $(this).prop('checked') == true ? 'Active' : 'Pending';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.subscription.changestatus') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                data: {
                    'status': status, 
                    'id': id
                },
                beforeSend:function(){
                    $('#preloader').css('display','block');
                },
                error:function(xhr,textStatus){
					
                    if(xhr && xhr.responseJSON.message){
						sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
					}else{
						sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
					}
                    $('#preloader').css('display','none');
                },
                success: function(data){
					$('#preloader').css('display','none');
                    sweetAlertMsg('success',data.message);
                }
            });
		});
		
    </script>                                           
@endpush