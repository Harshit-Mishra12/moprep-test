<?php

namespace App\Helpers;

use Ixudra\Curl\Facades\Curl;
use Session;
use DB;
use Google\Auth\OAuth2;
use Illuminate\Support\Facades\Cache;

class commonHelper
{

    public static function callAPI($method, $url, $data = array(), $files = array())
    {


        $url = env('APP_URL') . '/api' . $url;

        if ($method == 'GET') {

            return $response = Curl::to($url)
                ->returnResponseObject()
                ->get();
        } elseif ($method == 'PUT') {

            return $response = Curl::to($url)

                ->withData(['title' => 'Test', 'body' => 'body goes here', 'userId' => 1])
                ->returnResponseObject()
                ->put();
        } elseif ($method == 'DELETE') {

            return $response = Curl::to($url)

                ->delete();
        } elseif ($method == 'patch') {

            return $response = Curl::to($url)

                ->withData(['title' => 'Test', 'body' => 'body goes here', 'userId' => 1])
                ->returnResponseObject()
                ->patch();
        } elseif ($method == 'POST') {

            return $response = Curl::to($url)
                ->withData($data)
                ->returnResponseObject()
                ->post();
        } elseif ($method == 'POSTFILE') {

            return $response = Curl::to($url)
                ->withData($data)
                ->withFile($files['file_input'], $files['image_file'], $files['getMimeType'], $files['getClientOriginalName'])
                ->post();
        } elseif ($method == 'userTokenpost') {

            return $response = Curl::to($url)
                ->withData($data)
                ->withBearer(Session::get('5ferns_user'))
                ->returnResponseObject()
                ->post();
        } elseif ($method == 'userTokenget') {
            return $response = Curl::to($url)
                ->withBearer(Session::get('5ferns_user'))
                ->returnResponseObject()
                ->get();
        }
    }

    public static function buildMenu($parent, $menu, $sub = NULL)
    {

        $permission = session('permission') ?? []; // Ensure permissions are always an array

        $html = "";

        if (isset($menu['parents'][$parent])) {
            if (!empty($sub)) {
                $html .= "<ul id=" . $sub . " class='ml-menu'><li class=\"ml-menu\">" . $sub . "</li>\n";
            } else {
                $html .= "<ul class='list'>\n";
            }

            // dd($menu);

            foreach ($menu['parents'][$parent] as $itemId) {

                $menuDetails = $permission[$itemId] ?? [];


                $active = (request()->is($menu['items'][$itemId]['active_link'])) ? 'active' : '';

                $terget = null;
                if (!isset($menu['parents'][$itemId])) { //if condition is false only view menu
                    if ((!empty($menuDetails) && $menuDetails['read'] == 'true') || $itemId != '13') {
                        $html .= "<li class='" . $active . "' >\n  <a $terget title='" . $menu['items'][$itemId]['label'] . "' href='" . url($menu['items'][$itemId]['link']) . "'>\n <em class='" . $menu['items'][$itemId]['icon'] . " fa-fw'></em><span>" . $menu['items'][$itemId]['label'] . "</span></a>\n</li> \n";
                    }
                }

                if (isset($menu['parents'][$itemId])) { //if condition is true show with submenu

                    $menuDetails =  $permission[$itemId] ?? [];
                    // dd($permission);

                    if (isset($menuDetails) && $menuDetails['read'] == 'true') {
                        $html .= "<li class='" . $active . "'>\n  <a onclick='return false;' class='menu-toggle' href='#" . $menu['items'][$itemId]['label'] . "'> <em class='" . $menu['items'][$itemId]['icon'] . " fa-fw'></em><span>" . $menu['items'][$itemId]['label'] . "</span></a>\n";
                        $html .= self::buildMenu($itemId, $menu, $menu['items'][$itemId]['label']);
                        $html .= "</li> \n";
                    }
                }
            }
            $html .= "</ul> \n";
        }

        return $html;
    }

    public static function getSidebarMenu()
    {

        if (Session::has('fivefernsadminmenu')) {

            $result = Session::get('fivefernsadminmenu');

            $menu = array(
                'items' => array(),
                'parents' => array()
            );

            foreach ($result as $v_menu) {
                $menu['items'][$v_menu['menu_id']] = $v_menu;
                $menu['parents'][$v_menu['parent']][] = $v_menu['menu_id'];
            }

            return  \App\Helpers\commonHelper::buildMenu(0, $menu);
        }
    }

