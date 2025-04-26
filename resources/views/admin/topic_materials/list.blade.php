@extends('layouts/master')

@section('title', __('Topic List'))

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
                            <a class="btn-primary" href="{{ url('admin/topic-materials/add') }}">
                                <i class="fa fa-plus"></i> Add Topic
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
                        <h2><i class="fa fa-th"></i> Topic List</h2>
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
                                                        aria-label="#: activate to sort column descending"> Subject</th>

                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Chapter</th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Topic
                                                    </th>

                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Status
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Live
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Locked
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
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
														
															<td class="center">{{ \App\Helpers\commonHelper::getChapterName($value->batch_id)}}</td>
															<td class="center">{{ $value->topic}}</td>

															<td class="center">
                                                                <div class="switch mt-3">
                                                                    <label>
                                                                        <input type="checkbox" class="-change" data-id="{{ $value['id'] }}"@if($value['status']=='1'){{ 'checked' }} @endif>
                                                                        <span class="lever switch-col-red layout-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                            
                                                                <a href="{{ url('admin/question_bank/question/view/'.$value['course_id'] .'/revisionexam')}}" title="View Questions" target="_blank" class="btn btn-tbl-edit" style="line-height:0px;">
                                                                    <i class="fa fa-eye me-0"></i>
                                                                </a>
                                                                <a href="{{ url('admin/topic-materials/update/'.$value['id'] )}}" title="Edit Topic Materials" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a title="Delete Topic Materials" onclick="return confirm('Are you sure? You want to delete this topic materials.')" href="{{ url('admin/topic-materials/delete/'.$value['id'] )}}" class="btn btn-tbl-delete">
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

                                                    <th class="center" rowspan="1" colspan="1"> Subject </th>
                                                    <th class="center" rowspan="1" colspan="1"> Chapter </th>
                                                    <th class="center" rowspan="1" colspan="1"> Topic </th>
                                                    <th class="center" rowspan="1" colspan="1"> Status </th>
                                                    <th class="center" rowspan="1" colspan="1"> Live </th>
                                                    <th class="center" rowspan="1" colspan="1"> Locked </th>
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
                    url: "{{ route('admin.topic-materials.topicMaterialListData') }}",
                    data: function (d) {
                        d.name = $('#name-filter').val();
                    }
                },
                fnDrawCallback: function () {
                    Status();
                    Live();
                    Lock();
                },

                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'center' },
                    //   {data: 'coursemaster', name: 'coursemaster', className: 'center'},
                    { data: 'subject', name: 'subject', className: 'center' },
                    { data: 'chapter', name: 'chapter', className: 'center' },
                    { data: 'topic', name: 'topic', className: 'center' },
                    { data: 'status', name: 'status', className: 'center' },
                    { data: 'is_live', name: 'is_live', className: 'center' },
                    { data: 'is_lock', name: 'is_lock', className: 'center' },

                    {
                        data: 'action',
                        name: 'action',
                        className: 'center',
                        orderable: true,
                        searchable: true
                    },
                ]


            });

        });

        function Status() {

            $('.-change').change(function () {

                var status = $(this).prop('checked') == true ? '1' : '0';
                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.topic-materials.changestatus') }}",
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
        function Live() {
            $('.-live').change(function () {

                var is_live = $(this).prop('checked') == true ? '1' : '0';
                var id = $(this).data('id');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.topic-materials.changelive') }}",
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
                    url: "{{ route('admin.topic-materials.changelock') }}",
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

    </script>

@endpush