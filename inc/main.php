<?php
/**
 * WordPress qTranslate content copier plugin main class
*
* @author yann@abc.fr
* @see: https://github.com/Celyan-SAS/qtranslate-content-copier
*
*/
class wpqTCC {
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct() {
		
		/**
		 * Add bulk action to copy content over in all languages
		 *
		 */
		add_action( 'admin_footer', array( $this, 'addBulkActionInFooter' ) );		
	}
	
	/**
	 * Adds a new bulk action to the WP content list screens
	 * To copy content over in all qTranslate languages
	 *
	*/
	public function addBulkActionInFooter() {
		?>
		<script>
		(function($) {
			$(document).ready(function() {
				$('<option>').val('wpqtcc_dupe').text('<?php _e( 'Copy in all languages', 'wpqtcc' ); ?>').appendTo("select[name='action']");
				$('<option>').val('wpqtcc_dupe').text('<?php _e( 'Copy in all languages', 'wpqtcc' ); ?>').appendTo("select[name='action2']");
			});
		})( jQuery );
		</script>
		<?php 
	}
}
?>