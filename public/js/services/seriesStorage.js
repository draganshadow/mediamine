/**
 * Services that persists and retrieves TODOs from localStorage
 */
define(['./index'], function (services) {
    'use strict';
    services.factory('seriesStorage', function ($http) {

        var urlBase = 'rest/series';
        var service = {};

        service.list = function () {
            return $http.get(urlBase)
                .then(function(result) {
                    return result.data.list;
                });
        };

        service.get = function (id) {
            return $http.get(urlBase + '/' + id)
                .then(function(result) {
                    console.debug(result);
                    return result.data;
                });
        };

        service.create = function (cust) {
            return $http.post(urlBase, cust);
        };

        service.update = function (cust) {
            return $http.put(urlBase + '/' + cust.ID, cust)
        };

        service.delete = function (id) {
            return $http.delete(urlBase + '/' + id);
        };

        return service;
    });
});
