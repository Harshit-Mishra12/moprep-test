@extends('layouts/master')

@section('title', __('Mockup List'))

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
                        @if(isset($permission) && $permission['write'] == 'true')
                        <div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/mockup/add') }}">
                                <i class="fa fa-plus"></i> Add Mockup
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
                        <h2><i class="fa fa-th"></i> Mockup List</h2>
                    </div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
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
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Name
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Duration
                                                        (Mins)
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Start
                                                        Date/Time
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> End
                                                        Date/Time
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Image
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Created
                                                        Date
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Status
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Lock
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- @if(!empty($result))
													@foreach($result as $key=>$value)
														<tr class="gradeX odd">
															<td class="center">{{ $key+1}}</td>
															<td class="center">{{ $value->name }}</td>
															<td class="center">{{ $value->duration }}</td>
															<td class="center">{{ date('d-m-Y',strtotime($value->start_date)) }} {{ date('h:i A',strtotime($value->start_time)) }}</td>
															<td class="center">{{ date('d-m-Y',strtotime($value->end_date)) }} {{ date('h:i A',strtotime($value->end_time)) }}</td>
															<td class="center"><img src="{{ asset('uploads/banner/'.$value->image) }} " width="65px	"/></td> 
															<td class="center">{{ date('d-m-Y',strtotime($value->created_at)) }}</td>
																<td class="center">
																	<div class="switch mt-3">
																		<label>
																			<input type="checkbox" class="-change" data-id="{{ $value['id'] }}" @if($value['status']=='Active'){{ 'checked' }} @endif>
																			<span class="lever switch-col-red layout-switch"></span>
																		</label>
																	</div>
																</td>
                                                                
																<td class="center">
																
																	<a href="{{ url('admin/mockup/update/'.$value['id'] )}}" title="Edit Mockup" class="btn btn-tbl-edit">
																		<i class="fas fa-pencil-alt"></i>
																	</a>
																	<a title="Delete Mockup" onclick="return confirm('Are you sure? You want to delete this Mockup.')" href="{{ url('admin/mockup/delete/'.$value['id'] )}}" class="btn btn-tbl-delete">
																		<i class="fas fa-trash"></i>
																	</a>
																</td>
														</tr>
													@endforeach
												@endif -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1"># ID</th>
                                                    <th class="center" rowspan="1" colspan="1"> Course</th>
                                                    <th class="center" rowspan="1" colspan="1"> Name</th>
                                                    <th class="center" rowspan="1" colspan="1"> Duration (Mins)</th>
                                                    <th class="center" rowspan="1" colspan="1"> Start Date/Time</th>
                                                    <th class="center" rowspan="1" colspan="1"> End Date/Time</th>
                                                    <th class="center" rowspan="1" colspan="1"> Image </th>
                                                    <th class="center" rowspan="1" colspan="1"> Created Date </th>
                                                    <th class="center" rowspan="1" colspan="1"> Status </th>
                                                    <th class="center" rowspan="1" colspan="1"> Lock </th>
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
        $(function () {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.mockup.mockupListData') }}",
                    data: function (d) {
                        d.name = $('#name-filter').val();
                        d.course_id = $('#course_id').val();
                    }
                },
                fnDrawCallback: function () {
                    Status();
                    Lock();
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
                    data: 'name',
                    name: 'year_part',
                    className: 'center'
                },
                {
                    data: 'duration',
                    name: 'chapter_name',
                    className: 'center'
                },
                {
                    data: 'start_date_time',
                    name: 'start_date_time',
                    className: 'center'
                },
                {
                    data: 'end_date_time',
                    name: 'end_date_time',
                    className: 'center'
                },
                {
                    data: 'banner',
                    name: 'banner',
                    className: 'center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'center'
                },
                {
                    data: 'status',
                    name: 'status',
                    className: 'center'
                },
                {
                    data: 'is_lock',
                    name: 'is_lock',
                    className: 'center'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'center',
                    orderable: true,
                    searchable: true
                },
            ]


            });
            $('#course_id').change(function() {
                table.ajax.reload();
            });

        });

     
        function Status() {
            $('.-change').change(function () {

                var status = $(this).prop('checked') == true ? 'Active' : 'Pending';
                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.mockup.changestatus') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'status': status,
                        'id': id
                    },
                    beforeSend: function () {
                        $('#preloader').css('display', 'block');
                    },
                    error: function (xhr, textStatus) {

                        if (xhr && xhr.responseJSON.message) {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                        }
                        $('#preloader').css('display', 'none');
                    },
                    success: function (data) {
                        $('#preloader').css('display', 'none');
                        sweetAlertMsg('success', data.message);
                    }
                });
            });
        }

        function Lock() {
            $('.-lock').change(function () {

                var is_lock = $(this).prop('checked') == true ? '1' : '0';
                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.mockup.changeLock') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'is_lock': is_lock,
                        'id': id
                    },
                    beforeSend: function () {
                        $('#preloader').css('display', 'block');
                    },
                    error: function (xhr, textStatus) {

                        if (xhr && xhr.responseJSON.message) {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                        }
                        $('#preloader').css('display', 'none');
                    },
                    success: function (data) {
                        $('#preloader').css('display', 'none');
                        sweetAlertMsg('success', data.message);
                    }
                });
            });
        }
  </script>
@endpush