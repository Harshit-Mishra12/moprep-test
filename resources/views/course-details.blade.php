@extends('layouts/app')

@section('title',__(' Home'))

@section('content')
<div class="container-fluid coman-section d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                        <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18">  {{$courseDetail['name']}} </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid py-xxl-5 py-xl-5 py-lg-5 py-md-4 py-sm-4 py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="privacy-content">
                    <div class="fw-800 fs-xxl-18 fs-xl-18 fs-lg-18 fs-md-15 fs-sm-15 fs-18">Course Details</div>
                </div>
               <p>{{$courseDetail['description']}} </p>
               </div>
         </div>
      </div>
    </div>



@endsection 