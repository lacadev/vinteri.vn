<?php
/**
 * Block: About Craft Block — "The Foundation of Craft"
 * Template: render.php
 *
 * @package LacaDev
 */

$attr            = $attributes;
$container_class = $attr['containerLayout'] ?? 'container';
$bg_color        = $attr['backgroundColor'] ?? '';
$inline_style    = $bg_color ? ' style="background-color: ' . esc_attr( $bg_color ) . ';"' : '';

$section_label = $attr['sectionLabel'] ?? '';
$spec_text     = $attr['specText']     ?? '';
$heading       = $attr['heading']      ?? '';
$paragraph1    = $attr['paragraph1']   ?? '';
$paragraph2    = $attr['paragraph2']   ?? '';
$cta_text      = $attr['ctaText']      ?? '';
$cta_url       = $attr['ctaUrl']       ?? '#';
$image_url     = $attr['imageUrl']     ?? '';
$image_alt     = $attr['imageAlt']     ?? '';
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block about-craft-block' ] ); ?><?php echo $inline_style; ?>>
    <div class="<?php echo esc_attr( $container_class ); ?>">
        <div class="flex flex-col md:flex-row gap-16 xl:gap-24 items-center py-16 md:py-24">

            <?php /* ── Image Column (4/5 aspect) ───────────────────────────────── */ ?>
            <div class="about-craft-block__image-col w-full md:w-1/2 relative flex-shrink-0">
                <div class="about-craft-block__image-wrap aspect-[4/5] rounded-lg overflow-hidden">
                    <?php if ( $image_url ) : ?>
                        <img
                            src="<?php echo esc_url( $image_url ); ?>"
                            alt="<?php echo esc_attr( $image_alt ); ?>"
                            class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                            loading="lazy"
                        />
                    <?php else : ?>
                        <div class="w-full h-full bg-stone-100 flex items-center justify-center text-stone-400 text-sm">
                            <?php esc_html_e( 'Chưa có ảnh', 'laca' ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php /* ── Glass Card (Spec) ── */ ?>
                <?php if ( $section_label || $spec_text ) : ?>
                    <div class="about-craft-block__glass-card absolute -bottom-10 -right-10 w-64 p-6 rounded-lg border border-white/30 shadow-2xl backdrop-blur-md">
                        <?php if ( $section_label ) : ?>
                            <p class="text-xs uppercase tracking-[0.15em] opacity-60 mb-3 font-medium">
                                <?php echo esc_html( $section_label ); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ( $spec_text ) : ?>
                            <p class="font-serif text-base leading-relaxed">
                                <?php echo esc_html( $spec_text ); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php /* ── Text Column ───────────────────────────────────────────── */ ?>
            <div class="about-craft-block__text-col flex-1 md:pt-10">
                <?php if ( $heading ) : ?>
                    <h2 class="font-serif font-light leading-tight mb-8 text-[#2f3331]">
                        <?php echo esc_html( $heading ); ?>
                    </h2>
                <?php endif; ?>

                <div class="about-craft-block__paragraphs flex flex-col gap-5 mb-10 text-[#4a4f4d] font-light leading-[1.75]">
                    <?php if ( $paragraph1 ) : ?>
                        <p><?php echo esc_html( $paragraph1 ); ?></p>
                    <?php endif; ?>
                    <?php if ( $paragraph2 ) : ?>
                        <p><?php echo esc_html( $paragraph2 ); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ( $cta_text && $cta_url ) : ?>
                    <a
                        href="<?php echo esc_url( $cta_url ); ?>"
                        class="about-craft-block__cta group inline-flex items-center gap-3 text-xs uppercase tracking-[0.12em] border-b border-current pb-0.5 hover:opacity-70 transition-opacity duration-200 font-medium"
                    >
                        <?php echo esc_html( $cta_text ); ?>
                        <span class="transition-transform duration-200 group-hover:translate-x-1">→</span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
