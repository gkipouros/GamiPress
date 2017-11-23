<?php
/**
 * GamiPress Shortcodes Editor Class
 *
 * @package     GamiPress\Shortcodes\Editor
 * @since       1.0.0
*/
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class GamiPress_Shortcodes_Editor {

	public function __construct() {
		$this->shortcodes = gamipress_get_shortcodes();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 99 );
		add_action( 'media_buttons', array( $this, 'render_button'), 20 );
		add_action( 'admin_footer',  array( $this, 'render_modal' ) );

	}

	/**
	 * Enqueue and localize relevant admin_scripts.
	 *
	 * @since  1.0.0
	 */
	public function admin_scripts( $hook ) {

		global $post_type;

		// Just enqueue on add/edit views and on post types that supports editor feature
		if( ( $hook === 'post.php' || $hook === 'post-new.php' ) && post_type_supports( $post_type, 'editor' ) ) {

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Enqueue GamiPress Select2
			wp_enqueue_script( 'gamipress-select2-js' );
			wp_enqueue_style( 'gamipress-select2-css' );

			wp_enqueue_script( 'gamipress-shortcodes-editor', GAMIPRESS_URL . 'assets/js/gamipress-shortcodes-editor' . $min . '.js', array( 'jquery', 'gamipress-select2-js' ), GAMIPRESS_VER, true );

			wp_localize_script( 'gamipress-shortcodes-editor', 'gamipress_shortcodes_editor', array(
				'id_placeholder'          => __( 'Select a Post', 'gamipress' ),
				'id_multiple_placeholder' => __( 'Select Post(s)', 'gamipress' ),
				'user_placeholder'        => __( 'Select an User', 'gamipress' ),
				'post_type_placeholder'   => __( 'Default: All', 'gamipress' ),
				'rank_placeholder'        => __( 'Select a Rank', 'gamipress' ),
			) );

		}

    }

	/**
	 * Render shortcode modal insert button.
	 *
	 * @since 1.0.0
	 */
	public function render_button() {
		echo '<a id="insert_gamipress_shortcodes" href="#TB_inline?width=660&height=800&inlineId=select_gamipress_shortcode" class="thickbox button gamipress_media_link" data-width="800"><span class="wp-media-buttons-icon dashicons dashicons-gamipress"></span> ' . __( 'Add GamiPress Shortcode', 'gamipress' ) . '</a>';
	}

	/**
	 * Render shortcode modal content.
	 *
	 * @since 1.0.0
	 */
	public function render_modal() { ?>
		<div id="select_gamipress_shortcode" style="display:none;">
			<div class="wrap">
				<h3><?php _e( 'Insert a GamiPress shortcode', 'gamipress' ); ?></h3>
				<p><?php printf( __( 'See the %s page for more information', 'gamipress' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=gamipress_sub_help_support' ) . '">' . __( 'Help/Support', 'gamipress' ) . '</a>' ); ?></p>
				<div class="alignleft">
					<select id="select_shortcode"><?php echo $this->get_shortcode_selector(); ?></select>
				</div>
				<div class="alignright">
					<a id="gamipress_insert" class="button-primary" href="#" style="color:#fff;"><?php esc_attr_e( 'Insert Shortcode', 'gamipress' ); ?></a>
					<a id="gamipress_cancel" class="button-secondary" href="#"><?php esc_attr_e( 'Cancel', 'gamipress' ); ?></a>
				</div>
				<div id="shortcode_options" class="alignleft clear">
					<?php $this->get_shortcode_sections(); ?>
				</div>
			</div>
		</div>

	<?php
	}

	private function get_shortcode_selector() {
		$output = '';

		foreach( $this->shortcodes as $shortcode ) {
			$output .= sprintf( '<option value="%1$s">%2$s</option>', $shortcode->slug, $shortcode->name );
		}
		return $output;
	}

	private function get_shortcode_sections() {
		foreach( $this->shortcodes as $shortcode ) {
			$this->get_shortcode_section( $shortcode );
		}
	}

	/**
	 * @param GamiPress_Shortcode $shortcode
	 */
	private function get_shortcode_section( $shortcode ) {
		?>
		<div class="shortcode-section alignleft" id="<?php echo $shortcode->slug; ?>_wrapper">
			<p><strong>[<?php echo $shortcode->slug; ?>]</strong> - <?php echo $shortcode->description; ?></p>

			<?php $shortcode->show_form(); ?>
		</div>
		<?php
	}
}

function gamipress_shortcodes_add_editor_button() {
	new GamiPress_Shortcodes_Editor();
}
add_action( 'admin_init', 'gamipress_shortcodes_add_editor_button' );
