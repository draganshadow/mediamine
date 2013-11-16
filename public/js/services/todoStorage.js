/**
 * Services that persists and retrieves TODOs from localStorage
 */
define(['./index'], function (services) {
    'use strict';
    services.factory('todoStorage', function ($http) {
        var STORAGE_ID = 'todos-angularjs';

        return {
            get: function () {//return the promise directly.
                return $http.get('/rest/series')
                            .then(function(result) {
                                //resolve the promise as the data
                                var length = result.data.list.length,
                                    list = [];
                                for (var i = 0; i < length; i++) {
                                    list[i] = {
                                        title: result.data.list[i].name,
                                        completed: false
                                    };
                                    // Do something with element i.
                                }
                                return list;
                            });
                //return JSON.parse(localStorage.getItem(STORAGE_ID) || '[]');
            },

            put: function (todos) {
                localStorage.setItem(STORAGE_ID, JSON.stringify(todos));
            }
        };
    });
});
