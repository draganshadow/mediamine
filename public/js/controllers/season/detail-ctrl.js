define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeasonDetail', function ($scope, $routeParams, Restangular) {
        Restangular.one('season', $routeParams.id).get()
            .then(function(result) {
                $scope.season = result;
            });
    });
});