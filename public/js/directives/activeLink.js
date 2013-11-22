/**
 * Directive that executes an expression when the element it is applied to loses focus
 */
define(['./index'], function (directives) {
    'use strict';
    directives.directive('activeLink', ['$location', function(location) {
        return {
            link: function(scope, element, attrs, controller) {
                var clazz = attrs.activeLink;
                var path = attrs.href;
                path = path.substring(1); //hack because path does bot return including hashbang
                scope.location = location;
                scope.$watch('location.path()', function(newPath) {
                    if (path === newPath) {
                        element.addClass(clazz);
                    } else {
                        element.removeClass(clazz);
                    }
                });
            }

        };
    }]);
});
