// Muaz Khan     - https://github.com/muaz-khan
// MIT License   - https://www.webrtc-experiment.com/licence/
// Documentation - https://github.com/muaz-khan/WebRTC-Experiment/tree/master/websocket
var sendChannel = {};
sendChannel.readyState = "";
var mic, ftt;

(function () {

    window.PeerConnection = function (socketURL, userid) {
        this.userid = userid || getToken();
        this.peers = {};

        if (!socketURL) throw 'Socket-URL is mandatory.';

        new Signaler(this, socketURL);

        this.addStream = function (stream) {
            this.MediaStream = stream;
        };
    };

    function Signaler(root, socketURL) {
        var self = this;

        root.startBroadcasting = function () {
            if (!root.MediaStream) throw 'Offerer must have media stream.';

            (function transmit() {
                socket.send({
                    userid: root.userid,
                    broadcasting: true
                });
                !self.participantFound &&
                    !self.stopBroadcasting &&
                    setTimeout(transmit, 3000);
            })();
        };

        root.sendParticipationRequest = function (userid) {
            socket.send({
                participationRequest: true,
                userid: root.userid,
                to: userid
            });
        };

        // if someone shared SDP
        this.onsdp = function (message) {
            var sdp = message.sdp;

            if (sdp.type == 'offer') {
                root.peers[message.userid] = Answer.createAnswer(merge(options, {
                    MediaStream: root.MediaStream,
                    sdp: sdp
                }));
            }

            if (sdp.type == 'answer') {
                root.peers[message.userid].setRemoteDescription(sdp);
            }
        };

        root.acceptRequest = function (userid) {
            root.peers[userid] = Offer.createOffer(merge(options, {
                MediaStream: root.MediaStream
            }));
        };

        var candidates = [];
        // if someone shared ICE
        this.onice = function (message) {
            var peer = root.peers[message.userid];
            if (peer) {
                peer.addIceCandidate(message.candidate);
                for (var i = 0; i < candidates.length; i++) {
                    peer.addIceCandidate(candidates[i]);
                }
                candidates = [];
            } else candidates.push(candidates);
        };

        // it is passed over Offer/Answer objects for reusability
        var options = {
            onsdp: function (sdp) {
                socket.send({
                    userid: root.userid,
                    sdp: sdp,
                    to: root.participant
                });
            },
            onicecandidate: function (candidate) {
                socket.send({
                    userid: root.userid,
                    candidate: candidate,
                    to: root.participant
                });
            },
            onStreamAdded: function (stream) {
                console.debug('onStreamAdded', '>>>>>>', stream);

                stream.onended = function () {
                    if (root.onStreamEnded) root.onStreamEnded(streamObject);
                };

                var mediaElement = document.createElement('video');
                mediaElement.id = root.participant;
                mediaElement.srcObject = stream;
                mediaElement.autoplay = true;
                mediaElement.controls = true;
                mediaElement.play();

                var streamObject = {
                    mediaElement: mediaElement,
                    stream: stream,
                    userid: root.participant,
                    type: 'remote'
                };

                function afterRemoteStreamStartedFlowing() {
                    if (!root.onStreamAdded) return;
                    root.onStreamAdded(streamObject);
                }

                afterRemoteStreamStartedFlowing();
            }
        };

        function closePeerConnections() {
            self.stopBroadcasting = true;
            if (root.MediaStream) root.MediaStream.stop();

            for (var userid in root.peers) {
                root.peers[userid].peer.close();
            }
            root.peers = {};
        }

        root.close = function () {
            socket.send({
                userLeft: true,
                userid: root.userid,
                to: root.participant
            });
            closePeerConnections();
        };

        window.onbeforeunload = function () {
            root.close();
        };

        window.onkeyup = function (e) {
            if (e.keyCode == 116)
                root.close();
        };

        function onmessage(e) {
            var message = JSON.parse(e.data);

            if (message.userid == root.userid) return;
            root.participant = message.userid;

            // for pretty logging
            console.debug(JSON.stringify(message, function (key, value) {
                if (value && value.sdp) {
                    console.log(value.sdp.type, '---', value.sdp.sdp);
                    return '';
                } else return value;
            }, '---'));

            // if someone shared SDP
            if (message.sdp && message.to == root.userid) {
                self.onsdp(message);
            }

            // if someone shared ICE
            if (message.candidate && message.to == root.userid) {
                self.onice(message);
            }

            // if someone sent participation request
            if (message.participationRequest && message.to == root.userid) {
                self.participantFound = true;

                if (root.onParticipationRequest) {
                    root.onParticipationRequest(message.userid);
                } else root.acceptRequest(message.userid);
            }

            // if someone is broadcasting himself!
            if (message.broadcasting && root.onUserFound) {
                root.onUserFound(message.userid);
            }

            if (message.userLeft && message.to == root.userid) {
                closePeerConnections();
            }
        }

        var socket = socketURL;
        if (typeof socketURL == 'string') {
            socket = new WebSocket(socketURL);
            socket.push = socket.send;
            socket.send = function (data) {
                socket.push(JSON.stringify(data));
            };

            socket.onopen = function () {
                console.log('websocket connection opened.');
            };
        }
        socket.onmessage = onmessage;
    }

    var RTCPeerConnection = window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
    var RTCSessionDescription = window.mozRTCSessionDescription || window.RTCSessionDescription;
    var RTCIceCandidate = window.mozRTCIceCandidate || window.RTCIceCandidate;
    var RTCDataChannel = window.mozRTCDataChannel || window.webkitRTCDataChannel;

    var isFirefox = !!navigator.mozGetUserMedia;
    var isChrome = !!navigator.webkitGetUserMedia;

    var STUN = {
        url: isChrome ? 'stun:stun.l.google.com:19302' : 'stun:23.21.150.121'
    };

    var iceServers = {
        iceServers: [STUN]
    };

    if (typeof IceServersHandler !== 'undefined') {
        iceServers.iceServers = IceServersHandler.getIceServers();
    }

    var offerAnswerConstraints = {
        optional: [],
        mandatory: {
            OfferToReceiveAudio: true,
            OfferToReceiveVideo: true
        }
    };

    function getToken() {
        return Math.round(Math.random() * 9999999999) + 9999999999;
    }

    function onSdpError() {}

    // var offer = Offer.createOffer(config);
    // offer.setRemoteDescription(sdp);
    // offer.addIceCandidate(candidate);
    var Offer = {
        createOffer: function (config) {
            var peer = new RTCPeerConnection(iceServers);
            sendChannel = peer.createDataChannel("sendChannel");
            sendChannel.onopen = handleSendChannelStatusChange;
            sendChannel.onclose = handleSendChannelStatusChange;
            console.log(sendChannel);

            if (typeof peer.addTrack === 'function') {
                if (config.MediaStream) {
                    config.MediaStream.getTracks().forEach(function (track) {
                        peer.addTrack(track, config.MediaStream);
                    });
                }
                var dontDuplicate = {};
                peer.ontrack = function (event) {
                    var stream = event.streams[0];
                    if (dontDuplicate[stream.id]) return;
                    dontDuplicate[stream.id] = true;
                    config.onStreamAdded(stream);
                };
            } else {
                if (config.MediaStream) peer.addStream(config.MediaStream);
                peer.onaddstream = function (event) {
                    config.onStreamAdded(event.stream);
                };
            }

            peer.onicecandidate = function (event) {
                if (event.candidate)
                    config.onicecandidate(event.candidate);
            };

            peer.createOffer(offerAnswerConstraints).then(function (sdp) {
                peer.setLocalDescription(sdp).then(function () {
                    config.onsdp(sdp);
                });
            }).catch(onSdpError);
            this.peer = peer;

            return this;
        },
        setRemoteDescription: function (sdp) {
            this.peer.setRemoteDescription(new RTCSessionDescription(sdp));
        },
        addIceCandidate: function (candidate) {
            this.peer.addIceCandidate(new RTCIceCandidate({
                sdpMLineIndex: candidate.sdpMLineIndex,
                candidate: candidate.candidate
            }));
        }

    };

    // var answer = Answer.createAnswer(config);
    // answer.setRemoteDescription(sdp);
    // answer.addIceCandidate(candidate);
    var Answer = {
        createAnswer: function (config) {
            var peer = new RTCPeerConnection(iceServers);
            if (typeof peer.addTrack === 'function') {
                if (config.MediaStream) {
                    config.MediaStream.getTracks().forEach(function (track) {
                        peer.addTrack(track, config.MediaStream);
                    });
                }
                var dontDuplicate = {};
                peer.ontrack = function (event) {
                    var stream = event.streams[0];
                    if (dontDuplicate[stream.id]) return;
                    dontDuplicate[stream.id] = true;
                    config.onStreamAdded(stream);
                };
            } else {
                if (config.MediaStream) peer.addStream(config.MediaStream);
                peer.onaddstream = function (event) {
                    config.onStreamAdded(event.stream);
                };
            }

            peer.onicecandidate = function (event) {
                if (event.candidate)
                    config.onicecandidate(event.candidate);
            };

            peer.ondatachannel = function (event) {
                receiveChannel = event.channel;
                console.log(receiveChannel);
                receiveChannel.onmessage = handleReceiveMessage;
                receiveChannel.onopen = handleReceiveChannelStatusChange;
                receiveChannel.onclose = handleReceiveChannelStatusChange;

                function handleReceiveChannelStatusChange(event) {
                    if (receiveChannel) {
                        console.log("Receive channel's status has changed to " +
                            receiveChannel.readyState);
                    }
                }
            };

            peer.setRemoteDescription(new RTCSessionDescription(config.sdp)).then(function () {
                peer.createAnswer(offerAnswerConstraints).then(function (sdp) {
                    peer.setLocalDescription(sdp);
                    config.onsdp(sdp);
                }).catch(onSdpError);
            });

            this.peer = peer;

            return this;
        },
        addIceCandidate: function (candidate) {
            this.peer.addIceCandidate(new RTCIceCandidate({
                sdpMLineIndex: candidate.sdpMLineIndex,
                candidate: candidate.candidate
            }));
        }
    };



    function merge(mergein, mergeto) {
        for (var t in mergeto) {
            mergein[t] = mergeto[t];
        }
        return mergein;
    }

    navigator.getUserMedia = function (hints, onsuccess, onfailure) {
        if (!hints) hints = {
            audio: true,
            video: true
        };
        if (!onsuccess) throw 'Second argument is mandatory. navigator.getUserMedia(hints,onsuccess,onfailure)';

        navigator.mediaDevices.getUserMedia(hints).then(_onsuccess).catch(_onfailure);

        function _onsuccess(stream) {
            onsuccess(stream);
        }

        function _onfailure(e) {
            if (onfailure) onfailure(e);
            else throw Error('getUserMedia failed: ' + JSON.stringify(e, null, '\t'));
        }
    };
})();


function setup() {
    mic = new p5.AudioIn();
    mic.getSources(function (deviceList) {
        console.log(deviceList);
        mic.setSource(0);
    });
    mic.start();
    fft = new p5.FFT();
    fft.setInput(mic);
}

function draw() {
    var spectrum = fft.analyze();
    var vol = mic.getLevel();
    var tot_sp = 0;
    for (i = 0; i < spectrum.length; i++) {
        var sp = spectrum[i];
        tot_sp += sp;

    }
    var freq = tot_sp / spectrum.length;
    //console.log(freq,vol)
    // console.log(spectrum[0], spectrum[spectrum.length - 1]);
    // var freq = fft.getEnergy(spectrum[0], spectrum[spectrum.length - 1]);
    if (sendChannel.readyState == "open") {
       
            sendMessage(freq, vol);
       
    }
}



function handleSendChannelStatusChange(event) {
    if (sendChannel) {
        var state = sendChannel.readyState;
        if (state === "open") {
            console.log("open");

        } else {
            console.log("not open");
        }
    }
}
