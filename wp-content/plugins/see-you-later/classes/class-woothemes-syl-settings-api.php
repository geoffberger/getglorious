<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * See You Later Settings API Class
 *
 * A settings API (wrapping the WordPress Settings API).
 *
 * @package WordPress
 * @subpackage See You Later
 * @category Settings
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $page_slug
 * public $name
 * public $menu_label
 * public $settings
 * public $sections
 * public $fields
 * public $errors
 * public $settings_version
 *
 * public $has_range
 * public $has_imageselector
 *
 * - __construct()
 * - setup_settings()
 * - general_init()
 * - init_sections()
 * - init_fields()
 * - create_sections()
 * - create_fields()
 * - determine_method()
 * - parse_fields()
 * - register_settings_screen()
 * - settings_screen()
 * - get_settings()
 * - settings_fields()
 * - settings_errors()
 * - settings_description()
 * - form_field_text()
 * - form_field_checkbox()
 * - form_field_textarea()
 * - form_field_select()
 * - form_field_radio()
 * - form_field_multicheck()
 * - form_field_range()
 * - form_field_images()
 * - form_field_info()
 * - validate_fields()
 * - validate_field_text()
 * - validate_field_checkbox()
 * - validate_field_multicheck()
 * - validate_field_range()
 * - validate_field_url()
 * - check_field_timestamp()
 * - check_field_text()
 * - add_error()
 * - parse_errors()
 * - get_array_field_types()
 * - enqueue_scripts()
 * - enqueue_styles()
 */
class WooThemes_SYL_Settings_API {
	public $token;
	public $page_slug;
	public $name;
	public $menu_label;
	public $settings;
	public $sections;
	public $fields;
	public $errors;

	public $has_range;
	public $has_imageselector;
	public $has_tabs;
	private $tabs;
	public $settings_version;

	/**
	 * Constructor.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct () {
		$this->token = '';
		$this->page_slug = '';
		$this->sections = array();
		$this->fields = array();
		$this->remaining_fields = array();
		$this->errors = array();

		$this->has_range = false;
		$this->has_imageselector = false;
		$this->has_tabs = false;
		$this->tabs = array();
		$this->settings_version = '';
	} // End __construct()

	/**
	 * Setup the settings screen and necessary functions.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function setup_settings () {
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
		add_action( 'admin_init', array( $this, 'settings_fields' ) );
		add_action( 'wp_loaded', array( $this, 'general_init' ) );
	} // End setup_settings()

	/**
	 * Initialise settings sections, settings fields and create tabs, if applicable.
	 * @access  public
	 * @since   1.0.3
	 * @return  void
	 */
	public function general_init() {
		$this->init_sections();
		$this->init_fields();
		$this->get_settings();
		if ( $this->has_tabs == true ) {
			$this->create_tabs();
		} // End If Statement
	} // End general_init()

