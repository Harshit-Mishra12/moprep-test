@extends('layouts/master')

@section('title',__('Mockup Question List'))

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
						<h2><i class="fa fa-th"></i> Mockup Question List</h2>
					</div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <label for="inputName">Select Course </label>
                                                <select class="form-control" id="course_master_id" name="course_master_id" required >
                                                    <option selected value="">--Select--</option>
                                                    @if(!empty($courses))
                                                    @foreach($courses as $c)
                                                    <option value="{{ $c['id'] }}">{{ ucfirst($c['name']) }}
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
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"># ID</th>
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Mockup Name
                                                    </th> 
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Total Questions
                                                    </th> 
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
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
															<td class="center">{{ $value->name}}</td>
															<td class="center">{{ DB::table('mock_questions')->where('mock_id',$value->id)->count() }}</td>
                                                            <td class="center text-center">
                                                                <a href="{{ url('admin/mockupquestion/view-mock-question/'.$value->id)}}" target="_blank" class="btn btn-success btn_change d-inline-flex justify-content-center align-items-center">
                                                                    <i class="fa fa-eye" style="margin: 1px; font-size:14px;"> View Questions</i>
                                                                </a>
                                                            </td>                                                            
                                                          
                                                        </tr>
													@endforeach
												@endif -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                <th class="center" rowspan="1" colspan="1">#</th>
                                                <th class="center" rowspan="1" colspan="1"> Mockup Name </th>
                                                <th class="center" rowspan="1" colspan="1"> Total Questions </th>                
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

<script>
// Get the modal
var modal = document.getElementById("myModal");

// Function to open the modal
function openModal() {
    modal.style.display = "block";
}

// Function to close the modal
function closeModal() {
    modal.style.display = "none";
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}
</script>

@endsection

@push('custom_js')
<script>

$(function () {
      var table = $('.yajra-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
              url: "{{ route('admin.mockupquestion.mockupListData') }}",
              data: function (d) {
                  d.name = $('#name-filter').val();
                  d.course_master_id = $('#course_master_id').val();
              
              }
          }, 

      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'center'},
          {data: 'name', name: 'name', className: 'center'},
          {data: 'total_question', name: 'total_question', className: 'center'}, 
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
  $('#course_master_id').on('change', function() {
      $('.yajra-datatable').DataTable().ajax.reload();
  });
</script>
@endpush
   