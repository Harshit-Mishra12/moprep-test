<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;
use Validator;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class PreLoginController extends Controller
{


	public function register(Request $request)
	{

		$rules = [
			'mobile' => 'required|numeric|digits:10|unique:users,whatsapp_number',
			'email' => 'email|unique:users,email',
			'password' => 'required',
			'confirm_password' => 'required|same:password|min:6',
			'name' => 'required|string',
			'state' => 'required',
			//'firebase_token' => 'required',
		];

		$customMessages = [
			'mobile.required' => 'The mobile number field is required.',
			'mobile.unique' => 'The mobile number has already been taken.',
			'mobile.integer' => 'The mobile number field must be in number only.',
		];

		$validator = \Validator::make($request->json()->all(), $rules, $customMessages);

		if ($validator->fails()) {

			$message = $validator->errors()->first();
			return response(['error' => true, 'message' => $message], 400);
		} else {

			try {

				$user = new \App\Models\User();

				$user = \App\Models\User::create([
					'name' => $request->post('name'),
					'email' => $request->post('email'),
					'whatsapp_number' => $request->post('mobile'),
					'college' => $request->post('college'),
					'state' => $request->post('state'),
					'user_type' => 'User',
					'designation_id' => '2',
					'password' => Hash::make($request->post('password')),
					'otp_status' => 0,
					'firebase_token' => $request->post('firebase_token'),
				]);


				// if ($user->email && env('APP_ENV') == 'Production') {
				// 	$to = $request->post('email');
				// 	$subject = 'Welcome Email from StarIndiia Private Limited';
				// 	$msg = [
				// 		'name' => $request->post('name'),
				// 		'unique_id' => $uniqueId,
				// 		'password' => $request->password,
				// 		'mobile' => $request->post('mobile'),
				// 	];
				// 	\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, $template = 'register');
				// }

				return response(array(
					"error" => false,
					"message" => "Register successfully.",
					"authToken" => $user->createToken('authToken')->accessToken,
					"verify" => true,
					"firebase_token" => $user->firebase_token,
					"result" => $user->toArray(),

				), 200);
			} catch (\Exception $e) {
				return response(['error' => true, 'message' => $e->getMessage()], 400);
			}
		}
	}

	public function stateList(Request $request)
	{
		$result = DB::table('tbl_states')->where('country_id', '101')->orderBy('name', 'ASC')->get();

		if ($result->count() == 0) {
			return response(array("error" => true, "message" => "No data found. ", "result" => array()), 400);
		}

		$fresult = [];
		if (!empty($result)) {
			foreach ($result as $result) {
				$tResult = [
					'value' => $result->id,
					'label' => ucfirst($result->name)
				];

				$fresult[] = $tResult;
			}
		}

		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $fresult], 200);
	}

	public function privacyPolicy(Request $request)
	{
		$file = asset('uploads/privacy-policy.pdf');
		$privacy = url('privacy-policy');
		return response(["error" => false, "message" => "Data fetched successfully.", "result" => $file, "privacy" => $privacy], 200);
	}
	// public function login(Request $request)
	// {
	// 	// Validate incoming request
	// 	$validatedData = $request->validate([
	// 		'mobile' => 'required|numeric|digits:10',
	// 		'password' => 'required|min:6',
	// 		'firebase_token' => 'nullable|string',
	// 	]);

	// 	try {
	// 		$user = \App\Models\User::where('whatsapp_number', $validatedData['mobile'])->first();

	// 		if ($user && Hash::check($validatedData['password'], $user->password)) {

	// 			// Update firebase token if provided
	// 			if (!empty($validatedData['firebase_token'])) {
	// 				$user->update([
	// 					'firebase_token' => $validatedData['firebase_token'],
	// 				]);
	// 			}

	// 			return response([
	// 				"error" => false,
	// 				"message" => "Login successfully.",
	// 				"token" => $user->createToken('authToken')->accessToken,
	// 				"verify" => true,
	// 				"result" => $user->toArray()
	// 			], 200);
	// 		} else {
	// 			return response()->json([
	// 				"error" => true,
	// 				"message" => "Unauthenticated"
	// 			], 401);
	// 		}
	// 	} catch (\Exception $e) {
	// 		return response([
	// 			"error" => true,
	// 			"message" => $e->getMessage()
	// 		], 400);
	// 	}
	// }

	public function login(Request $request)
	{


		$userResult = \App\Models\User::where([
			['whatsapp_number', '=', $request->post('mobile')],
		])->first();
		dd($userResult->createToken('authToken')->accessToken);
		$rules = [
			'mobile' => 'required|numeric|digits:10',
			'password' => 'required|min:6',
		];



		$validator = Validator::make($request->json()->all(), $rules);



		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("error" => true, "message" => $message), 400);
		} else {

			try {
				$userResult = \App\Models\User::where([
					['whatsapp_number', '=', $request->post('mobile')],
				])->first();

				if ($userResult && \Hash::check($request->post('password'), $userResult->password)) {
					if ($request->post('firebase_token')) {
						\App\Models\User::where('whatsapp_number', $request->post('mobile'))->update(['firebase_token' => $request->post('firebase_token')]);
					}
					// dd($userResult);

					return response(array("error" => false, "message" => "Login successfully.", "token" => $userResult->createToken('authToken')->accessToken, "verify" => true, "result" => $userResult->toArray()), 200);
				} else {
					return response()->json(["error" => true, 'message' => 'Unauthenticated'], 401);
				}
			} catch (\Exception $e) {
				// dd($e);

				return response(array("error" => true, "message" => $e->getMessage()), 400);
			}
		}
	}

	public function mockTestSubmitAutoWithoutLogin(Request $request)
	{

		$currentDateTime = Carbon::now();


		$mockDurationData = DB::table('mockup_test_duration as d')
			->leftJoin('mockup_test_result as r', function ($join) {
				$join->on('d.user_id', '=', 'r.user_id')
					->on('d.mock_id', '=', 'r.mock_id');
			})
			->where('d.end_date_time', '<=', $currentDateTime)
			->where('r.mock_id', null)
			->select('d.*')
			->get();

		if (!empty($mockDurationData)) {
			foreach ($mockDurationData as $mock) {
				$mock_id = $mock->mock_id;
				$user_id = $mock->user_id;
				$mockData = DB::table('mockups')->where('id', $mock_id)->first();

				if (!empty($mockData)) {
					$average_time = $mockData->duration;

					$answered_questions = DB::table('mockup_test_answers')
						->where([
							'user_id' => $user_id,
							'mock_id' => $mock_id
						])
						->select(DB::raw('COUNT(*) as attempted_questions'))
						->selectRaw('SUM(CASE WHEN answer_status = 1 THEN 1 ELSE 0 END) as right_answers')
						->selectRaw('SUM(CASE WHEN answer_status = 2 THEN 1 ELSE 0 END) as wrong_answers')
						->first();

					$total_questions = DB::table('mock_questions')->where([
						'mock_id' => $mock_id
					])->count();

					$user_name = DB::table('users')->where('id', $user_id)->pluck('name')[0];

					$ansData = [
						"user_id" => $user_id,
						"mock_id" => (int) $mock_id,
						"user_name" => $user_name,
						"total_questions" => $total_questions,
						"attempted_questions" => $answered_questions->attempted_questions,
						"right_answers" => $answered_questions->right_answers,
						"wrong_answers" => $answered_questions->wrong_answers,
						"average_time" => (int) $average_time,
						"marks" => (string) round(($answered_questions->right_answers * 2.08) - ($answered_questions->wrong_answers * 0.69), '2')
					];

					$checkMock = DB::table('mockup_test_result')->where(['user_id' => $user_id, "mock_id" => $mock_id])->first();
					if (!empty($checkMock)) {
						DB::table('mockup_test_result')->where(['id' => $checkMock->id])->update($ansData);
					} else {
						DB::table('mockup_test_result')->insert($ansData);
					}
				}
			}
		}

		return response(["error" => false, "message" => "Exam submitted successfully."], 200);
	}


	public function verifyEmail(Request $request)
	{

		$rules = [
			'email' => 'required|email',
		];

		$validator = \Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("error" => true, "message" => $message), 400);
		} else {

			try {
				$userResult = \App\Models\User::where([
					['email', '=', $request->post('email')],
				])->first();

				if ($userResult) {
					$otp = rand('1111', '9999');
					$mobile = $userResult->whatsapp_number;
					$this->sendOtpSms($mobile, $otp);
					\App\Models\User::where('email', '=', $request->post('email'))->update(['otp' => $otp]);
					return response(array("error" => false, "message" => "OTP send successfully.", "result" => $userResult->toArray()), 200);
				} else {
					return response()->json(["error" => true, 'message' => 'Email not found'], 400);
				}
			} catch (\Exception $e) {

				return response(array("error" => true, "message" => $e->getMessage()), 400);
			}
		}
	}


	public function resendOtp(Request $request)
	{

		$rules = [
			'mobile' => 'required|numeric|digits:10',
		];

		$validator = \Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("error" => true, "message" => $message), 400);
		} else {

			try {
				$userResult = \App\Models\User::where([
					['whatsapp_number', '=', $request->post('mobile')],
				])->first();

				if ($userResult) {
					$otp = rand('1111', '9999');
					$this->sendOtpSms($request->post('mobile'), $otp);
					\App\Models\User::where('whatsapp_number', '=', $request->post('mobile'))->update(['otp' => $otp]);
					return response(array("error" => false, "message" => "OTP send successfully.", "result" => $userResult->toArray()), 200);
				} else {
					return response()->json(["error" => true, 'message' => 'Mobile number not found'], 400);
				}
			} catch (\Exception $e) {

				return response(array("error" => true, "message" => $e->getMessage()), 400);
			}
		}
	}

	public function sendOtpSms($mobile, $otp)
	{
		$method = "GET";
		// $otp = "1111";
		// $mobile = "9352309592";
		$url = "https://www.fast2sms.com/dev/bulkV2?authorization=MjzaY3HSEF2WtGOAiVT0ekRXIwNLfbCusxQ9ygDprvcJPKZB1qTDUCi8ynltmK9Xfz6bZJkMdvSQcrOh&route=otp&variables_values=$otp&flash=0&numbers=$mobile";
		//$url="https://www.fast2sms.com/dev/bulkV2?authorization=RNdEsqPvChoUpj6MIiL2rlnSm1QYAbZ73tcu8xHOyWwJ4VBGF0MXz2U4QfvojeKWO5AIxp6THdJk7r8g&route=otp&variables_values='".$otp."'&flash=0&numbers='".$mobile."'";

		//echo $url; die;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method
		));

		$response = curl_exec($curl);

		curl_close($curl);

		//echo "<pre>"; print_r($response); die;

		return $response;
	}

	public function verifyOtp(Request $request)
	{

		$rules = [
			'mobile' => 'required|numeric|digits:10',
			'otp' => 'required|numeric|digits:4',
		];

		$validator = \Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("error" => true, "message" => $message), 400);
		} else {

			try {
				$userResult = \App\Models\User::where([
					['whatsapp_number', '=', $request->post('mobile')],
					['otp', '=', $request->post('otp')]
				])->first();

				if ($userResult) {
					\App\Models\User::where('whatsapp_number', '=', $request->post('mobile'))->update(['otp' => '0', 'otp_status' => '1']);
					return response(array("error" => false, "message" => "OTP verified successfully.", "token" => $userResult->createToken('authToken')->accessToken, "result" => $userResult->toArray()), 200);
				} else {
					return response()->json(["error" => true, 'message' => 'Invalid Otp'], 400);
				}
			} catch (\Exception $e) {

				return response(array("error" => true, "message" => $e->getMessage()), 400);
			}
		}
	}



	public function createNewPassword(Request $request)
	{

		$rules = [
			'mobile' => 'required|numeric|digits:10',
			'password' => 'required',
			'confirm_password' => 'required|same:password|min:6',
		];

		$validator = \Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {

			$message = $validator->errors()->first();
			return response(['error' => true, 'message' => $message], 400);
		} else {

			try {
				$userResult = \App\Models\User::where([
					['whatsapp_number', '=', $request->post('mobile')]
				])->first();

				if ($userResult) {
					\App\Models\User::where('whatsapp_number', '=', $request->post('mobile'))->update(['password' => Hash::make($request->post('password'))]);
					return response(array("error" => false, "message" => "Password created successfully.", "authToken" => $userResult->createToken('authToken')->accessToken, "verify" => true, "result" => $userResult->toArray()), 200);
				} else {
					return response()->json(["error" => true, 'message' => 'Invalid mobile number'], 400);
				}
			} catch (\Exception $e) {
				return response(['error' => true, 'message' => $e->getMessage()], 400);
			}
		}
	}

	public function getWebhooks()
	{
		$url = 'https://api.razorpay.com/v1/webhooks';

		$apiKey = env('RAZORPAY_KEY');
		$apiSecret = env('RAZORPAY_SECRET');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
		}

		curl_close($ch);

		if (isset($error_msg)) {
			return response()->json(['error' => $error_msg], 500);
		}

		if ($httpcode !== 200) {
			return response()->json(['error' => 'Failed to fetch webhooks'], $httpcode);
		}

		return response()->json(json_decode($response, true));
	}



	// Old api
	public function checkExistUser(Request $request)
	{

		$rules = [
			'email' => 'required|email',
		];

		$validator = Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("message" => $message), 400);
		} else {

			try {

				//chk unique email
				$emailResult = \App\Models\User::where([
					['email', $request->json()->get('email')],
					['designation_id', '2']
				])->first();
				if ($emailResult) {

					return response(array('error' => false, 'message' => 'Email id is exist.'), 200);
				} else {

					return response(array('error' => true, 'message' => 'Email does not exist.'), 200);
				}
			} catch (\Exception $e) {

				return response(array("message" => $e->getMessage()), 400);
			}
		}
	}


	public function sendOtpOnMobile(Request $request)
	{

		$rules = [
			'email' => 'required|email',
		];

		$emailResult = \App\Models\User::where([
			['email', $request->json()->get('email')],
			['designation_id', '2']
		])->first();

		if (!$emailResult) {

			$rules['name'] = "required|string";
			$rules['telegram_number'] = "required";
			$rules['whatsapp_number'] = "required";
		}

		$validator = Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("message" => $message), 400);
		} else {

			try {

				if ($emailResult && $emailResult->status == '0') {
					return response(array('message' => 'Your account has been deactivated. Please contact the administrator'), 400);
				}

				$otp = \App\Helpers\commonHelper::getOtp();

				$regType = 'Old';
				if (!$emailResult) {

					$emailResult = new \App\Models\User();
					$emailResult->email = $request->json()->get('email');
					$emailResult->name = $request->json()->get('name');
					$emailResult->telegram_number = $request->json()->get('telegram_number');
					$emailResult->whatsapp_number = $request->json()->get('whatsapp_number');
					$emailResult->designation_id = '2';
					$regType = 'New';
				}

				$emailResult->otp = $otp;
				$emailResult->save();

				if ($regType == 'New') {
					$emailResult->unique_id = 'MRCEM' . str_pad($emailResult->id, 5, "0", STR_PAD_LEFT);
					$emailResult->save();
				}

				$data = array(
					'email' => $emailResult->email,
					'subject' => 'Your OTP for Verification',
					'otp' => $otp,
					'template' => 'otp',
					'name' => $emailResult->name
				);

				\App\Helpers\commonHelper::emailSendToUser($data);

				return response(array('message' => 'OTP sent successfully on your registered Email id.'), 200);
			} catch (\Exception $e) {

				return response(array("message" => $e->getMessage()), 400);
			}
		}
	}

	public function validateOtp(Request $request)
	{

		$rules['email'] = 'required|email';
		$rules['otp'] = 'required|size:4';

		$validator = Validator::make($request->json()->all(), $rules);

		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}

			return response(array("error" => true, "message" => $message), 200);
		} else {

			try {

				//chk unique mobile
				$userResult = \App\Models\User::where([
					['email', '=', $request->json()->get('email')],
					['designation_id', '=', '2'],
					['otp', '=', $request->json()->get('otp')],
				])->first();

				if (!$userResult) {

					return response(array("message" => "OTP doesn’t match. Please try again"), 400);
				} else {

					\DB::table('oauth_access_tokens')
						->where('user_id', $userResult->id)
						->delete();

					$userResult->otp = '0';
					$userResult->save();

					return response(array("message" => "OTP matched successfully.", "token" => $userResult->createToken('authToken')->accessToken, "result" => $userResult->toArray()), 200);
				}
			} catch (\Exception $e) {

				return response(array("message" => $e->getMessage()), 400);
			}
		}
	}

	public function webhookResponseData()
	{
		$result = \App\Models\Paymentconsole::orderBy('id', 'DESC')->limit('100')->get();
		echo '<pre>';
		print_r($result->toArray());
		die;
	}

	public function webhookResponse(Request $request)
	{

		$payload = $request->getContent();
		$signature = $request->header('X-Razorpay-Signature');
		//$webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');

		$webhookSecret = env('RAZORPAY_WEBHOOK_SECRET') ? env('RAZORPAY_WEBHOOK_SECRET') : 'fWy_bYAE7iGHZ32';


		$console = new \App\Models\Paymentconsole();
		$console->data = $payload;
		$console->save();

		if ($this->verifySignature($payload, $signature, $webhookSecret)) {
			$event = json_decode($payload, true);

			$consolen = new \App\Models\Paymentconsole();
			$consolen->data = $event['event'];
			$consolen->save();

			switch ($event['event']) {
				case 'payment.captured':
					$orderId = $event['payload']['payment']['entity']['order_id'];
					$transaction = DB::table('transactions')->where('paymentid', $orderId)->where('status', '0')->update(['status' => '1']);
					// Handle payment captured event
					//Log::info('Payment captured:', $event['payload']['payment']);
					break;
				case 'payment.failed':
					$orderId = $event['payload']['payment']['entity']['order_id'];
					$transaction = DB::table('transactions')->where('paymentid', $orderId)->where('status', '0')->update(['status' => '2']);
					// Handle payment failed event
					//Log::info('Payment failed:', $event['payload']['payment']);
					break;
				case 'order.paid':
					$orderId = $event['payload']['order']['entity']['id'];
					$transaction = DB::table('transactions')->where('paymentid', $orderId)->where('status', '0')->update(['status' => '1']);
					// Handle order paid event
					//Log::info('Order paid:', $event['payload']['order']);
					break;
				case 'refund.processed':
					// Handle refund processed event
					//Log::info('Refund processed:', $event['payload']['refund']);
					break;
				// Add other event cases here
				default:
					//Log::info('Received unknown event type');
			}

			return response()->json(['status' => 'success']);
		} else {
			return response()->json(['status' => 'failed'], 400);
		}
	}

	private function verifySignature($payload, $signature, $secret)
	{
		$generatedSignature = hash_hmac('sha256', $payload, $secret);
		return hash_equals($generatedSignature, $signature);
	}

	public function removeNotification()
	{
		DB::table('users')->update(['firebase_token' => NULL]);
		echo "success";
		die;
	}
}
