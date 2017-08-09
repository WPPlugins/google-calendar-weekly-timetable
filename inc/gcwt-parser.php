<?php

class GCWT_Parser{
	var $feeds = array();
	var $merged_feed_data = array();
	var $title = null;
	var $max_events_display = 0;

	function GCWT_Parser($feed_ids, $title_text = null, $max_events = 0, $start, $cache_duration, $refresh_one){
		$this->__construct($feed_ids, $title_text, $max_events, $start, $cache_duration, $refresh_one);
	}

	function __construct($feed_ids, $title_text = null, $max_events = 0, $start, $cache_duration, $refresh_one){
		require_once('gcwt-feed.php');
		$this->title = $title_text;
		$this->max_events_display = $max_events;

		//Get the feed options
		$options = get_option(GCWT_OPTIONS_NAME);
		
		// If user wants to refresh one feed every <$refresh_one> reload
		if ($refresh_one){
			$general_options = get_option(GCWT_GENERAL_OPTIONS_NAME);
			if ($refresh_number < 2147483646) {
				$refresh_number = ++$general_options['refresh_number'];
			} else{
				$general_options['refresh_number'] = 1;
				$refresh_number = 1;
			}
			update_option(GCWT_GENERAL_OPTIONS_NAME, $general_options);
			//if ($refresh_one > 1) $refresh_feed_key = intval($refresh_feed_key/$refresh_one);
			$refresh_feed_key = $refresh_number % count($feed_ids);
		}
		
		foreach($feed_ids as $key => $single_feed){
			//Get the options for this particular feed
			if(isset($options[$single_feed])){
				$feed_options = $options[$single_feed];

				$feed = new GCWT_Feed();

				$feed->set_feed_id($feed_options['id']);
				$feed->set_feed_url($feed_options['url']);
				//$feed->set_max_events($feed_options['max_events']);
				$feed->set_max_events(250);
				//If day limit is not blank, set end date to specified number of days in the future, including today
				//if($feed_options['day_limit'] != '') 
				
				//Set cache duration: it can be set in feed or overridden in shortcode with cache_duration=42000
				if ($refresh_feed_key == $key){
						$feed->set_cache_duration(0);
				}elseif ($cache_duration == null) {
					$feed->set_cache_duration($feed_options['cache_duration']);
				}else{
					$feed->set_cache_duration($cache_duration);
				}
				
				
				
				//Set the timezone if anything other than default
				if($feed_options['timezone'] != 'default') $feed->set_timezone($feed_options['timezone']);
				//convert the string to time (default string is "today")
				$start_timestamp = strtotime($start);
				//round to the nearest day (for several reasons, one of them is cache)
				$start_timestamp = mktime(0, 0, 0, date('m', $start_timestamp), date('d', $start_timestamp) , date('Y', $start_timestamp));
				$feed->set_start_date($start_timestamp - date('Z'));
				$feed->set_end_date($start_timestamp - date('Z') + (86400 * 8)); //sets the day limit for a week
					
				//}
				//Set date and time formats. If they have not been set by user, set to global WordPress formats 
				//$feed->set_date_format($feed_options['date_format'] == '' ? get_option('date_format') : $feed_options['date_format']);
				//$feed->set_time_format($feed_options['time_format'] == '' ? get_option('time_format') : $feed_options['time_format']);
				//Set whether to handle multiple day events
				//$feed->set_multi_day($feed_options['multiple_day'] == 'true' ? true : false);

				//Sets all display options
				$feed->set_display_options(array(
					'display_color' => $feed_options['display_color'],
					//'display_start' => $feed_options['display_start'],
					//'display_end' => $feed_options['display_end'],
					//'display_location' => $feed_options['display_location'],
					//'display_desc' => $feed_options['display_desc'],
					//'display_link' => $feed_options['display_link'],
					//'display_start_text' => $feed_options['display_start_text'],
					//'display_end_text' => $feed_options['display_end_text'],
					//'display_location_text' => $feed_options['display_location_text'],
					//'display_desc_text' => $feed_options['display_desc_text'],
					//'display_desc_limit' => $feed_options['display_desc_limit'],
					//'display_link_text' => $feed_options['display_link_text'],
					//'display_link_target' => $feed_options['display_link_target'],
					//'display_separator' => $feed_options['display_separator']
				));

				//SimplePie does the hard work
				$feed->init();

				//Add feed object to array of feeds
				$this->feeds[$single_feed] = $feed;
			}
		}
		
		

		//More SimplePie magic to merge items from all feeds together
		$this->merged_feed_data = SimplePie::merge_items($this->feeds);

		//Sort the items by into date order
		usort($this->merged_feed_data, array('SimplePie_Item_GCalendarWT', 'compare'));
	}

