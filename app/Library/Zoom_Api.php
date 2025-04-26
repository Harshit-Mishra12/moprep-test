<?php

namespace App\Library;

date_default_timezone_set('Asia/Kolkata'); 

use \Firebase\JWT\JWT;


class Zoom_Api
{
		private $apiKey = 'KzJ2uOmNSWqG3TTEEiClfg';
		private $apiSecret = 'BpxEo5P8sJtpv3C9XFTVk7TAw7TcXeIb';
		private $account_id = 'gO8-JXdiQ-aCE1PBOrtNWA';
		
    	public function createMeeting($data = array())
    	{

			$post_time  = $data['start_date'];
			$start_time = gmdate("Y-m-d\TH:i:s", strtotime($post_time));

			$createMeetingArray = array();
			if (!empty($data['alternative_host_ids'])) {
				if (count($data['alternative_host_ids']) > 1) {
				$alternative_host_ids = implode(",", $data['alternative_host_ids']);
				} else {
				$alternative_host_ids = $data['alternative_host_ids'][0];
				}
			}
			
			$createMeetingArray['topic']      = $data['topic'];
			$createMeetingArray['agenda']     = !empty($data['agenda']) ? $data['agenda'] : "";
			$createMeetingArray['type']       = !empty($data['type']) ? $data['type'] : 2; //Scheduled
			$createMeetingArray['start_time'] = $start_time;
			$createMeetingArray['timezone']   = 'Asia/kolkata';
			$createMeetingArray['default_password']   = !empty($data['default_password']) ? true : false;
			$createMeetingArray['duration']   = !empty($data['duration']) ? $data['duration'] : 60;

			// $createMeetingArray['settings']   = array(
			//     		'join_before_host'  => !empty($data['join_before_host']) ? true : false,
			//     		'host_video'        => !empty($data['option_host_video']) ? true : false,
			//     		'participant_video' => !empty($data['option_participants_video']) ? true : false,
			//     		'mute_upon_entry'   => !empty($data['option_mute_participants']) ? true : false,
			//     		'enforce_login'     => !empty($data['option_enforce_login']) ? true : false,
			//     		'auto_recording'    => !empty($data['option_auto_recording']) ? $data['option_auto_recording'] : "none",
			//     		'alternative_hosts' => isset($alternative_host_ids) ? $alternative_host_ids : ""
			// 	);

			return $this->sendRequest($createMeetingArray);
		}	
	
	//function to send request

    	protected function sendRequest($data){

			$base64Credentials = base64_encode($this->apiKey . ':' . $this->apiSecret);
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://zoom.us/oauth/token',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => 'grant_type=account_credentials&account_id='.$this->account_id,
				CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization:  Basic ' . $base64Credentials,
				),
			));

			$response = curl_exec($curl);
			
			curl_close($curl);

			$userProfile = json_decode($response);
		
			$accessToken = $userProfile->access_token;

		
			$postFields = json_encode($data);

			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.zoom.us/v2/users/satyam.img@gmail.com/meetings',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>$postFields,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Bearer '. $accessToken,
			),
			));

			$responseData = curl_exec($curl);
			

			curl_close($curl);
			return json_decode($responseData);
		}   
	
		public function deleteMeeting($meetingID){

			$base64Credentials = base64_encode($this->apiKey . ':' . $this->apiSecret);
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://zoom.us/oauth/token',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => 'grant_type=account_credentials&account_id=gO8-JXdiQ-aCE1PBOrtNWA',
					CURLOPT_HTTPHEADER => array(
					'Content-Type: application/x-www-form-urlencoded',
					'Authorization:  Basic ' . $base64Credentials,
					),
				));
		
				$response = curl_exec($curl);
				
				curl_close($curl);
		
				$userProfile = json_decode($response);
			
				$accessToken = $userProfile->access_token;

				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.zoom.us/v2/meetings/' . $meetingID . '?schedule_for_reminder=true&cancel_meeting_reminder=true',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'DELETE',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer ' . $accessToken,
					),
				));

				$response = curl_exec($curl);
				curl_close($curl);
				
			return json_decode($response);

		}

}

?>