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
                     @if(isset($permission) && $permission['write'] == 'true')
                        <div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/mockupquestion/add') }}">
                                <i class="fa fa-plus"></i> Add Mockup Question By Year
							</a>
                        </div>   
                        <div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/mockupquestion/addSubjectQuestion') }}">
                                <i class="fa fa-plus"></i> Add Mockup Question By Subject
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
						<h2><i class="fa fa-th"></i> Questions ({{$mockName->name}})</h2>
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

                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Year / Part
                                                        </th>   

                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Question
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Live
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
                                                    <?php
                                                        $yearName = "NA";
                                                        $getQuestionData = DB::table('bulk_questions')->where('id',$value->question_id)->first();
                                                           
                                                           if(!empty($getQuestionData)){
                                                            $yearData = DB::table('bulk_question_years')->where('id',$getQuestionData->year_id)->first();
                                                                if(!empty($yearData)){
                                                                    $yearName = $yearData->year.' / '.$yearData->part;
                                                                }
                                                           }
                                                    ?>
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
                                                            <td class="center">{{ $yearName}}</td>
                                                          
															<td class="center">{!! $getQuestionData->question ?? "NA" !!}</td>
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
                                                                <a href="{{ url('admin/questionBank/updateBulkQuestion/'.$value->question_id )}}" title="Edit Question" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                @endIf
                                                            @if(isset($permission) && $permission['delete'] == 'true')
                                                                <a title="Delete Question" onclick="return confirm('Are you sure? You want to delete this Question.')" href="{{ url('admin/mockupquestion/delete/'.$value->id )}}" class="btn btn-tbl-delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                                @endIf
                                                            </td>
														</tr>
													@endforeach
												@endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                <th class="center" rowspan="1" colspan="1">#</th>
                                                <th class="center" rowspan="1" colspan="1"> Year / Part</th>
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
                beforeSend:function(){
                    $('#preloader').css('display','block');
                },
                error:function(xhr,textStatus){
					
                    if(xhr && xhr.responseJSON.message){
						sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
					}else{
						sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
					}
                    $('#preloader').css('display','none');
                },
                success: function(data){
					$('#preloader').css('display','none');
                    sweetAlertMsg('success',data.message);
                }
            });
		});
		
		// function Live() {
    $('.-live').change(function() {

        var is_live = $(this).prop('checked') == true ? '1' : '0';
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.questionBank.changelive') }}",
            url: "{{ route('admin.mockupquestion.changelive') }}",
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