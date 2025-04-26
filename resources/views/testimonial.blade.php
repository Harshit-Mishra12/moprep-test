@extends('layouts/app')

@section('title',__(' Home'))

@section('content')

<div class="container-fluid coman-section d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                        <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18">  Testimonials</div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="container-fluid py-xxl-5 py-xl-5 py-lg-5 py-md-4 py-sm-4 py-4">
    <div class="container">
        <div class="row">
            <div class="col-xxl-6 col-xl-6 col-md-6 col-sm-12 col-12 mb-2">
                  <div class="border border-2 shadow rounded-20 p-4">  
                     <div class="row">
                        <div class="col-xl-2">
                           <img class="img-fluid rounded-50" alt="testimonial" src="{{asset('images/man.png')}}" width="80">
                        </div>
                        <div class="col-xl-10">
                            <div class="testimonial-content">
                                <div class="col-12 fs-xxl-17 fs-xl-17 fs-lg-17 fs-md-16 fs-sm-15 fs-15 mb-xxl-0 mb-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                                Quas vel sint, ut. Quisquam doloremque minus possimus eligendi dolore ad.</div>
                                <div class="col-12 fw-600 fs-xxl-18 fs-xl-18 fs-lg-18 fs-md-15 fs-sm-15 fs-180"> Rajkumar Saini </div>
                            </div>
                      </div>
                     </div>
                 </div>
               </div>

               <div class="col-xxl-6 col-xl-6 col-md-6 col-sm-12 col-12 mb-2">
                  <div class="border border-2 shadow rounded-20 p-4">  
                     <div class="row">
                        <div class="col-xl-2">
                           <img class="img-fluid rounded-50" alt="testimonial" src="{{asset('images/man.png')}}" width="80">
                        </div>
                        <div class="col-xl-10">
                            <div class="testimonial-content">
                                <div class="col-12 fs-xxl-17 fs-xl-17 fs-lg-17 fs-md-16 fs-sm-15 fs-15 mb-xxl-0 mb-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                                Quas vel sint, ut. Quisquam doloremque minus possimus eligendi dolore ad.</div>
                                <div class="col-12 fw-600 fs-xxl-18 fs-xl-18 fs-lg-18 fs-md-15 fs-sm-15 fs-180"> Rajkumar Saini </div>
                            </div>
                      </div>
                     </div>
                 </div>
               </div>

            </div>
           </div>
        </div>

@endsection 