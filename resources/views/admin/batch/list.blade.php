@extends('layouts/master')

@section('title',__('Batch List'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
						<div class="btn-group top-head-btn">
                            <a class="btn-primary" href="{{ url('admin/batch/add') }}">
                                <i class="fa fa-plus"></i> Add Batch 
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
						<h2><i class="fa fa-th"></i> Batch List</h2>
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
                                                        aria-label="#: activate to sort column descending"> Batch ID</th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Course</th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Name</th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Start Date</th> 
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> End Date</th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Price</th> 
                                                         <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Dollar Price</th>
                                                        <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending"> Image</th>
                                                        <th class="center sorting" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 193.017px;"
                                                            aria-label=" Email : activate to sort column ascending"> Status
                                                        </th>
                                                        <th class="center sorting" tabindex="0"
                                                            aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                            style="width: 85px;"
                                                            aria-label=" Action : activate to sort column ascending"> Action
                                                        </th> 
                                                </tr>
                                            </thead>
                                            <tbody>
												@if(!empty($result))
													@foreach($result as $key=>$value)
														<tr class="gradeX odd"  id="{{ $value->id }}">
															<td class="center">{{ $key+1}}</td>
															<td class="center">{{ $value->unique_id}}</td>
															<td class="center">{{ \App\Helpers\commonHelper::getCourseName($value->course_id)}}</td>
															<td class="center">{{ $value->name}}</td>
															<td class="center">{{ date('d-M-Y',strtotime($value->start_date))}}</td>
															<td class="center">{{ date('d-M-Y',strtotime($value->end_date))}}</td>
															<td class="center">{{ $value->price}}</td>
															<td class="center">{{ $value->dollar_price}}</td>
                                                            <td class="center"><img src="{{ asset('/uploads/batch/'.$value->image) }}" width="51px"></td>

															<td class="center">
                                                                <div class="switch mt-3">
                                                                    <label>
                                                                        <input type="checkbox" class="-change" data-id="{{ $value['id'] }}"@if($value['status']=='1'){{ 'checked' }} @endif>
                                                                        <span class="lever switch-col-red layout-switch"></span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td class="center">
                                                            
                                                                <a href="{{ url('admin/batch/update/'.$value['id'] )}}" title="Edit Batch" class="btn btn-tbl-edit btn_change">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a title="Delete Batch" onclick="return confirm('Are you sure? You want to delete this batch.')" href="{{ url('admin/batch/delete/'.$value['id'] )}}" class="btn btn-tbl-delete btn_change">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                                <a href="javascript:void(0)" class="btn btn-tbl-edit btn_change" onclick="seatGroupId('{{$value['unique_id']}}','{{ucfirst($value['name'])}}')" style="background-color: blue"><i class="fas fa-comments"></i></a>
                                                                <a title="User" href="{{ url('admin/user/list?id='.$value['id'] )}}" target="_blank" class="btn btn-tbl-edit btn_change" style="background-color: yellow">
                                                                    <i class="fa fa-users"></i>
                                                                </a>
                                                            </td>
														</tr>
													@endforeach
												@endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                <th class="center" rowspan="1" colspan="1">#</th>
                                                <th class="center" rowspan="1" colspan="1"> Batch ID </th>
                                                <th class="center" rowspan="1" colspan="1"> Course </th>
                                                <th class="center" rowspan="1" colspan="1"> Name </th>
                                                <th class="center" rowspan="1" colspan="1"> Start Date </th>
                                                <th class="center" rowspan="1" colspan="1"> End Date </th>
                                                <th class="center" rowspan="1" colspan="1"> Price </th>
                                                <th class="center" rowspan="1" colspan="1">Dollar Price </th>
                                                <th class="center" rowspan="1" colspan="1"> Image </th>
                                                <th class="center" rowspan="1" colspan="1"> Status </th>
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
<!-- exampleModal -->
<div class="modal fade chatboxs" id="bookMark" tabindex="-1" aria-labelledby="bookMarkLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered p-0">
    <div class="modal-content p-0">
      <div class="modal-header">
        <h5 class="modal-title text-white" data-bs-dismiss="modal" id="bookMarkLabel"><i class="fas fa-chevron-left" onclick="showChatModel()"></i> <span class="ps-4">Chat - <span class="group_name"></span></span> </h5>

      </div>
        <div class="modal-body" id="pinChat-box">
          {{-- Rendering Messages --}}
        </div>
      </div>
    </div>
  </div>

<!-- Modal -->
<div class="modal fade chatboxs" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered p-0">
    <div class="modal-content p-0">
      <div class="modal-header">
        <h5 class="modal-title text-white" data-bs-dismiss="modal" id="exampleModalLabel"><i class="fas fa-chevron-left"></i> <span class="ps-4">Chat - <span class="group_name"></span> </h5>
        <button class="bookmark-icon position-absolute" style="left: 93%;background: #fff;border: none;border-radius: 3px;" onclick="showAllPinnedMessages()">
            <i class="fa fa-bookmark-o"></i>
        </button>
      </div>
      <div class="modal-body" id="chat-box">
        {{-- Rendering Messages --}}
      </div>

      <div style="background: #183153; border-top-left-radius: 34px; border-top-right-radius: 34px; height: 50px; display: none;" id="replyDiv">
        <button onclick="closeReplyDiv()" style="position: absolute;  right: 10px; background: none; border: none; color: white; cursor: pointer; font-size: 18px; line-height: 1;">&times;</button>
        <div id="reply-input" style="align-items: center; margin-left: 29px; margin-top: 15px; color: white;"></div>
      </div>


        <div class="footer">
        
                <form>
                    <div class="row align-items-center footerbox">
                    <div class="col-1">
                        <i class="fa fa-paperclip" aria-hidden="true" id="loader-paperclip"></i>
                        <input class="form-control form-control-lg" id="file-input" type="file" style="opacity: 0" onchange="submitChatForm()"> 
                        <div id="loader" class="spinner-border" style="display: none;color:white" >
                        </div>
                    </div>
                    
                    <div class="col-10">
                        
                        <input type="text" autocomplete="off" class="form-control ps-4" id="message-input" placeholder="Message">
                        <div id="loader-sending" style="display: none;">Sending...
                        </div>
                        <button  type="submit" class="input-group-text" onclick="return sendMessage();"><svg viewBox="0 0 24 24" height="24" width="20" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 24 24">
                            <path fill="currentColor" d="M1.101,21.757L23.8,12.028L1.101,2.3l0.011,7.912l13.623,1.816L1.112,13.845 L1.101,21.757z"></path>
                        </svg>
                        </button> 
                    </div>
                    </div>
                </form>
        
        </div>
        
      </div>
    </div>
  </div>
</div>

@endsection
   
@push('custom_js') 
    <script>
	
		const authorData={"firstName": '{{auth()->user()->name}}', "id": '{{auth()->user()->id}}', "email": '{{auth()->user()->email}}'}
		
		let groupRef;
		let _groupId;
		let _groupName;

		function seatGroupId(groupId, groupName){
 
            if(!groupId){
                sweetAlertMsg('error','Please select any Batch');
                return false;
            }
			
			_groupId=groupId;
			_groupName=groupName;
			
			$('.group_name').text(_groupName);

			groupRef = db.collection('Chat').doc(groupId).collection('message');
			
			groupRef.orderBy('createdAt').onSnapshot((snapshot) => {
                console.log(snapshot);
				const messages = snapshot.docs.map((doc) => {
                    return {
                        message_id: doc.id,
                        ...doc.data(),
                    };
                });
				renderMessages(messages);

                messages.forEach((message) => {
                    const messageDocRef = groupRef.doc(message.message_id);

                    messageDocRef.update({ adminstatus: 'seen' }).then(() => {
                        console.log('Message marked as seen:', message.message_id);
                    }).catch((error) => {
                        console.error('Error updating isSeen field:', error);
                    });
                });
			});
            
			$('#exampleModal').modal('toggle');
		}
		 
		
        function submitChatForm(){
            sendMessage();
        }


        // Function to toggle the pinned status of a chat
        async function toggleChatPinned(messageId,chat_pinned) {

            try {
				// Construct a reference to the Firestore document
				const docRef = db.collection('Chat').doc(_groupId).collection('message').doc(messageId);
			
				let pinStatus='0';
				if(chat_pinned=='0'){
					pinStatus='1';
				}
				await docRef.update({
				  chat_pinned: pinStatus,
				});
				
				
			} catch (error) {
				console.error('Error updating message:', error);
			}
			
        }



    </script>
	
	<script src="{{asset('admin-assets/js/chat_app.js')}}"></script>
	
    <script>
	
        $('.-change').change(function() {

            var status = $(this).prop('checked') == true ? '1' : '0';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.batch.changestatus') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
                data: {
                    'status': status, 
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
		
    </script>  
  
    <script type="text/javascript">
        $(".row_position").sortable({
            delay: 150,
            stop: function() {
                var selectedData = new Array();
                $(".row_position>tr").each(function() {
                    selectedData.push($(this).attr("id"));
                });
                updateOrder(selectedData);
            }
        });

        function updateOrder(aData) {        
            $.ajax({
                url: "{{ route('admin.batch.change-order') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                type: 'POST',
                data: {
                    allData: aData
                },
                success: function() {
                    swal("Success!", "Your change successfully saved", "success");
                }
            });
        }
    </script> 
@endpush
