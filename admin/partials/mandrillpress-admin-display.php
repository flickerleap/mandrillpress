<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://flickerleap.com
 * @since      1.0.2
 *
 * @package    Mandrillpress
 * @subpackage Mandrillpress/admin/partials
 */

settings_errors( 'wporg_messages' );
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wporg"
		settings_fields( 'mandrillpress' );
		// output setting sections and their fields
		// (sections are registered for "wporg", each field is registered to a specific section)
		do_settings_sections( 'mandrillpress' );
		// output save settings button
		submit_button( 'Save Settings' );
		?>
	</form>
</div>
