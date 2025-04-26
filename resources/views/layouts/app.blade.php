<!DOCTYPE html>
<html lang="en">
   <head>
      <!-- Title tag -->
      <title> EduTech </title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible' />
      <!-- Required meta tags -->
      <meta name="title" content="" />
      <meta name="description" content="" />
      <meta name="keywords" content="" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
      <!--   <link rel="icon" type="images/x-icon" href="images/favicon.png"> -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css" />
      <!-- Fontawesome 4,5,6 links -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/fontawesome.min.css" />
      <!-- Google Fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Glagolitic&family=Rubik&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@500&display=swap" rel="stylesheet">
      <!-- Bootstrap links -->
      <link rel="stylesheet" type="text/css" href="{{asset('plugins/do-not-edit/css/bootstrap.min.css')}}">
      <!-- carousel stylesheet links -->
      <link rel="stylesheet" type="text/css" href="{{asset('css/owl.carousel.css')}}">
      <!-- bijarniadream stylesheet links -->
      <link rel="stylesheet" type="text/css" href="{{asset('plugins/do-not-edit/css/bijarniadream.min.css')}}">
      <!-- custom styles (optional) -->
      <link rel="stylesheet" type="text/css" href="{{asset('plugins/do-not-edit/css/style.min.css')}}">
   </head>
   <body>
      <!--- Start Section Header -->
        <div class="container-fluid header-section d-none">
         <div class="container">
            <div class="row align-items-center">
                 <nav class="navbar navbar-expand-lg py-2">
                  <a class="navbar-brand text-dark fw-800 " href="{{url('/')}}">
                  <!-- <img src="{{asset('images/logo.png')}}" class="img-fluid" alt="logo">-->
                      Dr. AA MRCEM
                 </a>

                  <button class="navbar-toggler fs-22 text-dark fa fa fa-bars" type="button" href="#menuOffcanvas" data-bs-toggle="offcanvas" data-bs-target="#menuOffcanvas" aria-controls="menuOffcanvas" aria-hidden="true">

                  </button>
                  <div class="collapse navbar-collapse menusbars" id="collapsiblenavbar">
                     <ul class="navbar-nav navbar-links w-100 justify-content-end align-items-center">
                        <li class="nav-item"><a class="nav-link fw-bold fs-xxl-14 fs-xl-14 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0" href="{{url('/')}}"><span>HOME</span></a>  </li>
                        <li class="nav-item dropdown ">
                           <a class="nav-link fw-bold fs-xxl-15 fs-xl-15 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0  dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span>COURSES</span> </a>
                           <ul class="dropdown-menu inner-menus py-0" aria-labelledby="navbarDropdownMenuLink">
                            @foreach ($courses as $item)
                                <li class="sub_modal"><a class="dropdown-item" href="{{url('course-details/'.$item['slug'])}}">{{$item['name']}}</a></li>
                            @endforeach
                           </ul>
                        </li>
                        <li class="nav-item dropdown ">
                           <a class="nav-link fw-bold fs-xxl-15 fs-xl-15 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0  dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span>DEMO</span> </a>
                           <ul class="dropdown-menu inner-menus py-0" aria-labelledby="navbarDropdownMenuLink">
                            @foreach ($courses as $item)
                              <li class="sub_modal"><a class="dropdown-item" href="{{url('demo/'.$item['slug'])}}">{{$item['name']}}</a></li>
                            @endforeach
                           </ul>
                        </li>
                        <li class="nav-item dropdown ">
                            <a class="nav-link fw-bold fs-xxl-15 fs-xl-15 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0  dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span>FAQ'S</span> </a>
                            <ul class="dropdown-menu inner-menus py-0" aria-labelledby="navbarDropdownMenuLink">
                                @foreach ($courses as $item)
                                    <li class="sub_modal"><a class="dropdown-item" href="{{url('faq/'.$item['slug'])}}">{{$item['name']}}</a></li>
                                @endforeach
                            </ul>
                         </li>
                        <li class="nav-item"><a class="nav-link fw-bold fs-xxl-14 fs-xl-14 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0" href="{{url('testimonial')}}"><span>TESTIMONIALS</span></a></li>
                        <li class="nav-item"><a class="nav-link fw-bold fs-xxl-14 fs-xl-14 fs-lg-13 fs-14 rounded text-dark px-xl-3 px-lg-2 px-sm-3 px-0" href="{{url('contact-us')}}"><span>CONTACT US</span></a></li>
                     </ul>
                     <ul class="navbar-nav ml-auto ms-3">
                        <a class="nav-link d-flex btn btn-button-bg fw-400 fs-xxl-14 fs-xl-15 fs-lg-14 fs-14 rounded-20 px-lg-5 py-xxl-2 py-xl-2 py-lg-1 py-md-1 py-sm-1 py-1 text-white" data-bs-toggle="modal" data-bs-target="#exampleModal" href="javascript:void(0)">ENROLL</a>
                     </ul>
                  </div>
               </nav>
            </div>
         </div>
      </div>

       <!-- Sidebar -->
       <div class="offcanvas offcanvas-start menuOffcanvas" tabindex="-1" id="menuOffcanvas" aria-labelledby="menuOffcanvasLabel">
            <div class="offcanvas-header">
                <a href="index.php" class="offcanvas-title text-dark fw-800 text-decoration-none" id="menuOffcanvasLabel">
                    <!-- <img src="{{asset('images/logo.png')}}" class="w-40"  class="img-fluid" alt="logo"> -->
                    Dr. AA MRCEM
                </a>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-3">

               <div class="accordion">
                     <a href="{{url('/')}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700"> Home  </a>
                <div>

                <div class="accordion" id="accordionExample">
                   <div class="accordion-item border-0">
                      <div class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fs-16 bg-white text-dark fs-16 border-0 mb-2 fw-700 itemmenu" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Courses
                        </button>
                     </div>
                     <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-0">
                           @foreach ($courses as $item)
                              <a href="{{url('course-details/'.$item['slug'])}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700">{{$item['name']}}</a>
                           @endforeach
                        </div>
                     </div>
                  </div>
                </div>

                <div class="accordion" id="accordionExample">
                   <div class="accordion-item border-0">
                      <div class="accordion-header" id="headingthree">
                        <button class="accordion-button collapsed fs-16 bg-white text-dark fs-16 border-0 mb-2 fw-700 itemmenu" type="button" data-bs-toggle="collapse" data-bs-target="#collapsethree" aria-expanded="false" aria-controls="collapsethree">
                            Demo
                        </button>
                     </div>
                     <div id="collapsethree" class="accordion-collapse collapse" aria-labelledby="headingthree" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-0">
                           @foreach ($courses as $item)
                              <a href="{{url('demo/'.$item['slug'])}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700"> {{$item['name']}}</a>
                           @endforeach
                        </div>
                     </div>
                  </div>
                </div>


                <div class="accordion" id="accordionExample">
                   <div class="accordion-item border-0">
                      <div class="accordion-header" id="headingfour">
                        <button class="accordion-button collapsed fs-16 bg-white text-dark fs-16 border-0 mb-2 fw-700 itemmenu" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                            FAQ
                        </button>
                     </div>
                     <div id="collapsefour" class="accordion-collapse collapse" aria-labelledby="headingfour" data-bs-parent="#accordionExample">
                        <div class="accordion-body p-0">
                           @foreach ($courses as $item)
                              <a href="{{url('faq/'.$item['slug'])}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700"> {{$item['name']}}</a>
                           @endforeach     
                        </div>
                     </div>
                  </div>
                </div>

                <div class="accordion">
                     <a href="{{url('testimonial')}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700"> Testimonials  </a>
                <div>

                <div class="accordion">
                     <a href="{{url('contact-us')}}" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700"> Contact Us  </a>
                <div>

                <div class="accordion">
                     <a href="javascript:void(0)" class="itemmenu text-decoration-none text-dark fs-16 border-0 mb-2 collapsed fw-700" data-bs-toggle="modal" data-bs-target="#exampleModal"> Enroll  </a>
                <div>

                 </div>
               </div>
               </div>
             </div>
            </div>
          </div>
	       </div>
        </div>
      </div>

      </div>

	        <!--- End Section Header -->

              <div class="right_fix_btn d-none">
                 <a href="https://web.whatsapp.com/" target="_blank" class="coccoc-alo-ph-img-circle d-flex justify-content-center align-items-center" style="background: #00d56f;">
                     <img alt="Mobile App Development" width="42" height="42" src="{{asset('images/whatsapp.svg')}}">
                 </a>
                 <a href="https://web.telegram.org/" target="_blank" class="coccoc-alo-ph-img-circle d-flex justify-content-center align-items-center" style="background: #229ED9;">
                      <img alt="Mobile App Development" width="42" height="42" src="{{asset('images/telegram.png')}}">
                 </a>
              </div>

              <div class="container-fluid footer-bottom py-xxl-3 py-xl-3 py-lg-3 py-md-3 py-sm-2 py-2 d-none">
               <div class="container">
                  <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-sm-12 col-xl-12 col-12 col-md-12 col-lg-12 col-12 text-center links">
                        <ul class="list-unstyled list-inline ps-0 mb-0">
                           <li class="list-inline-item"><a href="{{url('terms-condition')}}" class="text-dark text-decoration-none fw-600"> Terms & Condition</a></li>|
                           <li class="list-inline-item ps-1"><a href="{{url('privacy-policy')}}" class="text-dark text-decoration-none fw-600"> Privacy Policy</a></li>
                    </div>
                  </div>
               </div>
            </div>




            <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered">
    <div class="modal-content" style="background:#F4F6F9;border-radius: 10px;">
      <div class="modal-header py-0 pt-0 border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body mainoptions pb-3">
      <div class="col-12 fs-xxl-25 fs-xl-25 fs-lg-25 fs-md-25 fs-sm-30 fs-23 text-black fw-m lh-1 lh-sm position-relative fw-600 text-center">
      Download our app available  <span class="color2">on playstore. </span>
                              </div>

       <div class="col-12 fs-xxl-18 fs-xl-18 fs-lg-18 fs-md-15 fs-sm-14 fs-16 text-center">Our mobile apps are available now </div>
       <div class="row py-3">
          <div class="col-6 text-end">
           <a href="#0"> <img src="{{asset('images/play-store.png')}}" class="img-fluid w-65 rounded-10"> </a>
          </div>
          <div class="col-6 text-start">
          <a href="#0"> <img src="{{asset('images/App-store.png')}}" class="img-fluid w-65 rounded-10"> </a>
          </div>
       </div>
      </div>
    </div>
  </div>
</div>


      @yield('content')

        <!--- Start Footer Section -->

        <a href="#" class="top-arrow" style="display: block;"></a>

        <!-- jquery min Js -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

         <!-- bootstrap bundle JS -->
         <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

            <!-- Owl carousel Js -->
         <script src="{{asset('js/owl.carousel.js')}}"></script>

         <!-- FontAwesome 5 Js -->
         <script src="https://kit.fontawesome.com/0b8334f960.js" crossorigin="anonymous"></script>

         <!-- FontAwesome 6 Js -->
         <script src="https://kit.fontawesome.com/3080004259.js" crossorigin="anonymous"></script>

         <!-- custom js -->
         <script src="{{asset('js/main.min.js')}}"></script>
         <script>
            function redirectToApp() {
               var userAgent = navigator.userAgent || navigator.vendor || window.opera;

               if (/android/i.test(userAgent)) {
                  window.location.href = 'https://play.google.com/store/apps/details?id=your_android_app_package_name';
               } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                  window.location.href = 'https://apps.apple.com/us/app/your_ios_app_id';
               } else {
                  alert('Error: Like for enrollment download our app available on playstore.');
               }
        }
         </script>
    </body>
</html>
