<?php
/**
 * Plugin Name: Hello WordPress
 * Description: A simple plugin to display a greeting message.
 * Version: 1.0
 * Author: Raphael Mulenda
 */

// Add a shortcode to display a greeting message
function hello_wordpress_shortcode() {
    return '<p>Hello, WordPress!</p>';
}
add_shortcode('hello_wordpress', 'hello_wordpress_shortcode');