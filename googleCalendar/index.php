<?php
//取得機能・追加機能どちらにも共通
function calendarParams() {
    // composerでインストールしたライブラリを読み込む
    require_once __DIR__.'/vendor/autoload.php';
    // サービスアカウント作成時にダウンロードしたjsonファイル
    $aimJsonPath = __DIR__ . '/key/eco-layout-295906-2970f5908411.json';

    // サービスオブジェクトを作成
    $client = new Google_Client();

    // このアプリケーション名
    $client->setApplicationName('カレンダー操作テスト イベントの取得');

    // ※ 注意ポイント: 権限の指定
    // 予定を取得する時は Google_Service_Calendar::CALENDAR_READONLY
    // 予定を追加する時は Google_Service_Calendar::CALENDAR_EVENTS
    $client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);

    // ユーザーアカウントのjsonを指定
    $client->setAuthConfig($aimJsonPath);

    // サービスオブジェクトの用意
    $service = new Google_Service_Calendar($client);
    return $service;
}

function getCalendar($params) {
    //共通の設定の呼び出し
    $service = calendarParams();
    /*
     * 予定の取得
     */
    // カレンダーID
    require_once dirname(__FILE__) . '/../api_key.php';
    $calendar_id = getApiKey('calendar_id');

    // 取得時の詳細設定
 
    if ($params['type'] == 'anotherday') {
        //$params['datetime'] = '2021-01-28T00:24';
        $time_first = substr($params['datetime'], 0, -5);
        $time_first = $time_first . '00:00:00';
        //予定開始時間計算
        $time_first = (date('c',strtotime($time_first)));
        //予定開始unix時間計算
        $start_unixtime = strtotime($time_first);
        //予定終了unix時間計算
        $end_unixtime = $start_unixtime + 86400;
        //予定終了時間計算
        $time_end = date('c', $end_unixtime);

        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $time_first,
            'timeMax' => $time_end,
        );
    } else {
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c',strtotime("today")),//2019年1月1日以降の予定を取得対象
            'timeMax' => date('c',strtotime("tomorrow")),//2019年1月1日以降の予定を取得対象
        );
    }
    $results = $service->events->listEvents($calendar_id, $optParams);
    $events = $results->getItems();
    return $events;
}
/*
$params['type'] = 'anotherday';
$result = getCalendar($params);
echo "<pre>";
print_r($result);
print_r(date('c',strtotime("today")));
echo "<br>";
 */

//取得実行関数
function getCalendarAjust($params) {
    $events = getCalendar($params);
    $i = '';
    foreach ($events as $event) {
        $i++;
    }
    $task = array();
    $a = 0;
    foreach ($events as $event) {
        $summary = $event->summary;
        if (empty($summary)) {
            $summary = $event->summary;
        }
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        $end = $event->end->dateTime;
        if (empty($end)) {
            $end = $event->end->date;
        }
        $url = $event->htmlLink;
        if (empty($url)) {
            $url = $event->htmlLink;
        }
        $task += array($a => [
            'title' => $summary,
            'start' => $start,
            'end' => $end,
            'url' => $url . '&openExternalBrowser=1',
        ]);
        $a++;
    }
    //echo "<pre>";
    //print_r($task);
/*
    $messages = array();
    for ($i = 0; $i < 4; $i++) { 
        $messages += array($i => array('type' => 'text', 'text' => $task[$i]['title'] . "\n" . '開始時間：' . $task[$i]['start'] . "\n" . '終了時間：' . $task[$i]['end'] . "\n" . $task[$i]['url']));
    }
*/
    //print_r($messages);
    return $task;
}
//getCalendarAjust();

function insertCalendar($params) {
    //共通の設定の呼び出し
    $service = calendarParams();
    /*
     * 予定の追加
     */
    // カレンダーID
    require_once dirname(__FILE__) . '/../api_key.php';
    $calendar_id = getApiKey('calendar_id');

    if ($params['type'] == 2) {
        //zoom追加時の処理テスト
        //予定開始時間計算用
        $zoom_start_unixtime = strtotime($params['start_time']);
        //予定終了時間計算
        $zoom_end_unixtime = $zoom_start_unixtime + 3600;
        $end_time = date('c', $zoom_end_unixtime);
        //カレンダーに書き込むバラメータ決定
        //予定タイトル決定
        $params['title'] = $params['topic'];
        $params['start'] =  date('c', strtotime($params['start_time']));
        $params['end'] = $end_time;
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $params['title'], //予定のタイトル
            'start' => array(
                'dateTime' => $params['start'], // 開始日時
                'timeZone' => 'Asia/Tokyo',
            ),
            'end' => array(
                'dateTime' => $params['end'], // 終了日時
                'timeZone' => 'Asia/Tokyo',
            ),
            'description' => $params['settings']['contact_name'] . 'さんがあなたを予約されたZoomミーティングに招待しています。' . "\n" . 'Zoomミーティングに参加する' . "\n" . $params['join_url'] . "\n" . 'ミーティングID: ' . $params['id'] . "\n" . 'パスコード: ' . $params['password'],
            'location' => $params['join_url'],
        ));
    } else {
    }
    $event = $service->events->insert($calendar_id, $event);
    return $event;
}
/*
$params = array(
    'title' => 'aaa',
    'start' => '2021-01-28T11:00:00+09:00',
    //'end' => '2021-01-27T15:00:00+09:00',
);
print_r($params);
//追加実行関数
insertCalendar($params);
*/

?>
