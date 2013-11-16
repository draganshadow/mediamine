define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeriesList', function ($scope, Restangular) {
        Restangular.all('series').getList()
            .then(function(result) {
                $scope.series = result;
            });
    });
});