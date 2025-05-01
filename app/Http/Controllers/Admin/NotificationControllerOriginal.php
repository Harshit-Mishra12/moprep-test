<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Validator;
use Yajra\DataTables\DataTables;
use GuzzleHttp\Client;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationControllerOriginal extends Controller
{
	public function add(Request $request)
	{

		if ($request->isMethod('post')) {

			$rules = [
				'title' => 'required',
				'description' => 'required',
			];

			// if((int) $request->post('id')==0){

			// 	$rules['image']='required|image|mimes:jpeg,png,jpg,gif,svg';
			// }

			$validator = Validator::make($request->all(), $rules);

			if ($validator->fails()) {
				$message = "";
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message = $msg[0];
					break;
				}

				return response(array('message' => $message), 403);
			} else {

				try {
					if ((int) $request->post('id') > 0) {

						$notification = Notification::find($request->post('id'));
					} else {

						$notification = new Notification();
					}


					$notification->title = $request->post('title');
					$notification->description = $request->post('description');

					$notification->save();

					$accessToken = $this->getAccessToken();
					//$registrationToken = "fHx1eBKsR66_zvyz9Ubg_J:APA91bE_EZiS5lG1EfjHiuPT-HiZI22112D4CFmKVd_DHH0W6Zsb8tlYJv58WwxKTLxVOlCh_inZXThY_snK0HLr1e4zEKfmQWr3YNyMzn4O4wzU2_Jy9iG7_MDP8zh1uhYxDIlx1Efl";

					$this->sendNotification($accessToken, $request->post('title'), $request->post('description'));


					if ((int) $request->post('id') > 0) {

						return response(array('message' => 'Notification updated successfully.', 'reset' => false), 200);
					} else {

						return response(array('message' => 'Notification added successfully.', 'reset' => true, 'script' => false), 200);
					}
				} catch (\Exception $e) {

					return response(array("message" => $e->getMessage()), 403);
				}
			}

			return response(array('message' => 'Data not found.'), 403);
		}

		$result = [];
		return view('admin.notification.add', compact('result'));
	}

	public function base64UrlEncode($text)
	{
		return str_replace(
			['+', '/', '='],
			['-', '_', ''],
			base64_encode($text)
		);
	}

	function getAccessToken()
	{

		// Read service account details
		$authConfigPath = storage_path('app/auth/google-services.json');
		$authConfigString = file_get_contents($authConfigPath);

		// Parse service account details
		$authConfig = json_decode($authConfigString);

		// Read private key from service account details
		$secret = openssl_get_privatekey($authConfig->private_key);

		// Create the token header
		$header = json_encode([
			'typ' => 'JWT',
			'alg' => 'RS256'
		]);

		// Get seconds since 1 January 1970
		$time = time();

		// Allow 1 minute time deviation between client en server (not sure if this is necessary)
		$start = $time - 60;
		$end = $start + 3600;

		// Create payload
		$payload = json_encode([
			"iss" => $authConfig->client_email,
			"scope" => "https://www.googleapis.com/auth/firebase.messaging",
			"aud" => "https://oauth2.googleapis.com/token",
			"exp" => $end,
			"iat" => $start
		]);

		// Encode Header
		$base64UrlHeader = $this->base64UrlEncode($header);

		// Encode Payload
		$base64UrlPayload = $this->base64UrlEncode($payload);

		// Create Signature Hash
		$result = openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

		// Encode Signature to Base64Url String
		$base64UrlSignature = $this->base64UrlEncode($signature);

		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

		//-----Request token, with an http post request------
		// $options = array('http' => array(
		// 	'method'  => 'POST',
		// 	'content' => 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion='.$jwt,
		// 	'header'  => "Content-Type: application/x-www-form-urlencoded"
		// ));
		// $context  = stream_context_create($options);
		// $responseText = file_get_contents("https://oauth2.googleapis.com/token", false, $context);

		// $response = json_decode($responseText,true);
		// return $response['access_token'];

		$url = "https://oauth2.googleapis.com/token";
		$data = "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion=" . $jwt;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Content-Type: application/x-www-form-urlencoded"
		]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification temporarily
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL host verification temporarily

		$responseText = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($responseText, true);
		return $response['access_token'];
	}

	function sendNotification($accessToken, $title, $body)
	{
		$url = 'https://fcm.googleapis.com/v1/projects/moprep-4afca/messages:send';

		// Retrieve all non-null firebase_token values from the users table
		$registrationTokens = DB::table('users')
			->whereNotNull('firebase_token')
			->pluck('firebase_token')
			->toArray();

		$headers = [
			'Authorization' => 'Bearer ' . $accessToken,
			'Content-Type' => 'application/json'
		];

		$client = new Client();
		$responses = [];
		$i = 1;
		foreach ($registrationTokens as $token) {
			$message = [
				'message' => [
					'token' => $token,
					'notification' => [
						'title' => $title,
						'body' =>  $body,
					],
				]
			];


			try {
				$response = $client->post($url, [
					'headers' => $headers,
					'json' => $message
				]);
				$body = json_decode($response->getBody()->getContents(), true);
				\Log::info("Notification sent to token: $token", $body);
			} catch (\GuzzleHttp\Exception\ClientException $e) {
				$errorBody = '';
				if ($e->getResponse()) {
					$errorBody = $e->getResponse()->getBody()->getContents();
				}

				\Log::error("Failed token: $token");
				\Log::error("FCM error: " . $e->getMessage());
				\Log::error("Response: " . $errorBody);

				// Optionally remove the token if it's invalid
				// if (str_contains($errorBody, 'Requested entity was not found')) {
				//     DB::table('users')->where('firebase_token', $token)->update(['firebase_token' => null]);
				// }
			}
		}


		return $responses;
	}

	// 		function sendNotification($accessToken, $title, $body) {
	// 		$url = 'https://fcm.googleapis.com/v1/projects/moprep-4afca/messages:send';

	// 		// Retrieve all non-null firebase_token values from the users table
	// 		$registrationTokens = DB::table('users')
	// 			->whereNotNull('firebase_token')
	// 			->pluck('firebase_token')
	// 			->toArray();

	// 		$headers = [
	// 			'Authorization' => 'Bearer ' . $accessToken,
	// 			'Content-Type' => 'application/json'
	// 		];

	// 		$client = new Client();
	// 		$responses = [];
	// 	    $i=1;
	// 		foreach ($registrationTokens as $token) {
	// 			$message = [
	// 				'message' => [
	// 					'token' => $token,
	// 					'notification' => [
	// 						'title' => $title,
	// 						'body' => $body
	// 					]
	// 				]
	// 			];

	// 			try {
	// 				$response = $client->post($url, [
	// 					'headers' => $headers,
	// 					'json' => $message
	// 				]);

	// 				$responses[] = json_decode($response->getBody()->getContents(), true);
	// 			} catch (Exception $e) {
	// 				$responses[] = [
	// 					'error' => $e->getMessage()
	// 				];
	// 			}
	// 			$i++;
	// 		}

	// 		return $responses;
	// 	}

	function sendNotification_withoutTry($accessToken, $title, $body)
	{
		$url = 'https://fcm.googleapis.com/v1/projects/moprep-4afca/messages:send';

		// Retrieve all non-null firebase_token values from the users table
		$registrationTokens = DB::table('users')
			->whereNotNull('firebase_token')
			->pluck('firebase_token')
			->toArray();

		$headers = [
			'Authorization' => 'Bearer ' . $accessToken,
			'Content-Type' => 'application/json'
		];

		$client = new Client();
		$responses = [];
		$i = 1;
		foreach ($registrationTokens as $token) {
			$message = [
				'message' => [
					'token' => $token,
					'notification' => [
						'title' => $title,
						'body' => $body
					]
				]
			];


			$response = Http::withHeaders([
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $accessToken
			])
				->withOptions([
					'verify' => false // This disables SSL verification
				])
				->post($url, $message);

			$responseText = $response->body();
			$responseArray = $response->json();

			//echo '<pre>'; print_r($responseArray); die;

			//try {
			// $response = $client->post($url, [
			// 	'headers' => $headers,
			// 	'json' => $message
			// ]);

			//$responses[] = json_decode($response->getBody()->getContents(), true);
			// } catch (Exception $e) {
			// 	$responses[] = [
			// 		'error' => $e->getMessage()
			// 	];
			// }
			$i++;
		}

		return $responses;
	}

	public function notificationList()
	{

		//$result=Notification::orderBy('id','DESC')->get();


		$permission = session('permission') ?? [];
		$permission = isset($permission['63']) ? $permission['63'] : null;

		return view('admin.notification.list', compact('permission'));
	}

	public function notificationListData(Request $request)
	{


		$permission = session('permission') ?? [];
		$permission = isset($permission['63']) ? $permission['63'] : null;

		if ($request->ajax()) {

			$result = Notification::orderBy('id', 'DESC')->get();

			return Datatables::of($result)

				->addIndexColumn()
				->addColumn('created', function ($row) {

					return date('d-m-Y', strtotime($row->created_at));
				})
				->addColumn('status', function ($row) {
					$status = '<td class="center">
				<div class="switch mt-3">
				<label>
				<input type="checkbox" class="-change" data-id="' . $row->id . '"' . ($row->status == 'Active' ? 'checked' : '') . '>
				<span class="lever switch-col-red layout-switch"></span>
				</label>
				</div>
				</td>';
					return $status;
				})
				->addColumn('action', function ($row) use ($permission) {

					$editBtn = '';
					$deleteBtn = '';

					$editUrl = url('admin/notification/update/' . $row->id);
					$deleteUrl = url('admin/notification/delete/' . $row->id);

					if (isset($permission) && $permission['edit'] == 'true') {
						$editBtn = '<a href="' . $editUrl . '" title="Edit Notification" class="btn btn-tbl-edit btn_change"><i class="fas fa-pencil-alt"></i></a>';
					}

					if (isset($permission) && $permission['delete'] == 'true') {
						$deleteBtn = '<a href="' . $deleteUrl . '" title="Delete Notification" onclick="return confirm(\'Are you sure? You want to delete this Notification.\')" class="btn btn-tbl-delete btn_change"><i class="fas fa-trash"></i></a>';
					}

					return $editBtn . ' ' . $deleteBtn;
				})
				->rawColumns(['status', 'action'])
				->make(true);
		} else {

			return view('admin.notification.list');
		}
	}

	public function changeStatus(Request $request)
	{

		Notification::where('id', $request->post('id'))->update(['status' => $request->post('status')]);

		return response(array('message' => 'Notification status changed successfully.'), 200);
	}

	public function updateNotification(Request $request, $id)
	{

		$result = Notification::find($id);

		if ($result) {

			return view('admin.notification.add', compact('result'));
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}

	public function deleteNotification(Request $request, $id)
	{

		$result = Notification::find($id);

		if ($result) {

			Notification::where('id', $id)->delete();

			return redirect()->back()->with('5fernsadminsuccess', 'Notification deleted successfully.');
		} else {

			return redirect()->back()->with('5fernsadminerror', 'Something went wrong. Please try again.');
		}
	}
}
