<?php


add_action('rest_api_init', function () {
    register_rest_route('api', '/get_product_list', array(
        'methods' => 'GET',
        'callback' => 'get_product_list',
        'args' => array(
            'category' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return ($param);
                },
            ),
        ),
    ));
});


function get_product_list(WP_REST_Request $request){
    $data = $request->get_params();
    $category = $data['category'];
    if(is_numeric($category)){
        $term = get_term_by('id', $category, 'products_category');
    } else {
        $term = get_term_by('slug', $category, 'products_category');
    }
    $term_id = $term->term_id;

    $posts = get_posts([
        'post_type' => 'products',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'products_category',
                'field' => 'term_id',
                'terms' => $term_id,
            )
        )
    ]);
    $posts_by_cat = array();
    foreach($posts as $p){
        $cat_post = array();
        $cat_post['id'] =  $p->ID;
        $post_meta = get_product_meta($p->ID);
        $cat_post['title'] = $post_meta['product_title'];
        $cat_post['description'] = $post_meta['product_description'];
        $cat_post['image'] = $post_meta['product_main_image'];
        $cat_post['price'] = $post_meta['product_price'];
        $cat_post['is_on_sale'] = $post_meta['product_on_sale'];
        $cat_post['sale_price'] = $post_meta['product_sale_price'];
        array_push($posts_by_cat, $cat_post);
    }

    return $posts_by_cat;
}