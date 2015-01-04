<?php
/*

  Plugin Name: SeatID Social Solutions

  Plugin URI: http://www.seatid.com/seatid-for-wordpress/

  Description: SeatID allow websites' owners to easily implement SeatID Side Widget on their WordPress websites.

  Version: 1.0

  Author: SeatID

  Author URI: http://www.seatid.com

  License: GPL2

 */



//define constants

if (!defined('SeatID_PLUGIN_DIR')) {
    // /path/to/wordpress/wp-content/plugins/seatID
    define('SeatID_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('SeatID_IMG_DIR')) {
    define('SeatID_IMG_DIR', plugins_url('img', __FILE__));
}

// Actions and hooks

add_action('admin_menu', 'seatID_add_admin_menu');
add_action('admin_init', 'seatID_settings_init');
add_action('wp_head', 'seatid_script');
add_action('admin_init', 'seatid_cookie');
add_action('admin_notices', 'seatid_register_notice');
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'seatid_settings_link' );


/*

 * To add menu in sidebar

 */

function seatID_add_admin_menu() {

    global $seat_options;

    $seat_options = get_option('seatID_settings');

    add_menu_page('SeatID', 'SeatID', 'edit_posts', 'seatid', 'seatid_options_page', SeatID_IMG_DIR . '/SeatID_Icon_for_Menu.png');
}

/*

 * To add settings fields and sections

 */

function seatID_settings_init() {

    register_setting('pluginPage', 'seatID_settings', 'seatid_validation'); //register the setting page

    /* Add setting section */
    add_settings_section(
            'seatID_config_section', __('Application Configuration', 'seatID'), 'seatID_settings_section_callback', 'pluginPage'
    );

    /* Add App ID textbox */
    add_settings_field(
            'seatID_appID', __('App ID<span class="seat_imp">*</span>', 'seatID'), 'seatID_appID_render', 'pluginPage', 'seatID_config_section'
    );
    
    /* Add Acoount textbox */
    add_settings_field(
            'seatID_account', __('Account Name<span class="seat_imp">*</span>', 'seatID'), 'seatID_account_render', 'pluginPage', 'seatID_config_section'
    );
    
    /* Add Address textbox */
    add_settings_field(
            'seatID_address', __('Address<span class="seat_imp">*</span>', 'seatID'), 'seatID_address_render', 'pluginPage', 'seatID_config_section'
    );

    /* Add All Page/Post select box */
    add_settings_field(
            'seatID_show_page', __('Show widget', 'seatID'), 'seatID_show_page_render', 'pluginPage', 'seatID_config_section'
    );

    /* Add Confirmation page section */
    add_settings_section(
            'seatID_confirmation_section', __('<span class="seperator">&nbsp;</span>', 'seatID'), 'seatID_confirmation_section_callback', 'pluginPage'
    );
    
    /* Add Confirmation page select box */
    add_settings_field(
            'seatID_confirm_0', __('Confirmation Page<span class="seat_imp">*</span>', 'seatID'), 'seatID_confirm', 'pluginPage', 'seatID_confirmation_section'
    );


    /* Register seatID style.css */
    wp_register_style('seatid-css', plugins_url('/style.css', __FILE__), false);
    wp_enqueue_style('seatid-css');
    
}

/*
 * To add App ID text box
 */

function seatID_appID_render() {

    global $seat_options;

    $seat_options['seatID_appID'] = isset($seat_options['seatID_appID']) ? $seat_options['seatID_appID'] : ''; ?>

    <span title="Copy here your App ID as provided during your SeatID registration" class="circle-question-mark">?</span>

    <input type='text' id="seatID_appID"  name='seatID_settings[seatID_appID]' value='<?php echo $seat_options['seatID_appID']; ?>'><?php
}

/*
 * To add Account name text box
 */

function seatID_account_render() {

    global $seat_options;

    $seat_options['seatID_account'] = isset($seat_options['seatID_account']) ? $seat_options['seatID_account'] : ''; ?>

    <span title="Insert your company name or any other name that best describes your business or property (If you're a hotel, insert your hotel name. If you're selling tickets for an event, insert the event name)" class="circle-question-mark">?</span>

    <input type='text' id="seatID_account" name='seatID_settings[seatID_account]' value='<?php echo $seat_options['seatID_account']; ?>'><?php
}

