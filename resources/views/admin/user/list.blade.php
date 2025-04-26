@extends('layouts/master')

@section('title',__('Student List'))

@section('content')
<style>
    .btn_change{
        line-height: 0;
    }

    .btn_change i{
        margin: 0 !important; 
    }
</style>
<section class="content">
    <div class="container-fluid">
		
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <div class="card">
                  <div class="header">
                     <h2><i class="fa fa-th"></i> Filters</h2>
                  </div>
                  <div class="body">
                     <div class="row">
                        
                           <div class="row">
                           <!-- <div class="col-md-2">
                              <div class="form-group">
                                 <label for="">Student Added On :</label>
                                 <select class="form-control" required id="duration" name="duration">
                                    <option value="" disabled selected>-- Select --</option>
                                    <option value="day" <?php if(isset($duration) && !empty($duration) && $duration=='day'){ echo "selected"; } ?>>Last Day</option>
                                    <option value="week" <?php if(isset($duration) && !empty($duration) && $duration=='week'){ echo "selected"; } ?>>Last Week</option>
                                    <option value="month" <?php if(isset($duration) && !empty($duration) && $duration=='month'){ echo "selected"; } ?>>Last Month</option>
                                 </select>
                              </div>
                           </div> -->

                           <div class="col-md-2">
                              <div class="form-group">
                                 <label for="">From Date:</label>
                                 <input type="date" id="txtfromDate" value="@if(isset($_GET['startDate']) && $_GET['startDate'] != ''){{date('Y-m-d',strtotime($_GET['startDate']))}}@else{{date('Y-m-d')}} @endif" class="form-control" name="startDate">
                              </div>
                           </div>
                           <div class="col-md-2">
                              <div class="form-group">
                                 <label for="">To Date:</label>
                                 <input type="date" id="txttoDate" value="@if(isset($_GET['toDate']) && $_GET['toDate'] != ''){{date('Y-m-d',strtotime($_GET['toDate']))}}@else{{date('Y-m-d')}} @endif" class="form-control" name="toDate">
                              </div>
                           </div>
                                                 
                           <div class="col-md-1">
                              <div class="form-group">
                                 <!-- <div class="clearfix">&nbsp;</div> -->
                                 <button type="button" id="filterButton"  class="btn btn-success pull-left" style="margin-top:25px">
                                 Search
                                 <img src="/Content/loading/loading.gif" id="loaderImage" style="display: none; height: 15px;" class="img-responsive">
                                 </button>
                              </div>
                           </div>                             
                           <div class="col-md-2">
                              <div class="form-group">
                                    <!-- <div class="clearfix">&nbsp;</div> -->
                                    <a href="{{url('admin/user/list')}}" class="btn btn-danger pull-left" style="margin-top:25px;padding: 8px 14px;">
                                       Clear Filter
                                    </a>
                              </div>
                           </div>
                           </div>
                       
                     </div>
                  </div>
               </div>
            </div>
         </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                <div class="row header">
                        <div class="col-md-10">
                            <h2><i class="fa fa-th"></i> Student List</h2>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('admin/user-list-excel')}}"  class="btn btn-warning pull-left" style="padding-top: 9px;">
                                    Export Excel     
                            </a>
                        </div>
					</div>
                    <div class="body">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 table-responsive">
                                        <table class="table table-hover yajra-datatable contact_list dataTable"
                                            id="DataTables_Table_0" role="grid"
                                            aria-describedby="DataTables_Table_0_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"># ID</th>
                                                      
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Name</th>
                                                       
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Mobile Number
                                                        </th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Email Address
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> College
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> State
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Subscription Status
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Added On
                                                        </th> 
                                                        <th class="center sorting" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 193.017px;"
                                                            aria-label=" Email : activate to sort column ascending"> Status
                                                        </th>
                                                        <th class="center sorting" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 193.017px;"
                                                            aria-label=" Email : activate to sort column ascending"> Action
                                                        </th>
                                                </tr>
                                            </thead>
                                            <tbody>
												<!-- @if(!empty($result))
													@foreach($result as $key=>$value)
														<tr class="gradeX odd">
															<td class="center">{{ $key+1}}</td>
															
															<td class="center">{{ $value['name'] }}</td>
															
															<td class="center">{{ $value['whatsapp_number'] }}</td>
															<td class="center">{{ $value['college']!='' ? $value['college'] : 'NA' }}</td>
															<td class="center">{{ $value['state']!='' ? $value['state'] : 'NA' }}</td>
															<td class="center">{{ 'Inactive' }}</td>
															<td class="center">{{ date('d-m-Y',strtotime($value['created_at'])) }}</td>
															<td class="center">
                                                                <div class="switch mt-3">
                                                                    <label>
                                                                        <input type="checkbox" class="-change" data-id="{{ $value['id'] }}"@if($value['status']=='1'){{ 'checked' }} @endif>
                                                                        <span class="lever switch-col-red layout-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                            <a href="{{ url('admin/user/examDetail/'.$value['id'] )}}" title="View Student Analysis" class="btn btn-tbl-edit">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ url('admin/user/update/'.$value['id'] )}}" title="Edit Student" class="btn btn-tbl-edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>    
                                                            <a title="Remove User" onclick="return confirm('Are you sure? You want to delete this student.')" href="{{ url('admin/user/delete/'.$value['id'] )}}" class="btn btn-tbl-delete btn_change">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                           
                                                        </td>
														</tr>
													@endforeach
												@endif -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                <th class="center" rowspan="1" colspan="1">#</th>
                                                <th class="center" rowspan="1" colspan="1"> Name </th>
                                                <th class="center" rowspan="1" colspan="1"> Mobile Number </th>
                                                <th class="center" rowspan="1" colspan="1"> Email Address </th>
                                                <th class="center" rowspan="1" colspan="1"> College </th>
                                                <th class="center" rowspan="1" colspan="1"> State </th>
                                                <th class="center" rowspan="1" colspan="1"> Subscription Status </th>
                                                <th class="center" rowspan="1" colspan="1"> Added On </th>
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
 function initializeDataTable() {
            $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.user.userListData') }}",
                    data: function (d) {
                        d.name = $('#name-filter').val();
                        d.startDate = $('#txtfromDate').val();
                        d.toDate = $('#txttoDate').val();
                    }
                },
                fnDrawCallback: function() {
                    Status();
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'center'},
                    {data: 'name', name: 'name', className: 'center'},
                    {data: 'whatsapp_number', name: 'whatsapp_number', className: 'center'}, 
                    {data: 'email', name: 'email', className: 'center'}, 
                    {data: 'college', name: 'college', className: 'center'}, 
                    {data: 'state', name: 'state', className: 'center'}, 
                    {data: 'subscription_status', name: 'subscription_status', className: 'center'}, 
                    {data: 'created', name: 'created', className: 'center'}, 
                    {data: 'status', name: 'status', className: 'center'}, 
                    {
                        data: 'action', 
                        name: 'action', 
                        className: 'center',
                        orderable: true, 
                        searchable: true
                    }
                ],
            });
        }

        $(document).ready(function() {
            // Initialize the DataTable
            initializeDataTable();

            // Handle filter button click
            $('#filterButton').click(function() {
                $('.yajra-datatable').DataTable().ajax.reload();
            });
        });
</script>

    <script>
       function Status(){
        $('.-change').change(function() {

            var status = $(this).prop('checked') == true ? '1' : '0';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.user.changestatus') }}",
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
    }
		
    </script>  
  
 
    
@endpush