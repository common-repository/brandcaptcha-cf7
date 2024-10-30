<?php

/* A plugin to use WP-brandCAPTCHA in Contact Form 7.
 * This class needs WP-brandCAPTCHA and Contact Form 7 plugin installed and activated.
 * http://wordpress.org/extend/plugins/wp-brandcaptcha/
 * http://wordpress.org/extend/plugins/contact-form-7/
 */


require_once('WPASDPlugin.class.php');

if (!class_exists('CF7brandCAPTCHA')) {

    class CF7brandCAPTCHA extends WPASDPlugin {
        
        const BRANDCAPTCHATOOL_WP_BRANDCAPTCHA = "wp-brandcaptcha";     
   
        private $brandcaptcha_options_name = array(
            CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA => "brandcaptcha_options"
        );
        
        private $theme_option_name = array(

            CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA => 'theme_selection'
        );
        
        private $language_option_name = array(

            CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA => 'language_selection'
        );
        
        private $brandcaptcha_tools = array(
            
            CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA => "WP-brandCAPTCHA"
        );
	
    
	// member variables
	private $is_useable;
	
        private $brandcaptcha_tool = '';
	
	// php4 Constructor
	function CF7brandCAPTCHA($options_name, $textdomain_name) {
	    
	    $args = func_get_args();
	    call_user_func_array(array(&$this, "__construct"), $args);
	}
	
	
	// php5 Constructor
	function __construct($options_name, $textdomain_name) {
	    parent::__construct($options_name, $textdomain_name);
	    
	}
	
	function getClassFile() {
	    return __FILE__;
	}
	
	
	function pre_init() {
	    
	    // require the libraries
	    $this->require_library();
	    
	}
	
	
	function post_init() {
	
	    // register CF7 hooks
	    //$this->register_cf7(); // moved
	    
	}
	
	// set the default options
	function register_default_options() {
	    if (is_array($this->options) && isset($this->options['reset_on_activate']) && $this->options['reset_on_activate'] !== 'on')
		return;	
		
	    $default_options = $this->get_default_options();
	    
	    
	    // add the options based on the environment
	    WPASDPlugin::update_options($this->options_name, $default_options);
	}
        
        function get_default_options() {
            $default_options = array();
	    
	    // reset on aktivate
	    $default_options['reset_on_activate'] = 'on';
	    
            // for wp-brandcaptcha one of {'comments_theme', 'registration_theme', 'cf7brandcapext_theme'}
	    $default_options['theme_selection'] = 'cf7brandcapext_theme';
	    
	    // one of {'default'}
	    $default_options['cf7brandcapext_theme'] = 'default';
            
            // for wp-brandcaptcha one of {'language_selection', 'cf7brandcapext'}
	    $default_options['language_selection'] = 'language_selection';
	    
	    // one of {'en', 'pt', 'es' }
	    $default_options['cf7brandcapext_language'] = 'en';
            
            return $default_options;
        }
	
	
	function add_settings() {
	    
	    // Theme Options Section
	    add_settings_section(
                    'cf7brandcapext_theme_section', 
                    __('Theme Options', $this->textdomain_name), 
                    array(&$this, 'echo_theme_section_info'), 
                    $this->options_name . '_page');
    	    
            add_settings_field(
                    'cf7brandcapext_theme_preselection', 
                    __('Theme Preselection', $this->textdomain_name), 
                    array(&$this, 'echo_theme_selection_radio'), 
                    $this->options_name . '_page', 
                    'cf7brandcapext_theme_section');
    	    
            add_settings_field(
                    'cf7brandcapext_own_theme', 
                    __('Own Theme (<i>if selected</i>)', $this->textdomain_name), 
                    array(&$this, 'echo_theme_dropdown'), 
                    $this->options_name . '_page', 
                    'cf7brandcapext_theme_section');
    	    
    	    
    	    // General Options Section
    	    add_settings_section(
                    'cf7brandcapext_general_section',
                    __('General Options', $this->textdomain_name), 
                    array(&$this, 'echo_general_section_info'), 
                    $this->options_name . '_page');
    	    
            add_settings_field(
                    'cf7brandcapext_language_preselection', 
                    __('Language Preselection', $this->textdomain_name), 
                    array(&$this, 'echo_language_selection_radio'), 
                    $this->options_name . '_page', 
                    'cf7brandcapext_general_section');
    	    
            add_settings_field(
                    'cf7brandcapext_own_language', 
                    __('Own Language (<i>if selected</i>)', $this->textdomain_name), 
                    array(&$this, 'echo_language_dropdown'), 
                    $this->options_name . '_page', 
                    'cf7brandcapext_general_section');
    	    
	    // Debug Settings Section
	    add_settings_section(
                    'cf7brandcapext_debug_section', 
                    __('DEBUG Options', $this->textdomain_name), 
                    array(&$this, 'echo_debug_section_info'), 
                    $this->options_name . '_page');
	    
            add_settings_field(
                    'cf7brandcapext_reset_on_activate', 
                    __('Reset on Activate', $this->textdomain_name), 
                    array(&$this, 'echo_reset_on_activate_option'), 
                    $this->options_name . '_page', 
                    'cf7brandcapext_debug_section');
	}
	
	function echo_theme_section_info() {
	    echo '<p>';
            printf(
                    __('Here you can set which options to use for the themes option of the %s forms in the Contact Form 7 forms.', $this->textdomain_name), 
                    $this->brandcaptcha_tools[$this->brandcaptcha_tool] );
            echo "</p>\n";
	}
	
	function echo_general_section_info() {
	    echo '<p>';
            printf(
                    __('Here you can do the same with some of the general options of %s.', $this->textdomain_name),
                    $this->brandcaptcha_tools[$this->brandcaptcha_tool]);
            echo "</p>\n";
	}
	
	function echo_debug_section_info() {
	    echo '<p>' . 
                    __('Some debug options.', $this->textdomain_name) . 
                    "</p>\n";
	}
	
	function echo_reset_on_activate_option() {
	    $checked = ($this->options['reset_on_activate'] === 'on') ? ' checked="checked" ' : '';
	    
	    echo '<input type="checkbox" id="' . 
                    $this->options_name. 
                    '[reset_on_activate]" name="' . 
                    $this->options_name. 
                    '[reset_on_activate]" value="on"' . 
                    $checked . 
                    '/>'; 
	}
	
	function validate_options($input) {
            
            $validated = array();
	
            if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA) {
                
                $theme_selections = 
                    array(
                        'comments_theme', 		// if the theme for the comments should be used
                        'registration_theme', 	// if the theme for the registrations should be used
                        'cf7brandcapext_theme');	// if an own theme should be used

                $validated['theme_selection'] = 
                    $this->validate_dropdown(
                        $theme_selections, 
                        'theme_selection', 
                        $input['theme_selection']);
                
                $validated['select_theme'] = $this->options['select_theme'];
                
                $language_selections = 
                    array (
                        'language_selection',
                        'cf7brandcapext'
                        );

                $validated['language_selection'] = 
                    $this->validate_dropdown(
                        $language_selections,
                        'language_selection',
                        $input['language_selection']);

                $validated['select_lang'] = $this->options['select_lang'];
                
            } 
            
            if ($this->brandcaptcha_tool == CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA 
                            && $validated['theme_selection'] === 'cf7brandcapext_theme') {

                    $themes = 
                        array(
                            'default');

                    $validated['cf7brandcapext_theme'] = 
                        $this->validate_dropdown(
                            $themes,
                            'cf7rbrandcapext_theme',
                            $input['cf7brandcapext_theme']);
            } else {
                $validated['cf7brandcapext_theme'] = $this->options['cf7brandcapext_theme'];
            } 
            
            if ($this->brandcaptcha_tool == CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA 
                            && $validated['language_selection'] === 'cf7brandcapext') {

                    $brandcaptcha_languages = 
                        array(
                            'en', 
                            'pt',
                            'es');

                    $validated['cf7brandcapext_language'] =
                        $this->validate_dropdown(
                            $brandcaptcha_languages,
                            'cf7brandcapext_language',
                            $input['cf7brandcapext_language']); 
                } else {
                    $validated['cf7brandcapext_language'] = $this->options['cf7brandcapext_language'];
                }
            
            $validated['reset_on_activate'] = ($input['reset_on_activate'] === 'on') ? 'on' : 'off';
            
            return $validated;
               
	}
	
	function require_library() {
	    
	}
	
	
	function register_scripts() {
            
	}
	
	function register_actions() {
	    global $wp_version;
	
	
	    add_action( 'admin_notices', array(&$this, 'admin_notice') );
	    
	    $this->debugMessage('REGISTERING', 'registering actions', __FILE__, __LINE__);
	    
	    if ($this->useable()) {
		$this->debugMessage('USABLE', 'The "useable" function returned TRUE.', __FILE__, __LINE__);
		
		add_action( 'admin_init', array(&$this, 'tag_generator_brandcaptcha'), 46 );

		//add_action( 'admin_footer', array(&$this, 'add_script2admin_footer') );
		
		//add_action( 'edit_post', array(&$this, 'check_double_captcha') );
	    
	    } else {
		$this->debugMessage('NOT USABLE', 'The "useable" function returned FALSE', __FILE__, __LINE__);
	    }
	
	}
	
	function register_filters() {
	
	
	    if ( $this->useable() ) {
		
		/** added here because of structural changes in cf7 */
		add_action( 'wpcf7_init', array(&$this, 'register_cf7') );
		
		add_filter( 'wpcf7_validate_brandcaptcha', array(&$this, 'brandcaptcha_validation_filter'), 10, 2 );

		add_filter( 'wpcf7_ajax_json_echo', array(&$this, 'ajax_json_echo_filter') );
		
	    }
	}
	
	function register_cf7() {
	
	    // CF7 Shortcode Handler
	    if (function_exists('wpcf7_add_shortcode') && $this->useable() ) {
	    
		wpcf7_add_shortcode( 'brandcaptcha', array(&$this, 'shortcode_handler'), true );	

	    }
	}
	
	
	function useable() {
	    if (!isset($this->is_useable)) {
		$this->is_useable = $this->is_brandcaptcha_active() && $this->is_cf7_active();
	    }
	    
	    return $this->is_useable;
	}
    
	function is_brandcaptcha_active() {
            
 
           
            $wp_check = in_array( 
                    CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA
                    . '/'
                    . CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA
                    .'.php', 
                    apply_filters( 
                            'active_plugins', 
                            get_option( 
                                    'active_plugins' )));
            
           if ($wp_check) {
                $this->brandcaptcha_tool = CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA;
                return true;
            } else {
                return false;
            }
	
	}
	
	function is_cf7_active() {
	    return in_array( 
		'contact-form-7/wp-contact-form-7.php', 
		apply_filters( 
		    'active_plugins', 
		    get_option( 
			'active_plugins' ) ) );
	}
	
	function register_settings_page() {

	    $this->add_options_page(__('Contact Form 7 brandCAPTCHA Extension Options', $this->textdomain_name), __('CF7-brandCAP Extension', $this->textdomain_name));
	}
	
	function show_settings_page() {
	    include('settings.php');
	}
	
	function echo_theme_selection_radio() {
	
	    $brandcaptcha_options = WPASDPlugin::retrieve_options($this->brandcaptcha_options_name[$this->brandcaptcha_tool]);
	    
	    $themes =
		array (
		    'default'        => __('Default',         $this->textdomain_name));
	    
            
            $theme_options = array();
            
            $default_options = $this->get_default_options();
            
            if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA) {
                
                $comments_theme = (is_array($brandcaptcha_options) && isset($brandcaptcha_options['comments_theme']) ) ? ' (' . __('currently', $this->textdomain_name) . ': <i>' . $themes[$brandcaptcha_options['comments_theme']] . '</i>)' : '';
                $registration_theme = (is_array($brandcaptcha_options) && isset($brandcaptcha_options['registration_theme']) ) ? ' (' . __('currently', $this->textdomain_name) . ': <i>' . $themes[$brandcaptcha_options['registration_theme']] . '</i>)' : '';
	
                $theme_options[__('WP-brandCAPTCHA Comments Theme',     $this->textdomain_name) . $comments_theme] = 'comments_theme';
                $theme_options[__('WP-brandCAPTCHA Registration Theme', $this->textdomain_name) . $registration_theme] = 'registration_theme';
                
            }
                       
	    
            $theme_options[__('Own Theme' , $this->textdomain_name) . ' (<i>' . __('select below', $this->textdomain_name) . '</i>)'] = 'cf7brandcapext_theme';
	
	    $this->echo_radios(
                    $this->options_name . '[' . $this->theme_option_name[$this->brandcaptcha_tool] . ']', 
                    $theme_options, 
                    $this->options[$this->theme_option_name[$this->brandcaptcha_tool]],
                    $default_options[$this->theme_option_name[$this->brandcaptcha_tool]]);
	}
	
	function echo_theme_dropdown() {
	    $themes =
		array (
		    __('Default',         $this->textdomain_name) => 'default');
		
	    echo '<label for="' . $this->options_name . '[cf7brandcapext_theme]">' . __('Theme', $this->textdomain_name) . ":</label>\n";     
	    $this->echo_dropdown($this->options_name . '[cf7brandcapext_theme]', $themes, $this->options['cf7brandcapext_theme']);
	}
	
	function echo_language_selection_radio() {
	
	    $brandcaptcha_options = WPASDPlugin::retrieve_options($this->brandcaptcha_options_name[$this->brandcaptcha_tool]);
	    
	    $languages =
		array (
		    'en' => __('English',    $this->textdomain_name),
		    'pt' => __('Portuguese', $this->textdomain_name),
		    'es' => __('Spanish',    $this->textdomain_name));
            
            
            $language_options = array();
            $default_options = $this->get_default_options();
            
            if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA) {
                
                $brandcaptcha_language = (is_array($brandcaptcha_options) && isset($brandcaptcha_options['brandcaptcha_language']) ) ? ' (' . __('currently', $this->textdomain_name) . ': <i>' . $languages[$brandcaptcha_options['brandcaptcha_language']] . '</i>)' : '';
		    
                $language_options[__('WP-brandCAPTCHA Language', $this->textdomain_name) . $brandcaptcha_language] = 'language_selection';
                
            }
            
            $language_options[__('Own Language', $this->textdomain_name) . ' (<i>' . __('select below', $this->textdomain_name) . '</i>)'] = 'cf7brandcapext';
	
	    $this->echo_radios(
                    $this->options_name . '[' . $this->language_option_name[$this->brandcaptcha_tool] . ']' , 
                    $language_options, 
                    $this->options[$this->language_option_name[$this->brandcaptcha_tool]],
                    $default_options[$this->language_option_name[$this->brandcaptcha_tool]]);
	}
	
	function echo_language_dropdown() {
	    $languages = 
		array(
		    __('English',    $this->textdomain_name) => 'en',
		    __('Portuguese', $this->textdomain_name) => 'pt',
		    __('Spanish',    $this->textdomain_name) => 'es'
		    );
	    
	    echo '<label for="' . $this->options_name . '[cf7brandcapext_language]">' . __('Language', 'cf7brandcaptcha') . ":</label>\n";
	    $this->echo_dropdown($this->options_name . '[cf7brandcapext_language]', $languages, $this->options['cf7brandcapext_language']);
	}
	
	function ajax_json_echo_filter( $items ) {
	    if ( ! is_array( $items['onSubmit'] ) )
		$items['onSubmit'] = array();

	    $items['onSubmit'][] = 'if (typeof BrandCaptcha != "undefined") { BrandCaptcha.reload(); }';

	    return $items;
	}


	function brandcaptcha_validation_filter( $result, $tag ) {
	    global $brandcaptcha;
	    
	    $name = $tag['name'];
            
            $errors = new WP_Error();
            
            if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA && $this->is_multi_blog()) {

                $brandcaptcha->validate_brandcaptcha_response_wpmu($result);

            } else {
            
                if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA) {

                    $brandcaptcha->validate_brandcaptcha_response($errors);

                }

                $error_list = $errors->get_error_messages(null);
                
                if (!empty($error_list)) {

                    $result['valid'] = false;

                    $error_out = "";

                    foreach ($error_list as $value) {

                        $error_out .= $value;	

                    }

                    $result['reason'][$name] = $error_out;
                }
            }
	

	    return $result;
	}
	
	
	function tag_generator_brandcaptcha() {
	
	    if (function_exists('wpcf7_add_tag_generator') && $this->useable()) {
		wpcf7_add_tag_generator(
		    'brandcaptcha',
		    'brandCAPTCHA',
		    'cf7brandcaptcha-tg-pane',
		    array(&$this, 'tag_pane'));
	    }
	    
	}
        
       function wp_brandcaptcha_user_can_bypass() {
            
            $brandcaptcha_options = WPASDPlugin::retrieve_options($this->brandcaptcha_options_name[CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA]);
            
            // set the minimum capability needed to skip the captcha if there is one
            if (isset($brandcaptcha_options['bypass_for_registered_users']) && $brandcaptcha_options['bypass_for_registered_users'] && $brandcaptcha_options['minimum_bypass_level'])
                $needed_capability = $brandcaptcha_options['minimum_bypass_level'];

            // skip the brandCAPTCHA display if the minimum capability is met
            if (isset($needed_capability) && $needed_capability && current_user_can($needed_capability))
                return true;
            
            return false;
        }

	
	function shortcode_handler( $tag ) {
	    global $wpcf7_contact_form, $brandcaptcha;
            
            if( $this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA && $this->wp_brandcaptcha_user_can_bypass() ) {
                return '';
            } 
            
                

	    $name = $tag['name'];
	    
	    $brandcaptcha_options = WPASDPlugin::retrieve_options($this->brandcaptcha_options_name[$this->brandcaptcha_tool]);
	    
	    $used_theme = '';
            $used_language = '';
            
            if ($this->options[$this->theme_option_name[$this->brandcaptcha_tool]] === 'cf7brandcapext_theme' 
                    && isset($this->options['cf7brandcapext_theme'])) {

                $used_theme = $this->options['cf7brandcapext_theme'];
                
            } elseif (isset($brandcaptcha_options[$this->options[$this->theme_option_name[$this->brandcaptcha_tool]]])) {

                $used_theme = $brandcaptcha_options[$this->options[$this->theme_option_name[$this->brandcaptcha_tool]]];
                
            } else {
                $used_theme = 'default';
                
            }
            
            
            if ($this->options[$this->language_option_name[$this->brandcaptcha_tool]] === 'cf7brandcapext' 
                    && isset($this->options['cf7brandcapext_language'])) {
                $used_language = $this->options['cf7brandcapext_language'];
                
            } elseif (isset($brandcaptcha_options[$this->options[$this->language_option_name[$this->brandcaptcha_tool]]])) {
                $used_language = $brandcaptcha_options[$this->options[$this->language_option_name[$this->brandcaptcha_tool]]];
                
            } else {
                //$used_language = 'es';
		$used_language = $brandcaptcha_options[$this->options[$this->language_option_name[$this->brandcaptcha_tool]]];
            }
            
             
	    
	    $js_options = <<<JSOPTS
	    <script type='text/javascript'>
		var brandcaptchaOptions = { theme : '{$used_theme}', lang : '{$used_language}'};
	    </script>
JSOPTS;
                
	    $html = $js_options;
            if ($this->brandcaptcha_tool === CF7brandCAPTCHA::BRANDCAPTCHATOOL_WP_BRANDCAPTCHA) {
                
                $use_ssl = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on';

                $html .= $brandcaptcha->get_brandcaptcha_html( null, $use_ssl );
                
            } 

	    $validation_error = '';
	    if ( is_a( $wpcf7_contact_form, 'WPCF7_ContactForm' ) )
		$validation_error = $wpcf7_contact_form->validation_error( $name );
	
	    $html .= '<span class="wpcf7-form-control-wrap ' . $name . '">' . $validation_error . '</span>';

	    return $html;
	}


	function tag_pane( $contact_form ) {
?>
<div id="cf7brandcaptcha-tg-pane" class="hidden">
<form action="">
<table>

<?php if ( ! $this->useable() ) : ?>
<tr><td colspan="2"><strong style="color: #e6255b">you need brandCAPTCHA</strong><br /></td></tr>
<?php endif; ?>

<tr><td><?php _e( 'Name', $this->textdomain_name ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
</table>

<div class="tg-tag"><?php _e( "Copy this code and paste it into the form left.", $this->textdomain_name ); ?>
<br />
<input type="text" name="brandcaptcha" class="tag" readonly="readonly" onfocus="this.select()" />
</div>
</form>
</div>
<?php
	}	
	
	
	function admin_notice() {
	    global $plugin_page;

	    if ( ! $this->is_cf7_active() ) :

?>
<div id="message" class="updated fade"><p>
<?php _e( "You are using <b>Contact Form 7 brandCAPTCHA Extension</b>." , $this->textdomain_name); ?> 
<?php _e( "This works with the Contact Form 7 plugin but the Contact Form 7 plugin is not activated.", $this->textdomain_name ); ?>
 &mdash; Contact Form 7 <a href="http://wordpress.org/extend/plugins/contact-form-7/">http://wordpress.org/extend/plugins/contact-form-7/</a><p>
</div>
<?php
	    endif;


	    if ( ! $this->is_brandcaptcha_active() ) :

?>
<div id="message" class="updated fade"><p>
<?php _e( "You are using <b>Contact Form 7 brandCAPTCHA Extension</b>." , $this->textdomain_name); ?> 
<?php _e( "This needs a brandCAPTCHA plugin to work properly but neither of the recommended brandCAPTCHA plugins is installed and activated. Please install and activate the following plugin: ", $this->textdomain_name ); ?><br/>
&mdash; WP-brandCAPTCHA <a href="http://wordpress.org/extend/plugins/wp-brandcaptcha/">http://wordpress.org/extend/plugins/wp-brandcaptcha/</a>
</div>
<?php
	    endif;


	    
	    
	}
    
    } // end of class declaration

} // end of class exists clause

?>
