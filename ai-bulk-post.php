<?php
/**
 * Plugin Name: AI Bulk Post
 * Description: The AI Bulk Post Plugin for WordPress empowers you to effortlessly generate and schedule multiple posts using the power of ChatGPT, OpenAI's cutting-edge language model. Say goodbye to tedious manual content creation and scheduling – with this plugin, you can automate the process and focus on what matters most: growing your audience and engaging your readers.
 * Version: 1.0
 * Author: J4
 * Author URI: https://profiles.wordpress.org/j4cob/
 * Text Domain: ai-bulk-post
 * Requires PHP: 8.0
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * License: GPL v2 or later
 * 
 * AI Bulk Post is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * AI Bulk Post is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'AIBP_PATH', plugin_dir_path( __FILE__ ) );
define( 'AIBP_URL', plugin_dir_url( __FILE__ ) );
define( 'AIBP_VERSION', get_file_data( __FILE__, array('Version' => 'Version'), false)['Version'] );

require_once AIBP_PATH . 'includes/autoloader.php';
require_once AIBP_PATH . 'includes/plugin.php';