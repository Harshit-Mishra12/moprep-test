@extends('layouts/master')

@section('title')
	Student Result Detail
@endsection

@section('content')
<section class="content">
	<div class="container-fluid">


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="header row">
                    <div class="col-md-10">
                        <h2><i class="fa fa-th"></i> LeaderBoard ( <?= $mockName->name; ?> )</h2>
                    </div>
                    <div class="col-md-2">
                        <a href="{{url('admin/mock-exam-list-excel/'.$mockName->id)}}"  class="btn btn-warning pull-left" style="padding-top: 9px;">
                                Export Excel     
                        </a>
                    </div>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12 table-responsive">
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

                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Marks
                                                    </th> 
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Attempted
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Correct
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Incorrect
                                                    </th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Duration
                                                    </th>
                                                    
                                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; if(!empty($result)){
                                                   foreach($result as $totalStudents){
                                                   
                                                ?>
                                                    <tr class="gradeX odd">
                                                        <td class="center"><?= $i++; ?></td>
                                                        <td class="center"><?= $totalStudents->user_name; ?></td>
                                                        <td class="center"><?= $totalStudents->marks; ?></td>
                                                        <td class="center"><?= $totalStudents->attempted_questions; ?></td>
                                                        <td class="center"><?= $totalStudents->right_answers; ?></td>					
                                                        <td class="center"><?= $totalStudents->wrong_answers; ?></td>					
                                                        <td class="center"><?= $totalStudents->average_time.' Min'; ?></td>					
                                                    </tr>
                                            <?php  } } ?>        
                                                 
                                                
                                        </tbody>
                                        
                                    </table>
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