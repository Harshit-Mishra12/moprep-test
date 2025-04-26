@extends('layouts/master')

@section('title', __('Year List'))

@section('content')
<style>
    .btn_change {
        line-height: 0;
    }

    .btn_change i {
        margin: 0 !important;
    }
</style>

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
                            <a class="btn-primary" href="{{ url('admin/questionBank/addQuestionWord') }}">
                                <i class="fa fa-plus"></i> Add Question
                            </a>
                        </div>
                        <div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/questionBank/addQuestionSingle')}}">
                                <i class="fa fa-list"></i> Add Manual Question
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
                        <h2><i class="fa fa-th"></i> Year List</h2>
                    </div>

                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <label for="inputName">Select Course <label
                                                                class="text-danger">*</label></label>
                                                        <select class="form-control" name="course_id" required
                                                            id="course_id">
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
                                            <div class="col-sm-3">
                                                <label for="bulk-status">Bulk Update </label>
                                                <select id="bulk-status" class="form-control">
                                                    <option value="">Change Status</option>
                                                    <option value="1">Live</option>
                                                    <option value="0">Prelive</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3"> 
                                    
                                                <button id="bulk-update-btn" class="btn btn-primary mt-4">Apply Bulk Update</button>
                                            </div>
                                               
                                            </div>
    
                                        </div>
                            
                                        <table class="table table-hover contact_list dataTable yajra-datatable"
                                            id="DataTables_Table_0" role="grid"
                                            aria-describedby="DataTables_Table_0_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="center">
                                                        <input type="checkbox" id="select-all">
                                                    </th>
                                                    
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"># ID</th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Year
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Part
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Total
                                                        Questions
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Live
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
                                            <tbody class="row_position">
                                                <!-- @if(!empty($result))
													@foreach($result as $key=>$value)
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
															<td class="center">{{ $value->year}}</td>
															<td class="center">{{$value->part }}</td>
															<td class="center">{{ $value->question_count }}</td>
                                                            <td class="center text-center">
                                                                

                                                                <a href="{{ url('admin/questionBank/questionBulkList/'.$value->id)}}" title="View Questions" class="btn btn-tbl-edit btn_change">
                                                                    <i class="fa fa-eye"></i>
                                                                </a>

                                                                <a href="{{ url('admin/questionBank/yearPartUpdate/'.$value->id )}}" title="Edit Year" class="btn btn-tbl-edit btn_change">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a title="Delete Year and Its Questions" onclick="return confirm('Are you sure? You want to delete this Year and all its Questions.')" href="{{ url('admin/questionBank/deleteYearQuestion/'.$value->id )}}" class="btn btn-tbl-delete btn_change">
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
                                                    <th class="center" rowspan="1" colspan="1"> Year </th>
                                                    <th class="center" rowspan="1" colspan="1"> Part </th>
                                                    <th class="center" rowspan="1" colspan="1"> Total Questions </th>
                                                    <th class="center" rowspan="1" colspan="1"> Live </th>
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
                    url: "{{ route('admin.questionBank.questionYearListData') }}",
                    data: function (d) {
                        d.name = $('#name-filter').val();
                        d.course_id = $('#course_id').val();
                    }
                },

                fnDrawCallback: function () {
                    Live();
                    Lock();
                },
                columns: [
                    {
    data: 'id',
    name: 'id',
    className: 'center',
    orderable: false,
    searchable: false,
    render: function (data, type, row) {
        return `<input type="checkbox" class="row-checkbox" value="${data}">`;
    }
},{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'center'
                },
                {
                    data: 'year',
                    name: 'year',
                    className: 'center'
                },
                {
                    data: 'part',
                    name: 'part',
                    className: 'center'
                },
                {
                    data: 'total_question',
                    name: 'total_question',
                    className: 'center'
                },
                {
                    data: 'is_live',
                    name: 'is_live',
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
            $('#course_id').change(function () {
                table.ajax.reload();
            });

        });

        function Live() {
            $('.-live').change(function () {

                var is_live = $(this).prop('checked') == true ? '1' : '0';
                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.questionBank.changelivepreyearlist') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'is_live': is_live,
                        'id': id
                    },
                    beforeSend: function () {
                        $('#preloader').css('display', 'block');
                    },
                    error: function (xhr, textStatus) {

                        if (xhr && xhr.responseJSON.message) {
                            sweetAlertMsg('error', xhr.is_live + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.is_live + ': ' + xhr.statusText);
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
                    url: "{{ route('admin.questionBank.changeLockPrevYear') }}",
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
                            sweetAlertMsg('error', xhr.is_lock + ': ' + xhr.responseJSON.message);
                        } else {
                            sweetAlertMsg('error', xhr.is_lock + ': ' + xhr.statusText);
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
        $(document).ready(function () {
    var table = $('.yajra-datatable').DataTable();

    // Select/Deselect All Checkboxes
    $('#select-all').on('click', function () {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // Bulk Update Button Click
    $('#bulk-update-btn').on('click', function () {
        var selectedIds = [];
        $('.row-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        var status = $('#bulk-status').val();

        if (selectedIds.length === 0) {
            alert("Please select at least one row.");
            return;
        }

        if (!status) {
            alert("Please select a status.");
            return;
        }

        // Send AJAX request to update the status
        $.ajax({
            url: "{{ route('admin.questionBank.bulkUpdateStatus') }}",
            method: "POST",
            data: {
                ids: selectedIds,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                alert(response.message);
                table.ajax.reload();
            },
            error: function () {
                alert("An error occurred while updating.");
            }
        });
    });
});

    </script>

    
@endpush