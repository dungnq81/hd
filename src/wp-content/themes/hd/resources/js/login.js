/*jshint esversion: 6 */
Object.assign(window, { $: jQuery, jQuery });

'use strict';
$(() => {

    const login = $("#login");

    //login.find('.forgetmenot').remove();
    login.find('#backtoblog').remove();
    login.find('#nav').remove();
    login.find('.privacy-policy-page-link').remove();
});