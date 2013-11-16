define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeriesDetail', function ($scope, $routeParams, Restangular) {
        Restangular.one('series', $routeParams.id).get()
            .then(function(result) {
                $scope.serie = result;
            });
    });
});