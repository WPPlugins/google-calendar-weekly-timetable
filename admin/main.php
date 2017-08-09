<div class="wrap">

	<h3><?php _e('Add a New Feed', GCWT_TEXT_DOMAIN); ?>&nbsp;{<a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#experts">?</a>}</h3>
	
	<a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php&action=add'); ?>" class="button-secondary" title="<?php _e('Click here to add a new feed', GCWT_TEXT_DOMAIN); ?>"><?php _e('Add Feed', GCWT_TEXT_DOMAIN); ?></a>

	<br /><br />
	<h3><?php _e('Current Feeds', GCWT_TEXT_DOMAIN); ?></h3>

	<?php
	//Get saved feed options
	$options = get_option(GCWT_OPTIONS_NAME);
	//If there are no saved feeds
	if(empty($options)){
	?>

	<p><?php _e('You haven\'t added any Google Calendar feeds yet.', GCWT_TEXT_DOMAIN); ?></p>

	<?php //If there are saved feeds, display them ?>
	<?php }else{ ?>

	<table class="widefat">
		<thead>
			<tr>
				<th scope="col"><?php _e('ID', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"><?php _e('Title', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"><?php _e('URL', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col"><?php _e('ID', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"><?php _e('Title', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"><?php _e('URL', GCWT_TEXT_DOMAIN); ?></th>
				<th scope="col"></th>
			</tr>
		</tfoot>

		<tbody>
			<?php 
			foreach($options as $key => $event){ ?>
			<tr>
				<td><?php echo $key; ?></td>
				<td><?php echo $event['title']; ?></td>
				<td><?php echo $event['url']; ?></td>
				<td align="right">
					<a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php&action=edit&id=' . $key); ?>"><?php _e('Edit', GCWT_TEXT_DOMAIN); ?></a>&nbsp;|&nbsp;<a href="<?php echo admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php&action=delete&id=' . $key); ?>"><?php _e('Delete', GCWT_TEXT_DOMAIN); ?></a>
				</td>
			</tr>
			<?php } ?>
		</tbody>

	</table>

	<?php }
	//Get saved general options
	$options = get_option(GCWT_GENERAL_OPTIONS_NAME);
	?>

	<br />
	<h3><?php _e('General Options', GCWT_TEXT_DOMAIN); ?></h3>
	
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Custom stylesheet URL', GCWT_TEXT_DOMAIN); ?></th>
			<td>
				<span class="description"><?php _e('If you want to make changes to the default CSS, make a copy of <code>google-calendar-weekly-timetable/css/gcwt-style.css</code> on your server. Make any 
				changes to the copy and put it somewhere outside plugin folder. Enter the full URL to the copied file below. If you put it in wp-contents directory for example it will look something like http://yourdomain.lt/wp-content/gcwt-style_custom.css. This is because all the plugin files get deleted when plugin is updated', GCWT_TEXT_DOMAIN); ?></span>
				<br />
				<input type="text" name="gcwt_general[stylesheet]" value="<?php echo $options['stylesheet']; ?>" size="100" />
			</td>
		</tr>
	</table>

	<br />
	
	<input type="submit" class="button-primary" value="<?php _e('Save', GCWT_TEXT_DOMAIN); ?>" />
	
	<h3><?php _e('What next?', GCWT_TEXT_DOMAIN); ?></h3>
	
	<table class="form-table">
		<tr>
			<th scope="row"><a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/how-to-install#shortcodes"><?php _e('Include in Post/Page with shortcode', GCWT_TEXT_DOMAIN); ?></a></th>
			<td>
				<span class="description"><?php _e('There are 2 shortcodes:<br>1. New: [timetable] - this will a) load all feeds automatically even if you don\'t specify any feeds and will put caption to the table with credit and external link to plugin site (this will help others to find my plugin when they are looking for it)<br>2. Old: [gc-timetable] - requires to specify feeds with id="1,2" to work and will put caption credit but without external link.<br>', GCWT_TEXT_DOMAIN); ?></span>
				<br />
			</td>
		</tr>
	</table>

	
</div>
<div style="text-align: left;font-family:cursive;font-size:150%"><br/>
	
	</div>
<div id="feedback"> 
<table class="form-table">
<tr>
			<th scope="row" style="text-align: left;font-family:cursive;font-size:150%;"><a href="http://mission.lt/google-calendar-weekly-timetable-wordpress-plugin/donate"><img src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_donate_LG.gif"> </img></a><br/> <br/>Buy me a cup of tea:)</th>
			<td >
				<iframe src="https://spreadsheets.google.com/embeddedform?formkey=dEs2R1hNbF9aUnVGX2FISW9xRnlDMHc6MQ" width="600" height="800" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
			</td>
			
		</tr>
</table>
 </div>