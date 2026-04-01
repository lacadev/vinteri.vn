<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * EDITORIAL SINGLE PRODUCT LAYOUT
 * Left column (7/12): image gallery grid
 * Right column (5/12): sticky product info
 *
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Remove ALL WC default single product summary hooks - we render everything manually in sp-info.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title',   5  );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating',  10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price',   10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta',    40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

// Get product data
$product_id         = $product->get_id();
$main_image_id      = $product->get_image_id();
$gallery_image_ids  = $product->get_gallery_image_ids();
$all_image_ids      = array_merge( $main_image_id ? array( $main_image_id ) : array(), $gallery_image_ids );
$categories         = wc_get_product_category_list( $product_id, ', ' );
$short_description  = $product->get_short_description();
$description        = $product->get_description();
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-editorial', $product ); ?>>

	<div class="sp-layout">

		<?php /* === GALLERY COLUMN === */ ?>
		<div class="sp-gallery">
			<?php if ( ! empty( $all_image_ids ) ) : ?>
				<?php foreach ( $all_image_ids as $idx => $image_id ) :
					$image_src  = wp_get_attachment_image_url( $image_id, 'woocommerce_single' );
					$image_full = wp_get_attachment_image_url( $image_id, 'full' );
					$alt        = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					$alt        = $alt ? $alt : $product->get_name();
					$class      = ( $idx === 0 ) ? 'sp-gallery__item sp-gallery__item--main' : 'sp-gallery__item';
				?>
					<div class="<?php echo esc_attr( $class ); ?>">
						<a href="<?php echo esc_url( $image_full ); ?>" class="sp-gallery__link" data-fancybox="product-gallery">
							<img
								src="<?php echo esc_url( $image_src ); ?>"
								alt="<?php echo esc_attr( $alt ); ?>"
								class="sp-gallery__image"
								<?php echo $idx === 0 ? 'loading="eager"' : 'loading="lazy"'; ?>
							/>
						</a>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="sp-gallery__item sp-gallery__item--main">
					<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>"
						alt="<?php echo esc_attr( $product->get_name() ); ?>"
						class="sp-gallery__image" />
				</div>
			<?php endif; ?>
		</div>

		<?php /* === INFO COLUMN === */ ?>
		<div class="sp-info">

			<div class="sp-info__inner">

			<?php /* Category label - uppercase small tracking */ ?>
				<?php if ( $categories ) : ?>
					<p class="sp-info__label"><?php echo wp_strip_all_tags( $categories ); // phpcs:ignore ?></p>
				<?php endif; ?>

				<?php /* Title */ ?>
				<h1 class="sp-info__title"><?php the_title(); ?></h1>

				<?php /* Short description / Description */ ?>
				<?php if ( $short_description || $description ) : ?>
				<div class="sp-info__description-block">
					<h3 class="sp-info__section-label"><?php esc_html_e( 'Mô tả sản phẩm', 'lacadev-client' ); ?></h3>
					<div class="sp-info__description">
						<?php echo wp_kses_post( $short_description ?: wpautop( $description ) ); ?>
					</div>
				</div>
				<?php endif; ?>

				<?php /* Price */ ?>
				<div class="sp-info__price">
					<?php echo $product->get_price_html(); // phpcs:ignore ?>
				</div>

				<?php /* Add to Cart form - call specific function, no hooks */ ?>
				<div class="sp-info__cart">
					<?php
					// Stock status message
					$availability = $product->get_availability();
					if ( ! $product->is_in_stock() ) :
					?>
						<p class="sp-info__availability sp-info__availability--out">
							<?php esc_html_e( 'Hết hàng', 'lacadev-client' ); ?>
						</p>
					<?php endif; ?>
					<?php
					if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) :
						wc_get_template( 'single-product/add-to-cart/simple.php' );
					elseif ( $product->is_type( 'variable' ) ) :
						wc_get_template( 'single-product/add-to-cart/variable.php', array( 'product' => $product ) );
					elseif ( $product->is_type( 'external' ) ) :
						wc_get_template( 'single-product/add-to-cart/external.php' );
					endif;
					?>
				</div>

	

				<?php /* Attributes specs */ ?>
				<?php
				$weight     = $product->get_weight();
				$dimensions = $product->get_dimensions( false );
				$has_dims   = ! empty( $dimensions['width'] ) || ! empty( $dimensions['height'] );
				$attributes = $product->get_attributes();
				?>
				<?php if ( $has_dims || $weight || ! empty( $attributes ) ) : ?>
				<div class="sp-info__specs">
					<?php if ( $has_dims ) : ?>
					<div class="sp-info__spec-item">
						<p class="sp-info__spec-label"><?php esc_html_e( 'Kích thước', 'lacadev-client' ); ?></p>
						<p class="sp-info__spec-value"><?php echo esc_html( wc_format_dimensions( $dimensions ) ); ?></p>
					</div>
					<?php endif; ?>
					<?php if ( $weight ) : ?>
					<div class="sp-info__spec-item">
						<p class="sp-info__spec-label"><?php esc_html_e( 'Trọng lượng', 'lacadev-client' ); ?></p>
						<p class="sp-info__spec-value"><?php echo esc_html( $weight . ' ' . get_option( 'woocommerce_weight_unit' ) ); ?></p>
					</div>
					<?php endif; ?>
					<?php foreach ( $attributes as $attribute ) :
						if ( $attribute->get_visible() ) :
							$values = array();
							if ( $attribute->is_taxonomy() ) {
								$terms  = get_terms( array( 'taxonomy' => $attribute->get_name(), 'object_ids' => $product_id ) );
								foreach ( $terms as $term ) {
									$values[] = $term->name;
								}
							} else {
								$values = $attribute->get_options();
							}
							$label = wc_attribute_label( $attribute->get_name() );
					?>
					<div class="sp-info__spec-item">
						<p class="sp-info__spec-label"><?php echo esc_html( $label ); ?></p>
						<p class="sp-info__spec-value"><?php echo esc_html( implode( ', ', $values ) ); ?></p>
					</div>
					<?php
						endif;
					endforeach; ?>
				</div>
				<?php endif; ?>

				<?php /* SKU + Meta */ ?>
				<div class="sp-info__meta">
					<?php do_action( 'woocommerce_product_meta_start' ); ?>
					<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
						<span class="sp-info__meta-sku"><?php esc_html_e( 'SKU', 'lacadev-client' ); ?>: <?php echo $product->get_sku() ? esc_html( $product->get_sku() ) : esc_html__( 'N/A', 'woocommerce' ); ?></span>
					<?php endif; ?>
					<?php do_action( 'woocommerce_product_meta_end' ); ?>
				</div>

			</div><!-- .sp-info__inner -->
		</div><!-- .sp-info -->

	</div><!-- .sp-layout -->

	<?php /* === TABS / REVIEWS / RELATED BELOW === */ ?>
	<div class="sp-below">
		<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display          - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>

</div><!-- #product-xxx -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
