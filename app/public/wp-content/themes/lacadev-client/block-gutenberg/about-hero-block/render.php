<?php
/**
 * Block: About Hero Block
 * Template: render.php
 *
 * @package LacaDev
 */

$attr            = $attributes;
$container_class = $attr['containerLayout'] ?? 'container';
$bg_color        = $attr['backgroundColor'] ?? '';
$inline_style    = $bg_color ? ' style="background-color: ' . esc_attr( $bg_color ) . ';"' : '';

$subtitle        = $attr['subTitle']    ?? '';
$title           = $attr['title']       ?? '';
$title_italic    = $attr['titleItalic'] ?? '';
$description     = $attr['description'] ?? '';
$image_id        = absint( $attr['imageId'] ?? 0 );
$image_alt       = $attr['imageAlt']    ?? '';
$overlay         = intval( $attr['overlayOpacity'] ?? 10 );
$overlay_dec     = $overlay / 100;
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block about-hero-block' ] ); ?><?php echo $inline_style; ?>>
    <div class="about-hero-block__inner">
        <?php if ( $image_id ) : ?>
            <div class="about-hero-block__bg">
                <?php theResponsiveImage( $image_id, 'full', [
                    'class'         => 'about-hero-block__bg-img',
                    'loading'       => 'eager',
                    'fetchpriority' => 'high',
                    'alt'           => esc_attr( $image_alt ),
                ] ); ?>
                <div class="about-hero-block__overlay" style="background-color: rgba(0,0,0,<?php echo esc_attr( $overlay_dec ); ?>);"></div>
            </div>
        <?php endif; ?>

        <div class="<?php echo esc_attr( $container_class ); ?> about-hero-block__content">
            <div class="about-hero-block__text max-w-2xl">
                <?php if ( $subtitle ) : ?>
                    <span class="about-hero-block__subtitle block uppercase tracking-[0.3em] text-xs mb-6 opacity-70 font-medium">
                        <?php echo esc_html( $subtitle ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( $title || $title_italic ) : ?>
                    <h1 class="about-hero-block__heading font-serif font-light leading-[1.05] mb-8">
                        <?php if ( $title ) : ?>
                            <?php echo esc_html( $title ); ?>
                        <?php endif; ?>
                        <?php if ( $title_italic ) : ?>
                            <br /><em class="italic"><?php echo esc_html( $title_italic ); ?></em>
                        <?php endif; ?>
                    </h1>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                    <p class="about-hero-block__desc text-lg leading-relaxed max-w-xl font-light opacity-85">
                        <?php echo esc_html( $description ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
