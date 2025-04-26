@extends('layouts/master')

@section('title',__('Student List'))

@section('content')
<style>
    .btn_change{
        line-height: 0;
    }

    .btn_change i{
        margin: 0 !important; 
    }
</style>
<section class="content">
    <div class="container-fluid">
		
    <?php
        $currentDateTime = Carbon\Carbon::now();

        $cond = [
            "user_id" => $user_id,
            "status" => "1"
        ];

        $subscriptionStatus = DB::table('transactions')->where($cond)->where('start_date','<=',$currentDateTime)->where('end_date','>=',$currentDateTime)->orderBy('id','DESC')->first();

        if(!empty($subscriptionStatus)){
            $sStatus = "<span class='text-success'>Active</span>";
            $lastBilled = date('d-M-Y h:i A',strtotime($subscriptionStatus->created_at));
        }else{
            $sStatus = "<span class='text-danger'>Inactive</span>";
            $lastBilled = "NA";
        }
    ?>
        
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Student Exams Analysis</h2>
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
                                                        aria-label="#: activate to sort column descending"> Added On</th>

                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> College
                                                        </th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Subscription Status
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> State
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Last Billed On
                                                        </th>
                                                        
                                                </tr>
                                            </thead>
                                            <tbody>
												
														<tr class="gradeX odd">
															<td class="center">1</td>
															<td class="center">{{ ucfirst($result->name) }}</td>
															<td class="center">{{ date('d-m-Y', strtotime($result->created_at)) }}</td>
															<td class="center">{{ $result->college ? ucfirst($result->college) : 'NA' }}</td>
															<td class="center">{!! $sStatus !!}</td>
															<td class="center">{{ $result->state ? ucfirst($result->state) : 'NA' }}</td>
															<td class="center">{{ $lastBilled }}</td>
															
														</tr>
													
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


        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Attempted Question Bank Detail ( Total Attempted - <?= count($questionBankResult); ?> )</h2>
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
                                                            aria-label="#: activate to sort column descending"> Total Correct Answers
                                                        </th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Incorrect Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Unvisited Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Action
                                                        </th>
                                                        
                                                </tr>
                                            </thead>
                                            <tbody>
												<?php $i=1; $highestMarksQb="NA"; if(!empty($questionBankResult)){
                                                    
                                                    $highestMarksQb = DB::table('question_bank_result')->where('user_id', $user_id)->max('correct_questions');
                                                    
                                                    foreach($questionBankResult as $bank){ ?>
														<tr class="gradeX odd">
															<td class="center"><?= $i++; ?></td>
															<td class="center"><?= $bank->topic_name; ?></td>
															<td class="center"><?= $bank->correct_questions; ?></td>
															<td class="center"><?= $bank->incorrect_questions; ?></td>
															<td class="center"><?= $bank->unattempted_questions; ?></td>
															<td class="center">
                                                                <a href="<?= url('admin/user/detail' ).'/'.$bank->id ?>" title="View Student" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>					
														</tr>
                                                <?php } } ?>        
                                                        
													
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <!-- <div class="header">
                                    <h2> Average Rank in all attempted question bank - 2</h2>
                                </div> -->
                                <div class="header">
                                    <h2> Highest Marks in question bank - <?= $highestMarksQb ? $highestMarksQb : 'NA'; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Attempted Previous Year Questions Detail ( Total Attempted - <?= count($preResult); ?> )</h2>
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
                                                            aria-label="#: activate to sort column descending"> Total Correct Answers
                                                        </th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Incorrect Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Unvisited Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Action
                                                        </th>
                                                        
                                                </tr>
                                            </thead>
                                            <tbody>
												
                                            <?php $j=1; $highestMarksPre="NA"; if(!empty($preResult)){
                                                $highestMarksPre = DB::table('previous_year_result')->where('user_id', $user_id)->max('correct_questions');
                                                foreach($preResult as $pre){ ?>
														<tr class="gradeX odd">
															<td class="center"><?= $j++; ?></td>
															<td class="center"><?= $pre->topic_name; ?></td>
															<td class="center"><?= $pre->correct_questions; ?></td>
															<td class="center"><?= $pre->incorrect_questions; ?></td>
															<td class="center"><?= $pre->unattempted_questions; ?></td>
															<td class="center">
                                                                <a href="<?= url('admin/user/detailPre' ).'/'.$pre->id ?>" title="View Student" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>					
														</tr>
                                                <?php } } ?> 
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <!-- <div class="header">
                                    <h2> Average Rank in all attempted previous year questions - 2</h2>
                                </div> -->
                                <div class="header">
                                    <h2> Highest Marks in previous year questions - <?= $highestMarksPre ? $highestMarksPre : 'NA'; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Attempted Mockup Questions Detail ( Total Attempted - <?= count($mockResult); ?> )</h2>
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
                                                            aria-label="#: activate to sort column descending"> Total Correct Answers
                                                        </th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Incorrect Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Total Unvisited Answers
                                                        </th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 48.4167px;" aria-sort="ascending"
                                                            aria-label="#: activate to sort column descending"> Action
                                                        </th>
                                                        
                                                </tr>
                                            </thead>
                                            <tbody>
												<?php $mo=1; $highestMarksMock = 'NA'; if(!empty($mockResult)){ 
                                                        $highestMarksMock = DB::table('mockup_test_result')->where('user_id', $user_id)->max('marks');
                                                        foreach($mockResult as $mock){
                                                       $mockData = DB::table('mockups')->where('id',$mock->mock_id)->first();
                                                       if(!empty($mockData)){
                                                    ?>
														<tr class="gradeX odd">
															<td class="center"><?= $mo++; ?></td>
															<td class="center"><?= $mockData->name; ?></td>
															<td class="center"><?= $mock->right_answers; ?></td>
															<td class="center"><?= $mock->wrong_answers; ?></td>
															<td class="center"><?= $mock->total_questions-$mock->attempted_questions; ?></td>
															<td class="center">
                                                                <a href="<?= url('admin/user/detailMock' ).'/'.$mock->id ?>" title="View Student" class="btn btn-tbl-edit">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>					
														</tr>
                                                        <?php } } } ?>
                                                        
													
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <!-- <div class="header">
                                    <h2> Average Rank in all mockup questions - 5</h2>
                                </div> -->
                                <div class="header">
                                    <h2> Highest Marks in mockup questions - <?= $highestMarksMock ? $highestMarksMock : 'NA'; ?></h2>
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
 