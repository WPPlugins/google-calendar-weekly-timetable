<?php
//Redirect to the main plugin options page if form has been submitted
if(isset($_GET['action'])){
	if($_GET['action'] == 'delete' && isset($_GET['updated'])){
		wp_redirect(admin_url('options-general.php?page=' . GCWT_PLUGIN_NAME . '.php&updated=deleted'));
	}
}

add_settings_section('gcwt_delete', __('Delete Feed', GCWT_TEXT_DOMAIN), 'gcwt_delete_main_text', 'delete_feed_wt');
//Unique ID                                  //Title                            //Function                //Page         //Section ID
add_settings_field('gcwt_delete_id_field',    __('Feed ID', GCWT_TEXT_DOMAIN),    'gcwt_delete_id_field',    'delete_feed_wt', 'gcwt_delete');
add_settings_field('gcwt_delete_title_field', __('Feed Title', GCWT_TEXT_DOMAIN), 'gcwt_delete_title_field', 'delete_feed_wt', 'gcwt_delete');

//Main text
function gcwt_delete_main_text(){
	?>
	<p><?php _e('Are you want you want to delete this feed? (Remember to remove / adjust any widgets or shortcodes associated with this feed).', GCWT_TEXT_DOMAIN); ?></p>
	<?php
}

//ID
function gcwt_delete_id_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="text" disabled="disabled" value="<?php echo $options['id']; ?>" size="3" />
	<input type="hidden" name="gcwt_options[id]" value="<?php echo $options['id']; ?>" />
	<?php
}

//Title
function gcwt_delete_title_field(){
	$options = get_option(GCWT_OPTIONS_NAME);
	$options = $options[$_GET['id']];
	?>
	<input type="text" name="gcwt_options[title]" disabled="disabled" value="<?php echo $options['title']; ?>" size="50" />
	<?php
}
?>