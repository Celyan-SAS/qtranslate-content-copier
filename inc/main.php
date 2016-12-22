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
		
		/** Load i18n **/
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
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

				$('<option>').val('wpqtcc_ovrw').text('<?php _e( 'Overwrite in all languages', 'wpqtcc' ); ?>').appendTo("select[name='action']");
				$('<option>').val('wpqtcc_ovrw').text('<?php _e( 'Overwrite in all languages', 'wpqtcc' ); ?>').appendTo("select[name='action2']");

				//$('<option>').val('test').text('<?php echo 'lang: ' . get_locale(); ?>').appendTo("select[name='action']");
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

		if( !isset($_REQUEST['action']) && !isset($_REQUEST['action2']) )
			return;
		
		$action = false;
		
		if( 'wpqtcc_dupe' == $_REQUEST['action'] || 'wpqtcc_dupe' == $_REQUEST['action2'] )
			$action = 'copy';
		
		if( 'wpqtcc_ovrw' == $_REQUEST['action'] || 'wpqtcc_ovrw' == $_REQUEST['action2'] )
			$action = 'overwrite';
		
		if( !$action )
			return;
		
		/**/
		if( !$screen = get_current_screen() )
			return;
		
		if( empty( $screen->base ) || 'edit' != $screen->base )
			return;
		/**/

		if( !isset($_REQUEST['post']) || !$_REQUEST['post'] || !is_array($_REQUEST['post']) )
			return;

		$langs = qtrans_getSortedLanguages();
		$post_ids = $_REQUEST['post'];
		$error = false;
		
		//echo '<div class="notice notice-warning updated"><p><strong>wpqtcc</strong></p>';
		//echo '<ul>';									//debug
		foreach( $post_ids as $post_id ) {
			//echo '<li>Post id: ' . $post_id . '</li>';	//debug
			
			$post = get_post( $post_id );
			$orig_content = $content = $post->post_content;
			
			if( !preg_match( '/\[\:([a-z]{2})?\]/', $content ) ) {
				//echo 'Les langues ne sont pas définies.</br>';
				$content = '[:fr]' . $content . '[:]';
			}
				
			foreach( $langs as $lang ) {
				if( preg_match( '/\[:' . $lang . '\]/', $content ) ) {

					if( 'copy' == $action ) {
						//echo $lang . ' already there.<br/>';
						continue;
					} elseif( 'fr' != $lang ) {
						/** Overwrite **/
						$content = preg_replace( '/\[:' . $lang . '\](.*?)(\[\:([a-z]{2})?\])/ms', '\\2', $content );
						
						/*DEBUG:
						preg_match( '/\[:' . $lang . '\](.*?)(\[\:([a-z]{2})?\])/ms', $content, $matches );
						echo 'lang:' . $lang . "\n";
						var_dump( $matches );
						exit;
						*/
					}
				}
				//echo $lang . ' to copy.<br/>';
				
				if( false === strpos( $content, '[:fr]' ) ) {
					echo 'Erreur sur post id: ' . $post_id . '<brt/>';
					echo 'Il n\'y a pas le contenu français.<br/>';
					$error = true;
					continue;
				}
				
				$stripped = $content;
				if( preg_match( '/\[\:fr\](.+?)\[\:([a-z]{2})?\]/ms', $content, $matches ) ) {
					$stripped = $matches[1];
					//echo 'stripped ok.<br/>';
					$content = str_replace( '[:]', '[:' . $lang . ']' . $stripped . '[:]', $content );
				} else {
					echo 'Erreur sur post id: ' . $post_id . '<brt/>';
					echo 'Le contenu n\'a pas pu être extrait.<br/>';
					$error = true;
					continue;
				}
				
			}
			
			if( $content && $orig_content != $content ) {
				//echo 'updating post<br/>';
				$post->post_content = $content;
				wp_update_post( $post );
			}
		}
		//echo '</ul>';									//debug
		//echo '</div>';
		
		if( $error )
			die();										//debug
	}
	
	/**
	 * Load the text translation files
	 *
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wpqtcc', false, dirname( plugin_basename( dirname( __FILE__ ) ) ) . '/languages' );
		//echo dirname( plugin_basename( dirname( __FILE__ ) ) ) . '/languages'; exit;	
	}
}
?>