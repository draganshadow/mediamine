/**
 * Directive that executes an expression when the element it is applied to loses focus
 */
define(['./index'], function (directives) {
    'use strict';
    directives.directive('projekktor', function() {
        return function(scope, elm, attr) {
            var raw = elm[0];

            elm.bind('scroll', function() {
                if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
                    scope.$apply(attr.whenScrolled);
                }
            });
        };
    });
});
