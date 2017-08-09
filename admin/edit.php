<?php
//Redirect to the main plugin options page if form has been submitted
if(isset($_GET['action'])){
	if($_GET['action'] == 'edit' && isset($_GET['updated'])){
		wp_redirect(admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php&updated=edited'));
	}
}

add_settings_section('gcwt_edit', __('Edit Feed', GCWT_TEXT_DOMAIN), 'gcwt_edit_main_text', 'edit_feed_wt');
//Unique ID                                           //Title                                                                     //Function                         //Page       //Section ID
add_settings_field('gcwt_edit_id_field',               __('Feed ID', GCWT_TEXT_DOMAIN),                                             'gcwt_edit_id_field',               'edit_feed_wt', 'gcwt_edit');

add_settings_field('gcwt_edit_title_field',            __('Feed Title', GCWT_TEXT_DOMAIN),                                          'gcwt_edit_title_field',            'edit_feed_wt', 'gcwt_edit');
add_settings_field('gcwt_edit_url_field',              __('Feed URL', GCWT_TEXT_DOMAIN),                                            'gcwt_edit_url_field',              'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_show_past_events_field', __('Retrieve past events for current month?', GCWT_TEXT_DOMAIN),             'gcwt_edit_show_past_events_field', 'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_max_events_field',       __('Maximum number of events to retrieve', GCWT_TEXT_DOMAIN),                'gcwt_edit_max_events_field',       'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_day_limit_field',        __('Number of days in the future to retrieve events for', GCWT_TEXT_DOMAIN), 'gcwt_edit_day_limit_field',        'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_date_format_field',      __('Date format', GCWT_TEXT_DOMAIN),                                         'gcwt_edit_date_format_field',      'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_time_format_field',      __('Time format', GCWT_TEXT_DOMAIN),                                         'gcwt_edit_time_format_field',      'edit_feed_wt', 'gcwt_edit');
add_settings_field('gcwt_edit_timezone_field',         __('Timezone adjustment', GCWT_TEXT_DOMAIN),                                 'gcwt_edit_timezone_field',         'edit_feed_wt', 'gcwt_edit');
add_settings_field('gcwt_edit_cache_duration_field',   __('Cache duration', GCWT_TEXT_DOMAIN),                                      'gcwt_edit_cache_duration_field',   'edit_feed_wt', 'gcwt_edit');
//add_settings_field('gcwt_edit_multiple_field',         __('Show multiple day events on each day?', GCWT_TEXT_DOMAIN),               'gcwt_edit_multiple_field',         'edit_feed_wt', 'gcwt_edit');

add_settings_section('gcwt_edit_display_wt', __('Display Options', GCWT_TEXT_DOMAIN), 'gcwt_edit_display_wt_main_text', 'edit_display_wt');
add_settings_field('gcwt_edit_display_wt_color_field',             __('Feed Color', GCWT_TEXT_DOMAIN),                                          'gcwt_edit_display_wt_color_field',               'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_start_field',     __('Display start time / date?', GCWT_TEXT_DOMAIN),         'gcwt_edit_display_wt_start_field',     'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_end_field',       __('Display end time / date?', GCWT_TEXT_DOMAIN),  'gcwt_edit_display_wt_end_field',       'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_separator_field', __('Separator text / characters', GCWT_TEXT_DOMAIN), 'gcwt_edit_display_wt_separator_field', 'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_location_field',  __('Display location?', GCWT_TEXT_DOMAIN),           'gcwt_edit_display_wt_location_field',  'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_desc_field',      __('Display description?', GCWT_TEXT_DOMAIN),        'gcwt_edit_display_wt_desc_field',      'edit_display_wt', 'gcwt_edit_display_wt');
//add_settings_field('gcwt_edit_display_wt_link_field',      __('Display link to event?', GCWT_TEXT_DOMAIN),      'gcwt_edit_display_wt_link_field',      'edit_display_wt', 'gcwt_edit_display_wt');

//Main text
function gcwt_edit_main_text(){
	?>
	<p><?php _e('Make any changes you require to the feed details below, then click the Save Changes button.', GCWT_TEXT_DOMAIN); ?></p>
	<?php
}

//ID
function gcwt_edit_id_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="text" disabled="disabled" value="<?php echo $options['id']; ?>" size="3" />
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#feedid">?</a>]
	<input type="hidden" name="gcwt_options[id]" value="<?php echo $options['id']; ?>" />
	<?php
}

