define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeasonDetail', function ($scope, $routeParams, Restangular) {
        $scope.season = {};
        Restangular.one('season', $routeParams.id).get()
            .then(function(result) {
                $scope.season = result;
            });
        Restangular.all('video').getList({
            season: $routeParams.id,
            orderBy: 'episode'
        })
            .then(function(result) {
                $scope.season.episodes = result;
            });
    });
});