	//Returns an array of feed ids that have encountered errors
	function get_errors(){
		$errors = array();

		foreach($this->feeds as $feed){
			//Remove '//' on line below to see more error information
			if($feed->error()) $errors[] = $feed->get_feed_id();
		}

		return $errors;
	}

	//Returns array of days with events, with sub-arrays of events for that day
	/*function get_event_days(){
		$event_days = array();

		//Total number of events retrieved
		$count = count($this->merged_feed_data);

		//If maximum events to display is 0 (unlimited) set $max to 1, otherwise use maximum of events specified by user
		$max = $this->max_events_display == 0 ? 1 : $this->max_events_display;

		//Loop through entire array of events, or until maximum number of events to be displayed has been reached
		for($i = 0; $i < $count && $max > 0; $i++){
			$item = $this->merged_feed_data[$i];

			//Check that event end time isn't before start time of feed (ignores events from before start time that may have been inadvertently retrieved)
			if($item->get_end_date() > ($item->get_feed()->get_start_date() + date('Z'))){
			

				$start_date = $item->get_start_date();

				//Round start date to nearest day
				$start_date = mktime(0, 0, 0, date('m', $start_date), date('d', $start_date) , date('Y', $start_date));

				//If multiple day events should be handled, add multiple day event to required days
				if($item->get_feed()->get_multi_day()){
					$on_next_day = true;
					$next_day = $start_date + 86400;
					while($on_next_day){
						if($item->get_end_date() > $next_day){
							$event_days[$next_day][] = $item;
						}else{
							$on_next_day = false;
						}
						$next_day += 86400;
					}
				}

				//Add item into array of events for that day
				$event_days[$start_date][] = $item;

				//If maximum events to display isn't 0 (unlimited) decrement $max counter
				if($this->max_events_display != 0) $max--;
			}
		}

		return $event_days;
	}*/
	
	function sort_array_by_first_int_index($my_array){
		foreach ($my_array as $key => $sub_array){
			$indexes_array[] = $key;
		}
		sort($indexes_array);
		foreach ($indexes_array as $int_value){
			$result_array[$int_value] = $my_array[$int_value];
		}
		return $result_array;
	}
	
	function get_event_title_in_lang($full_title, $lang){
		if ($lang == null) return $full_title;
		//Deal with whitespace
		$title_without_spaces = preg_replace('/\s\s+/', ' ', $full_title);
		$title_without_spaces = str_replace('; ',';', $full_title);
		$title_without_spaces = str_replace(': ',':', $title_without_spaces);
		$title_without_spaces = str_replace(' ;',';', $title_without_spaces);
		$title_without_spaces = str_replace(' :',':', $title_without_spaces);
		//Match language
		$split_array = explode(";$lang:", $title_without_spaces);
		if(empty($split_array[1])){
			$first_default = preg_split("/;..:/", $title_without_spaces);
			return $first_default[0];
		} else{
			$ripped_title_array = explode(';', $split_array[1]);
			return $ripped_title_array[0];
		}
	}
	
	//Returns array of hours with events, with sub-arrays of week days for each hour
	function get_event_days_table($lang){
		$event_hours_days_table = array();

		//Total number of events retrieved
		$count = count($this->merged_feed_data);

		//If maximum events to display is 0 (unlimited) set $max to 1, otherwise use maximum of events specified by user
		$max = $this->max_events_display == 0 ? 1 : $this->max_events_display;

		//Loop through entire array of events, or until maximum number of events to be displayed has been reached
		for($i = 0; $i < $count && $max > 0; $i++){
			$item = $this->merged_feed_data[$i];
			
			// Get start and end timestamps of event
			$start_date = $item->get_start_date();
			$end_date = $item->get_end_date();
			
			//Get title of event in the language specified or return the whole string if the language was not found
			$event_title = $this->get_event_title_in_lang($item->get_title(),$lang);

			//Check that event end time isn't before start time of feed (ignores events from before start time that may have been inadvertently retrieved)
			$feed = $item->get_feed();
			if($end_date > ($feed->get_start_date() + date('Z'))){
				$event_week_day = date('N', $start_date);
				
				$event_time = date('H', $start_date).date('i', $start_date);
				$display_options = $feed->get_display_options();
				//Add item into array of events for that day
				$event_hours_days_table[$event_time][$event_week_day] = array('title'  => $event_title,
																			  'ftitle' => $feed->get_title(),
																			  'color' => $display_options['display_color'],
																			  'timestamp' => $start_date,
																			  'end_timestamp' => $end_date);		
				

				//If maximum events to display isn't 0 (unlimited) decrement $max counter
				if($this->max_events_display != 0) $max--;
			}
		}
		
		//$event_hours_days_table = $this->sort_array_by_first_int_index($event_hours_days_table);
		return $event_hours_days_table;
		
	}
	
