@extends('layouts/app')

@section('title',__(' Home'))

@section('content')

<div class="container-fluid coman-section d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                        <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18">  Frequently Asked Questions </div>
                </div>
            </div>
        </div>
    </div>
</div>




    <div class="container-fluid py-xxl-5 py-xl-5 py-lg-5 py-md-4 py-sm-4 py-4">
      <div class="container">
         <div class="row justify-content-center">
             <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18 mb-5">MRCEM Primary</div>
            <div class="col-md-12">
               <div class="accordion" id="accordionExample">
                  @if (!empty($course['faq_data']))
                     @foreach ($course['faq_data'] as $key => $item)
                     <div class="accordion-item mb-4 position-relative zi-2">
                        <div class="accordion-header" id="headingOne{{$key+1}}">
                           <button class="accordion-button fs-lg-14 fs-md-13 fs-13 fs-lg-14 fs-md-13 fs-13 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne{{$key+1}}" aria-expanded="false" aria-controls="collapseOne{{$key+1}}">
                              {{$item['title']}}
                           </button>
                        </div>
                        <div id="collapseOne{{$key+1}}" class="position-relative accordion-collapse collapse " aria-labelledby="headingOne{{$key+1}}" data-bs-parent="#accordionExample">
                           <div class="accordion-body fw-l fs-lg-15 fs-md-14 text-muted fs-14">
                              {{$item['description']}}
                           </div>
                        </div>
                     </div>
                     @endforeach
                  @else
                  <div class="d-flex justify-content-center p-2" style="height: 500px;">
                     <img src="{{ asset('images/noresult.png') }}" class="img-fluid"/>
                  </div>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div> 

   
        </div>
      </div>
    </div>


        
@endsection


                 