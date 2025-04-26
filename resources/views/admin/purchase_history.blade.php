@extends('layouts/master')

@section('title',__('Transaction History'))

@section('content')

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
                                 <button type="button" id="filterButton" class="btn btn-success pull-left" style="margin-top:25px">
                                 Search
                                 <img src="/Content/loading/loading.gif" id="loaderImage" style="display: none; height: 15px;" class="img-responsive">
                                 </button>
                              </div>
                           </div>                             
                           <div class="col-md-2">
                              <div class="form-group">
                                    <!-- <div class="clearfix">&nbsp;</div> -->
                                    <a href="{{url('admin/purchase-history')}}" class="btn btn-danger pull-left" style="margin-top:25px;padding: 8px 14px;">
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
                            <h2><i class="fa fa-th"></i> Transaction History</h2>
                        </div>
                        <div class="col-md-2">
                            <a href="{{url('admin/purchase-history-excel')}}"  class="btn btn-warning pull-left" style="padding-top: 9px;">
                                    Export Excel     
                            </a>
                        </div>
					</div>
                    <div class="body">
                        <div class="table-responsive">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 table-responsive">

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <label for="inputName">Select Course <label
                                                            class="text-danger">*</label></label>
                                                    <select class="form-control" name="course_id" required id="course_id">
                                                        <option selected value="">--Select--</option>
                                                        @if(!empty($course))
                                                        @foreach($course as $c)
                                                        <option value="{{ $c['id'] }}">{{ ucfirst($c['name']) }}
                                                        </option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-hover yajra-datatable contact_list dataTable"
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
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Student Name</th> 
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Student Mobile</th>     
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Paid Amount</th>    
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Transaction ID</th> 
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Order ID</th> 
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Subscription Plan Purchased
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Transaction Date
                                                    </th> 
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Start Date
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> End Date
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Status
                                                    </th> 
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="row_position">
												<!-- @if(!empty($result))
													@foreach($result as $key=>$value)
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
															<td class="center">{{ $value['student_name'] }}</td>
															<td class="center">{{ $value['student_mobile'] }}</td>
															<td class="center">{{ $value['paid_amount'] }}</td>
															<td class="center">{{ $value['transaction_id'] }}</td>
															<td class="center">{{ $value['subscription_name'] }}</td>
															<td class="center">{{ date('d-m-Y h:i A',strtotime($value['created_at'])) }}</td>
                                                            <td class="center">
                                                                <?php
                                                                    // if($value['status']==0){
                                                                    //     echo "<span class='text-warning'>Pending</span>";
                                                                    // }elseif($value['status']=='1'){
                                                                    //     echo "<span class='text-success'>Success</span>";
                                                                    // }elseif($value['status']=='2'){
                                                                    //     echo "<span class='text-danger'>Failed</span>";
                                                                    // }else{
                                                                    //     echo "NA";
                                                                    // }
                                                                ?>
                                                            </td>
                                                            <td class="center">
                                                                <a title="Remove Transaction history" onclick="return confirm('Are you sure? You want to delete this transaction history.')" href="{{ url('admin/delete-purchase-history/'.$value['id'] )}}" class="btn btn-tbl-delete">
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
                                                    <th class="center" rowspan="1" colspan="1"> Course </th>
                                                    <th class="center" rowspan="1" colspan="1"> Student Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Student Mobile </th>
                                                    <th class="center" rowspan="1" colspan="1"> Paid Amount</th>
                                                    <th class="center" rowspan="1" colspan="1"> Transaction ID </th>
                                                    <th class="center" rowspan="1" colspan="1"> Order ID </th>
                                                    <th class="center" rowspan="1" colspan="1"> Subscription Plan Purchased</th>
                                                    <th class="center" rowspan="1" colspan="1"> Transaction Date</th>
                                                    <th class="center" rowspan="1" colspan="1"> Start Date</th>
                                                    <th class="center" rowspan="1" colspan="1"> End Date</th>
                                                    <th class="center" rowspan="1" colspan="1"> Status</th>
                                                    <th class="center" rowspan="1" colspan="1"> Action</th>

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
                url: "{{ route('admin.purchasedHistoryData') }}",
                data: function(d) {
                    d.name = $('#name-filter').val();
                    d.startDate = $('#txtfromDate').val();
                    d.toDate = $('#txttoDate').val();
                    d.course_id = $('#course_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'center'
                },
                {
                    data: 'coursemaster',
                    name: 'coursemaster',
                    className: 'center'
                },
                {
                    data: 'student_name',
                    name: 'student_name',
                    className: 'center'
                },
                {
                    data: 'student_mobile',
                    name: 'student_mobile',
                    className: 'center'
                },
                {
                    data: 'paid_amount',
                    name: 'paid_amount',
                    className: 'center'
                },
                {
                    data: 'transaction_id',
                    name: 'transaction_id',
                    className: 'center'
                },
                {
                    data: 'paymentid',
                    name: 'paymentid',
                    className: 'center'
                },
                {
                    data: 'subscription_name',
                    name: 'subscription_name',
                    className: 'center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'center'
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    className: 'center'
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    className: 'center'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'center'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'center',
                    orderable: true,
                    searchable: true
                }
            ],
        });

        $('#course_id').change(function() {
             $('.yajra-datatable').DataTable().ajax.reload();
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
@endpush