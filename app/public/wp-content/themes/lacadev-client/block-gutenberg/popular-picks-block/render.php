<?php
/**
 * Popular Picks Block — Render Template.
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail nếu WooCommerce chưa active
if ( ! class_exists( 'WooCommerce' ) ) {
	echo '<p style="text-align:center;padding:2rem;color:#888;">'
		. esc_html__( 'Popular Picks Block: WooCommerce chưa được kích hoạt.', 'laca' )
		. '</p>';
	return;
}

// ── Attributes ────────────────────────────────────────────────────────────────
$section_title        = esc_html( $attributes['sectionTitle'] ?? 'Popular Picks' );
$number_of_products   = absint( $attributes['numberOfProducts'] ?? 4 );
$orderby              = sanitize_key( $attributes['orderby'] ?? 'date' );
$product_cat_ids      = array_map( 'absint', $attributes['productCategoryIds'] ?? [] );
$show_category_filter = (bool) ( $attributes['showCategoryFilter'] ?? true );
$bg_color             = $attributes['backgroundColor'] ?? '';
$container_layout     = $attributes['containerLayout'] ?? 'container';

$section_style   = $bg_color ? 'background-color: ' . esc_attr( $bg_color ) . ';' : '';
$container_class = $container_layout === 'container-fluid' ? 'px-8' : 'px-8 max-w-screen-2xl mx-auto';

// ── WC Query: products ────────────────────────────────────────────────────────
$wc_orderby = 'date';
$wc_order   = 'DESC';
switch ( $orderby ) {
	case 'popularity':
		$wc_orderby = 'meta_value_num';
		$meta_key   = 'total_sales';
		break;
	case 'price':
		$wc_orderby = 'meta_value_num';
		$meta_key   = '_price';
		$wc_order   = 'ASC';
		break;
	case 'rand':
		$wc_orderby = 'rand';
		break;
	case 'menu_order':
		$wc_orderby = 'menu_order';
		break;
	default:
		$wc_orderby = 'date';
}

$query_args = [
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => $number_of_products,
	'orderby'        => $wc_orderby,
	'order'          => $wc_order,
	'tax_query'      => [
		[
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'exclude-from-catalog',
			'operator' => 'NOT IN',
		],
	],
];

if ( isset( $meta_key ) ) {
	$query_args['meta_key'] = $meta_key; // phpcs:ignore WordPress.DB.SlowDBQuery
}

if ( ! empty( $product_cat_ids ) ) {
	$query_args['tax_query'][] = [
		'taxonomy' => 'product_cat',
		'field'    => 'term_id',
		'terms'    => $product_cat_ids,
		'operator' => 'IN',
	];
}

$products_query = new WP_Query( $query_args );

if ( ! $products_query->have_posts() ) {
	return;
}

// ── Fetch categories for filter tabs ─────────────────────────────────────────
$filter_cats = [];
if ( $show_category_filter ) {
	$cat_args = [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'number'     => 8,
	];
	if ( ! empty( $product_cat_ids ) ) {
		$cat_args['include'] = $product_cat_ids;
	}
	$filter_cats = get_terms( $cat_args );
	if ( is_wp_error( $filter_cats ) ) {
		$filter_cats = [];
	}
}

// ── Unique block ID for JS ────────────────────────────────────────────────────
$block_id = 'pp-block-' . wp_unique_id();
?>

<section <?php echo get_block_wrapper_attributes( [
	'class' => 'block-popular-picks bg-surface-container-low py-32 mb-32',
	'style' => $section_style,
	'id'    => esc_attr( $block_id ),
] ); ?>>

	<div class="<?php echo esc_attr( $container_class ); ?>">

		<?php /* ── Heading ── */ ?>
		<div class="text-center mb-16">
			<h2 class="font-headline text-5xl font-bold mb-8">
				<?php echo esc_html( $section_title ); ?>
			</h2>

			<?php /* ── Category filter tabs ── */ ?>
			<?php if ( $show_category_filter && ! empty( $filter_cats ) ) : ?>
			<div class="flex justify-center gap-8 overflow-x-auto pb-4 no-scrollbar" role="tablist" data-pp-tabrow>
				<button
					class="pp-tab-btn font-label text-xs uppercase tracking-[0.2em] pb-2 whitespace-nowrap is-active"
					data-cat="all"
					role="tab"
					aria-selected="true"
				>
					<?php esc_html_e( 'All Items', 'laca' ); ?>
				</button>
				<?php foreach ( $filter_cats as $cat ) : ?>
				<button
					class="pp-tab-btn font-label text-xs uppercase tracking-[0.2em] text-on-surface-variant hover:text-stone-900 pb-2 whitespace-nowrap transition-colors"
					data-cat="<?php echo esc_attr( $cat->slug ); ?>"
					role="tab"
					aria-selected="false"
				>
					<?php echo esc_html( $cat->name ); ?>
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<?php /* ── Product Grid ── */ ?>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10" data-pp-grid>

			<?php while ( $products_query->have_posts() ) :
				$products_query->the_post();
				$product = wc_get_product( get_the_ID() );
				if ( ! $product ) {
					continue;
				}

				$product_name  = esc_html( $product->get_name() );
				$product_price = $product->get_price_html();
				$product_url   = esc_url( get_permalink() );
				$product_img   = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
				$product_img   = $product_img ? esc_url( $product_img ) : '';

				// Collect category slugs for JS filtering
				$cat_terms = get_the_terms( get_the_ID(), 'product_cat' );
				$cat_slugs = [];
				if ( $cat_terms && ! is_wp_error( $cat_terms ) ) {
					$cat_slugs = array_map( fn( $t ) => $t->slug, $cat_terms );
				}
				$data_cats = esc_attr( implode( ' ', $cat_slugs ) );
			?>
			<div class="group" data-pp-card data-cats="<?php echo $data_cats; ?>">

				<div class="relative aspect-[3/4] rounded-lg overflow-hidden mb-6">
					<?php if ( $product_img ) : ?>
						<a href="<?php echo $product_url; ?>" tabindex="-1" aria-hidden="true">
							<img
								src="<?php echo $product_img; ?>"
								alt="<?php echo $product_name; ?>"
								class="pp-product-img w-full h-full object-cover"
								loading="lazy"
								decoding="async"
							/>
						</a>
					<?php else : ?>
						<div class="w-full h-full flex items-center justify-center text-6xl bg-stone-100">🛋</div>
					<?php endif; ?>
				</div>

				<div class="space-y-1">
					<h3 class="font-headline text-xl">
						<a href="<?php echo $product_url; ?>" class="hover:underline">
							<?php echo $product_name; ?>
						</a>
					</h3>
					<p class="font-body text-on-surface-variant">
						<?php echo wp_kses_post( $product_price ); ?>
					</p>
				</div>

			</div>
			<?php endwhile; wp_reset_postdata(); ?>

		</div>
	</div>

