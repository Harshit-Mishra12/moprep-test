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
                                            <tbody class="row_position">

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
    $(function() {
        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.topic-materials.topicMaterialListData') }}",
                data: function(d) {
                    d.name = $('#name-filter').val();
                }
            },
            rowId: 'id', // This tells DataTables to use the 'id' property as row ID
            lengthMenu: [
                [10, 25, 50, 100, 500],
                [10, 25, 50, 100, 500]
            ], // Added 500 option
            pageLength: 10, // Default to 10 entries
            scrollY: '60vh', // Set a fixed height for scrollable area
            scrollCollapse: true,
            paging: true,
            fnDrawCallback: function() {
                Status();
                Live();
                Lock();
                initializeSortable();

                // Log initial order of IDs when table loads/redraws
                var initialIds = table.rows({
                    search: 'applied'
                }).ids().toArray();
                console.log("Initial topic IDs order:", initialIds);
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'center'
                },
                //   {data: 'coursemaster', name: 'coursemaster', className: 'center'},
                {
                    data: 'subject',
                    name: 'subject',
                    className: 'center'
                },
                {
                    data: 'chapter',
                    name: 'chapter',
                    className: 'center'
                },
                {
                    data: 'topic',
                    name: 'topic',
                    className: 'center'
                },
                {
                    data: 'status',
                    name: 'status',
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

        // Store all IDs when table first loads
        var allTopicIds = [];

        // Function to fetch all IDs from server
        function fetchAllTopicIds() {
            $.ajax({
                url: "{{ route('admin.topic-materials.topicMaterialListData') }}",
                data: {
                    length: -1 // Get all records
                },
                success: function(response) {
                    allTopicIds = response.data.map(item => item.id);
                    console.log("All topic IDs loaded:", allTopicIds);
                }
            });
        }

        // Initial fetch
        fetchAllTopicIds();

        function initializeSortable() {
            $(".row_position").sortable({
                delay: 150,
                start: function(event, ui) {
                    // Check if search input has value
                    // var searchValue = table.search();
                    // if (searchValue.length > 0) {
                    //     $(".row_position").sortable("cancel");
                    //     swal("Warning!", "Sorting is disabled while searching", "warning");
                    //     return false;
                    // }
                },
                stop: function() {
                    // Get current page info
                    var pageInfo = table.page.info();

                    // Get new order of visible rows
                    var visibleOrder = $(".row_position>tr").map(function() {
                        return this.id;
                    }).get();

                    // Update our stored IDs with the new visible order
                    for (var i = 0; i < visibleOrder.length; i++) {
                        if (pageInfo.start + i < allTopicIds.length) {
                            allTopicIds[pageInfo.start + i] = visibleOrder[i];
                        }
                    }

                    console.log("Updated order of all topics:", allTopicIds);
                    updateTopicOrder(allTopicIds);
                }
            });
        }

        //     function initializeSortable() {
        //     $(".row_position").sortable({
        //         delay: 150,
        //         stop: function() {
        //             // Get all IDs from the DataTables data
        //             var allData = table.rows({ search: 'applied' }).data().toArray();
        //             var allTopicIds = allData.map(item => item.id);

        //             // Get visible row IDs
        //             var visibleOrder = [];
        //             $(".row_position>tr").each(function() {
        //                 var id = $(this).attr('id');
        //                 if (id) visibleOrder.push(id);
        //             });

        //             // Get current page info
        //             var pageInfo = table.page.info();

        //             // Update positions
        //             for (var i = 0; i < visibleOrder.length; i++) {
        //                 if (pageInfo.start + i < allTopicIds.length) {
        //                     allTopicIds[pageInfo.start + i] = visibleOrder[i];
        //                 }
        //             }

        //             // Filter valid IDs
        //             var validIds = allTopicIds.filter(id => id);

        //             console.log("Final IDs to update:", validIds);
        //             updateTopicOrder(validIds);
        //         }
        //     });
        // }


        function updateTopicOrder(aData) {

            console.log("updateTopicOrder:", aData);
            $.ajax({
                url: "{{ route('admin.topic-materials.changeorder') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    allData: aData
                },
                beforeSend: function() {
                    $('#preloader').css('display', 'block');
                },
                success: function() {
                    $('#preloader').css('display', 'none');
                    swal("Success!", "Topic order updated successfully", "success");
                    // table.draw(false);
                },
                error: function(xhr) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('error', xhr.responseJSON?.message || 'Error updating order');
                }
            });
        }
    });




    function Status() {

        $('.-change').change(function() {

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
    }

    function Live() {
        $('.-live').change(function() {

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
    }


    function Lock() {
        $('.-lock').change(function() {

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
                beforeSend: function() {
                    $('#preloader').css('display', 'block');
                },
                error: function(xhr, textStatus) {

                    if (xhr && xhr.responseJSON.message) {
                        sweetAlertMsg('error', xhr.is_lock + ': ' + xhr.responseJSON.message);
                    } else {
                        sweetAlertMsg('error', xhr.is_lock + ': ' + xhr.statusText);
                    }
                    $('#preloader').css('display', 'none');
                },
                success: function(data) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('success', data.message);
                }
            });
        });
    }
</script>

@endpush