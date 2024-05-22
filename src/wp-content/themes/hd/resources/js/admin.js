import { nanoid } from 'nanoid';
import Cookies from 'js-cookie';

Object.assign(window, { Cookies });

jQuery(function ($) {
    // codemirror
    if (typeof codemirror_settings !== 'undefined') {
        const codemirror_css = $('.codemirror_css');
        const codemirror_html = $('.codemirror_html');

        codemirror_css.each((index, el) => {
            rand_element_init(el);

            let editorSettings = codemirror_settings.codemirror_css ? _.clone(codemirror_settings.codemirror_css) : {};

            editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
                indentUnit: 3,
                tabSize: 3,
                lineNumbers: true,
                autoRefresh: true,
            });

            wp.codeEditor.initialize($(el), editorSettings);
        });

        codemirror_html.each((index, el) => {
            rand_element_init(el);

            let editorSettings = codemirror_settings.codemirror_html ? _.clone(codemirror_settings.codemirror_html) : {};

            editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
                indentUnit: 3,
                tabSize: 3,
                autoRefresh: true,
            });

            wp.codeEditor.initialize($(el), editorSettings);
        });
    }

    // hide notice
    $(document).on('click', '.notice-dismiss', function (e) {
        $(this).closest('.notice.is-dismissible').fadeOut();
    });

    // filter tabs
    const tabs_wrapper = $('.filter-tabs');
    tabs_wrapper.each((index, el) => {
        let _id = rand_element_init(el);

        const _nav = $(el).find('.tabs-nav');
        const _content = $(el).find('.tabs-content');

        _content.find('.tabs-panel').hide();
        let _cookie = 'cookie_' + _id + '_' + index;

        if (Cookies.get(_cookie) === '' || Cookies.get(_cookie) === 'undefined') {
            let _hash = _nav.find('a:first').attr('href');
            Cookies.set(_cookie, _hash, { expires: 7, path: '' });
        }

        _nav.find('a[href="' + Cookies.get(_cookie) + '"]').addClass('current');
        _nav.find('a')
            .on('click', function (e) {
                e.preventDefault();
                Cookies.set(_cookie, $(this).attr('href'), {
                    expires: 7,
                    path: '',
                });

                _nav.find('a.current').removeClass('current');
                _content.find('.tabs-panel:visible').removeClass('show').hide();

                $(this.hash).addClass('show').show();
                $(this).addClass('current');
            })
            .filter('.current')
            .trigger('click');
    });

    // user
    const create_user = $('#createuser');
    create_user.find('#send_user_notification').removeAttr('checked').attr('disabled', true);

    /**
     * @param el
     * @return {*|jQuery}
     */
    function rand_element_init(el) {
        const _rand = nanoid(9);
        $(el).addClass(_rand);

        let _id = $(el).attr('id');
        if (_id === 'undefined' || _id === '') {
            _id = _rand;
            $(el).attr('id', _id);
        }

        return _id;
    }

    /**
     * @param speed
     */
    $.fn.fadeOutAndRemove = function (speed) {
        $(this).fadeOut(speed, function () {
            $(this).remove();
        });
    };

    // ajax
    $(document).ajaxStart(() => {
        Pace.restart();
    });

    // ajax submit settings
    $(document).on('submit', '#hd_form', function (e) {
        e.preventDefault();
        let $this = $(this);

        let btn_submit = $this.find('button[name="hd_submit_settings"]');
        let button_text = btn_submit.html();
        let button_text_loading = '<i class="fa-solid fa-spinner fa-spin-pulse"></i>';

        let _ignoreFields = ['_wpnonce', '_wp_http_referer'];
        let _serialize = $this.serializeArray().filter((val) => _ignoreFields.indexOf(val.name) === -1);

        btn_submit.prop('disabled', true).html(button_text_loading);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'submit_settings',
                _data: _serialize,
                _ajax_nonce: $this.find('input[name="_wpnonce"]').val(),
                _wp_http_referer: $this.find('input[name="_wp_http_referer"]').val(),
            },
        })
            .done(function (data) {
                btn_submit.prop('disabled', false).html(button_text);
                $this.find('#hd_content').prepend(data);

                // dismissible auto hide
                setTimeout(() => {
                    $this.find('#hd_content').find('.dismissible-auto').fadeOutAndRemove(400);
                }, 3000);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                btn_submit.prop('disabled', false).html(button_text);
                console.log(errorThrown);
            });
    });
});
