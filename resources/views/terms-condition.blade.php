@extends('layouts/app')

@section('title',__(' Home'))

@section('content')

<div class="container-fluid coman-section d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                        <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18"> {{$termsCondition->title}} </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container py-xxl-5 py-xl-5 py-lg-5 py-md-3 py-sm-2 py-2">
   <div class="row">
        <p>{!!$termsCondition->description!!}</p>
   </div>
   </div>
 </div>



@endsection


