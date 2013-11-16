define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('VideoList', function ($scope, $location, Restangular) {
        Restangular.one('video').getList()
            .then(function(result) {
                $scope.videos = result;
            });
    });
});