define(['./../index', './../../directives/whenScrolled'], function (controllers) {
    'use strict';
    controllers.controller('VideoList', function ($scope, $routeParams, Restangular) {
        var params = {type : 'movie'};
        params.page = 0;
        if (typeof $routeParams.page !== 'undefined') {
            params.page = $routeParams.page;
        }
        $scope.videos = [];
        $scope.genres = [];

        Restangular.all('genre').getList(params)
            .then(function(result) {
                $scope.genres = $scope.genres.concat(result);
            });

        $scope.loadMore = function() {
            params.page++;
            Restangular.all('video').getList(params)
                .then(function(result) {
                    $scope.videos = $scope.videos.concat(result);
                });
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

        $scope.$watch("search.genre", function () {
            if ($scope.search.genre) {
                params.page = 1;
                params.genre = $scope.search.genre;
                Restangular.all('video').getList(params)
                    .then(function(result) {
                        $scope.videos = result;
                    });

            }
        });
    });
});

