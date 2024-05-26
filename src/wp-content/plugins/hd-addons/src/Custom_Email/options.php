<h2><?php _e( 'Email Settings', ADDONS_TEXT_DOMAIN ); ?></h2>

<?php

$emails_options = get_option( 'emails__options' );
$filter_custom_emails = filter_setting_options( 'custom_emails', [] );

if ( ! empty( $filter_custom_emails ) ) :
	foreach ( $filter_custom_emails as $key => $ar ) :
		$emails_list = $emails_options[$key] ?? '';

		if ( ! $ar ) {
			break;
		}
?>
<div class="section section-text" id="section_emails">
	<label class="heading" for="<?=$key?>"><?php _e( $ar, ADDONS_TEXT_DOMAIN ); ?></label>
    <div class="desc">The email addresses are separated by commas "comma".</div>
	<div class="option">
		<div class="controls">
			<input value="<?=esc_attr_strip_tags( $emails_list )?>" class="hd-input hd-control" type="text" id="<?=$key?>" name="<?=$key?>_email">
		</div>
	</div>
</div>
<?php endforeach; endif;
