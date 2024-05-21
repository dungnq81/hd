import './foundation/_foundation';
import { nanoid } from 'nanoid';

jQuery(function ($) {});

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
