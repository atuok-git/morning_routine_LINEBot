<?php
session_start();

require_once('./LINEBotTiny.php');
require_once('./function.php');
require_once('./message.php');
//require_once('../../youtube/search.php');

//print_r(searchVideos("腹筋"));
//print_r(message());
//$test = message();

 /**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */



$channelAccessToken = 'xxxxxxx';
$channelSecret = 'xxxxxxx';

//共通部分の関数化
function replyMessage($client, $reply_token, $messages) {
    return $client->replyMessage([
        'replyToken' => $reply_token,
        'messages' => $messages,
    ]);
}

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            //メッセージタイプがテキストの場合
            if ($message['type'] === 'text') {
                //メッセージのオウムがえし
                if ($message['text'] === '今日の予定') {
                    $params['type'] = 'today';
                    $messages = messageGetPlan($params);
                } elseif ($message['text'] === '会議') {
                    $messages = messageZoomSelect();
                } elseif ($message['text'] === '今すぐ開始') {
                    $messages = messageZoomNow();
                } elseif ($message['text'] === '肩トレ動画') {
                    $parts = '肩';
                    $messages = messageCarouselTemplate($parts);
                } elseif ($message['text'] === '腕トレ動画') {
                    $parts = '腕';
                    $messages = messageCarouselTemplate($parts);
                } elseif ($message['text'] === '胸トレ動画') {
                    $parts = '胸';
                    $messages = messageCarouselTemplate($parts);
                } elseif ($message['text'] === '背中トレ動画') {
                    $parts = '背中';
                    $messages = messageCarouselTemplate($parts);
                } elseif ($message['text'] === '脚トレ動画') {
                    $parts = '脚';
                    $messages = messageCarouselTemplate($parts);
                } elseif ($message['text'] === '全身動画') {
                    $parts = '全身';
                    $messages = messageCarouselTemplate($parts);
                } elseif (strpos($message['text'], '天気') !== false) {
                    $messages = messageWeatherDate();
                } elseif (strpos($message['text'], 'おはよう') !== false) {
                    $messages = messageTimeDate();
                } elseif (strpos($message['text'], '筋トレ') !== false) {
                    $messages = messageMuscle();
                } elseif (strpos($message['text'], '動画') !== false) {
                    //$messages = messageMuscle();
                    //unset($_SESSION['flag']);
                    //$messages = searchVideos($message['text']);
                } elseif (strpos($message['text'], '予定') !== false) {
                    $messages = messageConfirmPlan();
                } elseif (strpos($message['text'], 'test') !== false) {
                    $messages = messageCarouselTemplate();
                } else {
                    $messages = [
                        [
                            'type' => 'text',
                            'text' => $message['text'],
                        ]
                    ];
                }
                error_log(print_r($event, true) . "\n", 3, "./error.log");
                replyMessage($client, $event['replyToken'], $messages);
            //メッセージが位置情報の場合
            } elseif ($message['type'] === 'location') {
                //緯度経度取得
                $lat = $message['latitude'];
                $long = $message['longitude'];
                //HP API URL
                $url = "https://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=1f344d8b367e58b8&lat=" . $lat . "&lng=" . $long . "&range=5&format=json";
                //cURLセッションを初期化
                $ch = curl_init();
                //オプション設定
                curl_setopt($ch, CURLOPT_URL, $url); // 取得するURLを指定
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 実行結果を文字列で返す
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // サーバー証明書の検証を行わない
                // URLの情報を取得
                $response =  curl_exec($ch);
                // 取得結果を表示
                $result = json_decode($response, true);
                //データをトリミング
                $shop = $result['results']['shop'];
                //店舗取得数
                $shop_count = count($shop);
                //店舗名取得
                $shops = array_column($shop, 'name');
                //店舗URL取得
                $urls = array_column($shop, 'urls');

                //店舗名＋URLのまとめ
                $shopinfo = array();
                for ($i = 0; $i < $shop_count; $i++) {
                    $shopinfo += array($i => $shops[$i] . "\n" . $urls[$i]['pc'] . "\n");
                }

                //返信MAXは5件のため、条件ぎめ
                if ($shop_count > 5) {
                    $shop_count = 5;
                }

                //返信メッセージ作成
                $messages = array();
                for ($i = 0; $i < $shop_count; $i++) {
                    $messages += array($i => array('type' => 'text', 'text' => $shopinfo[$i]));
                }
                //返信実行
                replyMessage($client, $event['replyToken'], $messages);
                //debug
                error_log(print_r($event, true) . "\n", 3, "./error.log");

                // セッションを終了
                curl_close($ch);
            } else {
                $messages = [
                    [
                        'type' => 'text',
                        'text' => 'うまく動作しません。' . "\n" . '(๑ ⁼̴̩̩̀ᐜ⁼̴́๑)ｶﾀｼﾞｹﾅｲ'
                    ]
                ];
                error_log(print_r($event, true) . "\n", 3, "./error.log");
                replyMessage($client, $event['replyToken'], $messages);
            }
            break;
        case 'postback':
            $data = $event['postback'];
            //メッセージタイプが日時選択の場合
            //Zoom予約の時
            if ($data['data'] === 'dataId=12345') {
                //開始時間を追加
                $datetime = $data['params']['datetime'];
                $messages = messageAnotherDay($datetime);
            } elseif ($data['data'] === 'dataId=AnotherdayPlan') {
                //開始時間を追加
                $params['datetime'] = $data['params']['datetime'];
                $params['type'] = 'anotherday';
                $messages = messageGetPlan($params);
                /*
                $messages = [
                    [
                        'type' => 'text',
                        'text' => $params['day'] . $params['datetime'] . 'うまく動作しません。' . "\n" . '(๑ ⁼̴̩̩̀ᐜ⁼̴́๑)ｶﾀｼﾞｹﾅｲ'
                    ]
                ];
                 */
            }
            error_log(print_r($event, true) . "\n", 3, "./error.log");
            replyMessage($client, $event['replyToken'], $messages);
            break;
        default:
            error_log(print_r($event, true) . "\n", 3, "./error.log");
            break;
    }
};

?>
