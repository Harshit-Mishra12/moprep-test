@extends('layouts/master')

@section('title',__('Question View'))

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
                        <div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/questionBank/yearList') }}">
                                <i class="fa fa-plus"></i> Question List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="header">
                        <h2><i class="fa fa-th"></i> Questions ({{$yearName->year}} {{$yearName->part}})</h2>
                    </div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <select id="bulk-status" class="form-control">
                                            <label>Update Bulk PreLive</label>
                                            <option value="">Change Prelive/Live</option>
                                            <option value="1">Live</option>
                                            <option value="0">Prelive</option>
                                        </select>
                                    </div>
                                        <div class="col-sm-3">
                                            <button id="bulk-update-btn" class="btn btn-primary">Apply</button>
                                        </div>
                                        
                                    
                                    
                                    <div class="col-sm-12">
                                        <table class="table table-hover js-basic-example contact_list dataTable yajra-datatable"
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
                                                        aria-label="#: activate to sort column descending"> Question
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
                                                    <td class="center">
                                                        <input type="checkbox" class="row-checkbox" data-id="<?= $value->id ?>" value="<?= $value->id ?>" >
                                                    </td>
                                                    <td class="center">{{ $key+1}}</td>

                                                    <td class="center">{!! $value->question ?? "NA"!!}</td>

                                                    <td class="center">
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <!-- <input type="checkbox" class="-live"> -->
                                                                <input type="checkbox" class="-live" data-id="<?= $value->id ?>" <?= ($value->is_live == '1') ? 'checked' : '' ?>>
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <td class="center">
                                                         @if(isset($permission) && $permission['edit'] == 'true')
                                                        <a href="{{ url('admin/questionBank/updateBulkQuestion/'.$value->id )}}" title="Edit Question" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                         @endif
                                                         @if(isset($permission) && $permission['delete'] == 'true')
                                                        <a title="Delete Question" onclick="return confirm('Are you sure? You want to delete this Question.')" href="{{ url('admin/questionBank/deleteBulkQuestion/'.$value->id )}}" class="btn btn-tbl-delete">
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
                                                    <th class="center" rowspan="1" colspan="1"> Question </th>
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

        var recall = $(this).prop('checked') == true ? '1' : '0';
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.question_bank.question.changeRecall') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'recall': recall,
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
            url: "{{ route('admin.questionBank.changeliveprevyear') }}",
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
    $(document).ready(function () {
        $('.yajra-datatable').DataTable().destroy();
$('.yajra-datatable').DataTable({
    pageLength: 500,
    lengthMenu: [[10, 25, 50, 100, 500,1000], [10, 25, 50, 100, "500","1000"]]
});

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
            url: "{{ route('admin.questionBank.bulkUpdatePYQQuestion') }}",
            method: "POST",
            data: {
                ids: selectedIds,
                status: status,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                alert(response.message);
                window.location.reload();
            },
            error: function () {
                alert("An error occurred while updating.");
            }
        });
    });
});

</script>
@endpush