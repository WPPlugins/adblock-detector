<?php

/**
* The public-facing functionality of the plugin.
*
* @link       http://example.com
* @since      1.0.0
*
* @package    AP_Adblock_Detector
* @subpackage AP_Adblock_Detector/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    AP_Adblock_Detector
* @subpackage AP_Adblock_Detector/public
* @author     Your Name <email@example.com>
*/
class AP_Adblock_Detector_Public {

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of the plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in AP_Adblock_Detector_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The AP_Adblock_Detector_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ap-adblock-detector-public.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in AP_Adblock_Detector_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The AP_Adblock_Detector_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ap-adblock-detector-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'blockadblock', plugin_dir_url( __FILE__ ) . 'js/blockadblock.js', array(), '3.2.1', false );

	}

	public function inject_scripts() {
		?>
		<script type="text/javascript">
		// Function called if AdBlock is not detected
		function adBlockNotDetected() {
			// DO NOTHING
		}
		// Function called if AdBlock is detected
		function adBlockDetected() {
			if(!window.adBlockDetectedHitSent) {
				window.adBlockDetectedHitSent = true;
				var xhttp = new XMLHttpRequest();
				xhttp.open("POST", "<?php echo admin_url('admin-ajax.php') ?>", true);
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send("action=ap_adblock_detector_hit");
			}
		}

		// Recommended audit because AdBlock lock the file 'blockadblock.js'
		// If the file is not called, the variable does not exist 'blockAdBlock'
		// This means that AdBlock is present
		if(typeof blockAdBlock === 'undefined') {
			adBlockDetected();
		} else {
			blockAdBlock.onDetected(adBlockDetected);
			blockAdBlock.onNotDetected(adBlockNotDetected);
			// and|or
			blockAdBlock.on(true, adBlockDetected);
			blockAdBlock.on(false, adBlockNotDetected);
			// and|or
			blockAdBlock.on(true, adBlockDetected).onNotDetected(adBlockNotDetected);
		}

		// Change the options
		// and|or
		blockAdBlock.setOption({
			debug: true,
			checkOnLoad: true,
			resetOnEnd: true,
			loopMaxNumber: 5,
			loopCheckTime: 150
		});
		</script>
		<?php
	}

	public function ap_adblock_detector_ajax_hit() {
		GLOBAL $wpdb;
		$wpdb->insert( $wpdb->prefix . 'ap_adblock_detector' . '_log', array( 'timestamp' => current_time('mysql', true) ), array( '%s' ) );
		die('1'); // Not necessary
	}

}
