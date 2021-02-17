<?php
require('vendor/autoload.php');
//require('../googleCalendar/index.php');

use GuzzleHttp\Client;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Builder;

const BASE_URI = 'https://api.zoom.us/v2/';

function createJwtToken()
{
    $api_key = 'xxxxxx';
    $api_secret = 'xxxxxx';
    $signer = new Sha256;
    $key = new Key($api_secret);
    $time = time();
    $jwt_token = (new Builder())->setIssuer($api_key)
                            ->expiresAt($time + 3600)
                            ->sign($signer, $key)
                            ->getToken();
    return $jwt_token;
}

function getUserId() 
{
    $method = 'GET';
    $path = 'users';
    $client_params = [
      'base_uri' => BASE_URI,
    ];
    $result = sendRequest($method, $path, $client_params);
    $user_id = $result['users'][0]['id'];
    return $user_id;
}

function createMeeting($datas) 
{
    $user_id = getUserId();
    if ($datas['type'] == 2) {
        $start_time = $datas['datetime'] . ':00JST';
        $params = [
            'topic' => '予約会議',
            'type' => 2,
            'time_zone' => 'Asia/Tokyo',
            'start_time' => $start_time,
            'agenda' => '予約会議',
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'approval_type' => 0,
                'audio' => 'both',
                'enforce_login' => false,
                'waiting_room' => true,
                'registrants_email_notification' => false
            ]
        ];
    } else {
        $params = [
            'topic' => '会議',
            'type' => 1,
            'time_zone' => 'Asia/Tokyo',
            'agenda' => '会議',
            'settings' => [
                'host_video' => false,
                'participant_video' => false,
                'approval_type' => 0,
                'audio' => 'both',
                'enforce_login' => false,
                'waiting_room' => true,
                'registrants_email_notification' => false
            ]
        ];
    }
    $method = 'POST';
    $path = 'users/'. $user_id .'/meetings';
    $client_params = [
      'base_uri' => BASE_URI,
      'json' => $params
    ];
    $result = sendRequest($method, $path, $client_params);
    //Zoom会議の予約時にカレンダーにも追加
    if ($result['type'] == 2 ) {
        $result_calender = insertCalendar($result);
        $result['htmlLink'] = $result_calender['htmlLink'];
        $result['htmlLink'] = $result['htmlLink'] . '&openExternalBrowser=1';
    } else {
        $result_calender = '';
    }
    //print_r($result);
    //print_r($result_calender);
    return $result;
}

function sendRequest($method, $path, $client_params)
{
    $client = new Client($client_params);
    $jwt_token = createJwtToken();
    $response = $client->request($method, 
                    $path, 
                    [
                      'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $jwt_token,
                      ]
                    ]);
    $result_json = $response->getBody()->getContents();
    $result = json_decode($result_json, true);
    $result['start_url'] = $result['start_url'] . '&openExternalBrowser=1';
    $result['join_url'] = $result['join_url'] . '&openExternalBrowser=1';
    return $result;
}
/*
echo "<pre>";
$datas['type'] = 2;
$datas['datetime'] = '2021-01-27T21:00';
$meeting = createMeeting($datas);
//print_r($meeting);
*/

?>



