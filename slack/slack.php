<?php
function sendMessage($message) {
    $url = 'https://hooks.slack.com/services/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';
    $message = [
        "channel" => "slackapi",
        "username" => "System",
        "icon_emoji" => ":eyes:",
        "text" => $message,
    ];

    $ch = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'payload' => json_encode($message)
        ])
    ];
    curl_setopt_array($ch, $options);
    curl_exec($ch);
    curl_close($ch);
}

//$message = 'test';
//sendMessage($message);

?>
