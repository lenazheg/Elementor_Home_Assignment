<?php
/**
 * The [product] shortcode.
 *
 * Accepts product id, bg color and will display a box.
 *
 * @param array  $atts    Shortcode attributes. Default empty.
 * @param string $content Shortcode content. Default null.
 * @param string $tag     Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 */
function product_function($attr = [], $content = null, $tag = '' ){
    $product_id = $attr['product_id'];
    $bg_color = $attr['bg_color'];

    $main_image = get_post_meta($product_id, 'product_main_image', true);
    $product_title = get_post_meta($product_id, 'product_title', true);
    $product_price = get_post_meta($product_id, 'product_price', true);

    $sc_output = '<div style="border: 2px solid #000000;padding: 10px;margin: 5px;background-color:'.$bg_color.'">';
    $sc_output .= '<img src="'.$main_image.'" alt="" title="Main Image" />';
    $sc_output .= '<p>Title: '.$product_title.'</p>';
    $sc_output .= '<p>Price: '.$product_price.'$</p>';
    $sc_output .= '</div>';

    return $sc_output;
}

add_shortcode('product', 'product_function');



/**
 * Override output of [product] shortcode
 *
 * @param string $output The output from the shortcode
 * @param string $tag The name of the shortcode
 *
 * @return string The modified output
 */
function overidde_product_sc( $output, $tag ) {
    if ( $tag == 'product' ) {
        return 'Override output of product shortcode!';
    }
    return $output;
}
add_filter('do_shortcode_tag', 'overidde_product_sc', 10, 2);
