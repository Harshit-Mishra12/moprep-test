@extends('layouts/master')

@section('title')
	Student Detail
@endsection

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
                            <a class="btn-primary" href="{{ url('admin/user/list')}}">
                                <i class="fa fa-list"></i> Student List
							</a>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Student Exam Detail ( Total Questions - <?= $result->total_questions; ?> )</h2>
					</div>
					<div class="body">
					<div style="height:410px;width:410px">	
						<canvas id="myChart"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>

    <?php
          $user_id = $result->user_id; 
          $part_id = $result->part_id; 
          $totalStudents = DB::table('previous_year_result')->where('part_id',$result->part_id)->orderBy('correct_questions','DESC')->get();
        //   $rank = DB::table('previous_year_result')
		// 			->where('part_id', $part_id)
		// 			->where('correct_questions', '>', function ($query) use ($part_id,$user_id) {
		// 				$query->select('correct_questions')
		// 					  ->from('previous_year_result')
		// 					  ->where('part_id', $part_id)
		// 					  ->where('user_id', $user_id); // Ensure the subquery returns a single value
		// 			})
		// 			->count();

        $rank = DB::table('previous_year_result')
                ->where('part_id', $part_id)
                ->whereRaw('
                    (correct_questions * 2.083 - incorrect_questions * 0.69) > (
                        SELECT correct_questions * 2.083 - incorrect_questions * 0.69
                        FROM previous_year_result
                        WHERE part_id = ? AND user_id = ?
                    )
                ', [$part_id, $user_id])
                ->count();
      
    ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="header">
                    <h2> Rank - <?= $rank+1; ?> / <?= count($totalStudents); ?> Students</h2>
                </div>
            </div>
            <!-- <div class="card">
                <div class="header">
                    <h2> Average Time - 10 Mins</h2>
                </div>
            </div> -->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-th"></i> LeaderBoard <!-- ( Total Marks - 300 ) --></h2>
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
                                                        aria-label="#: activate to sort column descending"> Correct
                                                    </th>
                                                    
                                                    
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; if(!empty($totalStudents)){
                                                   foreach($totalStudents as $totalStudents){
                                                   $userData = DB::table('users')->where('id',$totalStudents->user_id)->first();
                                                ?>
                                                    <tr class="gradeX odd">
                                                        <td class="center"><?= $i++; ?></td>
                                                        <td class="center"><?php if(!empty($userData)){ echo $userData->name; } ?></td>
                                                        <td class="center"><?= round($totalStudents->correct_questions*2.083-$totalStudents->incorrect_questions*0.69,'2'); ?></td>
                                                        <td class="center"><?= $totalStudents->correct_questions; ?></td>					
                                                    </tr>
                                            <?php } } ?>        
                                                 
                                                
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@push('custom_js')
<script>
    // Dummy data for example
    const chartData = {
        labels: ['Total Correct', 'Total Incorrect', 'Total Unvisited'],
        datasets: [{
            label: 'Questions',
            data: [<?= $result->correct_questions; ?>, <?= $result->incorrect_questions; ?>, <?= $result->unattempted_questions; ?>],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)', // Total Correct - teal
                'rgba(255, 99, 132, 0.2)',  // Total Incorrect - red
                'rgba(255, 205, 86, 0.2)'   // Total Unvisited - yellow
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 205, 86, 1)'
            ],
            borderWidth: 1
        }]
    };

    // Get the context of the canvas element we want to select
    const ctx = document.getElementById('myChart').getContext('2d');

    // Create the chart
    const myChart = new Chart(ctx, {
        type: 'pie',
        data: chartData,
        // options: {
        //     scales: {
        //         y: {
        //             beginAtZero: true
        //         }
        //     }
        // }
    });
</script>
	@endpush