<?php
require_once('./function.php');
require_once('../../youtube/search.php');
require_once('../../zoom/zoom.php');
require_once('../../googleCalendar/index.php');
require_once('../../slack/slack.php');

//今の時間取得
function messageTimeDate() {
    $aaa = 'kouta';
    $messages = [
        [
            'type' => 'text',
            'text' => '今の時間は' . nowDate() . 'です。',
        ],
        [
            'type' => 'text',
            'text' => $aaa,
        ]
    ];
    return $messages;
}

//天気のAPI起動
function messageWeatherDate() {
    $result = weatherDate();
    $time = date('Y/m/d');
    $messages = [
        [
            'type' => 'text',
            'text' => $time . 'の天気は' . "\n"  . '朝は' . $result['list']['0']['weather']['0']['description'] . '、降水確率は' . $result['list']['0']['pop']*100 . '%' . "\n" . '昼頃は' . $result['list']['2']['weather']['0']['description'] . '、降水確率は' . $result['list']['2']['pop']*100 . '%' . "\n" . '夕方は' . $result['list']['4']['weather']['0']['description'] . '、降水確率は' . $result['list']['0']['pop']*100 . '%',
        ]
    ];
    return $messages;
}

//どの日の予定を確認するか選択
function messageConfirmPlan() {
    // 確認ダイアログタイプ
    $now_date = (date( DATE_ATOM ));
    $now_date = substr($now_date, 0, -9);
    $messages = [
        [
            'type' => 'template',
            'altText' => '確認ダイアログ',  // ここの文字列が、LINEの通知に表示されるはず
            'template' => [
                'type' => 'confirm',
                'text' => '選択してください。',
                'actions' => [
                        [
                            'type' => 'message',
                            'label' => '今日の予定',
                            'text' => '今日の予定'
                        ],
                        [
                            'type' => 'datetimepicker',
                            'label' => '別日の予定',
                            'data' => 'dataId=AnotherdayPlan',
                            'mode' => 'datetime',
                            //'initial' => "2021-01-26T16:10",
                            'initial' => $now_date,
                        ],

                ]
            ]
        ]
    ];
    return $messages;
}

//予定取得
function messageGetPlan($params) {
    //GoogleCalendar起動
    $tasks = getCalendarAjust($params);
    $task_count = 0;
    foreach ($tasks as $task) {
        $task_count++;
    }
    if ($task_count > 0) {
        $messages = array();
        for ($i = 0; $i < $task_count; $i++) {
            $messages += array($i => array('type' => 'text', 'text' => '【' . $tasks[$i]['title'] . '】' . "\n" . date('Y年m月d日　H時i分', strtotime($tasks[$i]['start'])) . "\n" . date('Y年m月d日　H時i分', strtotime($tasks[$i]['end'])) . "\n" . $tasks[$i]['url']));
        }
    } else {
        $messages = [
            [
                'type' => 'text',
                'text' => "予定はないです。",
            ]
        ];
    }
    return $messages;
}

function messageMuscle() {
    $messages = [
        [
            'type' => 'text',
            'text' => 'どこの部位を鍛える？',
            'quickReply' => [
                'items' => [
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '肩',
                            'text' => '肩トレ動画',
                        ]
                    ],
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '腕',
                            'text' => '腕トレ動画',
                        ]
                    ],
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '胸',
                            'text' => '胸トレ動画',
                        ]
                    ],
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '背中',
                            'text' => '背中トレ動画',
                        ]
                    ],
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '脚',
                            'text' => '脚トレ動画',
                        ]
                    ],
                    [
                        'type' => 'action',
                        'action' => [
                            'type' => 'message',
                            'label' => '全身',
                            'text' => '全身動画',
                        ]
                    ],
                ]
            ]
        ]
    ];
    return $messages;
}


function messageZoomSelect() {
    // 確認ダイアログタイプ
    $now_date = (date( DATE_ATOM ));
    $now_date = substr($now_date, 0, -9);
    $messages = [
        [
            'type' => 'template',
            'altText' => '確認ダイアログ',  // ここの文字列が、LINEの通知に表示されるはず
            'template' => [
                'type' => 'confirm',
                'text' => '選択してください。',
                'actions' => [
                        [
                            'type' => 'message',
                            'label' => '今すぐ開始',
                            'text' => '今すぐ開始'
                        ],
                        [
                            'type' => 'datetimepicker',
                            'label' => '開催日時設定',
                            'data' => 'dataId=12345',
                            'mode' => 'datetime',
                            //'initial' => "2021-01-26T16:10",
                            'initial' => $now_date,
                        ],
                ]
            ]
        ],
    ];
    return $messages;
}

