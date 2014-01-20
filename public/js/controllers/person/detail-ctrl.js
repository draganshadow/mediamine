define(['./../index'], function (controllers) {
    'use strict';
    controllers.controller('PersonDetail', function ($scope, $routeParams, Restangular) {
        $scope.person = {};
        Restangular.one('person', $routeParams.id).get()
            .then(function(result) {
                $scope.person = result;
            });
        Restangular.all('video').getList({person: $routeParams.id})
            .then(function(result) {
                $scope.person.videos = result;
            });
    });
});