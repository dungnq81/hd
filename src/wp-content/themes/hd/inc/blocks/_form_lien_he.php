<?php

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

$acf_fc_layout = $args['acf_fc_layout'] ?? '';

$fc_show_logo           = $args['fc_show_logo'] ?? false;
$fc_title               = $args['fc_title'] ?? '';
$fc_contact_information = $args['fc_contact_information'] ?? false;
$fc_form_title          = $args['fc_form_title'] ?? '';
$fc_form_desc           = $args['fc_form_desc'] ?? '';
$fc_form                = $args['fc_form'] ?? false;
$fc_css_class           = ! empty( $args['fc_css_class'] ) ? ' ' . esc_attr_strip_tags( $args['fc_css_class'] ) : '';

ob_start();

?>
<section class="section home-contact<?= $fc_css_class ?>">
    <div class="container">
        <div class="flex flex-x gap">
            <div class="cell stretch t-auto cell-1">
                <div class="info-inner">
                    <?php

                    if ( $fc_show_logo ) { echo Helper::siteLogo( 'default', 'site-logo' ); }
                    if ( $fc_title ) { echo '<div class="com">' . $fc_title . '</div>'; }

                    if ( $fc_contact_information ) : ?>
                    <ul class="r-list">
                        <?php foreach ( $fc_contact_information as $re ) :
                            $content      = '';
                            $re_icon_svg  = ! empty( $re['re_icon_svg'] ) ? $re['re_icon_svg'] : '';
                            $re_image     = ! empty( $re['re_image'] ) ? $re['re_image'] : '';
                            $re_title     = ! empty( $re['re_title'] ) ? $re['re_title'] : '';
                            $re_url       = $re['re_url'] ?? '';
                            $re_css_class = ! empty( $re['re_css_class'] ) ? ' class="' . $re['re_css_class'] . '"' : '';
                            ?>
                            <li<?=$re_css_class?>>
                                <?php
                                if ( $re_icon_svg ) { $content .= $re_icon_svg; }
                                if ( $re_image ) { $content .= Helper::iconImage( $re_image, 'thumbnail' ); }
                                if ( $re_title ) { $content .= '<span>' . $re_title . '</span>'; }
                                echo Helper::ACF_Link_Wrap( $content, $re_url, 'r-item', $re_title );
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cell stretch t-auto cell-2">
                <div class="form-inner contact-form-inner">
                    <?php

                    if ( $fc_form_title ) { echo '<p class="h3 heading-title">' . $fc_form_title . '</p>'; }
                    if ( $fc_form_desc ) { echo '<p class="desc">' . $fc_form_desc . '</p>'; }

                    if ( $fc_form ) {
	                    $form = \WPCF7_ContactForm::get_instance( $fc_form );
	                    if ( $form ) {
		                    echo do_shortcode( $form->shortcode() );
	                    }
                    }

                   ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
echo ob_get_clean(); // WPCS: XSS ok.
