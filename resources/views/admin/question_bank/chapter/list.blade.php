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
    $(document).ready(function() {
        // Initialize DataTable with enhanced configuration
        if ($.fn.DataTable.isDataTable('#DataTables_Table_0')) {
            $('#DataTables_Table_0').DataTable().destroy();
        }

        var table = $('#DataTables_Table_0').DataTable({
            lengthMenu: [
                [10, 25, 50, 100, 500],
                [10, 25, 50, 100, 500]
            ], // Added 500 option
            pageLength: 10, // Default to 10 entries
            scrollY: '70vh', // Set a fixed height for scrollable area
            scrollCollapse: true,
            dom: '<"top"lf>rt<"bottom"ip>', // Layout control
            stateSave: true // Remember user settings
        });

        // Function to get all row IDs
        function getAllRowIds() {
            return table.rows({
                search: 'applied'
            }).nodes().map(function(node) {
                return node.id;
            }).toArray();
        }

        // Initialize sortable with enhanced configuration
        $(".row_position").sortable({
            items: "> tr",
            cursor: "move",
            opacity: 0.8,
            axis: "y",
            delay: 150,
            tolerance: "pointer",
            containment: "parent",
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            start: function(e, ui) {
                ui.helper.css('background-color', '#f8f9fa');
                ui.helper.css('box-shadow', '0 0 10px rgba(0,0,0,0.2)');
                ui.placeholder.css('height', ui.helper.outerHeight() + 'px');

                // Check if search is active
                if (table.search().length > 0) {
                    $(".row_position").sortable("cancel");
                    swal("Warning!", "Sorting is disabled while searching", "warning");
                    return false;
                }
            },
            update: function(e, ui) {
                // Get all row IDs in current order
                var allRowIds = getAllRowIds();

                // Get the new order of visible rows
                var visibleOrder = [];
                $(".row_position>tr").each(function() {
                    visibleOrder.push($(this).attr("id"));
                });

                // Get current page info
                var pageInfo = table.page.info();

                // Update the complete array with the reordered visible rows
                for (var i = 0; i < visibleOrder.length; i++) {
                    if (pageInfo.start + i < allRowIds.length) {
                        allRowIds[pageInfo.start + i] = visibleOrder[i];
                    }
                }

                // Filter out any null/undefined values
                var validIds = allRowIds.filter(id => id);

                console.log("Final order to update:", validIds);
                updateOrder(validIds);
            }
        }).disableSelection();

        // Auto-scroll functionality during drag
        var scrollInterval;
        var scrollSpeed = 20; // pixels per interval
        var scrollZoneHeight = 100; // height of top/bottom scroll zones

        $(document).on('mousemove', function(e) {
            if ($(".row_position").sortable("instance").options.disabled) return;

            var mouseY = e.pageY - $(window).scrollTop();
            var windowHeight = $(window).height();

            // Clear any existing interval
            clearInterval(scrollInterval);

            // Check if we need to scroll up or down
            if (mouseY < scrollZoneHeight) {
                scrollInterval = setInterval(function() {
                    window.scrollBy(0, -scrollSpeed);
                }, 50);
            } else if (mouseY > windowHeight - scrollZoneHeight) {
                scrollInterval = setInterval(function() {
                    window.scrollBy(0, scrollSpeed);
                }, 50);
            }
        });

        $(document).on('mouseup', function() {
            clearInterval(scrollInterval);
        });

        function updateOrder(aData) {
            $('#preloader').css('display', 'block');

            $.ajax({
                url: "{{ route('admin.chapter.change-order') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: {
                    allData: aData
                },
                success: function(response) {
                    $('#preloader').css('display', 'none');
                    swal("Success!", "Chapter order updated successfully", "success");
                    // Optionally redraw the table
                    // table.draw(false);
                },
                error: function(xhr) {
                    $('#preloader').css('display', 'none');
                    if (xhr && xhr.responseJSON.message) {
                        sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                    } else {
                        sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                    }
                }
            });
        }

        // Status change handler
        $('.-change').change(function() {
            var status = $(this).prop('checked') ? 'Active' : 'De-Active';
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
                success: function(data) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('success', data.message);
                },
                error: function(xhr) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('error', xhr.responseJSON?.message || 'Error updating status');
                }
            });
        });

        // Live status change handler
        $('.-live').change(function() {
            var is_live = $(this).prop('checked') ? '1' : '0';
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
                success: function(data) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('success', data.message);
                },
                error: function(xhr) {
                    $('#preloader').css('display', 'none');
                    sweetAlertMsg('error', xhr.responseJSON?.message || 'Error updating live status');
                }
            });
        });
    });
</script>

<style>
    /* Enhanced sortable styles */
    .row_position tr {
        transition: all 0.2s ease;
        cursor: move;
    }

    .row_position tr.ui-sortable-helper {
        background: #f8f9fa !important;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
        z-index: 9999;
    }

    .row_position tr.ui-sortable-placeholder {
        background: #e9ecef;
        visibility: visible !important;
        height: 60px;
    }

    /* DataTables scroll container */
    .dataTables_scrollBody {
        overflow-y: auto !important;
        max-height: 70vh !important;
    }

    /* Preloader style */
    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }
</style>
@endpush