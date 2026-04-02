<?php
/**
 * Product Showcase Block — Render Template.
 *
 * Hiển thị sản phẩm WooCommerce dạng grid theo 2 chế độ:
 * - auto:   Tự động lấy theo danh mục, thứ tự, số lượng.
 * - manual: Hiển thị các sản phẩm được chỉ định thủ công.
 *
 * HTML giữ nguyên 100% Tailwind classes từ thiết kế gốc (Stitch).
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Guard: WooCommerce required ────────────────────────────────────────────────
if ( ! class_exists( 'WooCommerce' ) ) {
	echo '<p style="padding:2rem;text-align:center;color:#888;">'
		. esc_html__( 'WooCommerce chưa được kích hoạt.', 'laca' )
		. '</p>';
	return;
}

// ── Read Attributes ────────────────────────────────────────────────────────────
$mode              = in_array( $attributes['mode'] ?? 'auto', [ 'auto', 'manual' ], true )
	? $attributes['mode']
	: 'auto';
$number_of_products = absint( $attributes['numberOfProducts'] ?? 4 );
$columns            = absint( $attributes['columns'] ?? 4 );
$section_title      = esc_html( $attributes['sectionTitle'] ?? 'New Trending Pieces' );
$view_all_text      = esc_html( $attributes['viewAllText'] ?? 'View Full Collection' );
$view_all_url       = esc_url( $attributes['viewAllUrl'] ?? '' );
$show_view_all      = (bool) ( $attributes['showViewAll'] ?? true );
$auto_orderby       = $attributes['autoOrderby'] ?? 'date';
$auto_category_id   = absint( $attributes['autoCategoryId'] ?? 0 );
$manual_product_ids = array_map( 'absint', (array) ( $attributes['manualProductIds'] ?? [] ) );
$bg_color           = $attributes['backgroundColor'] ?? '';
$container_layout   = $attributes['containerLayout'] ?? 'container';

// Sanitize columns: 2, 3, or 4
$columns = in_array( $columns, [ 2, 3, 4 ], true ) ? $columns : 4;

// ── Map orderby ────────────────────────────────────────────────────────────────
$valid_orderby = [
	'date'       => [ 'orderby' => 'date',       'order' => 'DESC' ],
	'popularity' => [ 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC' ],
	'rand'       => [ 'orderby' => 'rand',        'order' => 'DESC' ],
	'price'      => [ 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'ASC' ],
	'price-desc' => [ 'orderby' => 'meta_value_num', 'meta_key' => '_price', 'order' => 'DESC' ],
	'rating'     => [ 'orderby' => 'meta_value_num', 'meta_key' => '_wc_average_rating', 'order' => 'DESC' ],
];

if ( ! isset( $valid_orderby[ $auto_orderby ] ) ) {
	$auto_orderby = 'date';
}

// ── Build WP_Query args ────────────────────────────────────────────────────────
if ( $mode === 'manual' && ! empty( $manual_product_ids ) ) {
	// Manual mode: hiển thị đúng thứ tự đã chọn
	$query_args = [
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => count( $manual_product_ids ),
		'post__in'            => $manual_product_ids,
		'orderby'             => 'post__in',
		'ignore_sticky_posts' => true,
	];
} else {
	// Auto mode
	$od = $valid_orderby[ $auto_orderby ];
	$query_args = [
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => $number_of_products,
		'orderby'             => $od['orderby'],
		'order'               => $od['order'],
		'ignore_sticky_posts' => true,
	];

	if ( isset( $od['meta_key'] ) ) {
		$query_args['meta_key'] = $od['meta_key'];
	}

	if ( $auto_category_id > 0 ) {
		$query_args['tax_query'] = [
			[
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => [ $auto_category_id ],
			],
		];
	}
}

$products_query = new WP_Query( $query_args );

// ── No products fallback ───────────────────────────────────────────────────────
if ( ! $products_query->have_posts() ) {
	echo '<p style="padding:2rem;text-align:center;color:#888;">'
		. esc_html__( 'Không tìm thấy sản phẩm nào.', 'laca' )
		. '</p>';
	wp_reset_postdata();
	return;
}

// ── Column classes ─────────────────────────────────────────────────────────────
$col_class_map = [
	2 => 'grid-cols-1 sm:grid-cols-2',
	3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
	4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
];
$col_classes = $col_class_map[ $columns ] ?? $col_class_map[4];

// ── Inline bg style ────────────────────────────────────────────────────────────
$inline_style = $bg_color ? ' style="background-color:' . esc_attr( $bg_color ) . ';"' : '';

// ── Render ─────────────────────────────────────────────────────────────────────
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block product-showcase-block' ] ); ?><?php echo $inline_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above ?>>
	<div class="<?php echo esc_attr( $container_layout ); ?>">
		<div class="px-6 md:px-12 py-32">

			<?php if ( $section_title || $show_view_all ) : ?>
			<div class="flex flex-col md:flex-row justify-between items-baseline mb-16 gap-4">
				<?php if ( $section_title ) : ?>
					<h2 class="text-4xl font-headline"><?php echo esc_html( $section_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $show_view_all && $view_all_text ) : ?>
					<a class="text-on-surface-variant hover:text-on-surface font-label border-b border-outline-variant transition-all pb-1"
					   href="<?php echo esc_url( $view_all_url ?: get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
						<?php echo esc_html( $view_all_text ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="grid <?php echo esc_attr( $col_classes ); ?> gap-x-8 gap-y-16">
				<?php while ( $products_query->have_posts() ) : $products_query->the_post(); ?>
					<?php
					global $product;
					$product = wc_get_product( get_the_ID() );
					if ( ! $product ) {
						continue;
					}

					$product_url   = get_permalink();
					$product_title = get_the_title();

					// Price
					$price_html = $product->get_price_html();

					// Variation/attribute summary (ví dụ: màu sắc / chất liệu)
					$short_desc = wp_strip_all_tags( get_the_excerpt() );
					$short_desc = $short_desc ? wp_trim_words( $short_desc, 8, '' ) : '';

					// Categories for subtitle
					$cats = wc_get_product_category_list( $product->get_id(), ', ', '', '' );
					$cats = wp_strip_all_tags( $cats );
					?>
					<div class="group cursor-pointer">
						<a href="<?php echo esc_url( $product_url ); ?>" class="block" tabindex="-1" aria-hidden="true">
							<div class="aspect-[4/5] overflow-hidden rounded-lg mb-6" style="background-color:var(--color-surface-container-low,#F5F5F5)">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php theResponsivePostThumbnail( 'mobile', [
										'class'   => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105',
										'loading' => 'lazy',
									] ); ?>
								<?php else : ?>
									<div class="w-full h-full flex items-center justify-center" style="color:var(--color-on-surface-variant,#999);font-size:3rem;">🛒</div>
								<?php endif; ?>
							</div>
						</a>

						<div class="mt-1">
							<a href="<?php echo esc_url( $product_url ); ?>">
								<h3 class="font-headline text-base leading-snug hover:underline mb-1"><?php echo esc_html( $product_title ); ?></h3>
							</a>
							<div class="flex justify-between items-baseline gap-3">
								<?php if ( $cats ) : ?>
									<p class="text-on-surface-variant text-sm font-label leading-tight"><?php echo esc_html( $cats ); ?></p>
								<?php else : ?>
									<span></span>
								<?php endif; ?>
								<?php if ( $price_html ) : ?>
									<span class="font-label text-on-surface shrink-0 text-sm">
										<?php echo wp_kses_post( $price_html ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

		</div>
	</div>
</section>
<?php
wp_reset_postdata();
