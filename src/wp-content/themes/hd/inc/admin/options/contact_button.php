<?php

use Cores\Helper;

$contact_options = Helper::getOption( 'contact_btn__options', false, false );

$contact_title        = $contact_options['contact_title'] ?? '';
$contact_url          = $contact_options['contact_url'] ?? '';
$contact_window       = $contact_options['contact_window'] ?? '';
$contact_waiting_time = $contact_options['contact_waiting_time'] ?? '';
$contact_show_repeat  = $contact_options['contact_show_repeat'] ?? '';

$contact_popup_content = Helper::getCustomPostContent( 'html_contact', false );

?>
<h2><?php _e('Contact Button Settings', TEXT_DOMAIN); ?></h2>
<div class="section section-text" id="section_contact_button_title">
    <label class="heading" for="contact_title"><?php _e('Contact Button Title', TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags($contact_title); ?>" class="hd-input hd-control" type="text" id="contact_title" name="contact_title">
        </div>
    </div>
</div>
<div class="section section-text" id="section_contact_url">
    <label class="heading" for="contact_url"><?php _e('Contact Button URL', TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags($contact_url); ?>" class="hd-input hd-control" type="url" id="contact_url" name="contact_url" placeholder="https://">
        </div>
    </div>
</div>
<div class="section section-checkbox" id="section_contact_button_window">
    <div class="option" style="padding-top: 15px;">
        <div class="controls">
            <label><input type="checkbox" class="hd-checkbox hd-control" name="contact_window" id="contact_window" <?php checked($contact_window, 1); ?> value="1"></label>
        </div>
        <div class="explain"><?php _e( 'Open link in a new window', TEXT_DOMAIN ); ?></div>
    </div>
</div>
<div class="section section-textarea" id="section_contact_popup_content">
    <label class="heading" for="contact_popup_content"><?php _e('Popup Content', TEXT_DOMAIN) ?></label>
    <div class="desc">The content of the popup, usually the content of a shortcode or image</div>
    <div class="option">
        <div class="controls">
            <textarea class="hd-textarea hd-control" name="contact_popup_content" id="contact_popup_content" rows="4"><?php echo $contact_popup_content; ?></textarea>
        </div>
    </div>
</div>
<div class="section section-text" id="section_contact_button_waiting_time">
    <label class="heading" for="contact_waiting_time"><?php _e('Popup display waiting time', TEXT_DOMAIN) ?></label>
    <div class="desc">The waiting time to display the popup, calculated in seconds.</div>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags($contact_waiting_time); ?>" class="hd-input hd-control" type="number" min="0" id="contact_waiting_time" name="contact_waiting_time">
        </div>
    </div>
</div>
<div class="section section-text" id="section_contact_show_repeat">
    <label class="heading" for="contact_show_repeat"><?php _e('Repeat Displays', TEXT_DOMAIN) ?></label>
    <div class="desc">Number of repeat displays of the popup.</div>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr_strip_tags($contact_show_repeat); ?>" class="hd-input hd-control" type="number" min="0" id="contact_show_repeat" name="contact_show_repeat">
        </div>
    </div>
</div>