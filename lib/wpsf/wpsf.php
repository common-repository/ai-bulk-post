<?php
/**
 * Plugin Name: WPS Framework
 * Description: Create a custom settings page for your plugin or theme easily!
 * Version: 1.2
 * Author: yakupseymen
 * Author URI: https://github.com/yakupseymen/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpsf
 * Domain Path: /languages
 * 
 * WPS Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * WPS Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'WPSF_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPSF_URL', plugin_dir_url( __FILE__ ) );
define( 'WPSF_VERSION', get_file_data( __FILE__, array('Version' => 'Version'), false)['Version'] );

require_once WPSF_PATH . 'includes/autoloader.php';
require_once WPSF_PATH . 'includes/framework.php';
