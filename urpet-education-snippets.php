<?php
/**
 * Plugin Name: URPET Education Snippets
 * Description: Шорткоды и шаблоны для раздела дополнительного образования URPET.
 * Version: 1.0.0
 * Author: Александр
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('URPET_EDU_SNIPPETS_VERSION', '1.0.0');
define('URPET_EDU_SNIPPETS_DIR', plugin_dir_path(__FILE__));
define('URPET_EDU_SNIPPETS_URL', plugin_dir_url(__FILE__));

require_once URPET_EDU_SNIPPETS_DIR . 'includes/education-common.php';
require_once URPET_EDU_SNIPPETS_DIR . 'includes/direction-single-page.php';
