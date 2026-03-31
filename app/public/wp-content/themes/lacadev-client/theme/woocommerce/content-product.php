<?php
/**
 * The template for displaying product content within loops
 * Editorial style - no star rating, clean minimal card
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

// Get product attributes for display (material, variant, etc.)
$short_description = $product->get_short_description();
$attributes        = $product->get_attributes();
$attr_display      = '';
$attr_values       = [];

foreach ( $attributes as $attribute ) {
	if ( ! $attribute->get_visible() ) {
		continue;
	}
	$options = $attribute->get_options();
	if ( ! empty( $options ) ) {
		if ( is_array( $options ) && is_numeric( $options[0] ) ) {
			// Taxonomy-based attribute
			$terms = array_map( 'get_term', $options );
			$vals  = wp_list_pluck( array_filter( $terms, function( $t ) { return $t instanceof WP_Term; } ), 'name' );
		} else {
			$vals = $options;
		}
		if ( ! empty( $vals ) ) {
			$attr_values[] = implode( ' / ', $vals );
		}
	}
}
$attr_display = implode( ' · ', array_slice( $attr_values, 0, 2 ) );

// Check if featured (used as "Editor's Pick" badge)
$is_featured = $product->is_featured();

// Product thumbnail
$image_id   = $product->get_image_id();
$image_src  = '';
$image_alt  = esc_attr( $product->get_name() );
if ( $image_id ) {
	$image_data = wp_get_attachment_image_src( $image_id, 'woocommerce_single' );
	if ( $image_data ) {
		$image_src = $image_data[0];
	}
}
if ( ! $image_src ) {
	$image_src = wc_placeholder_img_src( 'woocommerce_single' );
}

$product_link = get_permalink( $product->get_id() );
?>
<li <?php wc_product_class( 'product-card', $product ); ?>>
	<a href="<?php echo esc_url( $product_link ); ?>" class="product-card__link" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">

		<?php /* Image wrapper */ ?>
		<div class="product-card__image-wrapper">
			<img
				class="product-card__image"
				src="<?php echo esc_url( $image_src ); ?>"
				alt="<?php echo $image_alt; ?>"
				loading="lazy"
				decoding="async"
			/>

			<?php if ( $is_featured ) : ?>
				<span class="product-card__badge"><?php esc_html_e( "Editor's Pick", 'lacadev-client' ); ?></span>
			<?php endif; ?>
		</div>

		<?php /* Info section */ ?>
		<div class="product-card__info">
			<div class="product-card__meta">
				<h2 class="product-card__title">
					<?php echo esc_html( $product->get_name() ); ?>
				</h2>
				<div class="product-card__price">
					<?php echo $product->get_price_html(); ?>
				</div>
			</div>

			<?php if ( $attr_display ) : ?>
				<p class="product-card__attributes"><?php echo esc_html( $attr_display ); ?></p>
			<?php endif; ?>
		</div>

	</a>
</li>
