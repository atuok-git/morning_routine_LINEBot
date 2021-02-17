<?php
echo "<pre>";
weatherDate();

//関数
function test($aaa) {
    $bbb = 'kouta';
    return $bbb . $aaa;
}

function nowDate() {
    return date("Y/m/d H:i:s");
}

function weatherDate() {
    /*
    $weather_config1 = array(
        'appid' => 'd614a8c7432bb40b336d87097e3c4f4e',
        'lat' => '35.6895014',
        'lon' => '139.6917337',
    );
    $weather_json1 = file_get_contents('http://api.openweathermap.org/data/2.5/weather?lat=' . $weather_config1['lat'] . '&lon=' . $weather_config1['lon'] . '&units=metric&lang=ja&APPID=' . $weather_config1['appid']);
    $weather_array1 = json_decode($weather_json1, true);
    print_r($weather_array1);
    echo "<br>";
     */

    $weather_config2 = array(
        'appid' => 'd614a8c7432bb40b336d87097e3c4f4e',
        'lat' => '35.6895014',
        'lon' => '139.6917337',
    );
    $weather_json2 = file_get_contents('http://api.openweathermap.org/data/2.5/forecast?lat=' . $weather_config2['lat'] . '&lon=' . $weather_config2['lon'] . '&units=metric&lang=ja&APPID=' . $weather_config2['appid']);
    $weather_array2 = json_decode($weather_json2, true);
    //print_r($weather_array2);
    return $weather_array2;
}

//weatherDate();

function slackMessage() {
    $headers = [
        'Authorization: Bearer xoxb-893804149216-1649165679911-OOaCfNzYtKNukJQMt2t9AxRB', //（1)
        'Content-Type: application/json;charset=utf-8'
    ];

    $url = "https://slack.com/api/chat.postMessage"; //(2)

    //(3)
    $post_fields = [
        "channel" => '目標達成コミュニティ',
        "text" => "初めてのSlack Web APIからのメッセージ",
        "as_user" => true
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($post_fields)
    ];

    $ch = curl_init();

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);
    print_r($result);

    curl_close($ch);
}

//slackMessage();


?>
