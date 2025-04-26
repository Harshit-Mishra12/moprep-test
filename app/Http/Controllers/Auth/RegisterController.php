<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function userStore(Request $request){

        
        if($request->ajax()){

            $rules = [
                'name' => 'required',
                'email'=>'required|email',
                'mobile'=>'required|numeric|min:10',
                'referrer_id'=>'exists:users,unique_id',
                'password'=>'required',
                "password_confirmation"=>"required|same:password|min:8",
			];
			
			$validator = \Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				$message = [];
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				} 	
				return response(array("error" => true, "msg" => $message));
						
			}else{

				try{

                    $userData = User::where('email',$request->email)->first();
                    
                    if(!$userData){

                        $referrerId=$request->post('referrer_id');

                        if(!$request->post('referrer_id')){
                            $referrerId='ACUSHINE';
                        }

                        $user = \App\Models\User::create([
                            'name' => $request->post('name'),
                            'email' => $request->post('email'),
                            'mobile' =>  $request->post('mobile'),
                            'referrer_id' =>  $referrerId,
                            'unique_id' =>  'AS'.rand(00000,99999),
                            'user_type' => "Customer",
                            'designation_id' => "2",
                            'password' => Hash::make($request->password),
                        ]);

                        
                        \App\Models\UserDetails::insert([
                            'user_id' => $user->id,
                        ]);

                        
                        \App\Models\KYC::insert([
                            'user_id' => $user->id,
                        ]);
                        
                        \App\Models\Bankdetails::insert([
                            'user_id' => $user->id,
                        ]);
                        
                        if($user){
                           
                            $to = $request->post('email');

                            $subject = 'Welcome Email from Optishine Business Private Limited';

                            $msg = [
                                'name'=>$user->name,
                                'unique_id'=>$user->unique_id,
                                'password'=>$request->password,
                                'mobile'=>$request->post('mobile'),
                            ];

                            // \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg,$template='register');

                            $msgHtml='<div class="modal-body">
                            <div class="modal-txt text-center">
                             <h4 class="mb-3">CONGRATULATION! '.ucfirst($msg['name']).'</h4>
                             <h6>YOU HAVE SUCCESSFULLY SIGNED UP.</h6>
                           </div>
                           <div class="modal-table table-responsive">
                            <table class="table table-bordered">
                              
                                <tbody>
                                  <tr>
                                    <td class="text-dark">Your Profile Name</td>
                                    <td class="text-success">'.ucfirst($msg['name']).'</td>
                                  </tr>
                                  <tr>
                                    <td class="text-dark">Your Login User Id</td>
                                    <td class="text-success">'.$msg['unique_id'].'</td>
                                  </tr>
                                  <tr>
                                    <td class="text-dark">Your Login Password</td>
                                    <td class="text-success">'.$msg['password'].'</td>
                                  </tr>
                                </tbody>
                              </table>
                              <p class="not-box">NOTE: PLEASE SAVE THIS USER ID AND PASSWORD FOR FUTURE LOGIN!</p>
                           </div>
                    
                          </div>';
                            return response(array('error'=>false,"msg" =>$msgHtml));
                        
                        }else{

                            return response(array('error'=>true,"msg" =>'Something Wrong ! Please Try again.'));
                        }

                    }else{

                        return response(array('error'=>true,"msg" =>'User already exits'));
                    }



				}catch (\Exception $e){ 

                    return response(array("error"=> true, "msg" => $e->getMessage())); 
				}
			}

        }else{

            return view('auth.register');
        }
			
			 
    }

    public function checkReferralCodeUser(Request $request){

        
        if($request->ajax()){

            $rules = [
                'referrer_id' => 'required',
			];
			
			$validator = \Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				$message = [];
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				} 	
				return response(array("error" => true, "msg" => $message));
						
			}else{

				try{

                    $userData = User::where('unique_id',$request->referrer_id)->first();
 
                    if($userData){
  
                        $msg = $userData->name;

                        return response(array('error'=>false,"msg" =>'Referrer Name: '.$msg));
                        
                    }else{

                        return response(array('error'=>true,"msg" =>'Referrer id not found'));
                    }

				}catch (\Exception $e){ 

                    return response(array("error"=> true, "msg" => $e->getMessage())); 
				}
			}

        }
			 
    }
}
