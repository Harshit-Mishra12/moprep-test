


       
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
					<div class="header">
						<h2><i class="fa fa-th"></i> Questions</h2>
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
                                                        aria-label="#: activate to sort column descending"> <input type="checkbox" id="masterCheckbox" onchange="toggleCheckboxes()" value=""> Select
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
                                                    
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="row_position">
												@if(!empty($result))
                                                @php
                                                  $i='1';
                                                @endphp

													@foreach($result as $key=>$value)
                                                    <?php
                                                      
                                                      $checkPrev = DB::table('mock_questions')->where(['mock_id'=>$mock_id,'question_id'=>$value->question_id])->count();
                                                      
                                                      
                                                        $questionTitle = "NA";
                                                        $questionData = DB::table('bulk_questions')->where('id',$value->question_id)->first();
                                                        $questionTitle = $questionData->question;
                                                    ?>
														<tr class="gradeX odd"  id="{{ $value->id }}">
                                                            <td class="center"><?php if($checkPrev==0){ ?> <input type="checkbox" class="slaveCheckbox" name="selectedQuestion[]"  value="{{ $value->question_id }}"><?php }else{ echo "Allotted"; } ?></td>
															<td class="center">{{ $i++ }}</td>                    
															<td class="center">{!! $questionTitle !!}</td>                                                         
														</tr>
                                                        
													@endforeach
												@endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1">Select</th>
                                                    <th class="center" rowspan="1" colspan="1">#</th>
                                                    <th class="center" rowspan="1" colspan="1"> Question </th>                   
                                                    
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
        
            <script>
    function toggleCheckboxes() {
        var masterCheckbox = document.getElementById('masterCheckbox');
        var slaveCheckboxes = document.querySelectorAll('.slaveCheckbox');

        if (masterCheckbox.checked) {
            slaveCheckboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        } else {
            slaveCheckboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
        }
    }
</script>

