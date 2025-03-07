<?php
/*
Plugin Name: OpenAI Post Generator
Description: A plugin for generating posts using the OpenAI API.
Version: 1.0
Author: Luckyster
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use PostGenerator\SettingsPage;
use PostGenerator\AjaxHandler;

add_action('admin_menu', [SettingsPage::class, 'addPluginMenu']);
add_action('admin_init', [SettingsPage::class, 'registerSettings']);

add_action('wp_ajax_generate_post', [AjaxHandler::class, 'generatePost']);
add_action('wp_ajax_nopriv_generate_post', [AjaxHandler::class, 'generatePost']);

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script(
        'post-generator',
        plugin_dir_url(__FILE__) . 'assets/js/post-generator.js',
        [],
        '1.0',
        true
    );
    wp_localize_script('post-generator', 'post_generator', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('post_generator_nonce')
    ]);
    wp_enqueue_style(
        'post-generator-styles',
        plugin_dir_url(__FILE__) . 'assets/css/post-generator.css',
        [],
        '1.0'
    );
});