function messageZoomNow() {
    $type = 1;
    $result = createMeeting($type);
    $messages = [
		[
            'type' => 'template',
            'altText' => 'this is a carousel template',
            'template' => [
                'type' => 'carousel',
                'columns' => [
                    [
                        //'thumbnailImageUrl' => '',
                        'text' => 'Zoomを起動します。',
                        'actions' => [
                            [
                                'type' => 'uri',
                                'label' => 'Zoomに移動',
                                'uri' => $result['start_url'],
                            ],
                        ]
                    ],
                ],
                'imageAspectRatio' => 'rectangle',
                'imageSize' => 'cover'
            ]
        ],
        [
            'type' => 'text',
            'text' => '招待URL' . "\n" . 'このメッセージを転送してください。' . "\n" . $result['join_url'],
        ],
    ]; 
    if (!empty($result)) { 
        $text = $result['settings']['contact_name'] . '様からのZoom招待が届きました。' . "\n" . '今すぐ参加するなら以下リンクからお願いします。' . "\n" . $result['join_url'];
        sendMessage($text);
    }
    return $messages;
}
//messageZoomNow();

function messageAnotherDay($datetime) {
    $datas['type'] = 2;
    $datas['datetime'] = $datetime;
    $result = createMeeting($datas);
    if (!empty($result)) {
        //insertCalendar($params);
        $messages = [
            [
                'type' => 'text',
                'text' => 'Zoom会議を予約しました。' . "\n" . date('Y年m月d日　H時i分', strtotime($datas['datetime'])),
            ],
            [
                'type' => 'text',
                'text' => 'GoogleCalendarに追加しました。' . "\n" . date('Y年m月d日　H時i分', strtotime($datas['datetime'])) . "\n" . $result['htmlLink'],
            ],
        ];
        $text = $result['settings']['contact_name'] . '様からのZoom招待です。' . "\n" . '開始時間は' . date('Y年m月d日H時i分', strtotime($result['start_time'])) . 'です。' . "\n" . $result['join_url'];
        sendMessage($text);
    } else {
        $messages = [
            [
                'type' => 'text',
                'text' => 'うまく動作しません。' . "\n" . '(๑ ⁼̴̩̩̀ᐜ⁼̴́๑)ｶﾀｼﾞｹﾅｲ'
            ],
        ];
    }
    return $messages;
}
//$datetime = '2021-01-29T20:00';
//messageAnotherDay($datetime);
//$result['start_time'] = '2021-01-30T09:30:00Z';
//$aaa =  date('Y年m月d日　H時i分', strtotime($result['start_time']));
//echo $aaa;


function messageCarouselTemplate($parts) {
    $result = searchVideos($parts);
	$messages = [
		[
            'type' => 'template',
            'altText' => 'this is a carousel template',
            'template' => [
                'type' => 'carousel',
                'columns' => [
                    [
                        'thumbnailImageUrl' => $result['0']['text']['img_url'],
                        'text' => $result['0']['text']['title'],
                        'defaultAction' => [
                              'type' => 'uri',
                              'label' => '動画を見に行く',
                              'uri' => $result['0']['text']['url'],

                        ],
                        'actions' => [
                            [
                                'type' => 'uri',
                                'label' => '動画を見に行く',
                                'uri' => $result['0']['text']['url'],
                            ],
                        ]
                    ],
                    [
                        'thumbnailImageUrl' => $result['1']['text']['img_url'],
                        'text' => $result['1']['text']['title'],
                        'defaultAction' => [
                              'type' => 'uri',
                              'label' => '動画を見に行く',
                              'uri' => $result['1']['text']['url'],
                        ],
                        'actions' => [
                            [
                                'type' => 'uri',
                                'label' => '動画を見に行く',
                                'uri' => $result['1']['text']['url'],
                            ]
                        ]
                    ],
                    [
                        'thumbnailImageUrl' => $result['2']['text']['img_url'],
                        'text' => $result['2']['text']['title'],
                        'defaultAction' => [
                              'type' => 'uri',
                              'label' => '動画を見に行く',
                              'uri' => $result['2']['text']['url'],
                        ],
                        'actions' => [
                            [
                                'type' => 'uri',
                                'label' => '動画を見に行く',
                                'uri' => $result['2']['text']['url'],
                            ]
                        ]
                    ],
                ],
                'imageAspectRatio' => 'rectangle',
                'imageSize' => 'cover'
            ]
        ],
    ];
    return $messages;
}

?>
