/**
 * Directive that executes an expression when the element it is applied to loses focus
 */
define(['./index'], function (directives) {
    'use strict';
    directives.directive('goTo', function($location, $anchorScroll) {
        return function(scope, elm, attr) {
            elm.bind('click', function() {
                $location.hash(attr.goTo);
                $anchorScroll();
            });

        };
    });
});