//Title
function gcwt_edit_title_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Anything you like. \'Upcoming Club Events\', for example.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[title]" value="<?php echo $options['title']; ?>" size="50" />
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#feedtitle">?</a>]
	<?php
}

//URL
function gcwt_edit_url_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('This will probably be something like: <code>http://www.google.com/calendar/feeds/your-email@gmail.com/public/full</code>.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[url]" value="<?php echo $options['url']; ?>" size="100" />
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#feedurl">?</a>]
	<?php
}

//Show past events 
/*
function gcwt_edit_show_past_events_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('If checked, events will be retrieved from the first of this month onwards. If unchecked, events will be retrieved from today onwards.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="checkbox" name="gcwt_options[show_past_events]" value="true"<?php checked($options['show_past_events'], 'true'); ?> />
	<?php
}

//Max events
function gcwt_edit_max_events_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Set this to a few more than you actually want to display (due to caching and timezone issues). The exact number to display can be configured per shortcode / widget.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[max_events]" value="<?php echo $options['max_events']; ?>" size="3" />
	<?php
}

//Day limit
function gcwt_edit_day_limit_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('The number of days in the future to retrieve events for (from 12:00am today). Leave blank for no day limit.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" disabled="disabled" name="gcwt_options[day_limit]" value="<?php echo $options['day_limit']; ?>" size="3" />
	<?php
}

//Date format
function gcwt_edit_date_format_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('In <a href="http://php.net/manual/en/function.date.php">PHP date format</a>. Leave this blank if you\'d rather stick with the default format for your blog.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[date_format]" value="<?php echo $options['date_format']; ?>" />
	<?php
}

//Time format
function gcwt_edit_time_format_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('In <a href="http://php.net/manual/en/function.date.php">PHP date format</a>. Again, leave this blank to stick with the default.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[time_format]" value="<?php echo $options['time_format']; ?>" />
	<?php
}*/

//Timezone offset
function gcwt_edit_timezone_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	require_once 'timezone-choices.php';
	$timezone_list = gcwt_get_timezone_choices();
	//Set selected="selected" for selected timezone
	$timezone_list = str_replace(('<option value="' . $options['timezone'] . '"'), ('<option value="' . $options['timezone'] . '" selected="selected"'), $timezone_list);
	?>
	<span class="description"><?php _e('If you are having problems with dates and times displaying in the wrong timezone, select a city in your required timezone here.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<?php echo $timezone_list; ?>
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#timezone">?</a>]
	<?php
}

//Cache duration
function gcwt_edit_cache_duration_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('The length of time, in seconds, to cache the feed (43200 = 12 hours). If this feed changes regularly, you may want to reduce the cache duration.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[cache_duration]" value="<?php echo $options['cache_duration']; ?>" />
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#cache">?</a>]
	<?php
}
/*
//Multiple day events
function gcwt_edit_multiple_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Show events that span multiple days on each day that they span (There are some <a href="http://www.rhanney.co.uk/2010/08/19/google-calendar-events-0-4#multiday">limitations</a> of this feature to be aware of).', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="checkbox" name="gcwt_options[multiple_day]" value="true"<?php checked($options['multiple_day'], 'true'); ?> />
	<br /><br />
	<?php
}
*/

//Display options

function gcwt_edit_display_wt_main_text(){
	?>
	<p><?php _e('These settings control how this feed will appear in timetable.', GCWT_TEXT_DOMAIN); ?></p>
	<p><?php _e('', GCWT_TEXT_DOMAIN); ?></p>
	<?php
}

