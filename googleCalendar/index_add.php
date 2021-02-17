<?php
/*
 * 共通の記述
 */
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
$client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);

// ユーザーアカウントのjsonを指定
$client->setAuthConfig($aimJsonPath);

// サービスオブジェクトの用意
$service = new Google_Service_Calendar($client);
/*
 * 予定の取得
 */
// カレンダーID
$calendarId = 'goaisft8fq1a4t9d1pbi565sq0@group.calendar.google.com';

// 取得時の詳細設定
$optParams = array(
    'maxResults' => 10,
    'orderBy' => 'startTime',
    'singleEvents' => true,
    //'timeMin' => date('c',strtotime("2019-01-01")),//2019年1月1日以降の予定を取得対象
    'timeMin' => date('c',strtotime("2021-01-20")),//2019年1月1日以降の予定を取得対象
    'timeMax' => date('c',strtotime("2021-01-21")),//2019年1月1日以降の予定を取得対象
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

echo "<pre>";
//print_r($optParams);
$i = '';
foreach ($events as $event) {
    $i++;
    $start = $event->summary;
    //echo $start;
    echo "<br>";
}
    echo $i;
//print_r($events);
//die();
//



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>予定の取得サンプル</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
</head>
<body>
<section class="section">
    <div class="container">
    <h1 class="title">予定の追加</h1>
        <p>『<a href="<?php echo $event->htmlLink; ?>" target="_blank"><?php echo $event->summary; ?></a>』の予定を追加しました。</p>
        <h1 class="title">私のスケジュール</h1>
        <?php if (empty($events)): ?>
            <p>イベントが見つかりません</p>
        <?php else: ?>
            <h2 class="title is-4">今後のイベント</h2>
            <?php foreach ($events as $event): ?>
                <?php
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                $end = $event->end->dateTime;
                if (empty($end)) {
                    $end = $event->end->date;
                }
                ?>
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title"><?= $event->getSummary(); ?></p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p><?= $event->getDescription(); ?></p>
                            <p>
                                開始: <?= date('Y/m/d h:i:s',strtotime($start)); ?><br>
                                終了: <?= date('Y/m/d h:i:s',strtotime($end)); ?>
                            </p>
                            <p><a href="<?= $event->htmlLink; ?>" target="_blank" class="button">詳細へ</a></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
