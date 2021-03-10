<?php
//GoogleAPIライブラリを読み込む
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/../api_key.php';
//const API_KEY = "AIzaSyC1X3EFMgvz8qTDwmMLRgAKLFu6S9hC7Q0";
define('API_KEY', getApiKey('Youtube_api'));

//動画を取得する.
function searchVideos($parts) 
{
    $client = new Google_Client();
    $client->setDeveloperKey(API_KEY);
    $youtube = new Google_Service_YouTube($client);

    //qに好きなYouTubeのキーワードを入れる
    $params['q'] = '山本義徳 ' . $parts;
    //$params['channelTitle'] => '山本義徳 筋トレ大学';
    $params['type'] = 'video';
    $num = 5;
    $params['maxResults'] = $num;
    $params['order'] = 'relevance';

    try {
        $searchResponse = $youtube->search->listSearch('id,snippet', $params);

    } catch (Google_Service_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    } catch (Google_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    }

    foreach ($searchResponse['items'] as $search_result) {
        $videos[] = $search_result;
    }
        
    $video_info = array();
    for ($i = 0; $i < $num; $i++) {
        $video_info[$i] = 'https://www.youtube.com/watch?v=' . $videos[$i]['id']['videoId'];
    }

    //タイトル＋URL+サムネURLのまとめ
    $video_info = array();
    for ($i = 0; $i < $num; $i++) {
        $video_info[$i]['title'] = $videos[$i]['snippet']['title'];
        $video_info[$i]['url'] = 'https://www.youtube.com/watch?v=' . $videos[$i]['id']['videoId'];
        $video_info[$i]['img_url'] = $videos[$i]['snippet']['thumbnails']['high']['url'];
    }

    $messages = array();
    for ($i = 0; $i < $num; $i++) {
        $messages += array($i => array('type' => 'text', 'text' => $video_info[$i]));
    }

    //debug
    //echo "<pre>";
    //print_r($messages);

    return $messages;
}


//関数実行　Youtube検索
$parts = 'トレーニング動画';
//searchVideos($parts);


?>
