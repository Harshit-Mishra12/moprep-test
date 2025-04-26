@extends('layouts/master')

@section('title',__('Question View'))

@section('content')

<section class="content">
    <div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i>  Go To</h2>
					</div>
					<div class="body">
						<div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/question_bank/question/list') }}">
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
						<h2><i class="fa fa-th"></i> Questions ({{$courseName->name}})</h2>
					</div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <label for="inputName">Filter By Topic </label>
                                                <select class="form-control" id="topic" name="topic" required >
                                                 
                                                    <option selected value="">--Select--</option>
                                                    @if(!empty($topics))
                                                    @foreach($topics as $c)
                            
                                                    <option value="{{ $c['id'] }}">{{ ucfirst($c['topic']) }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <table class="table table-hover yajra-datatable contact_list dataTable"
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
                                                            aria-label="#: activate to sort column descending"> Year / Part
                                                        </th>   
                                                    
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Chapter Name
                                                        </th>
                                                    
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Topic
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Question
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                    aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                    style="width: 48.4167px;" aria-sort="ascending"
                                                    aria-label="#: activate to sort column descending"> Pre Live
                                                </th>
                                                    <!-- @if ($type == 'revisionexam')
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Recall
                                                    </th> 
                                                    @endif -->
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
                                                    <?php
                                                        // $yearName = "NA";
                                                        // $getQuestionData = DB::table('bulk_questions')->where('id',$value->question_id)->first();
                                                           
                                                        //    if(!empty($getQuestionData)){
                                                        //     $yearData = DB::table('bulk_question_years')->where('id',$getQuestionData->year_id)->first();
                                                        //         if(!empty($yearData)){
                                                        //             $yearName = $yearData->year.' / '.$yearData->part;
                                                        //         }
                                                        //    }
                                                    ?>
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
                                                            <td class="center">{{ $yearName}}</td>
                                                            <td class="center">{{ \App\Helpers\commonHelper::getChapterName($value->chapter_id) ?? "NA"}}</td>
                                                            <td class="center">{{ \App\Helpers\commonHelper::getTopicName($value->topic_id) ?? "NA"}}</td>
															<td class="center">{!! \App\Helpers\commonHelper::getQuestionName($value->question_id) ?? "NA" !!}</td>
                                                            
                                                            <td class="center">
                                                               
                                                                <a href="{{ url('admin/questionBank/updateBulkQuestion/'.$value->question_id )}}" title="Edit Question" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a title="Delete Question" onclick="return confirm('Are you sure? You want to delete this Question.')" href="{{ url('admin/question_bank/question/delete/'.$value->id )}}" class="btn btn-tbl-delete">
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
                                                <th class="center" rowspan="1" colspan="1"> Year / Part</th>
                                                <th class="center" rowspan="1" colspan="1"> Chapter Name</th>
                                                <th class="center" rowspan="1" colspan="1"> Topic </th>
                                                <th class="center" rowspan="1" colspan="1"> Question </th>
                                                <th class="center" rowspan="1" colspan="1"> Pre Live </th>
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
                url: "{{ route('admin.question_bank.question.viewCourseQuestions') }}",
                data: function(d) {
                    d.name = $('#name-filter').val();
                    d.course_id = {{ $course_id}}
                    d.course_master_id = {{ $course_master_id}}
                    d.topic = $('#topic').val();
                }
            },

            fnDrawCallback: function() {
                Live();
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
},

          {data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'center'},
          {data: 'year_part', name: 'year_part', className: 'center'},
          {data: 'chapter_name', name: 'chapter_name', className: 'center'}, 
          {data: 'topic', name: 'topic', className: 'center'}, 
          {data: 'question', name: 'question', className: 'center'}, 
          {data: 'is_live', name: 'is_live', className: 'center'}, 
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

    $('#topic').on('change', function() {
      $('.yajra-datatable').DataTable().ajax.reload();
  });

    function Live() {
        $('.-live').change(function() {

            var is_live = $(this).prop('checked') == true ? '1' : '0';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.questionBank.changelive') }}",
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
                    // sweetAlertMsg('success', data.message);
                }
            });
        });
    }$(document).ready(function () {
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
            url: "{{ route('admin.questionBank.bulkUpdateQuestion') }}",
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