function gcwt_edit_display_wt_color_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Enter color you want this feed to appear', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_color]" value="<?php echo $options['display_color']; ?>" />
	&nbsp;[<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#color">?</a>]
	<?php
}
/*
function gcwt_edit_display_wt_start_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Select how to display the start date / time.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<select name="gcwt_options[display_start]">
		<option value="none"<?php selected($options['display_start'], 'none'); ?>><?php _e('Don\'t display start time or date', GCWT_TEXT_DOMAIN); ?></option>
		<option value="time"<?php selected($options['display_start'], 'time'); ?>><?php _e('Display start time', GCWT_TEXT_DOMAIN); ?></option>
		<option value="date"<?php selected($options['display_start'], 'date'); ?>><?php _e('Display start date', GCWT_TEXT_DOMAIN); ?></option>
		<option value="time-date"<?php selected($options['display_start'], 'time-date'); ?>><?php _e('Display start time and date (in that order)', GCWT_TEXT_DOMAIN); ?></option>
		<option value="date-time"<?php selected($options['display_start'], 'date-time'); ?>><?php _e('Display start date and time (in that order)', GCWT_TEXT_DOMAIN); ?></option>
	</select>
	<br /><br />
	<span class="description"><?php _e('Text to display before the start time.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_start_text]" value="<?php echo $options['display_start_text']; ?>" />
	<?php
}

function gcwt_edit_display_wt_end_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('Select how to display the end date / time.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<select name="gcwt_options[display_end]">
		<option value="none"<?php selected($options['display_end'], 'none'); ?>><?php _e('Don\'t display end time or date', GCWT_TEXT_DOMAIN); ?></option>
		<option value="time"<?php selected($options['display_end'], 'time'); ?>><?php _e('Display end time', GCWT_TEXT_DOMAIN); ?></option>
		<option value="date"<?php selected($options['display_end'], 'date'); ?>><?php _e('Display end date', GCWT_TEXT_DOMAIN); ?></option>
		<option value="time-date"<?php selected($options['display_end'], 'time-date'); ?>><?php _e('Display end time and date (in that order)', GCWT_TEXT_DOMAIN); ?></option>
		<option value="date-time"<?php selected($options['display_end'], 'date-time'); ?>><?php _e('Display end date and time (in that order)', GCWT_TEXT_DOMAIN); ?></option>
	</select>
	<br /><br />
	<span class="description"><?php _e('Text to display before the end time.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_end_text]" value="<?php echo $options['display_end_text']; ?>" />
	<?php
}

function gcwt_edit_display_wt_separator_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<span class="description"><?php _e('If you have chosen to display both the time and date above, enter the text / characters to display between the time and date here (including any spaces).', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_separator]" value="<?php echo $options['display_separator']; ?>" />
	<?php
}

function gcwt_edit_display_wt_location_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="checkbox" name="gcwt_options[display_location]"<?php checked($options['display_location'], 'on'); ?> value="on" />
	<span class="description"><?php _e('Show the location of events?', GCWT_TEXT_DOMAIN); ?></span>
	<br /><br />
	<span class="description"><?php _e('Text to display before the location.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_location_text]" value="<?php echo stripslashes(esc_html($options['display_location_text'])); ?>" />
	<?php
}

function gcwt_edit_display_wt_desc_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="checkbox" name="gcwt_options[display_desc]"<?php checked($options['display_desc'], 'on'); ?> value="on" />
	<span class="description"><?php _e('Show the description of events?  (URLs in the description will be made into links).', GCWT_TEXT_DOMAIN); ?></span>
	<br /><br />
	<span class="description"><?php _e('Text to display before the description.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_desc_text]" value="<?php echo stripslashes(esc_html($options['display_desc_text'])); ?>" />
	<br /><br />
	<span class="description"><?php _e('Maximum number of words to show from description. Leave blank for no limit.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_desc_limit]" value="<?php echo $options['display_desc_limit']; ?>" size="3" />
	<?php
}

function gcwt_edit_display_wt_link_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="checkbox" name="gcwt_options[display_link]"<?php checked($options['display_link'], 'on'); ?> value="on" />
	<span class="description"><?php _e('Show a link to the Google Calendar page for an event?', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="checkbox" name="gcwt_options[display_link_target]"<?php checked($options['display_link_target'], 'on'); ?> value="on" />
	<span class="description"><?php _e('Links open in a new window / tab?', GCWT_TEXT_DOMAIN); ?></span>
	<br /><br />
	<span class="description"><?php _e('The link text to be displayed.', GCWT_TEXT_DOMAIN); ?></span>
	<br />
	<input type="text" name="gcwt_options[display_link_text]" value="<?php echo stripslashes(esc_html($options['display_link_text'])); ?>" />
	<?php
}*/
?>