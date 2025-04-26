@extends('layouts/master')

@section('title',__('Question List'))

@section('content')
<style>
    .btn_change {
        line-height: 0;
    }

    .btn_change i {
        margin: 0 !important;
    }
</style>

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
                            <a class="btn-primary" href="{{ url('admin/question_bank/question/add') }}">
                                <i class="fa fa-plus"></i> Add Question
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
                        <h2><i class="fa fa-th"></i> Question List</h2>
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
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="form-line">
                                                <label for="inputName">Select Subject </label>
                                                <select class="form-control subjectIdHtml fSelect" name="course_id" required id="course_id">
                                                    <option selected value="">--Select--</option>
                                                    @if(!empty($subjects))
                                                    @foreach($subjects as $c)
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
                                                        aria-label="#: activate to sort column descending"># ID
                                                    </th>

                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Course Name
                                                    </th>

                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Subject Name
                                                    </th>
                                                    
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Total Questions
                                                    </th>
                                                     {{-- <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Total Mock Questions
                                                    </th>      --}}
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1">#</th>
                                                    <th class="center" rowspan="1" colspan="1"> Course Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Subject Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Total Questions </th>
                                                    <!-- <th class="center" rowspan="1" colspan="1"> Total Mock Questions </th> -->
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
    $(function() {

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.question_bank.question.questionListData') }}",
                data: function(d) {
                    d.name = $('#name-filter').val();
                    d.course_master_id = $('#course_master_id').val(); // Get selected course_master_id
                    d.course_id = $('#course_id').val(); // Get selected course (subject)
                }
            },

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    className: 'center'
                },
                {
                    data: 'coursemaster',
                    name: 'coursemaster',
                    className: 'center'
                },
                {
                    data: 'name',
                    name: 'name',
                    className: 'center'
                },
                {
                    data: 'otherexam_count',
                    name: 'otherexam_count',
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

    });
    $('#course_master_id, #course_id').on('change', function() {
      
        $('.yajra-datatable').DataTable().ajax.reload();
    });
</script>
<script>
	$('#course_master_id').on('change', function() {
		var course_master_id = this.value;

		$.ajax({
			url: "{{ url('admin/question_bank/question/get-subject-name') }}",
			type: "POST",
			data: {
				course_master_id: course_master_id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			cache: false,
			success: function(response) {
				var subjectSelect = $(".subjectIdHtml");
				subjectSelect.empty();
				subjectSelect.append('<option value="0">--Select Subject--</option>');

				if (response.subjectData) {
					$.each(response.subjectData, function(index, subject) {
						subjectSelect.append('<option value="' + subject.id + '">' + subject.name + '</option>');
					});
				}
				var subjectIdValue = subjectSelect.data('course_id');
				if (subjectIdValue) {
					subjectSelect.append('<option selected value="' + subjectIdValue + '"></option>');
				}
				subjectSelect.fSelect('destroy');
				subjectSelect.fSelect('create');
			},
		});
	});

	$(document).ready(function() {
		$('.fSelect').fSelect();
	});

</script>
@endpush