/*
 * To add Address, latitude, longitude text box
 */

function seatID_address_render() {

    global $seat_options;

    $seat_options['seatID_address'] = isset($seat_options['seatID_address']) ? $seat_options['seatID_address'] : '';
    $seat_options['seatID_lat'] = isset($seat_options['seatID_lat']) ? $seat_options['seatID_lat'] : '';
    $seat_options['seatID_long'] = isset($seat_options['seatID_long']) ? $seat_options['seatID_long'] : '';  ?>


    <span title="Insert your business address. This is important as it allows us to show your visitors which of their friends and colleagues have been in the same area" class="circle-question-mark">?</span>

    <input type='text' id="seatID_address" name='seatID_settings[seatID_address]' value='<?php echo $seat_options['seatID_address']; ?>'>

    <input type='hidden' id="seatID_lat" name='seatID_settings[seatID_lat]' value='<?php echo $seat_options['seatID_lat']; ?>'>

    <input type='hidden' id="seatID_long" name='seatID_settings[seatID_long]' value='<?php echo $seat_options['seatID_long']; ?>'><?php
}

/*
 * To add Page/Post select box
 */

function seatID_show_page_render() {

    global $seat_options;
    $seat_options['seatID_show_page'] = isset($seat_options['seatID_show_page']) ? $seat_options['seatID_show_page'] : ""; ?>

    <span title="Select to show widget on all posts/pages" class="circle-question-mark">?</span>
    
    <select name='seatID_settings[seatID_show_page]'>

        <option value="both" <?php selected($seat_options['seatID_show_page'], "both"); ?>>Both</option>

        <option value='page' <?php selected($seat_options['seatID_show_page'], "page"); ?>>All Pages</option>

        <option value='post' <?php selected($seat_options['seatID_show_page'], "post"); ?>>All Posts</option>

    </select><?php
}

/*
 * To add Confirmation Page select  box
 */

