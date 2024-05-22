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

    $.fn.fadeOutAndRemove = function (speed) {
        $(this).fadeOut(speed, function () {
            $(this).remove();
        });
    };

    $.fn.serializeObject = function () {
        let data = {};

        function buildInputObject(arr, val) {
            if (arr.length < 1) {
                return val;
            }
            let objkey = arr[0];
            if (objkey.slice(-1) === ']') {
                objkey = objkey.slice(0, -1);
            }
            let result = {};
            if (arr.length === 1) {
                result[objkey] = val;
            } else {
                arr.shift();
                result[objkey] = buildInputObject(arr, val);
            }
            return result;
        }

        function gatherMultipleValues(that) {
            let final_array = [];
            $.each(that.serializeArray(), function (key, field) {
                // Copy normal fields to a final array without changes
                if (field.name.indexOf('[]') < 0) {
                    final_array.push(field);
                    return true;
                }

                // Remove "[]" from the field name
                let field_name = field.name.split('[]')[0];

                // Add the field value in its array of values
                let has_value = false;
                $.each(final_array, function (final_key, final_field) {
                    if (final_field.name === field_name) {
                        has_value = true;
                        final_array[final_key]['value'].push(field.value);
                    }
                });

                // If it doesn't exist yet, create the field's array of values
                if (!has_value) {
                    final_array.push({ name: field_name, value: [field.value] });
                }
            });
            return final_array;
        }

        // Manage fields allowing multiple values first (they contain "[]" in their name)
        let final_array = gatherMultipleValues(this);

        // Then, create the object
        $.each(final_array, function () {
            let val = this.value;
            let c = this.name.split('[');
            let a = buildInputObject(c, val);
            $.extend(true, data, a);
        });

        return data;
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
                    $this.find('#hd_content').find('.dismissible-auto').fadeOutAndRemove(400);
                }, 3000);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                btn_submit.prop('disabled', false).html(button_text);
                console.log(errorThrown);
            });
    });
});
