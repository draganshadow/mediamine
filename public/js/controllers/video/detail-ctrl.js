define(['./../index', 'videojs', 'jquery'], function (controllers) {
    'use strict';
    controllers.controller('VideoDetail', function ($scope, $routeParams, Restangular) {
        Restangular.one('video', $routeParams.id).get()
            .then(function(result) {
                $scope.video = result;
            });
        videojs.options.flash.swf = './js/libs/video-js/video-js.swf';
        var baseUrl = './stream/';
        var type = 'flv';
        $scope.$watch("video", function () {
            if ($scope.video) {
                var url = baseUrl + $scope.video.files[0].file.id + '.' + type;
                $('#videojs').find('source').attr("src", url);
                videojs("videojs", { "controls": true, "autoplay": false, "preload": "auto" , "width": 640, "height": 264}, function(){
                    // Player (this) is initialized and ready.
                });
            }
        });
    });
});