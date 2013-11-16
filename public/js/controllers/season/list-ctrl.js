define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('SeasonList', function ($scope, $location, Restangular) {
        Restangular.one('season').getList()
            .then(function(result) {
                $scope.seasons = result;
            });
    });
});