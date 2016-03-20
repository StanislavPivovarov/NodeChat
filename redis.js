/**
 * Created by stanislav on 19.03.16.
 */

var http = require('http').Server(),
 io = require('socket.io')(http),
 Redis = require('ioredis'),
 redis = new Redis;

redis.subscribe('test_channel');



redis.on('message', function(channel, message){
    message = JSON.parse(message);
    var messageBlock = {
        'user':message.data.username + ' say: ',
        'message':message.data.message
    };
    io.emit(channel + ':' + message.event, messageBlock );
});

http.listen(3000, function(){
    console.log('listening on *:3000');
});
