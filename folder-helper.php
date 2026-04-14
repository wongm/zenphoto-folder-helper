<?php
/**
 * Folder Helper
 *
 * Generates Windows file management commands to relocated your photos into date-based directories on your local machine based on caption data in your photo gallery.
 *
 * @author Marcus Wong (wongm)
 * @package plugins
 */

$plugin_description = gettext("Generates Windows file management commands to relocated your photos into date-based directories on your local machine based on caption data in your photo gallery.");
$plugin_author = "Marcus Wong (wongm)";
$plugin_version = '1.0.0'; 
$plugin_URL = "https://github.com/wongm/zenphoto-folder-helper/";
$plugin_is_filter = 500 | ADMIN_PLUGIN;

zp_register_filter('admin_utilities_buttons', 'folderHelper::button');

class folderHelper {
	
	static function button($buttons) {
		$buttons[] = array(
						'category'		 => gettext('Admin'),
						'enable'			 => true,
						'button_text'	 => gettext('Folder helper'),
						'formname'		 => 'zenphotoCaption_button',
						'action'			 => WEBPATH.'/plugins/folder-helper',
						'icon'				 => 'images/pencil.png',
						'title'				 => gettext('Use your caption data to organise your local image files.'),
						'alt'					 => '',
						'hidden'			 => '',
						'rights'			 => ALBUM_RIGHTS
		);
		return $buttons;
	}
}
?>