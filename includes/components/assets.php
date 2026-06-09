<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        [],
        '6.5.2'
    );

    wp_enqueue_style(
        'urpet-education-snippets',
        URPET_EDU_SNIPPETS_URL . 'assets/css/education.css',
        ['font-awesome'],
        URPET_EDU_SNIPPETS_VERSION
    );

    wp_enqueue_script(
        'urpet-education-sidebar',
        URPET_EDU_SNIPPETS_URL . 'assets/js/sidebar.js',
        [],
        URPET_EDU_SNIPPETS_VERSION,
        true
    );
});
