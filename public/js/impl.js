/**
 * This is where you add new components to the application
 * you don't need to sweat the dependency order (that is what RequireJS is for)
 * but implementations' `define`s placed elsewhere void the warranty
 */
define([
    'controllers/mediamine',
    'controllers/series/list-ctrl',
    'controllers/series/detail-ctrl',
    'controllers/season/detail-ctrl',
    'controllers/person/detail-ctrl',
    'controllers/video/detail-ctrl',
    'controllers/video/list-ctrl',
    'directives/activeLink'
], function () {});
