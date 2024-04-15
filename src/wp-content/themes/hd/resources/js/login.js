/*jshint esversion: 6 */
window.$ = window.jQuery = $;
Object.assign(window, { $, jQuery: $ });

'use strict';
$(() => {

    const login = $("#login");

    //login.find('.forgetmenot').remove();
    login.find('#backtoblog').remove();
    login.find('#nav').remove();
    login.find('.privacy-policy-page-link').remove();
});
