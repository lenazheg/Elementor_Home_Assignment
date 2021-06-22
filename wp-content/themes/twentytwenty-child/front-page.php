<?php


get_header();

$products = get_posts([
    'post_type' => 'products',
    'post_status' => 'publish',
    'numberposts' => -1
]);

?>


<main id="site-content" role="main">
    <div class="products-grid">
        <div class="row">
    <?php

    foreach($products as $product){
        $post_meta = get_product_meta($product->ID);
        $sale = '';
        if($post_meta['product_on_sale']){
            $sale = 'sale';
        }
        ?>
        <div class="product <?php echo $sale; ?>">
            <div class="sale"></div>
            <a href="<?php echo get_permalink($product); ?>">
                <div class="title"><?php echo $post_meta['product_title']; ?></div>
                <div class="main-image">
                    <img src="<?php echo $post_meta['product_main_image'];?>" alt="Main Image" title="Main Image" />
                </div>
            </a>
        </div>

    <?php }


    ?>
    </div>
    </div>
</main><!-- #site-content -->


<?php

get_footer();

?>
