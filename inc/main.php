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
		add_action( 'admin_notices', array( $this, 'wpqtcc_dupe_action' ) );
		//add_action( 'load-edit.php', array( $this, 'wpqtcc_dupe_action' ) );
		
	}
	
	/**
	 * Adds a new bulk action to the WP content list screens
	 * To copy content over in all qTranslate languages
	 *
	 */
	public function addBulkActionInFooter() {
		
		if( !$screen = get_current_screen() )
			return;
		
		if( empty( $screen->base ) || 'edit' != $screen->base )
			return;
		
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
	
	/**
	 * Performs the bulk content copy action
	 * 
	 */
	public function wpqtcc_dupe_action() {

		/*
		if( !$screen = get_current_screen() )
			return;
		
		if( empty( $screen->base ) || 'edit' != $screen->base )
			return;
		*/

		if( !isset($_REQUEST['post']) || !$_REQUEST['post'] || !is_array($_REQUEST['post']) )
			return;

		$langs = qtrans_getSortedLanguages();
		$post_ids = $_REQUEST['post'];
		
		echo '<div class="notice notice-warning updated"><p><strong>wpqtcc</strong></p>';
		echo '<ul>';									//debug
		foreach( $post_ids as $post_id ) {
			echo '<li>Post id: ' . $post_id . '</li>';	//debug
			
			$post = get_post( $post_id );
			$content = $post->post_content;
			
			foreach( $langs as $lang ) {
				if( preg_match( '/\[:' . $lang . '\]/', $content ) ) {
					echo $lang . ' already there.<br/>';
					continue;
				}
				echo $lang . ' to copy.<br/>';
			}
		}
		echo '</ul>';									//debug
		echo '</div>';
		//die();											//debug
	}
}
?>