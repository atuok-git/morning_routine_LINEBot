<?php
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL );

require_once (dirname(__FILE__) . '/vendor/autoload.php');
 
echo "<pre>";
class TestYouTube
{
    const API_KEY = "AIzaSyC1X3EFMgvz8qTDwmMLRgAKLFu6S9hC7Q0";
    public $youtube;
 
    public function __construct()
    {
        $this->youtube = new Google_Service_YouTube($this->getClient());
    }
 
    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName("youtubeTestApp");
        $client->setDeveloperKey(self::API_KEY);
        return $client;
    }
 
    public function getTop10()
    {
        $part = [
            'snippet',
            'statistics'
        ];
        $params = [
            'chart' => 'mostPopular',
            'maxResults' => 10,
            'regionCode' => 'JP',
        ];
        $search_results = $this->youtube->videos->listVideos($part, $params);
        $videos = [];
        foreach ($search_results['items'] as $search_result) {
            $videos[] = $search_result;
        }
        return $videos;
    }
 
}
 
$test_youtube = new TestYouTube();
$videos = $test_youtube->getTop10();
 
$i = 1;
foreach ($videos as $video) {
    $view_count = number_format($video['statistics']['viewCount']);
    $text = <<<TEXT
    {$i} 位
    タイトル : {$video['snippet']['title']}
    再生回数 : {$view_count} 回
    リンク   : https://www.youtube.com/watch?v={$video['id']}
    TEXT;
 
    echo $text . PHP_EOL . PHP_EOL;
    $i++;
}
?>
