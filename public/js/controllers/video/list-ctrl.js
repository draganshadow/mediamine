define(['./../index', './../../directives/whenScrolled'], function (controllers) {
    'use strict';
    controllers.controller('VideoList', function ($scope, $routeParams, Restangular) {
        var params = {type : 'movie'};
        var page = 1;
        if (typeof $routeParams.page !== 'undefined') {
            page = $routeParams.page;
        }
        $scope.videos = [];
        $scope.loadMore = function() {
            params.page = page;
            Restangular.all('video').getList(params)
                .then(function(result) {
                    $scope.videos = $scope.videos.concat(result);
                });
            page++;
        };
        $scope.loadMore();

        $scope.$watch("search.click", function () {
            if ($scope.search.text && $scope.search.click) {
                params.page = 1;
                params.text = $scope.search.text;
                Restangular.all('video').getList(params)
                    .then(function(result) {
                        $scope.videos = result;
                    });

            }
        });
    });
});

