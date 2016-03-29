<?php
/**
 * Plugin Name: WP Admin Voice
 * Plugin URI: https://github.com/eliorivero/wp-admin-voice
 * Description: Voice command your WP Admin.
 * Author: Elio Rivero
 * Version: 1.0
 * Author URI: http://twitter.com/eliorivero
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: wpavoice
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly
	exit;
}

/**
 * Class WPA_Voice
 *
 * @since 1.0.0
 */
class WPA_Voice {

	/**
	 * Language.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $language_options = array();

	/**
	 * Regional variety.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $variety_options = array();

	/**
	 * Class constructor
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'localization' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_assets' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Show logging area.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function admin_notices() {
		?>
		<button id="startListening">
			<img id="micImage" src="<?php echo esc_url( plugins_url( 'img/mic.gif', __FILE__ ) ); ?>" alt="<?php esc_html_e( 'Start', 'wpavoice' ); ?>">
		</button>
		<div id="results"></div>
		<div class="clear"></div>
		<?php
	}

	/**
	 * Pass variables.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_assets() {
		wp_enqueue_style( 'wpavoice', plugins_url( 'css/wp-admin-voice.css', __FILE__ ) );
		wp_enqueue_script( 'wpavoice', plugins_url( 'js/wp-admin-voice.js', __FILE__ ), array(), false, true );
		wp_localize_script(
			'wpavoice', 'WPAVoice', array(
			'language'   => str_replace( '_', '-', get_locale() ),
			'continuous' => true,
			'errors'     => array(
				'unsupported' => esc_html__( 'This browser is not supported. Use latest Chrome.', 'wpavoice' ),
				'blocked'     => esc_html__( 'Microphone access is blocked.', 'wpavoice' ),
				'noAudio'     => esc_html__( 'Your microphone is muted or not functioning.', 'wpavoice' ),
				'noSpeech'    => esc_html__( 'Say something. Try: add new post.', 'wpavoice' ),
			),
			'mic'        => plugins_url( 'img/mic.gif', __FILE__ ),
			'micAnimate' => plugins_url( 'img/mic-animate.gif', __FILE__ ),
			'micSlash'   => plugins_url( 'img/mic-slash.gif', __FILE__ ),
			'base_url'   => admin_url( '/' ),
			'takemeto'   => apply_filters(
				'wpavoice_takemeto', array(
					'add new post'          => 'post-new.php',
					'agregar nueva entrada' => 'post-new.php',
					'all posts'             => 'edit.php',
					'all entries'           => 'edit.php',
					'todas las entradas'    => 'edit.php',
					'ver entradas'          => 'edit.php',
					'general settings'      => 'options-general.php',
					'opciones generales'    => 'options-general.php',
					'ajustes generales'     => 'options-general.php',
					'writing options'       => 'options-writing.php',
					'opciones de escritura' => 'options-writing.php',
					'appearance'            => 'themes.php',
					'themes'                => 'themes.php',
					'apariencia'            => 'themes.php',
					'add new theme'         => 'theme-install.php',
					'plugins'               => 'plugins.php',
					'add new plugin'        => 'plugin-install.php',
				)
			),
			'dothis'     => apply_filters(
				'wpavoice_dothis', array(
					'add title'            => 'title',
					'add post title'       => 'title',
					'agregar titulo'       => 'title',
					'agregar título'       => 'title',
					'agregar contenido'    => 'content',
					'reemplazar contenido' => 'content-new',
					'add content'          => 'content',
					'replace content'      => 'content-new',
					'escribir'             => 'content',
					'start writing'        => 'content',
					'guardar'              => 'save-post',
					'guardar borrador'     => 'save-post',
					'save'                 => 'save-post',
					'save as draft'        => 'save-post',
					'publicar'             => 'publish',
					'publish'              => 'publish',
					'cancelar'             => 'cancel',
					'cancel'               => 'cancel',
					'trash post'           => 'trash',
					'borrar entrada'       => 'trash',
					'borrar una entrada'   => 'trash-specific',
					'borrar esta entrada'  => 'trash-specific',
					'borrar la entrada'    => 'trash-specific',
					'delete this post'     => 'trash-specific',
					'delete specific post' => 'trash-specific',
				)
			),
		)
		);
	}

	/**
	 * Initialize localization routines
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function localization() {
		load_plugin_textdomain( 'wpavoice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

new WPA_Voice();