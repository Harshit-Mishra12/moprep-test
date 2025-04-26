@extends('layouts/app')

@section('title',__(' Home'))

@section('content')
<div class="container-fluid coman-section d-flex align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="row justify-content-center">
                        <div class="col-12 fw-600 fs-xxl-23 fs-xl-23 fs-lg-23 fs-md-15 fs-sm-15 fs-18">  MRCEM Primary </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="container-fluid py-xxl-5 py-xl-5 py-lg-5 py-md-4 py-sm-4 py-4">
    <div class="container">
        <div class="row">
            @if (!empty($courseDemo['demo_video_data']))
                @foreach ($courseDemo['demo_video_data'] as $item)
                     <div class="col-xxl-3 col-xxl-3 col-xxl-3 col-md-4 col-sm-6 col-12 d-flex align-items-stretch my-4">
                        <div class="boxdiv w-100 px-3">
                        <div class="col-12 text-uppercase"> 
                            <iframe width="100%" height="150" src="{{$item['video_link']}}" title="{{$item['title']}}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                        <div class="col-12 text-uppercase"> <a href="#0" class="text-decoration-none text-dark"> {{$item['title']}} </a> </div>
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


@endsection


                 