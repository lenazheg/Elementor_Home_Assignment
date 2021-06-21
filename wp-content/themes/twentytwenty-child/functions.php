<?php

add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );
function child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'child-style'
        , get_stylesheet_directory_uri() . '/child-style.css'
        , array('parent-style') // declare the dependency
    // in order to load child-style after parent-style
    );
}