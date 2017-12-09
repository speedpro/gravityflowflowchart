<?php

// Include the Gravity Forms Add-On Framework.
GFForms::include_addon_framework();

class Gravity_Flow_Flowchart extends GFAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  0.1
	 * @access private
	 * @var    Gravity_Flow_Flowchart $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Gravity Flow Flowchart Extension Add-On.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_version Contains the version.
	 */
	protected $_version = GRAVITY_FLOW_FLOWCHART_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GRAVITY_FLOW_FLOWCHART_MIN_GF_VERSION;

	/**
	 * Defines the plugin slug.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravityflowflowchart';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravityflowflowchart/{{plugin_filename}}';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'http://gravityflow.io';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_title The title of the add-on.
	 */
	protected $_title = 'Gravity Flow Flowchart Extension Add-On';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  0.1
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Flowchart';

	protected $_capabilities = array(
		'gravityflowflowchart_view',
		'gravityflowflowchart_uninstall',
	);

	protected $_capabilities_form_settings = 'gravityflowflowchart_manage';
	protected $_capabilities_uninstall = 'gravityflowflowchart_uninstall';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  0.1
	 * @access public
	 * @static
	 *
	 * @return Gravity_Flow_Flowchart $_instance An instance of the Gravity_Flow_Flowchart class
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::$_instance = new Gravity_Flow_Flowchart();
		}

		return self::$_instance;
	}

	private function __clone() {
	} /* do nothing */

	public function init_ajax(){
		parent::init_ajax();
		add_action( 'wp_ajax_gravityflowflowchart_print_flowchart', array( $this, 'ajax_print_flowchart' ) );
		add_action( 'wp_ajax_gravityflowflowchart_get_step_data', array( $this, 'ajax_get_step_data' ) );

	}

	public function scripts() {
		$scripts = array();
		if ( $this->is_form_settings() ) {

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';


			$scripts[] = array(
				'handle'  => 'gravityflow_joint_dagre',
				'src'     => $this->get_base_url() . "/js/dagre{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
					array(
						'query' => 'admin-ajax.php?action=gravityflowflowchart_print_flowchart'
					)
				),
			);

			$scripts[] = array(
				'handle'  => 'gravityflow_joint_graphlib',
				'src'     => $this->get_base_url() . "/js/graphlib{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
				),
			);

			$scripts[] = array(
				'handle'  => 'gravityflow_joint_lodash',
				'src'     => $this->get_base_url() . "/js/lodash{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
				),
			);

			$scripts[] = array(
				'handle'  => 'gravityflow_joint_backbone',
				'src'     => $this->get_base_url() . "/js/backbone{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
				),
			);

			$scripts[] = array(
				'handle'  => 'gravityflow_joint_js',
				'src'     => $this->get_base_url() . "/js/joint{$min}.js",
				'deps'    => array( 'jquery', 'gravityflow_joint_backbone', 'gravityflow_joint_lodash' ),
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
				),
			);

			$scripts[] = array(
				'handle'  => 'gravityflow_flowchart_js',
				'src'     => $this->get_base_url() . "/js/flowchart{$min}.js",
				'deps'    => array( 'gravityflow_joint_js' ),
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_',
					),
					array(
						'query' => 'action=gravityflowflowchart_print_flowchart'
					)
				),
				'strings' => array(
					'vars' => array(
						//'steps' => $step_data,
						'paper' => array(
							'gridSize' => '10',
							'drawGrid' => array( 'name' => 'dot' )
						),
						'nonce' => wp_create_nonce( 'flowchart' ),
						'context' => 'display',
						'formId' => absint( rgget( 'id' ) ),
					),
				),
			 );


		}

		return array_merge( parent::scripts(), $scripts );
	}

	public function styles() {
		$min    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		$styles = array();

		$styles[] = array(
				'handle'  => 'gravityflow_joint_css',
				'src'     => $this->get_base_url() . "/css/joint{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_' ),
				),
			);

		$styles[] = array(
				'handle'  => 'gravityflow_flowchart_css',
				'src'     => $this->get_base_url() . "/css/flowchart{$min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'query' => 'page=gf_edit_forms&view=settings&subview=gravityflow&id=_notempty_' ),
				),
		);

		return array_merge( parent::styles(), $styles );
	}

	public function get_step_data( $form_id ) {

		$api = new  Gravity_Flow_Api( $form_id );

		$steps = $api->get_steps();

		$count = count( $steps );

		$step_data = array();

		foreach( $steps as $i => $step ) {
			if ( ! $step->is_active() ) {
				continue;
			}

			$step_id = $step->get_id();

			$step_icon = $this->get_step_icon( $step );

			$feed_meta =  $step->get_feed_meta();

			if ( $i < $count - 1 ) {
				$next_step      = $steps[ $i + 1 ];
				$next_step = $next_step->get_id();
			} else {
				$next_step = 'complete';
			}

			$scheduled = $step->scheduled ? $step->get_schedule_timestamp() : null;

			$data = array(
				'id'           => $step_id,
				'type'         => $step->get_type(),
				'name'         => $step->get_name(),
				'label'        => $step->get_label(),
				'icon'         => $step_icon,
				'settings_url' => admin_url( '?page=gf_edit_forms&view=settings&subview=gravityflow&id=' . $form['id'] . '&fid=' . $step_id ),
				'scheduled'    => $scheduled,
			);

			$statuses = $step->get_status_config();

			if ( $step->supports_expiration() && $step->expiration ) {
				$statuses[] = array( 'status' => 'expired' );
			}

			if ( $step->revertEnable && $step->revertValue ) {
				$statuses[] = array( 'status' => 'reverted' );
			}

			if ( $feed_meta['feed_condition_conditional_logic'] ) {
				$statuses[] = array( 'status' => 'skipped' );
			}

			$targets = array();

			foreach ( $statuses as $status ) {

				if ( $status['status'] == 'reverted' ) {
					$target = $step->revertValue;
				}elseif ( $status['status'] == 'skipped' ) {
					$target = $next_step;
				} else {
					$destination_status_key = 'destination_' . $status['status'];
					if ( isset( $step->{$destination_status_key} ) ) {
						$target = $step->{$destination_status_key};
					} else {
						$target = 'next';
					}

					if ( $target == 'next' ) {
						$target = $next_step;
					}
				}

				$targets[] = array(
					'step_id' => $target,
					'status' => $status['status'],
				);
			}
			$data['targets'] = $targets;

			$step_data[] = $data;
		}

		return $step_data;
	}

	public function ajax_print_flowchart() {
		check_ajax_referer( 'flowchart' );
		require_once( $this->get_base_path() . '/includes/class-print-flowchart.php' );
		Gravity_Flow_Flowchart_Print_Flowchart::render();
		exit();
	}

	public function ajax_get_step_data() {
		check_ajax_referer( 'flowchart' );
		$form_id = absint( rgget( 'id' ) );
		$step_data = $this->get_step_data( $form_id );
		echo json_encode( $step_data );
		wp_die();
	}

	/**
	 * @param Gravity_Flow_Step $step
	 *
	 * @return string
	 */
	public function get_step_icon( $step ) {
		$step_icon = $step ? $step->get_icon_url() : gravity_flow()->get_base_url() . '/images/gravityflow-icon-blue.svg';

		if ( strpos( $step_icon, 'http' ) === 0 ) {
			$icon = $step_icon;
		} else {
			switch ( $step->get_type() ) {
				case 'approval' :
					$icon = array(
						'text' => 'f00c',
						'color' => 'darkgreen'
					);
				break;
				case 'user_input' :
					$icon = array(
						'text' => 'f040',
						'color' => '#0074a2'
					);
					break;
				case 'notification' :
					$icon = array(
						'text' => 'f003',
						'color' => '#0074a2'
					);
					break;
				case 'folders' :
				case 'folders_remove' :
					$icon = array(
						'text' => 'f114',
						'color' => 'darkgreen'
					);
					break;
				default:
					$icon = gravity_flow()->get_base_url() . '/images/gravityflow-icon-blue.svg';

			}

		}
		return $icon;
	}
}