	function get_event_rowspan($start_times_array, $start_timestamp, $end_timestamp){
		//Convert start and end times to military time
		$start_time = (int)date('H', $start_timestamp).date('i', $start_timestamp);
		$end_time = (int)date('H', $end_timestamp).date('i', $end_timestamp);
		
		//Let's find what element in start_times_array our start of event corresponds to		
		foreach ($start_times_array as $key => $time){
			if ($time == $start_time) break;
		}
		$key++;
		//echo($key);
		//Starting with start of event. Find if event is still happening when next row time has begun
		for ($i=$key; $start_times_array[$i] < $end_time ; $i++){
			if (!isset($start_times_array[$i+1])) break;
		}
		$rowspan = $i - $key + 1;
		return $rowspan;
	}
	
	//Returns an array that indidicates where the rowspan is more than one and where the <td></td> empty tag should be skipped
	// 0 if <td> should be skipped, 1 if there's normal event (rowspan="1"), 2+ indicates rowspan number.
	function get_rowspan_array($event_days_table, $start_times_array){
		$rowspan_array = array();	
		//First we get array for all the events without putting 0 for skipping <td>. rowspan numbers won't be correct yet
		foreach($event_days_table as $time => $events_row){
			foreach($events_row as $weekday => $event){
												//It needs array of times and the beginning and end of event time
				$rowspan_array[$weekday][$time] = $this->get_event_rowspan($start_times_array, $event['timestamp'], $event['end_timestamp']);
			}
		}
		//Now we need to go through every day and check that the increased rowspan won't go over some event bellow. Adding "0" where we don't need a cell.
		foreach($rowspan_array as $weekday => $events_col){
			foreach($events_col as $time => $rowspan){
			
				/*Roll through rowspan array down adding 0 if rowspan is more than 1 and if the place is empty (no event)
				We need a cycle where it will repeat it for $rowspan number of times. 
				We should have access to next row's time (not next event's) so we can add 0 in every empty space. 
				Then we break the cycle if we find an event and subtract the number of missing cycles from $rowspan and store new $rowspan to array.*/
				
				//look where in our times array is the start time of current event
				
				// Check if we need to cycle to put zeroes for <td> to be skipped
				if($rowspan > 1){
					$time_key = array_search($time, $start_times_array); //where are we?
					for ($i = 1; $i < $rowspan; $i++){
						if (!isset($rowspan_array[$weekday][$start_times_array[$time_key+$i]])){
							$rowspan_array[$weekday][$start_times_array[$time_key+$i]] = 0;
						} else{
							$rowspan_array[$weekday][$time] = $i; //corrent the rowspan if there was an event detected
							break;
						}
					}
					 
				}
				
			}
		}
		return $rowspan_array;
	}
	
	//resolve the timestring
	/*
	function get_append($append_string, $start_timestamp, $end_timestamp){
		if (empty($append_string)) return '';
		

		
		return "whola";
	}*/
	
