@extends('layouts/master')

@section('title', __('Subject List'))

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
                            <a class="btn-primary" href="{{ url('admin/subject/add') }}">
                                <i class="fa fa-plus"></i> Add Subject
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
                        <h2><i class="fa fa-th"></i> Subject List</h2>
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
                                                    <select class="form-control" name="course_id" required
                                                        id="course_id">
                                                        <option selected value="">--Select--</option>
                                                        @if(!empty($course))
                                                            @foreach($course as $c)
                                                                <option value="{{ $c['id'] }}" {{isset($_GET['course_id']) && $_GET['course_id'] == $c['id'] ? 'selected' : ''}}>{{ ucfirst($c['name']) }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-hover js-basic-example contact_list dataTable"
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
                                            <tbody class="row_position">
                                                @if(!empty($result))
                                                    @foreach($result as $key => $value)
                                                        <tr class="gradeX odd" id="{{ $value->id }}">
                                                            <td class="center">{{ $key + 1}}</td>
                                                            <td class="center">{{ $value->name}}</td>
                                                            <td class="center">
                                                                <div class="switch mt-3">
                                                                    <label>
                                                                        <input type="checkbox" class="-change"
                                                                            data-id="{{ $value['id'] }}"
                                                                            @if($value['status'] == '1'){{ 'checked' }} @endif>
                                                                        <span class="lever switch-col-red layout-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                                <a href="{{ url('admin/subject/view/' . $value['id'])}}"
                                                                    title="View Chapters" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                   @if($permission['write'])
                                                                <a href="{{ url('admin/subject/update/' . $value['id'])}}"
                                                                    title="Edit Subject" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                 @endif
                                                                   @if($permission['delete'])
                                                                <a title="Delete Product"
                                                                    onclick="return confirm('Are you sure? You want to delete this subject.')"
                                                                    href="{{ url('admin/subject/delete/' . $value['id'])}}"
                                                                    class="btn btn-tbl-delete">
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
                                                    <th class="center" rowspan="1" colspan="1"> Name </th>
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


        $('#course_id').on('change', function () {
            let course_id = $(this).val();
            let url = "{{ route('admin.subject.list') }}?course_id=" + course_id;
            window.location.href = url;  // Redirect with GET request
        });

        $('.-change').change(function () {

            var status = $(this).prop('checked') == true ? '1' : '0';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.subject.changestatus') }}",
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
    </script>

    <script type="text/javascript">
        $(".row_position").sortable({
            delay: 150,
            stop: function () {
                var selectedData = new Array();
                $(".row_position>tr").each(function () {
                    selectedData.push($(this).attr("id"));
                });
                updateOrder(selectedData);
            }
        });

        function updateOrder(aData) {
            $.ajax({
                url: "{{ route('admin.subject.change-order') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    allData: aData
                },
                success: function () {
                    swal("Success!", "Your change successfully saved", "success");
                }
            });
        }
    </script>

@endpush