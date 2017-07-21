<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    AP_Adblock_Detector
 * @subpackage AP_Adblock_Detector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AP_Adblock_Detector
 * @subpackage AP_Adblock_Detector/admin
 * @author     Your Name <email@example.com>
 */
class AP_Adblock_Detector_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ap-adblock-detector-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( 'excanvas', plugin_dir_url( __FILE__ ) . 'js/excanvas.js' );
		wp_script_add_data( 'excanvas', 'conditional', 'lt IE 9' );

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-style', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-themes/smoothness/jquery-ui.min.css' );

		wp_enqueue_style( 'jquery-jqplot', plugin_dir_url( __FILE__ ) . 'css/jquery.jqplot.min.css' );
		wp_enqueue_script( 'jquery-jqplot', plugin_dir_url( __FILE__ ) . 'js/jquery.jqplot.min.js' );
		wp_enqueue_script( 'jqplot-dateAxisRenderer', plugin_dir_url( __FILE__ ) . 'js/jqplot.dateAxisRenderer.js' );
		wp_enqueue_script( 'jqplot-canvasTextRenderer', plugin_dir_url( __FILE__ ) . 'js/jqplot.canvasTextRenderer.js' );
		wp_enqueue_script( 'jqplot-canvasAxisLabelRenderer', plugin_dir_url( __FILE__ ) . 'js/jqplot.canvasAxisLabelRenderer.js' );
		wp_enqueue_script( 'jqplot-highlighter', plugin_dir_url( __FILE__ ) . 'js/jqplot.highlighter.js' );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ap-adblock-detector-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ap_adblock_detector', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	/**
	 * Register the plugin's settings menu.
	 *
	 * @since    1.0.0
	 */

	public function register_options_menu() {
		add_menu_page( 'AdBlock Detector', 'AdBlock Detector', 'manage_options', 'ap-adblock-detector', array($this, 'render_stats_page'), 'dashicons-chart-line', 80 );
		//add_submenu_page( 'ap-adblock-detector', 'Statistics - AdBlock Detector', 'Statistics', 'manage_options', 'ap-adblock-detector', array($this, 'render_stats_page') );
		//add_submenu_page( 'ap-adblock-detector', 'Settings - AdBlock Detector', 'Settings', 'manage_options', 'ap-adblock-detector-settings', array($this, 'render_settings_page') );
	}

	public function render_stats_page() {

		?>
		<form action='options.php' method='post'>
			<?php
			do_settings_sections( 'graphSection' );
			do_settings_sections( 'footerSection' );
			?>
		</form>
		<?php
	}

	public function render_settings_page() {

		?>
		<form action='options.php' method='post'>
			<?php
			settings_fields( 'settingsPage' );
			do_settings_sections( 'settingsPage' );
			submit_button();
			?>
		</form>
		<?php
	}

	public function render_ap_adblock_detector_fields() {
		$options = get_option( 'adblockmon_settings' );
		?>
		<label for="#ap_adblock_detector_enabled">
			Enabled
			<input id="ap_adblock_detector_enabled" type='radio' name='adblockmon_settings[ap_adblock_detector_enabled]' <?php checked( $options['ap_adblock_detector_enabled'], 1 ); ?> value='1'>
		</label>
		<label for="#ap_adblock_detector_disabled">
			Disabled
			<input id="ap_adblock_detector_disabled" type='radio' name='adblockmon_settings[ap_adblock_detector_enabled]' <?php checked( $options['ap_adblock_detector_enabled'], 0 ); ?> value='0'>
		</label>
		<?php
	}

	public function render_ap_adblock_detector_settings_section () {
		echo __( 'The plugin is currently dedicated to only tracking AdBlock-enabled pageviews. Future updates soon!', 'ap-adblock-detector' );
	}

	public function render_ap_adblock_detector_graph_section () {
		GLOBAL $wpdb;

		$query = "SELECT " .
		"(SELECT COUNT(*) FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE (`timestamp` >= NOW() - INTERVAL 24 HOUR)) as 'last24hours', " .
		"(SELECT COUNT(*) FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE (`timestamp` >= NOW() - INTERVAL 7 DAY)) as 'last7days', " .
		"(SELECT COUNT(*) FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE (`timestamp` >= NOW() - INTERVAL 30 DAY)) as 'last30days', " .
		"(SELECT COUNT(*) FROM `" . $wpdb->prefix . "ap_adblock_detector_log`) as 'total'";

		$results = $wpdb->get_row( $query );

		if($results) {
			echo '<div class="ap-abd-stats">';

			  echo '<div class="stats-head">' . __( 'Statistics : Pageviews affected by Ad Blocker', 'ap-adblock-detector') . '</div>';

			    echo '<div class="stat">';
				  echo '<h1>' . esc_html($results->last24hours) . '</h1>';
				  echo '<b>' . __( 'Last 24 hours', 'ap-adblock-detector') . '</b>';
			    echo '</div>';

			    echo '<div class="stat">';
				  echo '<h1>' . esc_html($results->last7days) . '</h1>';
				  echo '<b>' . __( 'Last 7 days', 'ap-adblock-detector') . '</b>';
			    echo '</div>';

			    echo '<div class="stat">';
				  echo '<h1>' . esc_html($results->last30days) . '</h1>';
				  echo '<b>' . __( 'Last 30 days', 'ap-adblock-detector') . '</b>';
			    echo '</div>';

			    echo '<div class="stat last">';
				  echo '<h1>' . esc_html($results->total) . '</h1>';
				  echo '<b>' . __( 'Total', 'ap-adblock-detector') . '</b>';
			    echo '</div>';

			  echo '<div class="clear-fix"></div>';

			echo '</div>';
		}
		else {
			echo '<div style="color:red">' . __('Some error occured while calculating stats', 'ap-adblock-detector') . '</div>';
		}

		echo '<div>';
			echo __( 'The graph below will give you stats about the total number of pageviews from your visitors with any AdBlocker enabled.', 'ap-adblock-detector' );
		echo '</div>';

		echo '<div class="linechart_utils">';

			$options = get_option( 'adblockmon_settings' );
			$period = isset($options['ap_adblock_detector_period']) ? $options['ap_adblock_detector_period'] : 'hourly';

			echo '<label for="ap-abd-graph-period">Period: ';
			echo '<select class="" id="ap-abd-graph-period"';
			echo 'name="period" type="text">';
				echo '<option value="hourly"' . (($period=="hourly")?" selected":"") . '>';
					echo 'Hourly';
				echo '</option>';
				echo '<option value="daily"' . (($period=="daily")?" selected":"") . '>';
					echo 'Daily';
				echo '</option> ';
				echo '<option value="weekly"' . (($period=="weekly")?" selected":"") . '>';
					echo 'Weekly';
				echo '</option>';
				echo '<option value="monthly"' . (($period=="monthly")?" selected":"") . '>';
					echo 'Monthly';
				echo '</option>';
			echo '</select>';
			echo '</label> ';

			echo '<label for="ap-abd-graph-from">Start Date: ';
				echo '<input type="text" id="ap-abd-from" name="from" value="" /> ';
			echo '</label> ';

			echo '<label for="ap-abd-graph-to">End Date: ';
				echo '<input type="text" id="ap-abd-to" name="to" value="" /> ';
			echo '</label> ';

			echo '<input type="button" id="reload_button" class="button" value="Reload Graph">';
				echo '<span id="reload_spinner" class="spinner"></span> ';
			echo '</div>';
			echo '<h2 class="no-data">- NO DATA -</h2>';
			echo '<div class="linechart_material_div">';
				echo '<div id="linechart_material" style="width: 92%; margin: 0 auto;">';
			echo '</div>';

		echo '</div>';
	}

	public function render_ap_adblock_detector_footer_section () {
/*		$img = plugin_dir_url( __FILE__ ) . 'img/banner.gif';
		echo '<a href="http://adpu.sh/easy-ad-link" target="_blank"><img class="ap-abd-img-responsive" src="' . esc_url($img) . '" /></a>';*/
	}


	public function build_settings_page() {

		register_setting( 'settingsPage', 'adblockmon_settings' );

		add_settings_section(
			'adblockmon_settings_section',
			__( 'AdBlock Detector', 'ap-adblock-detector' ),
			array($this, 'render_ap_adblock_detector_settings_section'),
			'settingsPage'
		);

		add_settings_field(
			'adblockmon_radio_field_0',
			__( 'Ad Blocks Counting', 'ap-adblock-detector' ),
			array($this, 'render_ap_adblock_detector_fields'),
			'settingsPage',
			'adblockmon_settings_section'
		);

		add_settings_section(
			'adblockmon_graph_section',
			__( 'AdBlock Detector', 'ap-adblock-detector' ),
			array($this, 'render_ap_adblock_detector_graph_section'),
			'graphSection'
		);

		add_settings_section(
			'adblockmon_graph_section',
			__( '', 'ap-adblock-detector' ),
			array($this, 'render_ap_adblock_detector_footer_section'),
			'footerSection'
		);

	}

	public function ap_adblock_detector_ajax_get_counts() {
		if(current_user_can('manage_options')) {
			GLOBAL $wpdb;

			$period = $_POST['period'];
			if(!in_array($period, array('hourly', 'daily', 'weekly', 'monthly'))) {
				$period = 'hourly';
			}
			$options = get_option( 'adblockmon_settings' );
			$db_period = isset($options['ap_adblock_detector_period']) ? $options['ap_adblock_detector_period'] : 'hourly';

			if($period != $db_period) {
				$options['ap_adblock_detector_period'] = $period;
				update_option('adblockmon_settings', $options);
			}

			$from = $_POST['from'];
			$to = $_POST['to'];

			// Not available in PHP 5.2 :(
			// $fromDt = DateTime::createFromFormat("Y-m-d", $from);
			// $toDt = DateTime::createFromFormat("Y-m-d", $to);
			$fromArr = explode('-', $_POST['from']);
			$toArr = explode('-', $_POST['to']);
			$fromDt = new DateTime();
			$toDt = new DateTime();

			// Set start date to be current date if invalid.
			// if( $toDt == false || array_sum($toDt->getLastErrors()) ) {
				// $toDt = new DateTime('now');
			// }
			if(sizeof($toArr) != 3) {
				$toDt = new DateTime('now');
			}
			else {
				$fromDt->setDate($fromArr[0], $fromArr[1], $fromArr[2]);
			}

			// Set end date to be start date - 30 days if invalid.
			// if( $fromDt == false || array_sum($fromDt->getLastErrors()) ) {
				// $fromDt = $toDt;
				// $fromDt->sub(new DateInterval('P30D'));
			// }
			if(sizeof($fromArr) != 3) {
				$fromDt = new DateTime('now');
				$fromDt->modify('-30 days');
			}
			else {
				$toDt->setDate($toArr[0], $toArr[1], $toArr[2]);
			}

			if( $fromDt > $toDt) {
				$fromDt = $toDt;
			}

			$tz_offset = get_option('gmt_offset') * 60;

			switch($period) {
				case 'daily' :
					$query = "SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') as 'timeslot', count(*) as 'blocks' FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE timestamp >= \"" . $fromDt->format('Y-m-d 00:00:00') . "\" AND timestamp <= \"" . $toDt->format('Y-m-d 23:59:59') . "\" GROUP BY DATE_FORMAT(DATE_ADD(timestamp, INTERVAL $tz_offset MINUTE), '%Y%m%d') ORDER BY timestamp";
					break;
				case 'weekly' :
					$query = "SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') as 'timeslot', count(*) as 'blocks' FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE timestamp >= \"" . $fromDt->format('Y-m-d 00:00:00') . "\" AND timestamp <= \"" . $toDt->format('Y-m-d 23:59:59') . "\" GROUP BY DATE_FORMAT(DATE_ADD(timestamp, INTERVAL $tz_offset MINUTE), '%Y%U') ORDER BY timestamp";
					break;
				case 'monthly' :
					$query = "SELECT DATE_FORMAT(timestamp, '%Y-%m-01') as 'timeslot', count(*) as 'blocks' FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE timestamp >= \"" . $fromDt->format('Y-m-d 00:00:00') . "\" AND timestamp <= \"" . $toDt->format('Y-m-d 23:59:59') . "\" GROUP BY DATE_FORMAT(DATE_ADD(timestamp, INTERVAL $tz_offset MINUTE), '%Y%m') ORDER BY timestamp";
					break;
				default:
					$query = "SELECT DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(DATE_ADD(timestamp, INTERVAL $tz_offset MINUTE)) DIV 3600 * 3600), INTERVAL 60 MINUTE), '%Y-%m-%d %H:%i:00') as 'timeslot', count(*) as 'blocks' FROM `" . $wpdb->prefix . "ap_adblock_detector_log` WHERE timestamp >= \"" . $fromDt->format('Y-m-d 00:00:00') . "\" AND timestamp <= \"" . $toDt->format('Y-m-d 23:59:59') . "\" GROUP BY DATE_ADD(timestamp, INTERVAL $tz_offset MINUTE) DIV 10000 ORDER BY timestamp";
					break;
			}

			$results = $wpdb->get_results( $query );

			if($results) {
				$output = array();
				foreach($results as $row) {
					array_push($output, array($row->timeslot, intval($row->blocks)));
				}
				echo json_encode($output);
			}

			die();
		}
		else {
			die('Are you sure you want to do this?!');
		}
	}

}
