<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();

global $post;
$post_meta = get_product_meta($post->ID);
$isSale = false;
if($post_meta['product_on_sale']){
    $isSale = true;
}

$related_posts = get_related_products($post);
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="single-product <?php echo $isSale ? 'sale' : ''; ?>">
                <div class="sale"></div>
                <div class="title"><h2><?php echo $post_meta['product_title']; ?></h2></div>
                <div class="desc">
                    <h4>Description</h4>
                    <span><?php echo $post_meta['product_description']; ?></span>
                    <div class="youtube">
                        <iframe src="<?php echo $post_meta['product_youtube']; ?>"
                                width="400" height="300" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="main-image">
                    <h4>Main Image</h4>
                    <img src="<?php echo $post_meta['product_main_image'];?>" alt="Main Image" title="Main Image" />
                </div>
                <div class="price">
                    <h4>Price: </h4>
                    <span class="old-price"><?php echo $post_meta['product_price'];?>$</span>
                        <?php if($isSale) { ?>
                             <span class="sale-price"><?php echo $post_meta['product_sale_price'];?>$</span>
                        <?php } ?>

                </div>

                <div class="gallery">
                    <h4>Gallery</h4>
                    <?php foreach($post_meta['product_gallery'][0] as $g){ ?>
                        <img src="<?php echo $g;?>" alt="Main Image" title="Gallery Image" />
                    <?php } ?>
                </div>

                <div class="related-products">
                    <h4>Related Products</h4>
                    <?php foreach($related_posts as $p){ ?>
                        <a href="<?php echo get_permalink($p); ?>"><?php echo $p->post_title; ?></a>
                    <?php } ?>
                </div>
            </div>
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();

