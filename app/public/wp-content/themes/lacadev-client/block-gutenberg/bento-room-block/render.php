<?php
/**
 * Bento Room Block — Render Template.
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$taxonomy       = esc_attr( $attributes['taxonomy'] ?? 'category' );
$term_ids       = array_map( 'absint', $attributes['termIds'] ?? [] );
$main_term_id   = absint( $attributes['mainTermId'] ?? 0 );
$curation_label = esc_html( $attributes['curationLabel'] ?? '' );
$cta_text       = esc_html( $attributes['ctaText'] ?? '' );
$bg_color       = $attributes['backgroundColor'] ?? '';
$is_full_width  = (bool) ( $attributes['isFullWidth'] ?? false );

$section_style = $bg_color ? 'background-color: ' . esc_attr( $bg_color ) . ';' : '';

// Inline helper — static closure avoids "Cannot redeclare" when block is used multiple times on the same page.
$get_term_image = static function ( $term ) {
	$tax = $term->taxonomy;
	$tid = $term->term_id;

	// ACF Pro / ACF Free
	if ( function_exists( 'get_field' ) ) {
		$acf = get_field( 'term_image', $tax . '_' . $tid );
		if ( $acf ) {
			return is_array( $acf ) ? esc_url( $acf['url'] ) : esc_url( $acf );
		}
	}

	// Custom meta: term_image_url
	$url = get_term_meta( $tid, 'term_image_url', true );
	if ( $url ) {
		return esc_url( $url );
	}

	// WooCommerce / taxonomy image plugins store attachment ID in 'thumbnail_id'
	$thumb_id = get_term_meta( $tid, 'thumbnail_id', true );
	if ( $thumb_id ) {
		$src = wp_get_attachment_image_url( absint( $thumb_id ), 'large' );
		if ( $src ) {
			return esc_url( $src );
		}
	}

	return '';
};

// Fetch terms (max 3)
$query_args = [
	'taxonomy'   => $taxonomy,
	'hide_empty' => false,
	'number'     => 3,
];
if ( ! empty( $term_ids ) ) {
	$query_args['include'] = array_slice( $term_ids, 0, 3 );
	$query_args['orderby'] = 'include';
}
$terms = get_terms( $query_args );
if ( is_wp_error( $terms ) || empty( $terms ) ) {
	return;
}

// Resolve main / small terms
$main_term   = null;
$small_terms = [];
if ( $main_term_id ) {
	foreach ( $terms as $t ) {
		if ( (int) $t->term_id === $main_term_id ) {
			$main_term = $t;
		} else {
			$small_terms[] = $t;
		}
	}
}
if ( ! $main_term ) {
	$main_term   = $terms[0];
	$small_terms = array_slice( $terms, 1, 2 );
}

$main_img  = $get_term_image( $main_term );
$main_link = esc_url( get_term_link( $main_term ) );
$main_name = esc_html( $main_term->name );

$container_class = $is_full_width ? '' : 'max-w-screen-2xl mx-auto';
?>

<section <?php echo get_block_wrapper_attributes( [
	'class' => 'block-bento-room',
	'style' => $section_style,
] ); ?>>

	<div class="<?php echo esc_attr( $container_class ); ?> px-8 mb-32">

		<div class="grid grid-cols-12 grid-rows-2 gap-8 bento-room-grid">

			<?php /* ── Main Panel (8 cols / 2 rows) ── */ ?>
			<a href="<?php echo $main_link; ?>" class="col-span-12 lg:col-span-8 row-span-2 relative rounded-2xl overflow-hidden group block">
				<?php if ( $main_img ) : ?>
					<img
						src="<?php echo $main_img; ?>"
						alt="<?php echo $main_name; ?>"
						class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000"
						loading="eager"
					/>
				<?php else : ?>
					<div class="w-full h-full flex items-center justify-center text-6xl bg-stone-100">🏠</div>
				<?php endif; ?>

				<div class="absolute inset-0 bg-stone-900/20 group-hover:bg-stone-900/40 transition-colors duration-500"></div>

				<div class="absolute bottom-16 left-16">
					<?php if ( $curation_label ) : ?>
						<span class="font-label text-xs uppercase tracking-widest text-white mb-2 block">
							<?php echo esc_html( $curation_label ); ?>
						</span>
					<?php endif; ?>

					<h2 class="font-headline text-6xl font-bold text-white mb-8">
						<?php echo $main_name; ?>
					</h2>

					<?php if ( $cta_text ) : ?>
						<span class="bg-white text-stone-900 px-8 py-4 rounded-full font-label text-xs uppercase tracking-widest hover:bg-stone-100 transition-all inline-block">
							<?php echo esc_html( $cta_text ); ?>
						</span>
					<?php endif; ?>
				</div>
			</a>

			<?php /* ── Small Panels (4 cols / 1 row each) ── */ ?>
			<?php foreach ( $small_terms as $small ) :
				$s_img  = $get_term_image( $small );
				$s_link = esc_url( get_term_link( $small ) );
				$s_name = esc_html( $small->name );
			?>
				<a href="<?php echo $s_link; ?>" class="col-span-12 lg:col-span-4 row-span-1 relative rounded-2xl overflow-hidden group block">
					<?php if ( $s_img ) : ?>
						<img
							src="<?php echo $s_img; ?>"
							alt="<?php echo $s_name; ?>"
							class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000"
							loading="lazy"
						/>
					<?php else : ?>
						<div class="w-full h-full flex items-center justify-center text-5xl bg-stone-100">🛋</div>
					<?php endif; ?>

					<div class="absolute inset-0 bg-stone-900/10"></div>

					<div class="absolute inset-0 flex flex-col justify-center items-center text-center p-8">
						<h3 class="font-headline text-3xl font-bold text-white mb-4">
							<?php echo $s_name; ?>
						</h3>
						<span class="font-label text-xs uppercase tracking-widest text-white border-b border-white pb-1">
							<?php esc_html_e( 'Explore Collections', 'laca' ); ?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>

		</div>
	</div>
</section>
