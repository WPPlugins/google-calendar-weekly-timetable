<?php
/*
Plugin Name: Google Calendar Weekly Timetable
Plugin URI: http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/
Description: Displays Google Calendar(s) as custom styled weekly timetable/schedule in your blog. Easy to manage complex timetables from google calendar.
Version: 0.3.3
Author: aurimus
Author URI: http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/donate
License: GPL2

---

Copyright 2010 Aurimas Kubeldzis (email: 4urimas@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('GCWT_PLUGIN_NAME', str_replace('.php', '', basename(__FILE__)));
define('GCWT_TEXT_DOMAIN', 'google-calendar-weekly-timetable');
define('GCWT_OPTIONS_NAME', 'gcwt_options');
define('GCWT_GENERAL_OPTIONS_NAME', 'gcwt_general');

//require_once 'widget/gce-widget.php';
require_once 'inc/gcwt-parser.php';

if(!class_exists('Google_Calendar_Weekly_Timetable')){
	class Google_Calendar_Weekly_Timetable{
		//PHP 4 constructor
		function Google_Calendar_Weekly_Timetable(){
			$this->__construct();
		}

		//PHP 5 constructor
		function __construct(){
			add_action('activate_google-calendar-weekly-timetable/google-calendar-weekly-timetable.php', array($this, 'activate_plugin'));
			add_action('init', array($this, 'init_plugin'));
			add_action('admin_menu', array($this, 'setup_admin'));
			add_action('admin_init', array($this, 'init_admin'));
			add_action('wp_print_styles', array($this, 'add_styles'));
			//add_action('wp_print_scripts', array($this, 'add_scripts'));
			//add_action('widgets_init', create_function('', 'return register_widget("GCWT_Widget");'));
			//add_action('wp_ajax_gcwt_ajax', array($this, 'gcwt_ajax'));
			//add_action('wp_ajax_nopriv_gcwt_ajax', array($this, 'gcwt_ajax'));
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
			add_shortcode('gc-timetable', array($this, 'shortcode_handler'));
			add_shortcode('timetable', array($this, 'shortcode_handler'));
		}

		//If any new options have been added between versions, this will update any saved feeds with defaults for new options (shouldn't overwrite anything saved)
		//Will do the same for general options
		function activate_plugin(){
			add_option(GCWT_OPTIONS_NAME);
			add_option(GCWT_GENERAL_OPTIONS_NAME);

			//Get feed options
			$options = get_option(GCWT_OPTIONS_NAME);

			if(!empty($options)){
				foreach($options as $key => $saved_feed_options){
					$defaults = array(
						'id' => 1, 
						'title' => '',
						'url' => '',
						//'show_past_events' => 'false',
						//'max_events' => 25,
						//'day_limit' => 7,
						//'date_format' => '',
						//'time_format' => '',
						'timezone' => 'default',
						'cache_duration' => 43200,
						//'multiple_day' => 'false',
						'display_color' => 'DarkOliveGreen',
						//'display_start' => 'time',
						//'display_end' => 'time-date',
						//'display_location' => '',
						//'display_desc' => '',
						//'display_link' => 'on',
						//'display_start_text' => 'Starts:',
						//'display_end_text' => 'Ends:',
						//'display_location_text' => 'Location:',
						//'display_desc_text' => 'Description:',
						//'display_desc_limit' => '',
						//'display_link_text' => 'More details',
						//'display_link_target' => '',
						//'display_separator' => ', '
					);

					//Update old display_start / display_end values
					//if(!isset($saved_feed_options['display_start']))
					//	$saved_feed_options['display_start'] = 'none';
					//elseif($saved_feed_options['display_start'] == 'on')
					//	$saved_feed_options['display_start'] = 'time';

					//if(!isset($saved_feed_options['display_end']))
					//	$saved_feed_options['display_end'] = 'none';
					//elseif($saved_feed_options['display_end'] == 'on')
					//	$saved_feed_options['display_end'] = 'time-date';

					//Merge saved options with defaults
					foreach($saved_feed_options as $option_name => $option){
						$defaults[$option_name] = $saved_feed_options[$option_name];
					}

					$options[$key] = $defaults;
				}
			}

			//Save feed options
			update_option(GCWT_OPTIONS_NAME, $options);

			//Get general options
			$options = get_option(GCWT_GENERAL_OPTIONS_NAME);

			$defaults = array(
				'stylesheet' => '',
				'refresh_number' => 1,
				//'javascript' => false,
				//'loading' => 'Loading...'
			);

			$old_stylesheet_option = get_option('gcwt_stylesheet');

			//If old custom stylesheet options was set, add it to general options, then delete old option
			if($old_stylesheet_option !== false){
				$defaults['stylesheet'] = $old_stylesheet_option;
				delete_option('gcwt_stylesheet');
			}elseif(isset($options['stylesheet'])){
				$defaults['stylesheet'] = $options['stylesheet'];
			}

			//if(isset($options['javascript'])) $defaults['javascript'] = $options['javascript'];
			//if(isset($options['loading'])) $defaults['loading'] = $options['loading'];

			//Save general options
			update_option(GCWT_GENERAL_OPTIONS_NAME, $defaults);
		}

		function init_plugin(){
			//Load text domain for i18n
			load_plugin_textdomain(GCWT_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
			if(get_option('timezone_string') != '') date_default_timezone_set(get_option('timezone_string'));
		}

		//Adds 'Settings' link to main WordPress Plugins page
		function add_settings_link($links){
			array_unshift($links, '<a href="options-general.php?page=google-calendar-weekly-timetable.php">' . __('Settings', GCWT_TEXT_DOMAIN) . '</a>');
			return $links;
		}

		//Setup admin settings page
		function setup_admin(){
			if(function_exists('add_options_page')) add_options_page('Google Calendar Weekly Timetable', 'Google Calendar Weekly Timetable', 'manage_options', basename(__FILE__), array($this, 'admin_page'));
		}

		//Prints admin settings page
		function admin_page(){
			//Add correct updated message (added / edited / deleted)
			if(isset($_GET['updated'])){
				switch($_GET['updated']){
					case 'added':
						?><div class="updated"><p><strong><?php _e('New Feed Added Successfully.', GCWT_TEXT_DOMAIN); ?></strong></p></div><?php
						break;
					case 'edited':
						?><div class="updated"><p><strong><?php _e('Feed Details Updated Successfully.', GCWT_TEXT_DOMAIN); ?></strong></p></div><?php
						break;
					case 'deleted':
						?><div class="updated"><p><strong><?php _e('Feed Deleted Successfully.', GCWT_TEXT_DOMAIN); ?></strong></p></div><?php
				}
			}?>

			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br /></div>

				<h2><?php _e('Google Calendar Weekly Timetable', GCWT_TEXT_DOMAIN); ?></h2>
				<form method="post" action="options.php" id="test-form">
					<?php
					if(isset($_GET['action'])){
						switch($_GET['action']){
							//Add feed section
							case 'add':
								settings_fields('gcwt_options');
								do_settings_sections('add_feed_wt');
								do_settings_sections('add_display_wt');
								?><p class="submit"><input type="submit" class="button-primary submit" value="<?php _e('Add Feed', GCWT_TEXT_DOMAIN); ?>" /></p>
								<p><a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php'); ?>" class="button-secondary"><?php _e('Cancel', GCWT_TEXT_DOMAIN); ?></a></p><?php
								break;
							//Edit feed section
							case 'edit':
								settings_fields('gcwt_options');
								do_settings_sections('edit_feed_wt');
								do_settings_sections('edit_display_wt');
								?><p class="submit"><input type="submit" class="button-primary submit" value="<?php _e('Save Changes', GCWT_TEXT_DOMAIN); ?>" /></p>
								<p><a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php'); ?>" class="button-secondary"><?php _e('Cancel', GCWT_TEXT_DOMAIN); ?></a></p><?php
								break;
							//Delete feed section
							case 'delete':
								settings_fields('gcwt_options');
								do_settings_sections('delete_feed_wt');
								?><p class="submit"><input type="submit" class="button-primary submit" name="gcwt_options[submit_delete]" value="<?php _e('Delete Feed', GCWT_TEXT_DOMAIN); ?>" /></p>
								<p><a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php'); ?>" class="button-secondary"><?php _e('Cancel', GCWT_TEXT_DOMAIN); ?></a></p><?php
						}
					}else{
						//Main admin section
						settings_fields('gcwt_general');
						require_once 'admin/main.php';
					}
					?>
				</form>
			</div>
		<?php
		}

		//Initialize admin stuff
		function init_admin(){
			register_setting('gcwt_options', 'gcwt_options', array($this, 'validate_feed_options'));
			register_setting('gcwt_general', 'gcwt_general', array($this, 'validate_general_options'));

			require_once 'admin/add.php';
			require_once 'admin/edit.php';
			require_once 'admin/delete.php';
		}

		//Check / validate submitted feed options data before being stored
		function validate_feed_options($input){
			//Get saved options
			$options = get_option(GCWT_OPTIONS_NAME);

			if(isset($input['submit_delete'])){
				//If delete button was clicked, delete feed from options array
				unset($options[$input['id']]);
			}else{
				//Otherwise, validate options and add / update them

				//Check id is positive integer
				$id = absint($input['id']);
				//Escape title text
				$title = esc_html($input['title']);
				//Escape feed url old version (before https fix)
				//$url = esc_url($input['url']);
				//Escape feed url. Replace https:// with http:// as SimplePie doesn't seem to support https:// Google Calendar URLs at present
				$url = str_replace('https://', 'http://', esc_url($input['url']));
				
				/*
				//Make sure show past events is either true of false
				$show_past_events = (isset($input['show_past_events']) ? 'true' : 'false');
				//Check max events is a positive integer. If absint returns 0, reset to default (25)
				$max_events = (absint($input['max_events']) == 0 ? 25 : absint($input['max_events']));
				//Check day limit is a positive integer. If not (or 0) set to ''
				$day_limit = absint($input['day_limit']) == 0 ? '' : absint($input['day_limit']);

				$date_format = wp_filter_kses($input['date_format']);
				$time_format = wp_filter_kses($input['time_format']);
				*/
				
				//Escape timezone
				$timezone = esc_html($input['timezone']);

				//Make sure cache duration is a positive integer or 0. If user has typed 0, leave as 0 but if 0 is returned from absint, set to default (43200)
				$cache_duration = $input['cache_duration'];
				if($cache_duration != '0') $cache_duration = (absint($cache_duration) == 0 ? 43200 : absint($cache_duration));
				/*
				$multiple_day = (isset($input['multiple_day']) ? 'true' : 'false');*/
				
				$display_color = $input['display_color'];

				/*
				$display_start = esc_html($input['display_start']);
				$display_end = esc_html($input['display_end']);

				//Display options must be 'on' or null
				$display_location = (isset($input['display_location']) ? 'on' : null);
				$display_desc = (isset($input['display_desc']) ? 'on' : null);
				$display_link = (isset($input['display_link']) ? 'on' : null);
				$display_link_target = (isset($input['display_link_target']) ? 'on' : null);

				//Filter display text
				$display_start_text = wp_filter_kses($input['display_start_text']);
				$display_end_text = wp_filter_kses($input['display_end_text']);
				$display_location_text = wp_filter_kses($input['display_location_text']);
				$display_desc_text = wp_filter_kses($input['display_desc_text']);
				$display_link_text = wp_filter_kses($input['display_link_text']);

				$display_separator = wp_filter_kses($input['display_separator']);

				$display_desc_limit = absint($input['display_desc_limit']) == 0 ? '' : absint($input['display_desc_limit']);
				*/
				//Fill options array with validated values
				$options[$id] = array(
					'id' => $id, 
					'title' => $title,
					'url' => $url,
					/*'show_past_events' => $show_past_events,
					'max_events' => $max_events,
					'day_limit' => $day_limit,
					'date_format' => $date_format,
					'time_format' => $time_format,*/
					'timezone' => $timezone,
					'cache_duration' => $cache_duration,
					//'multiple_day' => $multiple_day,
					'display_color' => $display_color,
					/*'display_start' => $display_start,
					'display_end' => $display_end,
					'display_location' => $display_location,
					'display_desc' => $display_desc,
					'display_link' => $display_link,
					'display_start_text' => $display_start_text,
					'display_end_text' => $display_end_text,
					'display_location_text' => $display_location_text,
					'display_desc_text' => $display_desc_text,
					'display_desc_limit' => $display_desc_limit,
					'display_link_text' => $display_link_text,
					'display_link_target' => $display_link_target,
					'display_separator' => $display_separator*/
				);
			}

			return $options;
		}

		//Validate submitted general options
		function validate_general_options($input){
			$options = get_option(GCWT_GENERAL_OPTIONS_NAME);

			$options['stylesheet'] = esc_url($input['stylesheet']);
			//$options['javascript'] = (isset($input['javascript']) ? true : false);
			//$options['loading'] = esc_html($input['loading']);

			return $options;
		}
		
		//Return array of right language table headline
		function week_days_in_language($lang_shortcode){
			$myFile = "weekdays.txt";
			$fh = fopen($myFile, 'r', true);
			
			
			do{
				$line_of_text = fgets($fh);
				$parts = explode(':', $line_of_text);
			} while ((!feof($fh)) && ($parts[0] <> $lang_shortcode));
			
			if ($parts[0] == $lang_shortcode){
				return explode(' ', $parts[1]);
			} 
			else {
				return 1;
			}

		}

		//Handles the shortcode stuff
		function shortcode_handler($atts, $wrapped_content, $thetag){
			$options = get_option(GCWT_OPTIONS_NAME);

			//Check that any feeds have been added
			if(is_array($options) && !empty($options)){
				extract(shortcode_atts(array(
					'id' => null,
					'type' => 'grid',
					'lang' => null,
					'time_format' => null, //are you sure $time_format is not reserved?
					'start_sunday' => null,
					'show_sunday' => null,
					'start' => null,
					'append' => '',
					'caption' => null,
					'timezone' => null,
					'cache_duration' => null,
					'refresh_one' => null, 
					'rowspan' => true,
					'title' => false,
					'max' => 0
				), $atts));
				
				if ($refresh_one == 0) $refresh_one = null;
				
				//Do a couple of things differently depending on the shortcode called
				if($thetag == 'timetable' && $id == null){
					// If there are no feeds, then put them all
					$feed_ids = array();
					foreach($options as $feed_id => $feed){
						$feed_ids[] = $feed_id;
					}					
				}elseif ($id == null){
					// If there are no feeds, then make it think it's the first one
						$feed_ids = array();
						$feed_ids[] = 1;
						
				}else{
					//Break comma delimited list of feed ids into array
					$feed_ids = explode(',', str_replace(' ', '', $id));
				}
				
				
				// Set timezone if user requests, otherwise, try to use wordpress settings
				if ($timezone == null) $timezone = get_option('timezone_string');
				date_default_timezone_set($timezone);
				
				//Put sunday options into an array. show_sunday can be null, false or true accordingly for 1)show if there are events 2)don't 3)show
				if ($start_sunday == null) $start_sunday = !(get_option('start_of_week'));
				$sunday_opt = array('start' => $start_sunday, 'show' => $show_sunday);
				
				//Did user request time format? if so then set it, otherwise use wordpress settings
				if ($time_format == null) $time_format = get_option('time_format');
				
				//If start was not specified, set it to the next weeks start day (Monday or Sunday)
				if ($start == null) $start = $start_sunday?'Sunday':'Monday';
				

				//Check each id is an integer, if not, remove it from the array
				foreach($feed_ids as $key => $feed_id){
					if(absint($feed_id) == 0) unset($feed_ids[$key]);
				}

				$no_feeds_exist = true;

				//If at least one of the feed ids entered exists, set no_feeds_exist to false
				foreach($feed_ids as $feed_id){
					if(isset($options[$feed_id])) $no_feeds_exist = false;
				}

				//Ensure max events is a positive integer
				$max_events = absint($max);
				
				//Load all the language variations of table headline
				if ($lang <> null){
					$week_days = $this->week_days_in_language($lang);
					if ($week_days == 1) $lang = null;
				}

				//Check that at least one valid feed id has been entered
				if(count((array)$feed_ids) == 0 || $no_feeds_exist){
					return __('No valid Feed IDs have been entered for this shortcode. Please check that you have entered the IDs correctly and that the Feeds have not been deleted.', GCWT_TEXT_DOMAIN);
				}else{
					//Turn feed_ids back into string or feed ids delimited by '-' ('1-2-3-4' for example)
					$feed_ids = implode('-', $feed_ids);

					//If title has been omitted from shortcode, set title_text to null, otherwise set to title (even if empty string)
					$title_text = ($title === false ? null : $title);

					
					return gcwt_print_table($feed_ids, $title_text, $max_events, false, $week_days, $time_format, $sunday_opt, $rowspan, $start, $append, $cache_duration, $thetag, $caption, $lang, $refresh_one);
				}
			}else{
				return __('From shortcode handler: No feeds have been added yet. You can add a feed in the Google Calendar Events settings.', GCWT_TEXT_DOMAIN);
			}
		}

		//Adds the required CSS
		function add_styles(){
			//Don't add styles if on admin screens
			if(!is_admin()){
				//If user has entered a URL to a custom stylesheet, use it. Otherwise use the default
				$options = get_option(GCWT_GENERAL_OPTIONS_NAME);
				if(isset($options['stylesheet']) && ($options['stylesheet'] != '')){
					wp_enqueue_style('gcwt_styles', $options['stylesheet']);
				}else{
					wp_enqueue_style('gcwt_styles', WP_PLUGIN_URL . '/' . GCWT_PLUGIN_NAME . '/css/gcwt-style.css');
				}
			}
		}

		//Adds the required scripts
		/*
		function add_scripts(){
			//Don't add scripts if on admin screens
			if(!is_admin()){
				$options = get_option(GCWT_GENERAL_OPTIONS_NAME);
				$add_to_footer = (bool)$options['javascript'];

				wp_enqueue_script('jquery');
				wp_enqueue_script('gcwt_jquery_qtip', WP_PLUGIN_URL . '/' . GCWT_PLUGIN_NAME . '/js/jquery-qtip.js', array('jquery'), null, $add_to_footer);
				wp_enqueue_script('gcwt_scripts', WP_PLUGIN_URL . '/' . GCWT_PLUGIN_NAME . '/js/gcwt-script.js', array('jquery'), null, $add_to_footer);
				wp_localize_script('gcwt_scripts', 'GoogleCalendarEvents', array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'loading' => $options['loading']
				));
			}
		}*/

		//AJAX stuffs
		/*function gcwt_ajax(){
			if(isset($_GET['gcwt_feed_ids'])){
				if($_GET['gcwt_type'] == 'page'){
					//The page grid markup to be returned via AJAX
					echo gcwt_print_grid($_GET['gcwt_feed_ids'], $_GET['gcwt_title_text'], $_GET['gcwt_max_events'], true, $_GET['gcwt_month'], $_GET['gcwt_year']);
				}elseif($_GET['gcwt_type'] == 'widget'){
					//The widget grid markup to be returned via AJAX
					gcwt_widget_content_grid($_GET['gcwt_feed_ids'], $_GET['gcwt_title_text'], $_GET['gcwt_max_events'], $_GET['gcwt_widget_id'], true, $_GET['gcwt_month'], $_GET['gcwt_year']);
				}
				die();
			}
		}*/
	}
}



function gcwt_print_table($feed_ids, $title_text, $max_events, $grouped = false, $week_days_names, $time_format, $sunday_opt, $rowspan, $start, $append, $cache_duration, $thetag, $caption, $lang, $refresh_one){
	//Create new GCWT_Parser object, passing array of feed id(s)
	$table = new GCWT_Parser(explode('-', $feed_ids), $title_text, $max_events, $start, $cache_duration, $refresh_one);
	//If the feed(s) parsed ok, return the table markup, otherwise return an error message
	if(count($table->get_errors()) == 0){
		return '<div class="gcwt-table">' . $table->get_table($grouped, $week_days_names, $time_format, $sunday_opt, $rowspan, $append, $thetag, $caption, $lang) . '</div>';
	}else{
		return sprintf(__('The following feeds were not parsed successfully: %s. Please check that the feed URLs are correct and that the feeds have public sharing enabled.'), implode(', ', $table->get_errors()));
	}
}



$gce = new Google_Calendar_Weekly_Timetable();
?>