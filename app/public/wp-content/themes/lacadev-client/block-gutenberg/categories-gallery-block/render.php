<?php
/**
 * Categories Gallery Block — Render Template.
 *
 * @package LacaDev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sub_title       = esc_html( $attributes['subTitle'] ?? '' );
$title           = esc_html( $attributes['title'] ?? '' );
$browse_all_text = esc_html( $attributes['browseAllText'] ?? '' );
$browse_all_url  = esc_url( $attributes['browseAllUrl'] ?? '' );
$taxonomy        = esc_attr( $attributes['taxonomy'] ?? 'category' );
$term_ids        = array_map( 'absint', $attributes['termIds'] ?? [] );
$bg_color        = $attributes['backgroundColor'] ?? '';
$is_full_width   = (bool) ( $attributes['isFullWidth'] ?? false );

$section_style = $bg_color ? 'background-color: ' . esc_attr( $bg_color ) . ';' : '';

// Inline helper — returns ['id' => int, 'url' => string] for responsive image support.
$get_term_image = static function ( $term ) {
	$tax = $term->taxonomy;
	$tid = $term->term_id;

	// WooCommerce / taxonomy image plugins store attachment ID in 'thumbnail_id'
	$thumb_id = get_term_meta( $tid, 'thumbnail_id', true );
	if ( $thumb_id ) {
		return [ 'id' => absint( $thumb_id ), 'url' => '' ];
	}

	// ACF Pro / ACF Free
	if ( function_exists( 'get_field' ) ) {
		$acf = get_field( 'term_image', $tax . '_' . $tid );
		if ( $acf ) {
			if ( is_array( $acf ) && ! empty( $acf['ID'] ) ) {
				return [ 'id' => absint( $acf['ID'] ), 'url' => '' ];
			}
			$url = is_array( $acf ) ? ( $acf['url'] ?? '' ) : $acf;
			return [ 'id' => 0, 'url' => esc_url( $url ) ];
		}
	}

	// Custom meta
	$url = get_term_meta( $tid, 'term_image_url', true );
	if ( $url ) {
		return [ 'id' => 0, 'url' => esc_url( $url ) ];
	}

	return [ 'id' => 0, 'url' => '' ];
};

// Fetch terms
$query_args = [
	'taxonomy'   => $taxonomy,
	'hide_empty' => false,
	'number'     => 50,
];
if ( ! empty( $term_ids ) ) {
	$query_args['include'] = $term_ids;
	$query_args['orderby'] = 'include';
}
$terms = get_terms( $query_args );
if ( is_wp_error( $terms ) ) {
	$terms = [];
}
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'block-categories-gallery' ] ); ?>>
	<?php if ( $section_style ) : ?>
	<style>.wp-block-lacadev-categories-gallery-block { <?php echo esc_attr( $section_style ); ?> }</style>
	<?php endif; ?>

	<div class="<?php echo $is_full_width ? 'w-full' : 'max-w-screen-2xl mx-auto'; ?> px-8 mb-32">

		<?php if ( $sub_title || $title ) : ?>
			<div class="flex items-end justify-between mb-12">
				<div>
					<?php if ( $sub_title ) : ?>
						<span class="font-label text-xs uppercase tracking-widest text-secondary font-bold mb-2 block">
							<?php echo $sub_title; ?>
						</span>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h2 class="font-headline text-4xl font-semibold m-0"><?php echo $title; ?></h2>
					<?php endif; ?>
				</div>
				<?php if ( $browse_all_text ) : ?>
					<a href="<?php echo $browse_all_url ?: '#'; ?>"
					   class="font-label text-xs uppercase tracking-widest text-on-surface-variant border-b border-outline-variant pb-1 hover:text-on-surface transition-colors">
						<?php echo $browse_all_text; ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $terms ) ) : ?>
			<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
				<?php foreach ( $terms as $term ) :
					$link  = esc_url( get_term_link( $term ) );
					$name  = esc_html( $term->name );
					$count = absint( $term->count );
					$img_data = $get_term_image( $term );
				?>
					<a href="<?php echo $link; ?>" class="block group">
						<div class="aspect-[4/5] rounded-xl overflow-hidden bg-surface-container-low mb-4">
							<?php if ( $img_data['id'] ) : ?>
								<?php theResponsiveImage( $img_data['id'], 'mobile', [
									'class'   => 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-105',
									'loading' => 'lazy',
									'alt'     => $name,
								] ); ?>
							<?php elseif ( $img_data['url'] ) : ?>
								<img
									src="<?php echo $img_data['url']; ?>"
									alt="<?php echo $name; ?>"
									class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
									loading="lazy"
								/>
							<?php else : ?>
								<div class="w-full h-full flex items-center justify-center text-5xl bg-surface-container">🖼</div>
							<?php endif; ?>
						</div>
						<p class="font-headline text-lg font-medium mb-1"><?php echo $name; ?></p>
						<p class="font-label text-xs uppercase tracking-widest text-on-surface-variant">
							<?php printf( esc_html__( '%d sản phẩm', 'laca' ), $count ); ?>
						</p>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
