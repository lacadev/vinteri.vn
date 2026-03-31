<?php
/**
 * The Template for displaying product archives - Editorial Layout (Vietnamese)
 * Layout: sidebar filters + product grid with AJAX sort & price filter
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

// Remove WC default sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
// Remove WC default content wrappers (we build our own layout)
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
// Remove breadcrumb from default hook position
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
// Remove result count and ordering (we use our own in shop-archive-header)
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Set 3 columns for product loop
add_filter(
	'loop_shop_columns',
	function () {
		return 3;
	}
);

get_header( 'shop' );
?>

<main class="woocommerce-shop-page container">
	<div class="shop-layout">

		<?php /* ===== SIDEBAR ===== */ ?>
		<aside class="shop-sidebar" id="shop-sidebar">

			<?php /* Categories */ ?>
			<div class="shop-sidebar__section">
				<h3 class="shop-sidebar__title">Danh mục</h3>
				<ul class="shop-sidebar__cat-list">

					<?php $is_all_active = ! is_product_category() && ! is_product_tag(); ?>
					<li>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
							class="shop-sidebar__cat-item <?php echo $is_all_active ? 'shop-sidebar__cat-item--active' : ''; ?>">
							<span class="shop-sidebar__cat-name">Tất cả sản phẩm</span>
							<span class="shop-sidebar__cat-count"><?php echo esc_html( absint( wp_count_posts( 'product' )->publish ) ); ?></span>
						</a>
					</li>

					<?php
					$current_cat = is_product_category() ? get_queried_object() : null;
					$categories  = get_terms(
						[
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'parent'     => 0,
							'orderby'    => 'name',
						]
					);
					if ( ! is_wp_error( $categories ) && $categories ) :
						foreach ( $categories as $cat ) :
							$is_active = $current_cat && ( $current_cat->term_id === $cat->term_id );
							?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
									class="shop-sidebar__cat-item <?php echo $is_active ? 'shop-sidebar__cat-item--active' : ''; ?>">
									<span class="shop-sidebar__cat-name"><?php echo esc_html( $cat->name ); ?></span>
									<span class="shop-sidebar__cat-count"><?php echo str_pad( absint( $cat->count ), 2, '0', STR_PAD_LEFT ); ?></span>
								</a>
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>

			<?php /* Custom Price Filter - AJAX via form submit */ ?>
			<div class="shop-sidebar__section">
				<h3 class="shop-sidebar__title">Lọc theo giá</h3>
				<?php
				global $wpdb;
				$price_lookup = $wpdb->get_row(
					"SELECT MIN(min_price) as min_price, MAX(max_price) as max_price
					FROM {$wpdb->wc_product_meta_lookup} l
					INNER JOIN {$wpdb->posts} p ON l.product_id = p.ID
					WHERE p.post_status = 'publish' AND p.post_type = 'product'"
				);
				$db_min = $price_lookup ? floatval( $price_lookup->min_price ) : 0;
				$db_max = $price_lookup ? floatval( $price_lookup->max_price ) : 10000000;
				// Pads max by 10% to make slider useful
				if ( $db_min === $db_max ) {
					$db_max = $db_min * 2 ?: 10000000;
				}
				$cur_min = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : $db_min;
				$cur_max = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : $db_max;

				$shop_url = wc_get_page_permalink( 'shop' );
				if ( is_product_category() ) {
					$shop_url = get_term_link( get_queried_object() );
				}
				// Keep other query params (sort, etc.)
				$extra_params = '';
				if ( isset( $_GET['orderby'] ) ) {
					$extra_params .= '<input type="hidden" name="orderby" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ) . '">';
				}
				?>
				<form class="shop-price-filter" action="<?php echo esc_url( $shop_url ); ?>" method="get" id="shop-price-filter-form">
					<?php echo $extra_params; ?>
					<div class="shop-price-filter__slider" id="price-range-slider">
						<div class="shop-price-filter__track">
							<div class="shop-price-filter__fill" id="price-fill"></div>
						</div>
						<input type="range" class="shop-price-filter__range shop-price-filter__range--min"
							id="price-range-min" name="min_price"
							min="<?php echo esc_attr( $db_min ); ?>"
							max="<?php echo esc_attr( $db_max ); ?>"
							step="10000"
							value="<?php echo esc_attr( $cur_min ); ?>">
						<input type="range" class="shop-price-filter__range shop-price-filter__range--max"
							id="price-range-max" name="max_price"
							min="<?php echo esc_attr( $db_min ); ?>"
							max="<?php echo esc_attr( $db_max ); ?>"
							step="10000"
							value="<?php echo esc_attr( $cur_max ); ?>">
					</div>
					<div class="shop-price-filter__labels">
						<span id="price-label-min"><?php echo wc_price( $cur_min ); ?></span>
						<span id="price-label-max"><?php echo wc_price( $cur_max ); ?></span>
					</div>
					<button type="submit" class="shop-price-filter__btn">Lọc</button>
				</form>
				<script>
				(function(){
					var slider = document.getElementById('price-range-slider');
					if(!slider) return;
					var rangeMin = document.getElementById('price-range-min');
					var rangeMax = document.getElementById('price-range-max');
					var labelMin = document.getElementById('price-label-min');
					var labelMax = document.getElementById('price-label-max');
					var fill = document.getElementById('price-fill');
					var dbMin = parseFloat(rangeMin.min);
					var dbMax = parseFloat(rangeMin.max);
					function fmt(v){ return (new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND',minimumFractionDigits:0})).format(v); }
					function update(){
						var minV = parseFloat(rangeMin.value);
						var maxV = parseFloat(rangeMax.value);
						if(minV > maxV){ rangeMax.value = minV; maxV = minV; }
						if(maxV < minV){ rangeMin.value = maxV; minV = maxV; }
						labelMin.textContent = fmt(minV);
						labelMax.textContent = fmt(maxV);
						var pct = (dbMax - dbMin) || 1;
						fill.style.left  = ((minV - dbMin) / pct * 100) + '%';
						fill.style.right = ((dbMax - maxV) / pct * 100) + '%';
					}
					rangeMin.addEventListener('input', update);
					rangeMax.addEventListener('input', update);
					update();
				})();
				</script>
			</div>

		</aside>

		<?php /* ===== MAIN CONTENT ===== */ ?>
		<div class="shop-content" id="shop-products">

			<?php /* Archive header: title + sort */ ?>
			<header class="shop-archive-header">
				<div class="shop-archive-header__title-area">
					<?php if ( is_product_category() ) : ?>
						<h1 class="shop-archive-header__title"><?php single_term_title(); ?></h1>
						<?php
						$term_desc = term_description();
						if ( $term_desc ) :
							echo '<p class="shop-archive-header__description">' . wp_kses_post( $term_desc ) . '</p>';
						endif;
						?>
					<?php elseif ( is_search() ) : ?>
						<h1 class="shop-archive-header__title">
							<?php printf( 'Tìm kiếm: "%s"', esc_html( get_search_query() ) ); ?>
						</h1>
					<?php else : ?>
						<h1 class="shop-archive-header__title"><?php woocommerce_page_title(); ?></h1>
					<?php endif; ?>
				</div>

				<?php /* Sort by - WC renders a <form> that WooCommerce JS handles natively */ ?>
				<div class="shop-archive-header__sort">
					<span class="shop-archive-header__sort-label">Sắp xếp theo:</span>
					<?php woocommerce_catalog_ordering(); ?>
				</div>
			</header>


			<?php /* Notices */ ?>
			<?php woocommerce_output_all_notices(); ?>

			<?php /* Product loop */ ?>
			<?php if ( woocommerce_product_loop() ) : ?>

				<?php woocommerce_product_loop_start(); ?>

				<?php
				if ( wc_get_loop_prop( 'total' ) ) :
					while ( have_posts() ) :
						the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
				endif;
				?>

				<?php woocommerce_product_loop_end(); ?>

				<?php /* Pagination */ ?>
				<?php do_action( 'woocommerce_after_shop_loop' ); ?>

			<?php else : ?>
				<?php do_action( 'woocommerce_no_products_found' ); ?>
			<?php endif; ?>

		</div><!-- .shop-content -->
	</div><!-- .shop-layout -->
</main>

<?php get_footer( 'shop' ); ?>
