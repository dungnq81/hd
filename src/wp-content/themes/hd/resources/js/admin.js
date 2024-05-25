import { nanoid } from 'nanoid';
import Cookies from 'js-cookie';

Object.assign(window, { Cookies });

jQuery(function ($) {
    // codemirror
    if (typeof codemirror_settings !== 'undefined') {
        const initializeCodeMirror = (selector, settingsKey) => {
            $(selector).each((index, el) => {
                rand_element_init(el);
                let editorSettings = codemirror_settings[settingsKey] ? { ...codemirror_settings[settingsKey] } : {};

                editorSettings.codemirror = {
                    indentUnit: 3,
                    tabSize: 3,
                    autoRefresh: true,
                    lineNumbers: true,
                };

                wp.codeEditor.initialize($(el), editorSettings);
            });
        };

        initializeCodeMirror('.codemirror_css', 'codemirror_css');
        initializeCodeMirror('.codemirror_html', 'codemirror_html');
    }

    // hide notice
    $(document).on('click', '.notice-dismiss', function (e) {
        $(this).closest('.notice.is-dismissible').fadeOut();
    });

    // filter tabs
    const filter_tabs = $('.filter-tabs');
    filter_tabs.each(function (i, el) {
        const $el = $(el),
            _id = rand_element_init(el),
            $nav = $el.find('.tabs-nav'),
            $content = $el.find('.tabs-content');

        const _cookie = `cookie_${_id}_${i}`;
        let cookieValue = Cookies.get(_cookie);

        if (!cookieValue) {
            cookieValue = $nav.find('a:first').attr('href');
            Cookies.set(_cookie, cookieValue, { expires: 7, path: '' });
        }
        $nav.find(`a[href="${cookieValue}"]`).addClass('current');
        $nav.find('a')
            .on('click', function (e) {
                e.preventDefault();

                const $this = $(this);
                const hash = $this.attr('href');
                Cookies.set(_cookie, hash, { expires: 7, path: '' });

                $nav.find('a.current').removeClass('current');
                $content.find('.tabs-panel:visible').removeClass('show').hide();

                $($this.attr('href')).addClass('show').show();
                $this.addClass('current');
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
        const $el = $(el);
        const _rand = nanoid(9);
        $el.addClass(_rand);

        let _id = $el.attr('id');
        if (!_id) {
            _id = _rand;
            $el.attr('id', _id);
        }

        return _id;
    }

    $.fn.fadeOutAndRemove = function (speed) {
        return this.fadeOut(speed, function () {
            $(this).remove();
        });
    };

    $.fn.serializeObject = function () {
        let obj = {};
        let array = this.serializeArray();

        $.each(array, function () {
            let name = this.name;
            let value = this.value || '';

            // Check if the name ends with []
            if (name.indexOf('[]') > -1) {
                // Remove the trailing []
                name = name.replace('[]', '');

                // Ensure the object property is an array
                if (!obj[name]) {
                    obj[name] = [];
                }

                // Push the value into the array
                obj[name].push(value);
            } else {
                // Check if the object already has a property with the given name
                if (obj[name] !== undefined) {
                    if (!Array.isArray(obj[name])) {
                        obj[name] = [obj[name]];
                    }
                    obj[name].push(value);
                } else {
                    obj[name] = value;
                }
            }
        });

        return obj;
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

        btn_submit.prop('disabled', true).html(button_text_loading);
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'submit_settings',
                _data: $this.serializeObject(),
                _ajax_nonce: $this.find('input[name="_wpnonce"]').val(),
                _wp_http_referer: $this.find('input[name="_wp_http_referer"]').val(),
            },
        })
            .done(function (data) {
                btn_submit.prop('disabled', false).html(button_text);
                $this.find('#hd_content').prepend(data);

                // dismissible auto hide
                setTimeout(() => {
                    $this.find('#hd_content').find('.dismissible-auto')?.fadeOutAndRemove(400);
                }, 3000);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                btn_submit.prop('disabled', false).html(button_text);
                console.log(errorThrown);
            });
    });
});
