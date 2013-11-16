/**
 * Services that persists and retrieves TODOs from localStorage
 */
define(['./index'], function (services) {
    'use strict';
    services.factory('seasonStorage', function ($http) {

        var urlBase = 'rest/season';
        var season = {};

        season.list = function () {
            return $http.get(urlBase)
                .then(function(result) {
                    return result.data.list;
                });
        };

        season.get = function (id) {
            return $http.get(urlBase + '/' + id)
                .then(function(result) {
                    console.debug(result);
                    return result.data;
                });
        };

        season.create = function (cust) {
            return $http.post(urlBase, cust);
        };

        season.update = function (cust) {
            return $http.put(urlBase + '/' + cust.ID, cust)
        };

        season.delete = function (id) {
            return $http.delete(urlBase + '/' + id);
        };

        return season;
    });
});
