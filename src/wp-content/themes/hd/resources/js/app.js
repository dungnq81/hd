import './foundation/_foundation';
import { nanoid } from 'nanoid';
import device from 'current-device';

// const is_mobile = () => device.mobile();
// const is_tablet = () => device.tablet();

// import Cookies from 'js-cookie';
// window.Cookies = Cookies;
// Object.assign(window, { Cookies });

import { Fancybox } from '@fancyapps/ui';

//------------------------------------

Fancybox.bind('.fcy-popup, .fcy-video, .banner-video a', {});
Fancybox.bind('.wp-block-gallery .wp-block-image a, [id^="gallery-"] a, [data-rel="lightbox"]', {
    groupAll: true, // Group all items
});

//------------------------------------

jQuery(($) => {
    // replaceState
    // let url = new URL(window.location.href);
    // if (url.searchParams.has('added')) {
    //     url.searchParams.delete('added');
    //     window.history.replaceState(null, '', url.toString());
    // }

    //...
    const onload_events = () => {};

    onload_events();
    $(window).on('load', () => {
        onload_events();
    });
    device.onChangeOrientation(() => {
        onload_events();
    });
});

//------------------------------------

/** DOMContentLoaded */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a._blank, a.blank, a[target="_blank"]').forEach((el) => {
        if (!el.hasAttribute('target') || el.getAttribute('target') !== '_blank') {
            el.setAttribute('target', '_blank');
        }

        const relValue = el.getAttribute('rel');
        if (!relValue || !relValue.includes('noopener') || !relValue.includes('nofollow')) {
            const newRelValue = (relValue ? relValue + ' ' : '') + 'noopener nofollow';
            el.setAttribute('rel', newRelValue);
        }
    });

    // javascript disable right click
    //document.addEventListener('contextmenu', event => event.preventDefault());
    // document.addEventListener('contextmenu', function (event) {
    //     if (event.target.nodeName === 'IMG') {
    //         event.preventDefault();
    //     }
    // });

    /** remove style img tag */
    document.querySelectorAll('img').forEach((el) => el.removeAttribute('style'));
});

//------------------------------------

/** vars */
const getParameters = (URL) =>
    JSON.parse('{"' + decodeURI(URL.split('?')[1]).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');

//------------------------------------

/**
 * @param el
 * @returns {*|jQuery}
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

/**
 * @param url
 * @param $delay
 */
function redirect(url = null, $delay = 10) {
    setTimeout(function () {
        if (url === null || url === '' || typeof url === 'undefined') {
            document.location.assign(window.location.href);
        } else {
            url = url.replace(/\s+/g, '');
            document.location.assign(url);
        }
    }, $delay);
}
