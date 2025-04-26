<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getChapter(Request $request){

        $courseId=$request->get('course_id');

        $option="<option value=''>--Select Chapter--</option>";

        if($courseId>0){

            $chapterResult=\App\Models\Chapter::where('course_id',$courseId)->where('status','Active')->get();

            foreach($chapterResult as $chapter){
    
                $option.="<option value='".$chapter['id']."'>".ucfirst($chapter['name'])."</option>";
            }
            

        }

        return response(array('message'=>'Chapter fetched successfully.','html'=>$option));
    }


    public function getTopic(Request $request){

        $courseId=$request->get('chapter_id');

        $option="<option value=''>--Select Topic--</option>";

        if($courseId>0){

            $chapterResult=\App\Models\TopicMaterials::where('batch_id',$courseId)->where('status','1')->get();

            foreach($chapterResult as $chapter){
    
                $option.="<option value='".$chapter['id']."'>".ucfirst($chapter['topic'])."</option>";
            }
            

        }

        return response(array('message'=>'Topic fetched successfully.','html'=>$option));
    }

    public function index(){
        $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
        return view('home',compact('courses'));
    }

    public function faq($slug){
        $course = \App\Models\Course::with(['faq_data'=>function($query){
            $query->where('status','Active');
            $query->where('deleted_at',NULL);
        }])->whereHas('faq_data')->where('slug',$slug)->first();

        return view('faq',compact('course'));
    }
    
    public function demo($slug){
        $courseDemo = \App\Models\Course::with(['demo_video_data'=>function($query){
            $query->where('status','Active');
            $query->where('deleted_at',NULL);
        }])->whereHas('demo_video_data')->where('slug',$slug)->first();

        return view('demo',compact('courseDemo'));
    }
    
    public function courseDetails($slug){
        $courseDetail = \App\Models\Course::where('slug',$slug)->where('status','1')->where('deleted_at',NULL)->first();
        return view('course-details',compact('courseDetail'));
    }

    public function testimonial(){
        return view('testimonial');
    }
    
    public function contactUs(){
        return view('contact');
    }

    public function privacyPolicy(){
        $privacyPolicy = \App\Models\Information::where('id','2')->where('status','Active')->first();
        return view('privacy-policy',compact('privacyPolicy'));
    }

    public function termsCondition(){
        $termsCondition = \App\Models\Information::where('id','1')->where('status','Active')->first();
        return view('terms-condition',compact('termsCondition'));
    }

    public function updatePayment(Request $request){

        $paymentConsole = \App\Models\Paymentconsole::create(['data'=>json_encode($request->all())]);
        $result=$request->all();
		$data=$result['data']['object']['metadata'];
        
        $purchasedHistory = \App\Models\Purchasedhistory::create($data);
        if($purchasedHistory){
            return response(array('message'=>'Batch Purchased Successfully.'),200);
        }

    }
}
