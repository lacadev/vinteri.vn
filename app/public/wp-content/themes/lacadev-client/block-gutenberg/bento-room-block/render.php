<?php
/**
 * Bento Room Block — Render Template.
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$taxonomy         = esc_attr( $attributes['taxonomy'] ?? 'category' );
$term_ids         = array_map( 'absint', $attributes['termIds'] ?? [] );
$main_term_id     = absint( $attributes['mainTermId'] ?? 0 );
$curation_label   = esc_html( $attributes['curationLabel'] ?? '' );
$cta_text         = esc_html( $attributes['ctaText'] ?? '' );
$bg_color         = $attributes['backgroundColor'] ?? '';
$is_full_width    = (bool) ( $attributes['isFullWidth'] ?? false );

$section_style = $bg_color ? 'background-color: ' . esc_attr( $bg_color ) . ';' : '';

// Inline helper — no global function to avoid "Cannot redeclare" fatal error
// when the block appears more than once on the same page/request.
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

	// Custom meta: term_image_url (e.g. registered via block REST field)
	$url = get_term_meta( $tid, 'term_image_url', true );
	if ( $url ) {
		return esc_url( $url );
	}

	// WooCommerce / most taxonomy image plugins store the attachment ID in 'thumbnail_id'
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
?>

<section <?php echo get_block_wrapper_attributes( [
	'class' => 'block-bento-room',
	'style' => $section_style,
] ); ?>>

	<div class="<?php echo $is_full_width ? '' : 'max-w-screen-2xl mx-auto'; ?> px-8 py-16">

		<div class="block-bento-room__grid">

			<?php /* ── Main Panel ── */ ?>
			<a href="<?php echo $main_link; ?>" class="block-bento-room__main group">
				<?php if ( $main_img ) : ?>
					<img src="<?php echo $main_img; ?>" alt="<?php echo $main_name; ?>" class="block-bento-room__img" loading="eager" />
				<?php else : ?>
					<div class="block-bento-room__img-placeholder">🏠</div>
				<?php endif; ?>
				<div class="block-bento-room__overlay"></div>
				<div class="block-bento-room__main-content">
					<?php if ( $curation_label ) : ?>
						<span class="block-bento-room__curation"><?php echo $curation_label; ?></span>
					<?php endif; ?>
					<h2 class="block-bento-room__main-title"><?php echo $main_name; ?></h2>
					<?php if ( $cta_text ) : ?>
						<span class="block-bento-room__cta"><?php echo $cta_text; ?></span>
					<?php endif; ?>
				</div>
			</a>

			<?php /* ── Small Panels ── */ ?>
			<?php foreach ( $small_terms as $small ) :
				$s_img  = $get_term_image( $small );
				$s_link = esc_url( get_term_link( $small ) );
				$s_name = esc_html( $small->name );
			?>
				<a href="<?php echo $s_link; ?>" class="block-bento-room__small group">
					<?php if ( $s_img ) : ?>
						<img src="<?php echo $s_img; ?>" alt="<?php echo $s_name; ?>" class="block-bento-room__img" loading="lazy" />
					<?php else : ?>
						<div class="block-bento-room__img-placeholder">🛋</div>
					<?php endif; ?>
					<div class="block-bento-room__overlay block-bento-room__overlay--light"></div>
					<div class="block-bento-room__small-content">
						<h3 class="block-bento-room__small-title"><?php echo $s_name; ?></h3>
						<span class="block-bento-room__explore"><?php esc_html_e( 'Explore Collections', 'laca' ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>

		</div>
	</div>
</section>
