<?php
/**
 * Related Products - Editorial Layout
 *
 * Displays related products in editorial card style,
 * consistent with the shop page product card design.
 *
 * @package WooCommerce\Templates
 * @version 10.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! $related_products ) {
	return;
}

// Ensure lazy loading for related product images
if ( function_exists( 'wp_increase_content_media_count' ) ) {
	$content_media_count = wp_increase_content_media_count( 0 );
	if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
		wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
	}
}
?>

<section class="sp-related">

	<div class="sp-related__header">
		<div class="sp-related__header-left">
			<p class="sp-related__label"><?php esc_html_e( 'Tương tự', 'lacadev-client' ); ?></p>
			<h2 class="sp-related__title"><?php esc_html_e( 'Sản phẩm liên quan', 'lacadev-client' ); ?></h2>
		</div>
		<a class="sp-related__view-all" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<?php esc_html_e( 'Xem tất cả', 'lacadev-client' ); ?> &nearr;
		</a>
	</div>

	<div class="sp-related__grid">
		<?php foreach ( $related_products as $idx => $related_product ) :
			$post_object = get_post( $related_product->get_id() );
			setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore

			$img_id  = $related_product->get_image_id();
			$img_src = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
			$img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $related_product->get_name();
			$link    = get_permalink( $related_product->get_id() );
			$name    = $related_product->get_name();
			$price   = $related_product->get_price_html();
			// Stagger grid: middle card slightly lower on desktop
			$offset_class = ( $idx === 1 ) ? 'sp-related__card--offset' : '';
		?>
			<article class="sp-related__card <?php echo esc_attr( $offset_class ); ?>">
				<a href="<?php echo esc_url( $link ); ?>" class="sp-related__card-link">
					<div class="sp-related__card-image">
						<img src="<?php echo esc_url( $img_src ); ?>"
							alt="<?php echo esc_attr( $img_alt ); ?>"
							loading="lazy"
							class="sp-related__card-img" />
					</div>
					<div class="sp-related__card-info">
						<div>
							<h3 class="sp-related__card-name"><?php echo esc_html( $name ); ?></h3>
						</div>
						<p class="sp-related__card-price"><?php echo wp_kses_post( $price ); ?></p>
					</div>
				</a>
			</article>
		<?php endforeach; ?>
	</div>

</section>

<?php wp_reset_postdata(); ?>