function seatID_confirm() {

    global $seat_options;
    
    $seat_options['seatID_confirm'] = isset($seat_options['seatID_confirm']) ? $seat_options['seatID_confirm'] : "";    

    // The Query

    $pages = get_pages(array('post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1)); ?>

    <div id="seatid-clone" class="clone-wrapper"><?php
        if (isset($seat_options['seatID_confirm']) && count($seat_options['seatID_confirm']) != 0) {

            foreach ((array) $seat_options['seatID_confirm'] as $confirmpage) { ?>

                    <div class="toclone">        

                        <select name='seatID_settings[seatID_confirm][]'>

                            <option value="" <?php selected($confirmpage, ""); ?>>Select Confirmation Page</option><?php 
                            foreach ($pages as $page) { ?>
                                <option value='<?php echo $page->ID; ?>' <?php selected($confirmpage, $page->ID); ?>><?php echo $page->post_title; ?></option><?php 
                            } ?>

                        </select>

                        <a href="#" class="clone button button-primary">+</a> <!-- Add button -->

                        <a href="#" class="delete button">-</a> <!-- Delete button -->

                    </div><?php
            }
        } else { ?>

            <div class="toclone">        

                <select name='seatID_settings[seatID_confirm][]'>

                    <option value="" <?php selected("", ""); ?>>Select Confirmation Page</option><?php 
                    foreach ($pages as $page) { ?>
                        <option value='<?php echo $page->ID; ?>' ><?php echo $page->post_title; ?></option><?php 
                    } ?>

                </select>

                <a href="#" class="clone button button-primary">+</a>

                <a href="#" class="delete button">-</a>

            </div><?php 
        } ?>

    </div><?php
}

/*
 * Insert text/message before App ID field
 */

function seatID_settings_section_callback() {

    echo __('Insert your App ID that was provided during SeatID registration. If you haven\'t registered yet, please <a target="_blank" href="http://www.seatid.com/register?r=wp" title="register now">register now</a>.', 'seatID');
}

/*
 * To add Confirmation Page message
 */

function seatID_confirmation_section_callback() {

    echo '<h3>Confirmation Page</h3>';

    echo __('Confirmation page that your visitors will see upon completion of the booking 

	or reservation process. The page was originally built either by you or by your booking engine.<br><br>  

	In order to show relevant information to your website visitors, you must define which WordPress page is designated as the confirmation page.

	If you have several confirmation pages, you can add them by pressing the + button below.', 'seatID');
}

/*
 * To add Confirmation select  box
 */

function seatID_options_page() {

    global $pagenow; ?>

    <div id="seatid_wrap" class="wrap">

        <h2>SeatID Social Solutions</h2><?php
        
        /* Show settings updated confirmation message  */
        if (isset($_GET['settings-updated']) && esc_attr($_GET['settings-updated']) == "true")
            echo '<div class="updated" ><p>Settings updated.</p></div>';

        /* Show Setting/DAshboard Tabs  */
        if (isset($_GET['tab']))
            seatID_tabs($_GET['tab']);
        else
            seatID_tabs('setting');

    
        if ($pagenow == 'admin.php' && $_GET['page'] == 'seatid') {

            if (isset($_GET['tab']))
                $tab = $_GET['tab'];
            else
                $tab = 'setting';

            switch ($tab) {

                case 'setting' : ?>
        
                        <form id="seatid" action='options.php' method='post'>

                            <div class="seat_errors"></div>

                            <p>First time here? Read about the <a href="http://www.seatid.com/seatid-for-wordpress/" target="_blank" title="SeatID for WordPress Plugin" >SeatID for WordPress Plugin</a>. If you have any questions please <a href="mailto:support@seatid.com" title="contact us">contact us</a>.</p><?php
                            
                            settings_fields('pluginPage');

                            do_settings_sections('pluginPage');

                            submit_button(); ?>

                            <span class="seat_imp">* Mandatory Fields.</span>

                        </form><?php
                        break;

                case 'dashboard' : ?>
                        <!-- Show Sidebar iframe -->
                        <iframe class="seatid_sideframe" width="100%" height="auto" scrolling="yes" src="https://api.seatid.com/en/dashboard">

                        <p>Your browser does not support iframes.</p>

                        </iframe><?php
                        break;
            }
        }

        /* Show Dashboard Page/iframe */
        if (( $_GET['page'] == 'seatid' && !isset($_GET['tab']) ) || ( isset($_GET['tab']) && $_GET['tab'] != "dashboard" )) { ?>

                <iframe class="seatid_sideframe" width="29%" height="auto" scrolling="no" src="http://www.seatid.com/wordpresssettingspageiframe/">

                <p>Your browser does not support iframes.</p>

                </iframe><?php             
        } ?>

    </div>
    
    <!-- Load Scripts on plugin page only -->

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
    <script src= "<?php echo plugins_url('/js/jquery-cloneya.min.js', __FILE__); ?>"></script><?php   
    
    wp_enqueue_script( 'seatid_js', plugin_dir_url( __FILE__ ) . 'js/custom.js' ); // Custom JS

}

/* 
 * Show Setting/Dashboard Tabs
 */
function seatID_tabs($current = 'setting') {

    $tabs = array('setting' => 'Settings', 'dashboard' => 'Dashboard');

    $links = array();

    echo '<div id="icon-themes" class="icon32"><br></div>';

    echo '<h2 class="nav-tab-wrapper">';

    foreach ($tabs as $tab => $name) {

        $class = ( $tab == $current ) ? ' nav-tab-active' : '';

        echo "<a class='nav-tab$class' href='?page=seatid&tab=$tab'>$name</a>";
    }

    echo '</h2>';
}

/*
 *  To strip html,php tags & validate the data
 */
function seatid_validation($input) {

    //echo '<pre>';print_r($input);exit;
    // Create our array for storing the validated options

    $output = $input;


    $output['seatID_appID'] = strip_tags(trim($input['seatID_appID']));
    if (is_array($input['seatID_confirm']) && count($input['seatID_confirm']) != 0) {

        $output['seatID_confirm'] = array();

        foreach ($input['seatID_confirm'] as $confirm) {

            if ($confirm != "") {

                $output['seatID_confirm'][] = $confirm;
            }
        }
    }

    // Return the array processing any additional functions filtered by this action

    return apply_filters('seatid_validation', $output, $input);
}

/* 
 * Function to run main 
 * seaitID widget JS code with 
 * dynamic values entered by user in backend
 */
function seatid_script() {

    $seat_options = get_option('seatID_settings');

    /* Return if App ID is not set */
    if (!isset($seat_options['seatID_appID']) || (isset($seat_options['seatID_appID']) && trim($seat_options['seatID_appID']) == "" ))
        return;

    /* Return if widget not to be displayed on Post */
    if (isset($seat_options['seatID_show_page'])) {
        if ($seat_options['seatID_show_page'] == "page" && is_single())
            return; // If all pages is set & not to show on single post            
    }

    $confirm_page = "false";

    if (isset($seat_options['seatID_confirm']) && count($seat_options['seatID_confirm']) != 0) {

        /* Return if widget not to be displayed on page and also not a confirmation page */
        if ($seat_options['seatID_show_page'] == "post" && is_page() && !in_array(get_the_ID(), $seat_options['seatID_confirm']))
            return; // If all pages is set & not to show on single post            

        /* Set value true on confirmation page */
        if (in_array(get_the_ID(), $seat_options['seatID_confirm'])) {
            $confirm_page = "true";
        }
    } ?>

    <!-- SeatID code - Begin -->

    <script type="text/javascript" src="https://api.seatid.com/assets/widget/seat_id_widget.js" id="seat_id_assignments_widget_script"

    data-appid = <?php echo $seat_options['seatID_appID']; ?>></script>

    <script>

        SeatID.ready(function() {
            SeatID.displaySideBar({
                assignment_type: "hotel_booking",
                chain_name: "SeatID_For_WP",
                name: "<?php echo $seat_options['seatID_account']; ?>",
                latitude: <?php echo $seat_options['seatID_lat']; ?>,
                longitude: <?php echo $seat_options['seatID_long']; ?>,
                start_date: "2222-02-02T00:00:00Z",
                end_date: "3333-03-03T00:00:00Z",
            }, <?php echo $confirm_page; ?>)
        });

    </script>

    <!-- SeatID code - End -->
    <?php
}

/* 
 * Function to set cookie to hide 
 * register message for one hour
 */
function seatid_cookie() {
          
    if (false !== strpos($_SERVER['QUERY_STRING'], 'seatid-notice=dismiss')) {

        if (!isset($_COOKIE['seatid_dismiss'])) {

            setcookie('seatid_dismiss', 'hide', time() + 3600);
        }
    }

    if (isset($_REQUEST['seatid_reset'])) {

        update_option('seatID_settings', false);
    }
}

/*
 * Function to display/hide register notice
 */
function seatid_register_notice() {

    global $seat_options;
    settings_errors('seat_id');

    if (!current_user_can('manage_options'))
        return;

    /* IF user has pressed close button on message or Cookie is set then remove it */
    if (false !== strpos($_SERVER['QUERY_STRING'], 'seatid-notice=dismiss') || ( isset($_COOKIE['seatid_dismiss']) && $_COOKIE['seatid_dismiss'] == "hide" )) {
        return;
    }

    /* IF App id is added, remove this register notice */
    
    if (isset($seat_options['seatID_appID']) && trim($seat_options['seatID_appID']) != "")
        return;

    $dismiss_and_deactivate_url = wp_nonce_url(add_query_arg('seatid-notice', 'dismiss'), 'seatid-deactivate'); ?>

    <div id="seatid_message" class="updated seatid-message">

        <div id="seatid-dismiss" class="seatid-close-button-container">

            <a class="seatid-close-button" href="<?php echo esc_url($dismiss_and_deactivate_url); ?>" title="<?php _e('Dismiss this notice.', 'seatid'); ?>"></a>

        </div>

        <div class="seatid-wrap-container">

            <div class="seatid-register-container">

                <a href="http://www.seatid.com/register?r=wp" target="_blank" class="button button-primary button-hero"><?php _e('Register to SeatID', 'seatid'); ?></a>

            </div>

            <div class="seatid-text-container">					

                <p><?php _e('<strong>Your SeatID app is almost ready!</strong>', 'seatid'); ?></p>

                <p class="small"><?php _e('Register now on <a href="http://www.seatid.com/register?r=wp" target="_blank" class="seat_register">www.seatid.com/register</a> to get your App ID.', 'seatid'); ?></p>

            </div>

        </div>

    </div> <?php
}

/* 
 * Adding SeatID setting link 
 * in plugin page
 */
function seatid_settings_link($links) {
    $url = get_admin_url() . 'admin.php?page=seatid';
    $settings_link = '<a href="'.$url.'">' . __( 'Settings', 'textdomain' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
?>