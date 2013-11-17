define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeriesDetail', function ($scope, $routeParams, Restangular) {
        $scope.serie = {};
        Restangular.one('series', $routeParams.id).get()
            .then(function(result) {
                $scope.serie = result;
            });
        Restangular.all('season').getList({serie: $routeParams.id})
            .then(function(result) {
                $scope.serie.seasons = result;
            });
    });
});