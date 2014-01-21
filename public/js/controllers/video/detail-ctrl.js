define(['./../index', 'projekktor'], function (controllers) {
    'use strict';
    controllers.controller('VideoDetail', function ($scope, $routeParams, Restangular) {
        var bitrates = [150,300,500,1000];
        $scope.bitrates = bitrates;
        $scope.player = {bitrate: 300};

        Restangular.one('video', $routeParams.id).get()
            .then(function(result) {
                $scope.video = result;
                projekktor('#player_a', {
                        platforms: ['browser', 'flash'],
                        poster: 'images/400-400-' + $scope.video.images[0].pathKey + '.jpg',
                        title: $scope.video.name,
                        playerFlashMP4: './js/libs/projekktor/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
                        playerFlashMP3: './js/libs/projekktor/swf/swf/StrobeMediaPlayback/StrobeMediaPlayback.swf',
                        width: 640,
                        height: 385,
                        enableFlashFallback:true,
                        playlist: [
                            {
                                0: {src: 'stream/' + $scope.player.bitrate + '-' + $scope.video.files[0].file.pathKey + '.flv', type: 'video/flv'}
//                                1: {src: "media/intro.ogv", type: "video/ogg"},
//                                2: {src: "media/intro.webm", type: "video/webm"}
                            }
                        ]
                    }, function(player) {} // on ready
                );

                $scope.$watch("player.bitrate", function () {
                    projekktor('#player_a').setFile([
                        {
                            0: {src: 'stream/' + $scope.player.bitrate + '-' + $scope.video.files[0].file.pathKey + '.flv', type: 'video/flv'}
                        }
                    ]);
                });
            });
//        videojs.options.flash.swf = './js/libs/video-js/video-js.swf';
//        var baseUrl = './stream/';
//        var type = 'mp4';
//        $scope.$watch("video", function () {
//            if ($scope.video) {
//                var url = baseUrl + $scope.video.files[0].file.id + '.' + type;
//                $('#videojs').find('source').attr("src", url);
//                videojs("videojs", { "controls": true, "autoplay": false, "preload": "auto" , "width": 640, "height": 264}, function(){
//                    // Player (this) is initialized and ready.
//                });
//            }
//        });
    });
});