	/**
	 * Register the settings sections.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_sections () {
		// Override this function in your class and assign the array of sections to $this->sections.
		_e( 'Override init_sections() in your class.', WooThemes_SYL::TEXT_DOMAIN );
	} // End init_sections()

	/**
	 * Register the settings fields.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_fields () {
		// Override this function in your class and assign the array of sections to $this->fields.
		_e( 'Override init_fields() in your class.', WooThemes_SYL::TEXT_DOMAIN );
	} // End init_fields()

	/**
	 * Construct and output HTML markup for the settings tabs.
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function settings_tabs () {
		if ( ! $this->has_tabs ) { return; }

		if ( count( $this->tabs ) > 0 ) {
			$html = '';
			//$html .= '<h2 class="nav-tab-wrapper">';
			$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";

			//$sections = array();
			$sections = array(
						'all' => array( 'href' => '#all', 'name' => __( 'All', 'woothemes-syl' ), 'class' => 'current all tab' )
					);

			foreach ( $this->tabs as $k => $v ) {
				//$sections[$k] = array( 'href' => '#' . esc_attr( $k ), 'name' => esc_attr( $v['name'] ), 'class' => 'nav-tab', 'id' => esc_attr( $k ) );
				$url = '#' . esc_attr( $k );
				$target = '';
				$sections[$k] = array( 'href' => $url, 'name' => esc_attr( $v['name'] ), 'class' => 'tab', 'id' => esc_attr( $k ), 'target' => $target );
			}

			$count = 1;
			foreach ( $sections as $k => $v ) {
				$count++;
				$html .= '<li><a href="' . $v['href'] . '"';
				//$html .= '<a href="' . $v['href'] . '"';
				if ( isset( $v['id'] ) && ( $v['id'] != '' ) ) { $html .= ' id="' . esc_attr( $v['id'] ) . '"'; }

				if ( isset( $v['class'] ) && ( $v['class'] != '' ) ) {
					$html .= ' class="' . esc_attr( $v['class'] ) . '"';
				}
				$html .= $target . '>' . esc_attr( $v['name'] ) . '</a>';
				if ( $count <= count( $sections ) ) { $html .= ' | '; }
					$html .= '</li>' . "\n";
			}
			//$html .= '<h2><div class="clear"></div>' . "\n";
			$html .= '</ul><div class="clear"></div>' . "\n";

			echo $html;
		}
	} // End settings_tabs()

	/**
	 * Create settings tabs based on the settings sections.
	 * @access private
	 * @since  1.1.0
	 * @return void
	 */
	private function create_tabs () {
		if ( count( $this->sections ) > 0 ) {
			$tabs = array();
			foreach ( $this->sections as $k => $v ) {
				$tabs[$k] = $v;
			}

			$this->tabs = $tabs;
		}
	} // End create_tabs()

	/**
	 * Create settings sections.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function create_sections () {
		if ( count( $this->sections ) > 0 ) {
			foreach ( $this->sections as $k => $v ) {
				add_settings_section( $k, $v['name'], array( $this, 'section_description' ), $this->token );
			}
		}
	} // End create_sections()

	/**
	 * Create settings fields.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function create_fields () {
		if ( count( $this->sections ) > 0 ) {
			// $this->parse_fields( $this->fields );

			foreach ( $this->fields as $k => $v ) {
				$method = $this->determine_method( $v, 'form' );
				$name = $v['name'];
				if ( $v['type'] == 'info' ) { $name = ''; }
				add_settings_field( $k, $name, $method, $this->token, $v['section'], array( 'key' => $k, 'data' => $v ) );

				// Let the API know that we have a colourpicker field.
				if ( $v['type'] == 'range' && $this->has_range == false ) { $this->has_range = true; }
			}
		}
	} // End create_fields()

	/**
	 * Determine the method to use for outputting a field, validating a field or checking a field.
	 * @access protected
	 * @since  1.0.0
	 * @param  array $data
	 * @return array or string
	 */
	protected function determine_method ( $data, $type = 'form' ) {
		$method = '';

		if ( ! in_array( $type, array( 'form', 'validate', 'check' ) ) ) { return; }

		// Check for custom functions.
		if ( isset( $data[$type] ) ) {
			if ( function_exists( $data[$type] ) ) {
				$method = $data[$type];
			}

			if ( $method == '' && method_exists( $this, $data[$type] ) ) {
				if ( $type == 'form' ) {
					$method = array( $this, $data[$type] );
				} else {
					$method = $data[$type];
				}
			}
		}

		if ( $method == '' && method_exists ( $this, $type . '_field_' . $data['type'] ) ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_' . $data['type'] );
			} else {
				$method = $type . '_field_' . $data['type'];
			}
		}

		if ( $method == '' && function_exists ( $this->token . '_' . $type . '_field_' . $data['type'] ) ) {
			$method = $this->token . '_' . $type . '_field_' . $data['type'];
		}

