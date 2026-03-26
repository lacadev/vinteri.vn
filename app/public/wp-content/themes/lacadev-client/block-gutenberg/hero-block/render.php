<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hero Block — render.php
 * Layout: text (5/12) + ảnh sản phẩm (7/12) theo phong cách editorial.
 *
 * @package lacadev-client
 */

$attr            = $attributes;
$container_class = $attr['containerLayout'] ?? 'container';
$bg_color        = $attr['backgroundColor'] ?? '';
$inline_style    = $bg_color ? ' style="background-color: ' . esc_attr( $bg_color ) . ';"' : '';

// Text content
$sub_title    = esc_html( $attr['subTitle'] ?? 'New Season Collection' );
$title        = esc_html( $attr['title'] ?? 'The Arlow' );
$title_italic = esc_html( $attr['titleItalic'] ?? 'Lounge' );
$description  = esc_html( $attr['description'] ?? '' );

// CTA Button
$button_text = esc_html( $attr['buttonText'] ?? 'Shop The Piece' );
$button_url  = esc_url( $attr['buttonUrl'] ?? '#' );

// Image
$image_url = esc_url( $attr['imageUrl'] ?? '' );
$image_alt = esc_attr( $attr['imageAlt'] ?? '' );
?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block' ] ); ?><?php echo $inline_style; ?>>
    <div class="<?php echo esc_attr( $container_class ); ?>">
        <div class="relative hero-section flex items-center overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center w-full">

                <!-- Text Column -->
                <div class="md:col-span-5 z-10 py-12">
                    <?php if ( $sub_title ) : ?>
                        <span class="text-secondary font-label tracking-widest uppercase text-xs mb-4 block">
                            <?php echo $sub_title; ?>
                        </span>
                    <?php endif; ?>

                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-headline text-on-surface leading-tight mb-8">
                        <?php echo $title; ?>
                        <?php if ( $title_italic ) : ?>
                            <br /><span class="italic font-light"><?php echo $title_italic; ?></span>
                        <?php endif; ?>
                    </h1>

                    <?php if ( $description ) : ?>
                        <p class="text-on-surface-variant text-lg max-w-md mb-10 leading-relaxed">
                            <?php echo $description; ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( $button_text ) : ?>
                        <div class="mt-2">
                            <a href="<?php echo $button_url; ?>" class="hero-btn">
                                <?php echo $button_text; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Image Column -->
                <div class="md:col-span-7 relative">
                    <?php if ( $image_url ) : ?>
                        <div class="hero-image-wrapper w-full rounded-xl overflow-hidden shadow-sm relative group bg-surface-container">
                            <img
                                src="<?php echo $image_url; ?>"
                                alt="<?php echo $image_alt; ?>"
                                class="w-full h-full object-cover"
                                loading="eager"
                            />
                        </div>
                        <!-- Decorative blur element -->
                        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-secondary-fixed/30 rounded-full blur-3xl -z-10 pointer-events-none"></div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