	//Return weekly timetable
	function get_table($grouped = false, $week_days_names, $time_format, $sunday_opt, $enable_rowspan, $append_date, $thetag, $caption, $lang){
		
		$event_days_table = $this->get_event_days_table($lang);
		
		//If event_days is empty, there are no events in the feed(s), so return a message indicating this
		if(count((array)$event_days_table) == 0) {
			return '<p>' . __('There are currently no upcoming events.', GCWT_TEXT_DOMAIN) . '</p>';
		}else{
			$event_days_table = $this->sort_array_by_first_int_index($event_days_table);
		}
		
		//Check if Sunday has events and set and change the option accordingly
		//Also get start_times_array and end_times_array that we will use in getting rowspan_array later (just to save some processor power)
		$sunday_has_events = false;
		if($enable_rowspan) { // these we need only if rowspan is enabled
			$start_times_array = array();
			$end_times_array = array();
		}
		foreach($event_days_table as $time => $events_row){
			if(isset($events_row[7])){
				$sunday_has_events = true;
			}
			if($enable_rowspan){
				$first_event_on_the_row = current($events_row);
				$end_time = date('H', $first_event_on_the_row['end_timestamp']).date('i', $first_event_on_the_row['end_timestamp']);
				array_push($start_times_array, $time);
				array_push($end_times_array, $end_time);
			}
		}
		
		//If user did not ask for Sunday to be visible/invisible then set it according to whether there are events
		if (!isset($sunday_opt['show'])) $sunday_opt['show'] = $sunday_has_events;
		
		if($enable_rowspan) {
			sort($end_times_array);
			array_push($start_times_array, end($end_times_array)); // put the end time of table to the end of start times array
			
			// We need to get where we should increse rowspan because some events may stretch over a few rows (according to times).
			// This will be a matrix with 0 - for skipping <td> tag, 1, 2, 3 etc - an event with 1,2,3 etc. rowspan
			$rowspan_array = $this->get_rowspan_array($event_days_table, $start_times_array);
		}
		
		//Add caption to what user have set or to default text
		if (isset($caption) && !empty($caption)){
			$caption_text = $caption;
		}else{
			$caption_text = '..by Google Calendar Weekly Timetable WP plugin';
		}
		
		//Start the table, add the caption.
		If ($thetag == 'timetable'){
			$markup = '<table id="timetable"><caption><a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/">'.$caption_text.'</a></caption><tr><th scope="col"></th>';
		}else{
			$markup = '<table id="timetable"><caption>'.$caption_text.'</caption><tr><th scope="col">Start<br>Time</th>';
		}
		
		//Print week days names to markum which will be returned. Note that in $week_days_names, 0 - Monday, 1 - Tuesday ... 6 - Sunday
		
		//Override default week day names by wordpress default if there is no $lang parameter passed
		if ($lang == null){
			$week_days_names = array(date_i18n('l' ,strtotime("monday")));
			for($i=1;$i<7;$i++){
				$week_days_names[] = date_i18n('l' ,strtotime("monday + $i day"));
			}
		}
		
		
		//If sunday is the start of week then print it at the beginning (before cycle)
		if ($sunday_opt['start'] && $sunday_opt['show']) $markup .= '<th scope="col">'.$week_days_names[6].'</th>';
		// Print days from Monday to Saturday
		for($i=0;$i < 6; $i++){
			$markup .= '<th scope="col">'.$week_days_names[$i].'</th>';
		}
		//If sunday is the end of week then print it at the end (after cycle)
		if (!$sunday_opt['start'] && $sunday_opt['show']) $markup .= '<th scope="col">'.$week_days_names[6].'</th>';
		
		// Cycle through rows		
		foreach($event_days_table as $time => $events_row){
			
			/* From now on in this foreach cycle we are dealing with one row of events */

			//Set time format accordingly. It is set in google-calendar-weekly-timetable.php
			$events_row_index = array_values($events_row);
			$timestamp = $events_row_index[0]['timestamp']; // get timestamp of start time in any of rows
			$time_string = date($time_format, $timestamp);

			$markup .= '</tr><tr>
			<th scope="row">'.$time_string.'</th>';
			
			//If sunday is the start of week then print sunday events at the beginning (before cycle)
			if ($sunday_opt['start'] && $sunday_opt['show']){
				$rowspan = ($enable_rowspan)? $rowspan_array[7][$time]:null;
				//Print an event or empty space if there isn't any				
				if(isset($events_row[7]['title'])){
					if (!$enable_rowspan) $rowspan = 1;		
					$markup .=
						'<td rowspan="'.$rowspan.'" style="background:'.$events_row[7]['color'].'">' .
						$events_row[7]['title'].date($append_date, $events_row[7]['end_timestamp']).//get_append($append_date,$events_row[7]['timestamp'], $events_row[7]['end_timestamp']).
						'</td>';		
				} else {
					$markup .= (isset($rowspan) && $enable_rowspan) ? '': '<td></td>';
				}
			}
			
			//Let's print a row of events
			for($i = 1; $i <= 6; $i++){
				$rowspan = ($enable_rowspan)? $rowspan_array[$i][$time]:null;
				//Print an event or empty space if there isn't any				
				if(isset($events_row[$i]['title'])){
					if (!$enable_rowspan) {$rowspan = 1;}
					
					$markup .=
						'<td rowspan="'.$rowspan.'" style="background:'.$events_row[$i]['color'].'">' .
						$events_row[$i]['title'].date($append_date, $events_row[$i]['end_timestamp']).//get_append($append_date,$events_row[$i]['timestamp'], $events_row[$i]['end_timestamp']).
						'</td>';		
				} else {
					$markup .= (isset($rowspan) && $enable_rowspan) ? '': '<td></td>';
				}
			}
			
			//If sunday is the end of week then print sunday events at the end (after cycle)
			if (!$sunday_opt['start'] && $sunday_opt['show']){
				$rowspan = ($enable_rowspan)? $rowspan_array[7][$time]:null;
				//Print an event or empty space if there isn't any				
				if(isset($events_row[7]['title'])){
					if (!$enable_rowspan) {$rowspan = 1;}		
					$markup .=
						'<td rowspan="'.$rowspan.'" style="background:'.$events_row[7]['color'].'">' .
						$events_row[7]['title'].date($append_date, $events_row[7]['end_timestamp']).//get_append($append_date,$events_row[7]['timestamp'], $events_row[7]['end_timestamp']).
						'</td>';		
				} else {
					$markup .= (isset($rowspan) && $enable_rowspan) ? '': '<td></td>';
				}
			}
			
			
			$markup .= '</tr>';
		}
		$markup .= '</table>';
		
		return $markup;
	}

	
}
?>