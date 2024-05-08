import $ from "jquery";

window.jQuery = window.$ = $;

import { Foundation } from "foundation-sites/js/foundation.core";
import * as CoreUtils from "foundation-sites/js/foundation.core.utils";

Foundation.addToJquery($);

// Foundation Utilities
import { Keyboard } from "foundation-sites/js/foundation.util.keyboard";
import { Box } from "foundation-sites/js/foundation.util.box";
import { Nest } from "foundation-sites/js/foundation.util.nest";
import { MediaQuery } from "foundation-sites/js/foundation.util.mediaQuery";
import { Touch } from "foundation-sites/js/foundation.util.touch";
import { Triggers } from "foundation-sites/js/foundation.util.triggers";
import { Move, Motion } from "foundation-sites/js/foundation.util.motion";
import { onImagesLoaded } from "foundation-sites/js/foundation.util.imageLoader";
import { Timer } from "foundation-sites/js/foundation.util.timer";

// Foundation Utilities Functions
Foundation.rtl = CoreUtils.rtl;
Foundation.GetYoDigits = CoreUtils.GetYoDigits;
Foundation.RegExpEscape = CoreUtils.RegExpEscape;
Foundation.transitionend = CoreUtils.transitionend;
Foundation.onLoad = CoreUtils.onLoad;
Foundation.ignoreMousedisappear = CoreUtils.ignoreMousedisappear;

Foundation.Keyboard = Keyboard;
Foundation.Box = Box;
Foundation.Nest = Nest;
Foundation.onImagesLoaded = onImagesLoaded;
Foundation.MediaQuery = MediaQuery;
Foundation.Motion = Motion;
Foundation.Move = Move;
Foundation.Touch = Touch;
Foundation.Triggers = Triggers;
Foundation.Timer = Timer;

Touch.init($);
Triggers.init($, Foundation);
MediaQuery._init();

// Require non-modular scripts
//require('motion-ui');
//require('what-input');

// https://get.foundation/sites/docs/dropdown.html
import { Dropdown } from "foundation-sites/js/foundation.dropdown";

Foundation.plugin(Dropdown, "Dropdown");

// https://get.foundation/sites/docs/dropdown-menu.html
import { DropdownMenu } from "foundation-sites/js/foundation.dropdownMenu";

Foundation.plugin(DropdownMenu, "DropdownMenu");

// https://get.foundation/sites/docs/accordion.html
import { Accordion } from "foundation-sites/js/foundation.accordion";

Foundation.plugin(Accordion, "Accordion");

// https://get.foundation/sites/docs/accordion-menu.html
import { AccordionMenu } from "foundation-sites/js/foundation.accordionMenu";

Foundation.plugin(AccordionMenu, "AccordionMenu");

// https://get.foundation/sites/docs/responsive-navigation.html
import { ResponsiveMenu } from "foundation-sites/js/foundation.responsiveMenu";
import { ResponsiveToggle } from "foundation-sites/js/foundation.responsiveToggle";

Foundation.plugin(ResponsiveMenu, "ResponsiveMenu");
Foundation.plugin(ResponsiveToggle, "ResponsiveToggle");

// https://get.foundation/sites/docs/off-canvas.html
import { OffCanvas } from "foundation-sites/js/foundation.offcanvas";

Foundation.plugin(OffCanvas, "OffCanvas");

// https://get.foundation/sites/docs/reveal.html
import { Reveal } from "foundation-sites/js/foundation.reveal";

Foundation.plugin(Reveal, "Reveal");

// https://get.foundation/sites/docs/tooltip.html
import { Tooltip } from "foundation-sites/js/foundation.tooltip";

Foundation.plugin(Tooltip, "Tooltip");

// https://get.foundation/sites/docs/smooth-scroll.html
import { SmoothScroll } from "foundation-sites/js/foundation.smoothScroll";

Foundation.plugin(SmoothScroll, "SmoothScroll");

// https://get.foundation/sites/docs/magellan.html
import { Magellan } from "foundation-sites/js/foundation.magellan";

Foundation.plugin(Magellan, "Magellan");

// https://get.foundation/sites/docs/sticky.html
import { Sticky } from "foundation-sites/js/foundation.sticky";

Foundation.plugin(Sticky, "Sticky");

// https://get.foundation/sites/docs/toggler.html#
import { Toggler } from "foundation-sites/js/foundation.toggler";

Foundation.plugin(Toggler, "Toggler");

// https://get.foundation/sites/docs/equalizer.html
import { Equalizer } from "foundation-sites/js/foundation.equalizer";

Foundation.plugin(Equalizer, "Equalizer");

// https://get.foundation/sites/docs/interchange.html
import { Interchange } from "foundation-sites/js/foundation.interchange";

Foundation.plugin(Interchange, "Interchange");

// https://get.foundation/sites/docs/abide.html
import { Abide } from "foundation-sites/js/foundation.abide";

Foundation.plugin(Abide, "Abide");

jQuery(document).ready(($) => $(document).foundation());

export default Foundation;
