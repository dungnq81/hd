import './foundation/_foundation';
import { nanoid } from 'nanoid';
import device from 'current-device';

// const is_mobile = () => device.mobile();
// const is_tablet = () => device.tablet();

// import Cookies from 'js-cookie';
// window.Cookies = Cookies;
// Object.assign(window, { Cookies });

//------------------------------------

jQuery(($) => {
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
    /*attribute target="_blank" is not W3C compliant*/
    const _blanks = [...document.querySelectorAll('a._blank, a.blank, a[target="_blank"]')];
    _blanks.forEach((el, index) => {
        el.removeAttribute('target');
        el.setAttribute('target', '_blank');
        if (!1 === el.hasAttribute('rel')) {
            el.setAttribute('rel', 'noopener nofollow');
        }
    });

    // javascript disable right click
    //document.addEventListener('contextmenu', event => event.preventDefault());
    /*document.addEventListener("contextmenu", function(e) {
        if (e.target.nodeName === "IMG") {
            e.preventDefault();
        }
    }, false);*/

    /** remove style img tag */
    const _img = document.querySelectorAll('img');
    Array.prototype.forEach.call(_img, (el) => {
        el.removeAttribute('style');
    });
});

//------------------------------------

/**
 * @param el
 * @returns {*|jQuery}
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
