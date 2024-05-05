import LazyLoad from "vanilla-lazyload";

(function () {
    let ll = new LazyLoad({
        class_applied: "lz-applied",
        class_loading: "lz-loading",
        class_loaded: "lz-loaded",
        class_error: "lz-error",
        class_entered: "lz-entered",
        class_exited: "lz-exited",

        callback_loaded: (el) => {
            el.removeAttribute('data-src');
        },

        unobserve_entered: true,
    });
})();
