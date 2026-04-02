<?php
/**
 * Loop template for product in search results
 *
 * @package lacadev
 */

if (!defined('ABSPATH')) {
    exit;
}

global $post;

$product = wc_get_product($post->ID);
if (!$product) {
    return;
}

$image_id  = $product->get_image_id();
$image_src = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src('woocommerce_thumbnail');
$image_alt = esc_attr($product->get_name());
$link      = get_permalink($product->get_id());
$price     = $product->get_price_html();
?>

<div class="loop-product">
    <a href="<?php echo esc_url($link); ?>" class="loop-product__link">
        <div class="loop-product__image">
            <img src="<?php echo esc_url($image_src); ?>" 
                 alt="<?php echo $image_alt; ?>" 
                 loading="lazy" decoding="async">
        </div>
        <div class="loop-product__info">
            <h3 class="loop-product__title"><?php echo esc_html($product->get_name()); ?></h3>
            <?php if ($price): ?>
                <div class="loop-product__price"><?php echo $price; ?></div>
            <?php endif; ?>
        </div>
    </a>
</div>
