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
		//add_action( 'admin_notices', array( $this, 'wpqtcc_dupe_action' ) );
		add_action( 'load-edit.php', array( $this, 'wpqtcc_dupe_action' ) );
		
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
			$orig_content = $content = $post->post_content;
			
			if( !preg_match( '/\[:([a-z]{2})?\]/', $content ) ) {
				echo 'Les langues ne sont pas définies.</br>';
				$content = '[:fr]' . $content . '[:]';
			}
				
			foreach( $langs as $lang ) {
				if( preg_match( '/\[:' . $lang . '\]/', $content ) ) {
					echo $lang . ' already there.<br/>';
					continue;
				}
				echo $lang . ' to copy.<br/>';
				
				if( false === strpos( $content, '[:fr]' ) ) {
					echo 'Il n\'y a pas le français->continue.';
					continue;
				}
				
				$stripped = $content;
				if( preg_match( '/\[:fr\](.+?)\[:([a-z]{2})?\]/', $content, $matches ) )
					$stripped = $matches[1];
				$content = str_replace( '[:]', '[:' . $lang . ']' . $stripped . '[:]', $content );
			}
			
			if( $content && $orig_content != $content ) {
				echo 'updating post<br/>';
				$post->post_content = $content;
				wp_update_post( $post );
			}
		}
		echo '</ul>';									//debug
		echo '</div>';
		die();											//debug
	}
}
?>