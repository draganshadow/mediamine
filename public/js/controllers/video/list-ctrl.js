define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('VideoList', function ($scope, $routeParams, Restangular) {
        var params = {type : 'movie'};
        if (typeof $routeParams.page !== 'undefined') {
            params.page = $routeParams.page;
        }
        Restangular.all('video').getList(params)
            .then(function(result) {
                $scope.videos = result;
            });
    });
});