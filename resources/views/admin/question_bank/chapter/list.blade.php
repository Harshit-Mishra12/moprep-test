@extends('layouts/master')

@section('title',__('Chapter List'))

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
                            <a class="btn-primary" href="{{ url('admin/question_bank/chapter/add') }}">
                                <i class="fa fa-plus"></i> Add Chapter
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="header">
                        <h2><i class="fa fa-th"></i> Chapter List</h2>
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
                                                    {{-- <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Course Name --}}
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Subject Name
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Chapter Name
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
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="row_position">
                                                @if(!empty($result))
                                                @foreach($result as $key=>$value)
                                                <tr class="gradeX odd" id="{{ $value->id }}">
                                                    <td class="center">{{ $key+1}}</td>
                                                    {{-- <td class="center">{{ $value->course_master_details->name ?? "NA"}}</td> --}}
                                                    <td class="center">{{ $value->course_details->name ?? "NA"}}</td>
                                                    <td class="center">{{ $value->name}}</td>
                                                    <td class="center">
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change" data-id="{{ $value['id'] }}" @if($value['status']=='Active' ){{ 'checked' }} @endif>
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-live" data-id="{{ $value['id'] }}" @if($value['is_live']=='1' ){{ 'checked' }} @endif>
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="center">
                                                        <a href="{{ url('admin/question_bank/chapter/view/'.$value['id'] )}}" title="View Topics" class="btn btn-tbl-edit">
                                                            <i class="fas fa-eye"></i>
                                                            </a>
                                                             @if(isset($permission) && $permission['edit'] == 'true')
                                                            <a href="{{ url('admin/question_bank/chapter/update/'.$value['id'] )}}" title="Edit Chapter" class="btn btn-tbl-edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endif
                                                             @if(isset($permission) && $permission['delete'] == 'true')
                                                            <a title="Delete Product" onclick="return confirm('Are you sure? You want to delete this chapter.')" href="{{ url('admin/question_bank/chapter/delete/'.$value['id'] )}}" class="btn btn-tbl-delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1">#</th>
                                                    {{-- <th class="center" rowspan="1" colspan="1"> Course Name </th> --}}
                                                    <th class="center" rowspan="1" colspan="1"> Subject Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Chapter Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Status </th>
                                                    <th class="center" rowspan="1" colspan="1"> Live </th>
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

        var status = $(this).prop('checked') == true ? 'Active' : 'De-Active';
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.question_bank.chapter.changestatus') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'status': status,
                'id': id
            },
            beforeSend: function() {
                $('#preloader').css('display', 'block');
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
                $('#preloader').css('display', 'none');
            },
            success: function(data) {
                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
            }
        });
    });
</script>

<script>
    $('.-live').change(function() {

        var is_live = $(this).prop('checked') == true ? '1' : '0';
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.question_bank.chapter.changelivechapterlist') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'is_live': is_live,
                'id': id
            },
            beforeSend: function() {
                $('#preloader').css('display', 'block');
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.is_live + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.is_live + ': ' + xhr.statusText);
                }
                $('#preloader').css('display', 'none');
            },
            success: function(data) {
                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
            }
        });
    });
</script>
@endpush