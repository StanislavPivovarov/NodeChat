<?php

require "vendor/predis/predis/autoload.php";
Predis\Autoloader::register();

$redis = new Predis\Client();

$redis->set('test','value');

if($_POST)
{
    $haystack = $_POST['message'];
    $needle = '/';
    $pos = strripos($haystack, $needle);
    $message = substr($haystack, $pos + 1);
    $sender = substr($haystack, 0, $pos);
    $data = [
        'event' => 'User SignIn',
        'data'  => [
            'username' => $sender ? $sender : 'Admin',
            'message'  => $message
        ]
    ];
    $redis->publish('test_channel', json_encode($data));

    return false;
}

echo <<<HTML
<!doctype html>
<html>
<head>
    <title>Socket.IO chat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font: 13px Helvetica, Arial; }
        form { background: #000; padding: 3px; position: fixed; bottom: 0; width: 100%; }
        form input { border: 0; padding: 10px; width: 90%; margin-right: .5%; }
        form button { width: 9%; background: rgb(130, 224, 255); border: none; padding: 10px; }
        #messages { list-style-type: none; margin: 0; padding: 0; }
        #messages li { padding: 5px 10px; }
        #messages li:nth-child(odd) { background: #eee; }
        .sender{ background: red;}
    </style>
</head>
<body>
<ul id="messages"></ul>
<form action="">
    <input id="m" autocomplete="off" /><button>Send</button>
</form>
<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.js"></script>
<script>
    var socket = io('http://localhost:3000');
    $('form').submit(function(){
        $.post('http://localhost/nodeSocket/index.php', {
            'message':$('#m').val()
            });
        return false;
    });
    socket.on('test_channel:User SignIn', function(data){
        $('#messages').append($('<li>').html('<span class="sender">' + data.user + '</span>' + data.message));
    });
</script>
</body>
</html>
HTML;
?>