		if ( $method == '' ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_text' );
			} else {
				$method = $type . '_field_text';
			}
		}

		return $method;
	} // End determine_method()

	/**
	 * Parse the fields into an array index on the sections property.
	 * @access public
	 * @since  1.0.0
	 * @param  array $fields
	 * @return void
	 */
	public function parse_fields ( $fields ) {
		foreach ( $fields as $k => $v ) {
			if ( isset( $v['section'] ) && ( $v['section'] != '' ) && ( isset( $this->sections[$v['section']] ) ) ) {
				if ( ! isset( $this->sections[$v['section']]['fields'] ) ) {
					$this->sections[$v['section']]['fields'] = array();
				}

				$this->sections[$v['section']]['fields'][$k] = $v;
			} else {
				$this->remaining_fields[$k] = $v;
			}
		}
	} // End parse_fields()

	/**
	 * Register the settings screen within the WordPress admin.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function register_settings_screen () {
		$hook = add_options_page( $this->name, $this->menu_label, 'manage_options', $this->page_slug, array( $this, 'settings_screen' ) );
		$this->hook = $hook;

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == $this->page_slug ) ) {
			add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
		}
	} // End register_settings_screen()

	/**
	 * The markup for the settings screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function settings_screen () {
		global $woothemes_syl;
		?>
		<div id="woothemes-syl" class="wrap <?php echo esc_attr( $this->token ); ?>">
			<?php screen_icon( 'options-general' ); ?>
			<h2>
				<?php echo esc_html( $this->name ); ?><?php if ( '' != $this->settings_version ) { echo ' <span class="version">' . $this->settings_version . '</span>'; } ?>
				<?php
					// Used for the live preview button and to allow themes and plugins to act here.
					do_action( 'woothemes_syl_admin_inside_title' );
				?>
			</h2>
			<?php
				// Used for the live preview button and to allow themes and plugins to act here.
				do_action( 'woothemes_syl_admin_header' );
			?>
			<p class="powered-by-woo"><?php _e( 'Powered by', WooThemes_SYL::TEXT_DOMAIN ); ?><a href="http://www.woothemes.com/" title="WooThemes"><img src="<?php echo $woothemes_syl->plugin_url; ?>assets/images/woothemes-admin.png" alt="WooThemes" /></a></p>
			<?php $this->settings_tabs(); ?>
			<form action="options.php" method="post">
				<?php do_action( 'woothemes_syl_settings_form_top' ); ?>
				<?php settings_fields( $this->token ); ?>
				<?php do_settings_sections( $this->token ); ?>
				<?php do_action( 'woothemes_syl_settings_form_bottom' ); ?>
				<?php submit_button(); ?>
			</form>
		</div><!--/#woothemes-syl-->
		<?php
	} // End settings_screen()

	/**
	 * Retrieve the settings from the database.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function get_settings () {
		if ( ! is_array( $this->settings ) ) {
			$this->settings = get_option( $this->token, array() );
		}

		foreach ( $this->fields as $k => $v ) {
			if ( ! isset( $this->settings[$k] ) && isset( $v['default'] ) ) {
				$this->settings[$k] = $v['default'];
			}
			if ( $v['type'] == 'checkbox' && $this->settings[$k] != true ) {
				$this->settings[$k] = 0;
			}
		}
		return $this->settings;
	} // End get_settings()

	/**
	 * Register the settings fields.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function settings_fields () {
		register_setting( $this->token, $this->token, array( $this, 'validate_fields' ) );
		$this->create_sections();
		$this->create_fields();
	} // End settings_fields()

	/**
	 * Display the description for a settings section.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function section_description ( $section ) {
		if ( isset( $this->sections[$section['id']]['description'] ) ) {
			echo wpautop( wp_kses_post( $this->sections[$section['id']]['description'] ) );
		}
	} // End section_description_main()

	/**
	 * Generate text input field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_text ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['prepend'] ) ) {
			echo '<span>' . $args['data']['prepend'] . '</span>';
		}
		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" size="40" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_text()

	/**
	 * Generate datetime input field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_datetime ( $args ) {
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" class="short date-picker fl" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_text()

	/**
	 * Generate datetime input field with previous dates support
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_datetime_backward ( $args ) {
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" class="short date-picker-backward fl" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_text()

	/**
	 * Generate checkbox field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_checkbox ( $args ) {
		$options = $this->get_settings();

		$has_description = false;
		if ( isset( $args['data']['description'] ) ) {
			$has_description = true;
			echo '<label for="' . $this->token . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
		}
		echo '<input id="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="checkbox" value="1"' . checked( esc_attr( $options[$args['key']] ), '1', false ) . ' />' . "\n";
		if ( $has_description ) {
			echo wp_kses_post( $args['data']['description'] ) . '</label>' . "\n";
		}
	} // End form_field_text()

	/**
	 * Generate upload field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_upload ( $args ) {
		$options = $this->get_settings();

		echo '<input id="' . $args['key'] . '" class="upload-url" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		echo '<input id="st_upload_button" class="st_upload_button button" type="button" name="upload_button" value="Upload" />';
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_text()

	/**
	 * Generate upload field.
	 * @access public
	 * @since  1.1.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_multiupload ( $args ) {
		$options = $this->get_settings();

		echo '<textarea style="float:left;" id="' . $args['key'] . '" class="upload-urls" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" cols="42" rows="5" wrap="off">' . esc_attr( $options[$args['key']] ) . '</textarea>' . "\n";
		echo '<input id="st_multi_upload_button" class="st_multi_upload_button button" type="button" name="upload_button" value="Upload" />';
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_text()


	/**
	 * Generate textarea field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_textarea ( $args ) {
		$options = $this->get_settings();

		echo '<textarea id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" cols="30" rows="5">' . esc_html( $options[$args['key']] ) . '</textarea>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
	} // End form_field_textarea()

	/**
	 * Generate select box field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_select ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_select()

	/**
	 * Generate radio button field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_radio ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_radio()

	/**
	 * Generate multicheck field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_multicheck ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '<div class="multicheck-container" style="height: 100px; overflow-y: auto;">' . "\n";
			foreach ( $args['data']['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $k, (array)$options[$args['key']] ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][]" class="multicheck multicheck-' . esc_attr( $args['key'] ) . '" value="' . esc_attr( $k ) . '"' . $checked . ' /> ' . $v . '<br />' . "\n";
			}
			$html .= '</div>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_multicheck()

	/**
	 * Generate range field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_range ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" class="range-input">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_range()

	/**
	 * Generate image-based selector form field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_images ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_images()

	/**
	 * Generate information box field.
	 * @access public
	 * @since  1.0.0
	 * @param  array $args
	 * @return void
	 */
	public function form_field_info ( $args ) {
		$class = '';
		if ( isset( $args['data']['class'] ) ) {
			$class = ' ' . esc_attr( $args['data']['class'] );
		}
		$html = '<div id="' . $args['key'] . '" class="info-box' . $class . '">' . "\n";
		if ( isset( $args['data']['name'] ) && ( $args['data']['name'] != '' ) ) {
			$html .= '<h3 class="title">' . esc_html( $args['data']['name'] ) . '</h3>' . "\n";
		}
		if ( isset( $args['data']['description'] ) && ( $args['data']['description'] != '' ) ) {
			$html .= '<p>' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
		}
		$html .= '</div>' . "\n";

		echo $html;
	} // End form_field_info()

	/**
	 * Validate registered settings fields.
	 * @access public
	 * @since  1.0.0
	 * @param  array $input
	 * @uses   $this->parse_errors()
	 * @return array $options
	 */
	public function validate_fields ( $input ) {
		$options = $this->get_settings();

		foreach ( $this->fields as $k => $v ) {
			// Make sure checkboxes are present even when false.
			if ( $v['type'] == 'checkbox' && ! isset( $input[$k] ) ) { $input[$k] = false; }

			if ( isset( $input[$k] ) ) {
				// Perform checks on required fields.
				if ( isset( $v['required'] ) && ( $v['required'] == true ) ) {
					if ( in_array( $v['type'], $this->get_array_field_types() ) && ( count( (array) $input[$k] ) <= 0 ) ) {
						$this->add_error( $k, $v );
						continue;
					} else {
						if ( $input[$k] == '' ) {
							$this->add_error( $k, $v );
							continue;
						}
					}
				}

				$value = $input[$k];

				// Check if the field is valid.
				$method = $this->determine_method( $v, 'check' );

				if ( function_exists ( $method ) ) {
					$is_valid = $method( $value );
				} else {
					if ( method_exists( $this, $method ) ) {
						$is_valid = $this->$method( $value );
					}
				}

				if ( ! $is_valid ) {
					$this->add_error( $k, $v );
					continue;
				}

				$method = $this->determine_method( $v, 'validate' );

				if ( function_exists ( $method ) ) {
					$options[$k] = $method( $value );
				} else {
					if ( method_exists( $this, $method ) ) {
						$options[$k] = $this->$method( $value );
					}
				}
			}
		}

		// Parse error messages into the Settings API.
		$this->parse_errors();
		return $options;
	} // End validate_fields()

	/**
	 * Validate text fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_text ( $input ) {
		return trim( esc_attr( $input ) );
	} // End validate_field_text()

	/**
	 * Validate checkbox fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_checkbox ( $input ) {
		if ( ! isset( $input ) ) {
			return 0;
		} else {
			return (bool)$input;
		}
	} // End validate_field_checkbox()

	/**
	 * Validate multicheck fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_multicheck ( $input ) {
		$input = (array) $input;

		$input = array_map( 'esc_attr', $input );

		return $input;
	} // End validate_field_multicheck()

	/**
	 * Validate range fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_range ( $input ) {
		$input = number_format( floatval( $input ), 0 );

		return $input;
	} // End validate_field_range()

	/**
	 * Validate URL fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_url ( $input ) {
		return trim( esc_url( $input ) );
	} // End validate_field_url()

	/**
	 * Validate html fields.
	 * @access public
	 * @since  1.0.0
	 * @param  string $input
	 * @return string
	 */
	public function validate_field_html ( $input ) {
		return trim( $input );
	} // End validate_field_text()

	/**
	 * Check and validate the input from text fields.
	 * @param  string $input String of the value to be validated.
	 * @since  1.1.0
	 * @return boolean Is the value valid?
	 */
	public function check_field_text ( $input ) {
		$is_valid = true;

		return $is_valid;
	} // End check_field_text()

	/**
	 * Log an error internally, for processing later using $this->parse_errors().
	 * @access protected
	 * @since  1.0.0
	 * @param  string $key
	 * @param  array $data
	 * @return void
	 */
	protected function add_error ( $key, $data ) {
		if ( isset( $data['error_message'] ) ) {
			$message = $data['error_message'];
		} else {
			$message = sprintf( __( '%s is a required field', 'woothemes-syl' ), $data['name'] );
		}
		$this->errors[$key] = $message;
	} // End add_error()

	/**
	 * Parse logged errors.
	 * @access  protected
	 * @since   1.0.0
	 * @return  void
	 */
	protected function parse_errors () {
		if ( count ( $this->errors ) > 0 ) {
			foreach ( $this->errors as $k => $v ) {
				add_settings_error( $this->token . '-errors', $k, $v, 'error' );
			}
		}
	} // End parse_errors()

	/**
	 * Return an array of field types expecting an array value returned.
	 * @access protected
	 * @since  1.0.0
	 * @return void
	 */
	protected function get_array_field_types () {
		return array( 'multicheck' );
	} // End get_array_field_types()

	/**
	 * Load in JavaScripts where necessary.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		global $woothemes_syl;
		//Media Uploader Scripts
		wp_enqueue_media();
		wp_enqueue_script( $this->token . '-upload', esc_url( $woothemes_syl->assets_url . 'js/uploader.js' ), array( 'jquery' ) );
		// Datetimepicker
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-datetimepicker', esc_url( $woothemes_syl->assets_url . 'js/jquery-ui-timepicker-addon.js' ), array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_enqueue_script( 'jquery-ui-slider' );
		// Custom admin js
		wp_enqueue_script( $this->token . '-admin', esc_url( $woothemes_syl->assets_url . 'js/admin.js' ), array( 'jquery' ), $woothemes_syl->version );
		wp_localize_script( 'jquery-ui-datepicker', 'syl_params',
            array( 'calendar_image' => admin_url()  . 'images/date-button.gif' ) );
	} // End enqueue_scripts()

	/**
	 * Load in CSS styles where necessary.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $wp_scripts, $woothemes_syl;
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( $this->token . '-settings-api', esc_url( $woothemes_syl->assets_url . 'css/settings.css' ), '', $woothemes_syl->version );

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
	} // End enqueue_styles()

} // End Class
?>