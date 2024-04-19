<?php

use Cores\Helper;

$emails_options = Helper::getOption( 'emails__options' );

?>
<h2><?php _e( 'Email Settings', TEXT_DOMAIN ); ?></h2>

<?php

$hd_email_list = apply_filters( 'hd_email_list', [] );
if ( ! empty( $hd_email_list ) ) :

    foreach ( $hd_email_list as $key => $ar ) :
        $title = Helper::mbUcFirst( $ar );
        $emails_list = $emails_options[$key] ?? '';

        if ( ! $title ) {
            break;
        }
?>
<div class="section section-text" id="section_emails">
	<label class="heading" for="<?=$key?>"><?php _e( $title, TEXT_DOMAIN ); ?></label>
	<div class="option">
		<div class="controls">
			<input value="<?=$emails_list?>" class="hd-input hd-control" type="text" id="<?=$key?>" name="<?=$key?>_email">
		</div>
		<div class="explain">The email addresses are separated by commas ','.</div>
	</div>
</div>
<?php
endforeach; endif;
