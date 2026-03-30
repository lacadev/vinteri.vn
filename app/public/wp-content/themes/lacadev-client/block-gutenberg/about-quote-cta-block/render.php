<?php
/**
 * Block: About Quote CTA Block
 * Template: render.php
 *
 * @package LacaDev
 */

$attr            = $attributes;
$container_class = $attr['containerLayout'] ?? 'container';
$bg_color        = $attr['backgroundColor'] ?? '';
$inline_style    = $bg_color ? ' style="background-color: ' . esc_attr( $bg_color ) . ';"' : '';

$quote_text    = $attr['quoteText']     ?? '';
$quote_author  = $attr['quoteAuthor']   ?? '';
$cta_heading   = $attr['ctaHeading']    ?? '';
$cta_subtext   = $attr['ctaSubtext']    ?? '';
$cta_btn_text  = $attr['ctaButtonText'] ?? '';
$cta_btn_url   = $attr['ctaButtonUrl']  ?? '#';
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block about-quote-cta-block' ] ); ?><?php echo $inline_style; ?>>

    <?php /* ── Quote Section ──────────────────────────────────────────────── */ ?>
    <div class="about-quote-cta-block__quote bg-[#faf9f7] py-20 md:py-28">
        <div class="<?php echo esc_attr( $container_class ); ?>">
            <div class="max-w-4xl mx-auto text-center">
                <span class="text-5xl md:text-6xl text-[#afb3b0] leading-none block mb-8" aria-hidden="true">"</span>

                <?php if ( $quote_text ) : ?>
                    <blockquote class="font-serif font-light italic text-[#2f3331] leading-snug mb-8">
                        <?php echo esc_html( $quote_text ); ?>
                    </blockquote>
                <?php endif; ?>

                <?php if ( $quote_author ) : ?>
                    <p class="text-xs uppercase tracking-[0.4em] text-[#4a4f4d] opacity-60 font-medium">
                        <?php echo esc_html( $quote_author ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php /* ── CTA Section ──────────────────────────────────────────────────── */ ?>
    <div class="about-quote-cta-block__cta bg-[#e6e9e6] py-10 md:py-12">
        <div class="<?php echo esc_attr( $container_class ); ?>">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                <div>
                    <?php if ( $cta_heading ) : ?>
                        <h2 class="font-serif font-light text-2xl md:text-3xl text-[#2f3331] mb-1">
                            <?php echo esc_html( $cta_heading ); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ( $cta_subtext ) : ?>
                        <p class="text-[#4a4f4d] font-light opacity-75 text-sm">
                            <?php echo esc_html( $cta_subtext ); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ( $cta_btn_text && $cta_btn_url ) : ?>
                    <a
                        href="<?php echo esc_url( $cta_btn_url ); ?>"
                        class="about-quote-cta-block__btn inline-flex items-center justify-center px-8 py-4 rounded-full bg-[#5f5e5e] text-[#faf7f6] text-xs uppercase tracking-widest font-medium hover:bg-[#2f3331] transition-colors duration-200 whitespace-nowrap"
                    >
                        <?php echo esc_html( $cta_btn_text ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</section>
