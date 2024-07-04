<?php
/**
 * The template for displaying 'Contact'
 * Template Name: Contact
 * Template Post Type: page
 */

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

// header
get_header( 'contact' );

if ( have_posts() ) {
	the_post();
}

if ( post_password_required() ) :
	echo get_the_password_form(); // WPCS: XSS ok.

	return;
endif;

// template-parts/parts/page-title.php
the_page_title_theme();

$ID = $post->ID ?? false;
try {
	$ACF = Helper::acfFields( $ID ) ?? '';
} catch ( JsonException $e ) {}

$alternative_title = $ACF->alternative_title ?? '';
$c_phone           = $ACF->c_phone ?? '';
$c_email           = $ACF->c_email ?? '';
$c_address         = $ACF->c_address ?? '';
$c_contact_form    = $ACF->c_contact_form ?? false;
$c_iframe_map      = $ACF->c_iframe_map ?? '';

?>
<section class="section singular page page-contact">
	<div class="container">
        <header>
            <h1 class="heading-title"><?= $alternative_title ?: get_the_title() ?></h1>
            <ul class="re-contact">
                <?php if ( $c_phone ) : ?>
                <li class="phone">
                    <span class="icon phone-icon"></span>
                    <p>
                        <span class="title"><?= __( 'Phone', TEXT_DOMAIN )?></span>
                        <a href="tel:<?=Helper::stripSpace( $c_phone )?>" title="<?=esc_attr_strip_tags( $c_phone )?>"><?=$c_phone?></a>
                    </p>
                </li>
                <?php endif; ?>
                <?php if ( $c_email ) : ?>
                <li class="email">
                    <span class="icon email-icon"></span>
                    <p>
                        <span class="title"><?= __( 'Email', TEXT_DOMAIN )?></span>
                        <a href="mailto:<?=$c_email?>" title="<?=esc_attr_strip_tags( $c_email )?>"><?=$c_email?></a>
                    </p>
                </li>
                <?php endif; ?>
                <?php if ( $c_address ) : ?>
                <li class="address">
                    <span class="icon address-icon"></span>
                    <p>
                        <span class="title"><?= __( 'Address', TEXT_DOMAIN )?></span>
                        <span class="content"><?=$c_address?></span>
                    </p>
                </li>
                <?php endif; ?>
            </ul>
        </header>
        <article <?=Helper::microdata( 'article' )?>>
            <div class="form-infos">

                <?php the_content(); ?>

            </div>
            <div class="form-inner contact-form-inner">
		        <?php

		        if ( $c_contact_form ) {
			        $form = \WPCF7_ContactForm::get_instance( $c_contact_form );
			        if ( $form ) {
				        echo do_shortcode( $form->shortcode() );
			        }
		        }

		        ?>
            </div>
            <?php if ( $c_iframe_map ) : ?>
            <div class="form-map res ar[2-1]"><?=$c_iframe_map?></div>
            <?php endif; ?>
        </article>
	</div>
</section>
<?php

// footer
get_footer( 'contact' );
