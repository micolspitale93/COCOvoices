$(document).ready(function () {
    window.face = $("#Face_lamb");
    window.mouth = $("#Mouth");
    window.mouth_1 = $("#Ah");
    window.mouth_9 = $("#W-Oo");
    window.mouth_3 = $("#Uh");
    window.mouth_5 = $("#S");
    window.mouth_6 = $("#R");
    window.mouth_7 = $("#Oh");
    window.mouth_2 = $("#L");
    window.mouth_4 = $("#Ee");
    window.mouth_8 = $("#Surprised");
    window.mouth_10 = $("#Smile");

    window.mouse_chosen = window.mouth_1;
    window.mouth_2.hide();
    window.mouth_3.hide();
    window.mouth_4.hide();
    window.mouth_5.hide();
    window.mouth_6.hide();
    window.mouth_7.hide();
    window.mouth_8.hide();
    window.mouth_9.hide();
    window.mouth_10.hide();


    // Set event listeners for user interface widgets

    if (!location.hash.replace('#', '').length) {
        //location.href = location.href.split('#')[0] + '#' + (Math.random() * 100).toString().replace('.', '');
        location.href = location.href.split('#')[0] + '#' + ("micol").toString().replace('.', '')
        location.reload();
    }
    var channel = location.href.replace(/\/|:|#|%|\.|\[|\]/g, '');

    var pub = 'pub-f986077a-73bd-4c28-8e50-2e44076a84e0';
    var sub = 'sub-b8f4c07a-352e-11e2-bb9d-c7df1d04ae4a';

    WebSocket = PUBNUB.ws;

    var websocket = new WebSocket('wss://pubsub.pubnub.com/' + pub + '/' + sub + '/' + channel);

    websocket.onerror = function () {
        //location.reload();
    };

    websocket.onclose = function () {
        //location.reload();
    };

    websocket.push = websocket.send;
    websocket.send = function (data) {
        websocket.push(JSON.stringify(data));
    };

    var peer = new PeerConnection(websocket);

    peer.onUserFound = function (userid) {
        if (document.getElementById(userid)) return;
        var tr = document.createElement('tr');

        var td1 = document.createElement('td');
        var td2 = document.createElement('td');

        td1.innerHTML = userid + ' has camera. Are you interested in video chat?';

        var button = document.createElement('button');
        button.innerHTML = 'Join';
        button.id = userid;
        button.style.float = 'right';
        button.onclick = function () {
            button = this;
            getUserMedia(function (stream) {
                peer.addStream(stream);
                peer.sendParticipationRequest(button.id);
                document.getElementById("alien").style.display = "block";
                document.getElementById("experiment").style.display = "none"
            });

            button.disabled = true;
        };
        td2.appendChild(button);

        tr.appendChild(td1);
        tr.appendChild(td2);
        roomsList.appendChild(tr);
    };

    peer.onStreamAdded = function (e) {
        console.log(e);
        if (e.type == 'local') document.querySelector('#start-broadcasting').disabled = false;
        var video = e.mediaElement;
        video.setAttribute('width', 600);
        video.setAttribute('controls', true);

        videosContainer.insertBefore(video, videosContainer.firstChild);

        video.play();
        //rotateVideo(video);
        scaleVideos();
    };

    peer.onStreamEnded = function (e) {
        var video = e.mediaElement;
        if (video) {
            video.style.opacity = 1;
            rotateVideo(video);
            setTimeout(function () {
                video.parentNode.removeChild(video);
                scaleVideos();
            }, 1000);
        }
    };

    document.querySelector('#start-broadcasting').onclick = function () {
        this.disabled = true;
        setup();
        draw();
        getUserMedia(function (stream) {
            peer.addStream(stream);
            peer.startBroadcasting();

        });
    };

    document.querySelector('#your-name').onchange = function () {
        peer.userid = this.value;
    };

    var videosContainer = document.getElementById('videos-container') || document.body;
    var btnSetupNewRoom = document.getElementById('setup-new-room');
    var roomsList = document.getElementById('rooms-list');

    if (btnSetupNewRoom) btnSetupNewRoom.onclick = setupNewRoomButtonClickHandler;

    function scaleVideos() {
        var videos = document.querySelectorAll('video'),
            length = videos.length,
            video;

        var minus = 130;
        var windowHeight = 700;
        var windowWidth = 600;
        var windowAspectRatio = windowWidth / windowHeight;
        var videoAspectRatio = 4 / 3;
        var blockAspectRatio;
        var tempVideoWidth = 0;
        var maxVideoWidth = 0;

        for (var i = length; i > 0; i--) {
            blockAspectRatio = i * videoAspectRatio / Math.ceil(length / i);
            if (blockAspectRatio <= windowAspectRatio) {
                tempVideoWidth = videoAspectRatio * windowHeight / Math.ceil(length / i);
            } else {
                tempVideoWidth = windowWidth / i;
            }
            if (tempVideoWidth > maxVideoWidth)
                maxVideoWidth = tempVideoWidth;
        }
        for (var i = 0; i < length; i++) {
            video = videos[i];
            if (video)
                video.width = maxVideoWidth - minus;
        }
    }

    window.onresize = scaleVideos;

    // you need to capture getUserMedia yourself!
    function getUserMedia(callback) {
        var hints = {
            audio: true,
            video: true
        };
        /* video:{
             optional: [],
             mandatory: {}
         }*/
        navigator.getUserMedia(hints, function (stream) {
            var video = document.createElement('video');
            video.srcObject = stream;
            video.controls = true;
            video.muted = true;
            peer.onStreamAdded({
                mediaElement: video,
                userid: 'self',
                stream: stream
            });

            callback(stream);
        });
    }
    (function () {
        var uniqueToken = document.getElementById('unique-token');
        if (uniqueToken)
            if (location.hash.length > 2) uniqueToken.parentNode.parentNode.parentNode.innerHTML = '<h2 style="text-align:center;font-size:20px"><a href="' + location.href + '" target="_blank">Share</a></h2>';
            else uniqueToken.innerHTML = uniqueToken.parentNode.parentNode.href = '#' + ("micol").toString(36).toUpperCase().replace(/\./g, '-');
        /*else uniqueToken.innerHTML = uniqueToken.parentNode.parentNode.href = '#' + (Math.random() * new Date().getTime()).toString(36).toUpperCase().replace(/\./g, '-');*/
    })();
});

function sendMessage(fr, vo) {
    console.log("message");
    var message = [];
    message.push(fr);
    message.push(vo);
    sendChannel.send(message);
}

function handleReceiveMessage(event) {
   // console.log("Ricevuto:" + event.data);
    var res = event.data.split(",");
    animation(res[0],res[1]);
}



function animation(f, v) {
    console.log(f, v);
    window.mouse_chosen.attr("transform-origin", "50% 50%");
    window.mouse_chosen.attr("transform", "scale(" + (1 + 1*v) + "," + (1 + 1*v) + ")");
    selectedMouth(f);
}

function selectedMouth(f) {
    console.log("selected frequencies");
    if (f > -1 && f < 26) {
        window.mouse_chosen = window.mouth_1;
        window.mouth_1.show();
        window.mouth_2.hide();
        window.mouth_3.hide();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 25 && f < 51) {
        window.mouse_chosen = window.mouth_2;
        window.mouth_2.show();
        window.mouth_1.hide();
        window.mouth_3.hide();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 50 && f < 76) {
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_3.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
        window.mouse_chosen = window.mouth_3;
    } else if (f > 75 && f < 101) {
        window.mouse_chosen = window.mouth_4;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_4.show();
        window.mouth_3.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 100 && f < 126) {
        window.mouse_chosen = window.mouth_5;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_5.show();
        window.mouth_4.hide();
        window.mouth_3.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 125 && f < 151) {
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_6.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_3.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
        window.mouse_chosen = window.mouth_6;
    } else if (f > 150 && f < 176) {
        window.mouse_chosen = window.mouth_7;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_7.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_3.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 175 && f < 201) {
        window.mouse_chosen = window.mouth_8;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_8.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_3.hide();
        window.mouth_9.hide();
        window.mouth_10.hide();
    } else if (f > 200 && f < 226) {
        window.mouse_chosen = window.mouth_9;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_9.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_3.hide();
        window.mouth_10.hide();
    } else if (f > 225 && f < 256) {
        window.mouse_chosen = window.mouth_10;
        window.mouth_2.hide();
        window.mouth_1.hide();
        window.mouth_10.show();
        window.mouth_4.hide();
        window.mouth_5.hide();
        window.mouth_6.hide();
        window.mouth_7.hide();
        window.mouth_8.hide();
        window.mouth_9.hide();
        window.mouth_3.hide();
    }
}