    public static function getOtp()
    {

        $otp = '1111';
        if (env('APP_ENV') == 'Production') {
            $otp = mt_rand(1000, 9999);
        }

        return $otp;
    }

    public static function sendMsg($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }

    public static function emailSendToUser($data)
    {

        $to = $data['email'];
        $subject = $data['subject'];

        \Mail::send('email_templates.' . $data['template'],  ['data' => $data], function ($message) use ($to, $subject) {
            $message->from(env('MAIL_USERNAME'), str_replace('_', ' ', env('MAIL_FROM_NAME')));
            $message->subject($subject);
            $message->to($to);
        });
    }


    public static function getCourseName($id)
    {

        $result = \App\Models\Course::where('id', $id)->first();

        if ($result) {
            return ucfirst($result->name);
        } else {
            return 'N/A';
        }
    }

    public static function getBatchName($id)
    {

        $result = \App\Models\Batch::where('id', $id)->first();

        if ($result) {
            return ucfirst($result->name);
        } else {
            return 'N/A';
        }
    }

    public static function getChapterName($id)
    {

        $result = \App\Models\Chapter::where('id', $id)->first();

        if ($result) {
            return ucfirst($result->name);
        } else {
            return 'N/A';
        }
    }

    public static function getTopicName($id)
    {

        $result = \App\Models\TopicMaterials::where('id', $id)->first();

        if ($result) {
            return ucfirst($result->topic);
        } else {
            return 'N/A';
        }
    }

    public static function getQuestionName($id)
    {

        $result = DB::table('bulk_questions')->where('id', $id)->first();

        if ($result) {
            return $result->question;
        } else {
            return 'N/A';
        }
    }

    public static function uploadFile($file, $folder)
    {
        $filename = strtotime(date('Y-m-d H:i:s')) . rand(11, 99) . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('/uploads/' . $folder), $filename);

        // Check if the uploaded file is an image (jpg, jpeg, png) or a PDF
        $ext = $file->getClientOriginalExtension();
        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $isImage = in_array($ext, $imageExtensions);

        // Convert image to webp or handle PDF differently
        $dir = public_path('/uploads/' . $folder . '/');

        if ($isImage) {
            $img = imagecreatefromstring(file_get_contents($dir . $filename));
            unlink($dir . $filename);
            $filename = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $filename);
            imagewebp($img, $dir . $filename, 50);
        } elseif ($ext == 'pdf') {
            // Handle PDF differently, if needed
        }

        return $filename;
    }


    function sendNotification($title, $description)
    {
        // Firebase server key
        $serverKey = 'YOUR_SERVER_KEY';

        // FCM endpoint
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        // Notification details
        $notification = [
            'title' => $title,
            'body'  => $description,
            'sound' => 'default',
        ];

        // Data payload
        // $data = [
        // 	'key1' => 'value1',
        // 	'key2' => 'value2',
        // ];

        // Topic to send notification to
        $topic = '/topics/all'; // Ensure all clients are subscribed to this topic

        $fcmNotification = [
            'to'        => $topic,
            'notification' => $notification,
            //'data' => $data,
        ];

        // cURL request
        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public static function getAccessToken()
    {
        $cachedToken = Cache::get('firebase_access_token');
        if ($cachedToken) {
            return $cachedToken;
        }

        $credentialsPath = storage_path('app/firebase-service-account.json'); // Path to JSON

        $oauth = new OAuth2([
            'audience' => 'https://oauth2.googleapis.com/token',
            'issuer' => json_decode(file_get_contents($credentialsPath), true)['client_email'],
            'signingAlgorithm' => 'RS256',
            'signingKey' => file_get_contents($credentialsPath),
            'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
            'scope' => ['https://www.googleapis.com/auth/firebase.messaging'],
        ]);

        $token = $oauth->fetchAuthToken();
        Cache::put('firebase_access_token', $token['access_token'], now()->addMinutes(50));
        return $token['access_token'];
    }

    public static function getCourseMasterName($id)
    {

        $result = \App\Models\CourseMaster::where('id', $id)->first();

        if ($result) {
            return ucfirst($result->name);
        } else {
            return 'N/A';
        }
    }
}
