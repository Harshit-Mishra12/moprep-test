@extends('layouts/app')

@section('title',__(' Home'))

@section('content')

 <!---  Start Section Silder -->
 <div class="container-fluid overflow-hidden silder-section position-relative d-none">
          <div class="container">
                <div class="row align-items-center justify-content-xl-start justify-content-md-center pt-xl-5 pt-lg-5 pt-md-5 pt-sm-5 pt-5 pb-xl-5 pb-lg-5 pb-md-5 pb-sm-5 pb-5">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-10 col-sm-12 col-12 position-relative pt-xl-0 pt-lg-0 pt-md-0 pt-sm-5 pt-5">
                            <div class="col-12 fs-xxl-52 fs-xl-42 fs-lg-36 fs-md-45 fs-sm-30 fs-23 text-black fw-m lh-1 lh-sm position-relative silder-head fw-800">
                                FAST TRACK TO <span class="color2"> EXCELLENCE </span>
                              </div>

                               <div class="col-12 pb-2 pt-2 text-dark fs-xxl-18 fs-xl-18 fs-lg-18 fs-md-15 fs-sm-14 fs-16">
                                 Lorem ipsum dolor sit amet consectetur adipisng elit Ut et massa mi. Aliquam in hendr Pellentesque
                                 sit amet sapiengilla, mattis ligula consectetur, ultrices mauris. Maecenas.
                              </div>

                              <div class="row align-items-center mt-4">
                                    @if (count($courses)>0)
                                       @foreach ($courses as $item)
                                             <a class="col-auto d-flex btn btn-button-bg fw-400 fs-xxl-15 fs-xl-15 fs-lg-15 fs-10 text-white rounded px-lg-3 py-xxl-1 py-xl-1 py-lg-1 py-md-1 py-sm-1 py-1 ms-3 pe-2 px-3 mb-2" href="{{url('course-details/'.$item['slug'])}}">{{$item['name']}}</a>
                                       @endforeach
                                    @endif
                                 </div>
                    </div>

                     <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-10 col-sm-12 col-12 position-relative">
                        <div class="col-12 text-xxl-center text-xl-center text-lg-center text-md-center text-center">
                           <img src="images/dr-silder.png" class="img-fluid" alt="dr">
                      </div>
                   </div>
                </div>
              </div>
             </div>






@endsection
