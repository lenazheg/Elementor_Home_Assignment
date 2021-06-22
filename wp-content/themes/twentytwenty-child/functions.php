<?php

global $dummy_categories;
$dummy_categories = array('shoes', 'clothes', 'shirts', 'sunglasses', 'pants', 'shorts', 'hats', 'accessories');
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


/* Part 4 */

function register_custom_post_products() {
    global $dummy_categories;

    $labels = array(
        'name' => _x( 'Products', 'post type general name' ),
        'singular_name' => _x( 'Product', 'post type singular name' ),
        'add_new' => _x( 'Add New', 'Product' ),
        'add_new_item' => __( 'Add New Product' ),
        'edit_item' => __( 'Edit Product' ),
        'new_item' => __( 'New Product' ),
        'all_items' => __( 'All Product' ),
        'view_item' => __( 'View Product' ),
        'search_items' => __( 'Search Product' ),
        'not_found' => __( 'No products found' ),
        'not_found_in_trash' => __( 'No products found in the Trash' ),
        'parent_item_colon' => '',
        'menu_name' => 'Products'
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Displays products',
        'public' => true,
        'menu_position' => 4,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail'),
        'taxonomies' => array(),
        'register_meta_box_cb' => 'meta_box_for_products',
        'has_archive' => true,
    );

    // create new post type
    register_post_type( 'products', $args );

    $tax_labels = array(
        'name' => _x( 'Product Category', 'taxonomy general name' ),
        'singular_name' => _x( 'Product Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Categories' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'menu_name' => __( 'Categories' ),
    );

    $tax_args = array(
        'hierarchical'          => FALSE,
        'labels'                => $tax_labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'products_category' ),
    );

    //register the new product tax
    register_taxonomy(
        'products_category',
        'products',
        $tax_args
    );

    // Create the dummy terms
    foreach($dummy_categories as $category){
        $term = term_exists($category, 'products_category');
        if(( $term == 0 || $term == null )){
            wp_insert_term(
                $category,
                'products_category',
                array(
                    'name' => $category,
                    'description' => $category,
                    'slug' => $category,
                )
            );
        }
    }

    create_products();

}
add_action( 'init', 'register_custom_post_products' );


function meta_box_for_products( $post ){
    add_meta_box( 'product_custom_fields', __( 'Product Data', 'elementor' ), 'products_fields', 'products', 'normal', 'low' );
}

function products_fields($post ) {
    wp_nonce_field( basename( __FILE__ ), 'products_meta_box_nonce' );

    $product_main_image = get_post_meta($post->ID, 'product_main_image', true);
    $product_gallery = get_post_meta($post->ID, 'product_gallery');
    $product_title = get_post_meta($post->ID, 'product_title', true);
    $product_description = get_post_meta($post->ID, 'product_description', true);
    $product_price = get_post_meta($post->ID, 'product_price', true);
    $product_sale_price = get_post_meta($post->ID, 'product_sale_price', true);
    $product_on_sale = get_post_meta($post->ID, 'product_on_sale', true);
    $product_youtube = get_post_meta($post->ID, 'product_youtube', true);
    $sale = $product_on_sale ? 'Yes' : 'No';

    echo '<h2>Main Image</h2><img src="'.$product_main_image.'" alt="" title="Main Image" />';

    echo '<h2>Product image gallery</h2>';
    foreach($product_gallery[0] as $g){
        echo '<img src="'.$g.'" alt="" title="" />';
    }

    echo '<h2>Product Title</h2> <p>'.$product_title.'</p><hr>';

    echo '<h2>Product Description</h2> <p>'.$product_description.'</p><hr>';

    echo '<h2>Product Price</h2> <p>'.$product_price.'</p><hr>';

    echo '<h2>Product Sale Price</h2> <p>'.$product_sale_price.'</p><hr>';

    echo '<h2>Is on sale?</h2> <p>'.$sale.'</p><hr>';

    echo '<h2>Youtube</h2> <p>'.$product_youtube.'</p><hr>';

}

function create_products(){
    $is_already_created = get_option('products_created');

    if(!isset($is_already_created) || !$is_already_created){
        global $user_ID;

        include plugin_dir_path(__FILE__) . 'dummy/products.php';
        $posts_arr = array();
        foreach($products as $key=>$product){
            $index = $key+1;
            $new_post = array(
                'post_title' => 'Product '.$index,
                'post_content' => '',
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => 'products',
            );
            $post_id = wp_insert_post($new_post);
            foreach($product as $data_key=>$data){
                add_post_meta($post_id, $data_key, $data);
            }
            array_push($posts_arr, $post_id);
        }
        create_product_category($posts_arr);
        update_option('products_created', true);
    }
}

function create_product_category($posts){
    global $dummy_categories;
    foreach($dummy_categories as $category){
        $posts_arr = $posts;
        $term = get_term_by('slug', $category, 'products_category');
        $term_id = $term->term_id;
        $num_of_posts = rand(2,4);
        for($i = 0; $i <= $num_of_posts; $i++){
            $index = rand(0,count($posts_arr));
            $post_id = $posts[$index];
            wp_set_post_terms($post_id, array($term_id), 'products_category', true);
            unset($posts_arr[$index]);
        }
    }
}

function get_product_meta($post_id){
    $post_meta = array();
    $post_meta['product_main_image'] = get_post_meta($post_id, 'product_main_image', true);
    $post_meta['product_gallery'] = get_post_meta($post_id, 'product_gallery');
    $post_meta['product_title'] = get_post_meta($post_id, 'product_title', true);
    $post_meta['product_description'] = get_post_meta($post_id, 'product_description', true);
    $post_meta['product_price'] = get_post_meta($post_id, 'product_price', true);
    $post_meta['product_sale_price'] = get_post_meta($post_id, 'product_sale_price', true);
    $post_meta['product_on_sale'] = get_post_meta($post_id, 'product_on_sale', true);
    $post_meta['product_youtube'] = get_post_meta($post_id, 'product_youtube', true);
    return $post_meta;
}

function get_related_products($post){
    $terms = get_the_terms($post, 'products_category');
    $term_ids = array();
    foreach($terms as $term){
        array_push($term_ids, $term->term_id);
    }
    return get_posts([
        'post_type' => 'products',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'products_category',
                'field' => 'term_id',
                'terms' => $term_ids,
            )
        )
    ]);
}
