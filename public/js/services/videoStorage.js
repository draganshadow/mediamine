/**
 * Services that persists and retrieves TODOs from localStorage
 */
define(['./index'], function (services) {
    'use strict';
    services.factory('videoStorage', function ($http) {

        var urlBase = 'rest/video';
        var video = {};

        video.list = function () {
            return $http.get(urlBase)
                .then(function(result) {
                    return result.data.list;
                });
        };

        video.get = function (id) {
            return $http.get(urlBase + '/' + id)
                .then(function(result) {
                    console.debug(result);
                    return result.data;
                });
        };

        video.create = function (cust) {
            return $http.post(urlBase, cust);
        };

        video.update = function (cust) {
            return $http.put(urlBase + '/' + cust.ID, cust)
        };

        video.delete = function (id) {
            return $http.delete(urlBase + '/' + id);
        };

        return video;
    });
});
