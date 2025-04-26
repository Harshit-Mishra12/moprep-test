@extends('layouts/master')

@section('title',__('Dashboard'))

@section('content')

<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                         <h4 class="page-title" style="font-weight:bold;color:black;font-size:20px;">Dashboard</h4>
                    </div>
                </div>
            </div>
            <div class="row ">
                @php
                $permission_user = isset($permission['10']) ? $permission['10'] : null;
                $permission_transaction = isset($permission['13']) ? $permission['13'] : null;
                $permission_question = isset($permission['34']) ? $permission['34'] : null;
                $permission_mockup = isset($permission['56']) ? $permission['56'] : null;
                $permission_question_year = isset($permission['50']) ? $permission['50'] : null;
                @endphp
                @if($permission_user['read'])
                <div class="col-xl-3 col-lg-6">
                    <a href="#">
                        <div class="card l-bg-blue-dark">
                            <a href="{{url('admin/user/list')}}">
                            <div class="info-box-5 p-4" style="height: 140px;">
                                <div class="card-icon card-icon-large"><i class="fa fa-users"></i></div>
                                <div class="mb-4">
                                    <h5 class="font-20 mb-0" style="color:#ffffff;"> <b>Total Registered</b></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8" style="margin-top: 23px;">
                                        <h2 class="d-flex align-items-center mb-0" style="color:#ffffff;">
                                         <?php echo DB::table('users')->count()-1;
                                        ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </a>
                </div>
                @endif
             @if($permission_transaction['read'])
                <div class="col-xl-3 col-lg-6">
                    <a href="#">
                        <div class="card l-bg-blue-dark">
                            <a href="{{url('admin/purchase-history')}}">
                            <div class="info-box-5 p-4" style="height: 140px;">
                                <div class="card-icon card-icon-large"><i class="fa fa-money"></i></div>
                                <div class="mb-4">
                                    <h5 class="font-20 mb-0" style="color:#ffffff;"> <b>Total Subscription Sold</b></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8">
                                        <h2 class="d-flex align-items-center mb-0" style="color:#ffffff;">
                                        <?php echo  DB::table('transactions')->where('status','1')->where('paid_amount','>','0')->count(); 
                                        ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </a>
                </div>
            @endif
            @if($permission_question['read'])
                <div class="col-xl-3 col-lg-6">
                    <a href="#">
                        <div class="card l-bg-blue-dark">
                            <a href="{{url('admin/question_bank/question/list')}}">
                            <div class="info-box-5 p-4" style="height: 140px;">
                                <div class="card-icon card-icon-large"><i class="fa fa-question"></i></div>
                                <div class="mb-4">
                                    <h5 class="font-20 mb-0" style="color:#ffffff;"> <b>Total Question Bank Uploaded</b></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8">
                                        <h2 class="d-flex align-items-center mb-0" style="color:#ffffff;">
                                         <?php echo DB::table('courses')->count(); ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </a>
                </div>
                @endif
                  @if($permission_mockup['read'])
                <div class="col-xl-3 col-lg-6">
                    <a href="#">
                        <div class="card l-bg-blue-dark">
                            <a href="{{url('admin/mockup/list')}}">
                            <div class="info-box-5 p-4" style="height: 140px;">
                                <div class="card-icon card-icon-large"><i class="fa fa-book"></i></div>
                                <div class="mb-4">
                                    <h5 class="font-20 mb-0" style="color:#ffffff;"> <b>Total Mockup Tests Uploaded</b></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8">
                                        <h2 class="d-flex align-items-center mb-0" style="color:#ffffff;">
                                        <?php echo DB::table('mockups')->count();
                                        ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </a>
                </div>
  @endif
                  @if($permission_question_year['read'])
                <div class="col-xl-3 col-lg-6">
                    <a href="#">
                        <div class="card l-bg-blue-dark">
                            <a href="{{url('admin/questionBank/yearList')}}">
                            <div class="info-box-5 p-4" style="height: 160px;">
                                <div class="card-icon card-icon-large"><i class="fa fa-history"></i></div>
                                <div class="mb-4">
                                    <h5 class="font-20 mb-0" style="color:#ffffff;"> <b>Total Previous Year Questions Uploaded</b></h5>
                                </div>
                                <div class="row align-items-center mb-2 d-flex">
                                    <div class="col-8">
                                        <h2 class="d-flex align-items-center mb-0" style="color:#ffffff;">
                                        
                                        <?php 
                                         $yearIdsArray = \DB::table('bulk_question_years')->pluck('id')->toArray(); echo DB::table('bulk_questions')->whereIn('year_id',$yearIdsArray)->count(); ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </a>
                </div>
                  @endif
            </div>
            
        </div>
    </section>

@endsection


@push('custom_js')
<script src="{{ asset('admin-assets/js/chart.min.js')}}"></script>
<script src="{{ asset('admin-assets/js/echarts.js')}}"></script>
<script src="{{ asset('admin-assets/js/apexcharts.min.js') }}"></script>

@endpush