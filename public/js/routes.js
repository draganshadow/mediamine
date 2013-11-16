/**
 * Defines the main routes in the application.
 * The routes you see here will be anchors '#/' unless specifically configured otherwise.
 */

define(['./app', './config'], function (app) {
    'use strict';
    app.config(function ($routeProvider) {

        $routeProvider.when('/', {
            templateUrl: 'partials/series/list.html',
            controller: 'SeriesList'
        });

        $routeProvider.when('/series/:id', {
            templateUrl: 'partials/series/detail.html',
            controller: 'SeriesDetail'
        });

        $routeProvider.when('/season/:id', {
            templateUrl: 'partials/season/detail.html',
            controller: 'SeasonDetail'
        });

        $routeProvider.when('/video/:id', {
            templateUrl: 'partials/video/detail.html',
            controller: 'VideoDetail'
        });

        $routeProvider.otherwise({
            redirectTo: '/'
        });

        //$locationProvider.html5Mode(true);
    });
});