</section>

<?php /* ── Category Tab Filter — Vanilla JS ── */ ?>
<?php if ( $show_category_filter && ! empty( $filter_cats ) ) : ?>
<script>
( function () {
	const section = document.getElementById( <?php echo wp_json_encode( $block_id ); ?> );
	if ( ! section ) return;

	const tabRow = section.querySelector( '[data-pp-tabrow]' );
	const cards  = section.querySelectorAll( '[data-pp-card]' );

	if ( ! tabRow ) return;

	tabRow.addEventListener( 'click', function ( e ) {
		const btn = e.target.closest( '[data-cat]' );
		if ( ! btn ) return;

		const cat = btn.dataset.cat;

		// Update active tab
		tabRow.querySelectorAll( '.pp-tab-btn' ).forEach( function ( b ) {
			b.classList.remove( 'is-active' );
			b.setAttribute( 'aria-selected', 'false' );
		} );
		btn.classList.add( 'is-active' );
		btn.setAttribute( 'aria-selected', 'true' );

		// Filter cards
		cards.forEach( function ( card ) {
			if ( cat === 'all' ) {
				card.style.display = '';
			} else {
				const cardCats = ( card.dataset.cats || '' ).split( ' ' );
				card.style.display = cardCats.includes( cat ) ? '' : 'none';
			}
		} );
	} );
} )();
</script>
<?php endif; ?>
