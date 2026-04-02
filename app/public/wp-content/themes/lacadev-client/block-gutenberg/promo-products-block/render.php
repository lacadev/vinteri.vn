<?php
/**
 * Promo Products Block — Render Template.
 * Hiển thị DANH MỤC sản phẩm WooCommerce dạng editorial promo:
 * 1 featured large (left) + 1 promo top + N small cards (right).
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Guard: WooCommerce must be active ──────────────────────────────────────────
if ( ! class_exists( 'WooCommerce' ) ) {
	echo '<p style="padding:2rem;text-align:center;color:#888;">'
		. esc_html__( 'WooCommerce chưa được kích hoạt.', 'laca' )
		. '</p>';
	return;
}

// ── Attributes ────────────────────────────────────────────────────────────────
$featured_cta     = esc_html( $attributes['featuredCtaText']   ?? 'Shop Now' );
$promo_cta        = esc_html( $attributes['promoCtaText']      ?? 'Shop Now' );
$num_categories   = absint( $attributes['numberOfCategories']  ?? 4 );
$orderby          = $attributes['orderby']                     ?? 'count';
$hide_empty       = (bool) ( $attributes['hideEmpty']          ?? true );
$parent_id        = absint( $attributes['parentCategoryId']    ?? 0 );
$bg_color         = $attributes['backgroundColor']             ?? '';
$container_layout = $attributes['containerLayout']             ?? 'container';

// ── Validate orderby ───────────────────────────────────────────────────────────
$valid_orderby = [ 'count', 'name', 'slug', 'menu_order', 'rand' ];
if ( ! in_array( $orderby, $valid_orderby, true ) ) {
	$orderby = 'count';
}

// ── get_terms: lấy danh mục sản phẩm ─────────────────────────────────────────
$terms_args = [
	'taxonomy'   => 'product_cat',
	'orderby'    => $orderby,
	'order'      => ( $orderby === 'count' ) ? 'DESC' : 'ASC',
	'hide_empty' => $hide_empty,
	'number'     => max( 4, $num_categories ),
	'parent'     => $parent_id,
	'exclude'    => [ get_option( 'default_product_cat', 0 ) ], // loại bỏ "Uncategorized"
];

$terms = get_terms( $terms_args );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	// Try again without excluding uncategorized
	$terms_args['exclude'] = [];
	$terms = get_terms( $terms_args );
}

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	echo '<p style="padding:2rem;text-align:center;color:#888;">'
		. esc_html__( 'Không tìm thấy danh mục sản phẩm nào.', 'laca' )
		. '</p>';
	return;
}

// ── Slot categories into positions ────────────────────────────────────────────
$c0         = $terms[0] ?? null; // Featured large
$c1         = $terms[1] ?? null; // Promo top
$small_cats = array_slice( $terms, 2 ); // Small cards
$small_count = count( $small_cats );

// Bottom grid column classes
if ( $small_count >= 3 ) {
	$bottom_cols = 'grid-cols-3';
} elseif ( $small_count === 2 ) {
	$bottom_cols = 'grid-cols-2';
} else {
	$bottom_cols = 'grid-cols-1';
}

// ── Helper: get category thumbnail ID (from WooCommerce thumbnail meta) ───────
if ( ! function_exists( 'laca_pp_get_cat_thumb_id' ) ) {
	function laca_pp_get_cat_thumb_id( $term_id ) {
		$thumb_id = get_term_meta( $term_id, 'thumbnail_id', true );
		return $thumb_id ? absint( $thumb_id ) : 0;
	}
}

// ── Layout variables ───────────────────────────────────────────────────────────
$section_style   = $bg_color ? 'background-color: ' . esc_attr( $bg_color ) . ';' : '';
$container_class = ( $container_layout === 'container-fluid' ) ? '' : 'max-w-[1920px] mx-auto';
?>

<section <?php echo get_block_wrapper_attributes( [
	'class' => 'block-promo-products px-6 md:px-12 py-20',
	'style' => $section_style,
] ); ?>>

	<div class="<?php echo esc_attr( $container_class ); ?>">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 promo-grid">

			<?php /* ──────────────── LEFT: Featured Large Card ──────────────── */ ?>
			<?php if ( $c0 ) :
				$c0_url      = esc_url( get_term_link( $c0 ) );
				$c0_thumb_id = laca_pp_get_cat_thumb_id( $c0->term_id );
			?>
			<div class="relative rounded-lg overflow-hidden group flex items-center p-12 md:p-16 min-h-[24rem] md:min-h-0" style="background-color:var(--color-surface-container-low,#F5F5F5)">
				<div class="z-10 w-1/2">
					<span class="text-xs font-label uppercase tracking-widest text-on-surface-variant mb-4 block">
						<?php
						$desc_text = wp_strip_all_tags( $c0->description );
						echo $desc_text
							? esc_html( wp_trim_words( $desc_text, 6 ) )
							: esc_html( sprintf( _n( '%s sản phẩm', '%s sản phẩm', $c0->count, 'laca' ), number_format_i18n( $c0->count ) ) );
						?>
					</span>
					<h2 class="text-4xl md:text-5xl font-headline mb-8 leading-tight">
						<?php echo esc_html( $c0->name ); ?>
					</h2>
					<a href="<?php echo $c0_url; ?>"
					   class="px-8 py-3 border border-on-surface text-on-surface hover:bg-on-surface hover:text-background transition-colors font-label text-sm uppercase tracking-widest">
						<?php echo $featured_cta; ?>
					</a>
				</div>
				<div class="absolute right-0 top-1/2 -translate-y-1/2 w-1/2 h-4/5">
					<?php if ( $c0_thumb_id ) : ?>
						<?php theResponsiveImage( $c0_thumb_id, 'tablet', [
							'class'   => 'w-full h-full object-contain promo-card-img group-hover:scale-105 transition-transform duration-700',
							'loading' => 'lazy',
							'alt'     => esc_attr( $c0->name ),
						] ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" alt="<?php echo esc_attr( $c0->name ); ?>" class="w-full h-full object-contain" loading="lazy" />
					<?php endif; ?>
				</div>
			</div>
			<?php else : ?>
			<div class="rounded-lg flex items-center justify-center min-h-[24rem] p-8" style="background-color:var(--color-surface-container-low,#F5F5F5);color:var(--color-on-surface-variant,#49454F)">
				<?php esc_html_e( 'Chưa có danh mục', 'laca' ); ?>
			</div>
			<?php endif; ?>

			<?php /* ──────────────── RIGHT: Split Grid ──────────────── */ ?>
			<div class="grid grid-rows-2 gap-6">

				<?php /* ── Top Right: Promo Card ── */ ?>
				<?php if ( $c1 ) :
					$c1_url      = esc_url( get_term_link( $c1 ) );
					$c1_thumb_id = laca_pp_get_cat_thumb_id( $c1->term_id );
				?>
				<div class="rounded-lg overflow-hidden group flex items-center p-8 md:p-12 relative" style="background-color:var(--color-surface-container-low,#F5F5F5)">
					<div class="z-10 w-1/2">
						<span class="text-xs font-label uppercase tracking-widest text-on-surface-variant mb-2 block">
							<?php
							$desc1 = wp_strip_all_tags( $c1->description );
							echo $desc1
								? esc_html( wp_trim_words( $desc1, 6 ) )
								: esc_html( sprintf( _n( '%s sản phẩm', '%s sản phẩm', $c1->count, 'laca' ), number_format_i18n( $c1->count ) ) );
							?>
						</span>
						<h3 class="text-3xl font-headline mb-6">
							<?php echo esc_html( $c1->name ); ?>
						</h3>
						<a href="<?php echo $c1_url; ?>"
						   class="px-6 py-2 border border-on-surface text-on-surface hover:bg-on-surface hover:text-background transition-colors font-label text-xs uppercase tracking-widest">
							<?php echo $promo_cta; ?>
						</a>
					</div>
					<div class="absolute right-0 top-1/2 -translate-y-1/2 w-1/2 h-full py-4">
						<?php if ( $c1_thumb_id ) : ?>
							<?php theResponsiveImage( $c1_thumb_id, 'mobile', [
								'class'   => 'w-full h-full object-contain promo-card-img group-hover:scale-105 transition-transform duration-700',
								'loading' => 'lazy',
								'alt'     => esc_attr( $c1->name ),
							] ); ?>
						<?php else : ?>
							<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" alt="<?php echo esc_attr( $c1->name ); ?>" class="w-full h-full object-contain" loading="lazy" />
						<?php endif; ?>
					</div>
				</div>
				<?php else : ?>
				<div class="rounded-lg flex items-center justify-center p-8" style="background-color:var(--color-surface-container-low,#F5F5F5);color:var(--color-on-surface-variant,#49454F)">
					<?php esc_html_e( '–', 'laca' ); ?>
				</div>
				<?php endif; ?>

				<?php /* ── Bottom Right: Small Category Cards ── */ ?>
				<?php if ( ! empty( $small_cats ) ) : ?>
				<div class="grid <?php echo esc_attr( $bottom_cols ); ?> gap-6">
					<?php foreach ( $small_cats as $sc ) :
						$sc_url      = esc_url( get_term_link( $sc ) );
						$sc_thumb_id = laca_pp_get_cat_thumb_id( $sc->term_id );
					?>
					<a href="<?php echo $sc_url; ?>"
					   class="rounded-lg overflow-hidden group p-6 flex flex-col justify-between no-underline hover:no-underline" style="background-color:var(--color-surface-container-low,#F5F5F5)">
						<div>
							<span class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-2 block">
								<?php echo esc_html( sprintf( _n( '%s sản phẩm', '%s sản phẩm', $sc->count, 'laca' ), number_format_i18n( $sc->count ) ) ); ?>
							</span>
							<h3 class="text-lg font-headline leading-tight text-on-surface">
								<?php echo esc_html( $sc->name ); ?>
							</h3>
						</div>
						<div class="mt-4 flex-grow flex items-center justify-center h-32 overflow-hidden">
							<?php if ( $sc_thumb_id ) : ?>
								<?php theResponsiveImage( $sc_thumb_id, 'mobile', [
									'class'   => 'w-full h-full object-contain promo-card-img group-hover:scale-105 transition-transform duration-700',
									'loading' => 'lazy',
									'alt'     => esc_attr( $sc->name ),
								] ); ?>
							<?php else : ?>
								<img src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ); ?>" alt="<?php echo esc_attr( $sc->name ); ?>" class="w-full h-full object-contain" loading="lazy" />
							<?php endif; ?>
						</div>
					</a>
					<?php endforeach; ?>
				</div>
				<?php else : ?>
				<div class="grid grid-cols-2 gap-6">
					<?php for ( $i = 0; $i < 2; $i++ ) : ?>
					<div class="rounded-lg p-6 flex items-center justify-center" style="background-color:var(--color-surface-container-low,#F5F5F5);color:var(--color-on-surface-variant,#49454F)">
						<?php esc_html_e( '—', 'laca' ); ?>
					</div>
					<?php endfor; ?>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>

</section>
