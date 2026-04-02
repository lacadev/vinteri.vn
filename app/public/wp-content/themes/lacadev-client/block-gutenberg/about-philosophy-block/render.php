<?php
/**
 * Block: About Philosophy Block — Bento Grid 4 sections
 * Template: render.php
 *
 * @package LacaDev
 */

$attr            = $attributes;
$container_class = $attr['containerLayout'] ?? 'container';
$bg_color        = $attr['backgroundColor'] ?? '';
$inline_style    = $bg_color ? ' style="background-color: ' . esc_attr( $bg_color ) . ';"' : '';

$section_title = $attr['sectionTitle']   ?? '';
$item1_title   = $attr['item1Title']     ?? '';
$item1_text    = $attr['item1Text']      ?? '';
$item1_id      = absint( $attr['item1ImageId'] ?? 0 );
$item1_alt     = $attr['item1ImageAlt']  ?? '';
$item2_title   = $attr['item2Title']     ?? '';
$item2_text    = $attr['item2Text']      ?? '';
$item3_title   = $attr['item3Title']     ?? '';
$item3_text    = $attr['item3Text']      ?? '';
$item4_title   = $attr['item4Title']     ?? '';
$item4_id      = absint( $attr['item4ImageId'] ?? 0 );
$item4_alt     = $attr['item4ImageAlt']  ?? '';
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'vp-block about-philosophy-block' ] ); ?><?php echo $inline_style; ?>>
    <div class="<?php echo esc_attr( $container_class ); ?> py-16 md:py-24">

        <?php if ( $section_title ) : ?>
            <div class="text-center mb-12 md:mb-16">
                <h2 class="font-serif font-light text-[#2f3331]">
                    <?php echo esc_html( $section_title ); ?>
                </h2>
                <div class="mx-auto mt-4 h-px w-24 bg-[#afb3b0]"></div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">

            <?php /* ── Bento 1 — col-span-8 (ảnh + text) ──────────────────── */ ?>
            <div class="about-philosophy-block__bento1 md:col-span-8 bg-white rounded-2xl flex flex-col gap-8">
                <div>
                    <?php if ( $item1_title ) : ?>
                        <h3 class="font-serif font-light text-2xl md:text-3xl mb-4 text-[#2f3331]">
                            <?php echo esc_html( $item1_title ); ?>
                        </h3>
                    <?php endif; ?>
                    <?php if ( $item1_text ) : ?>
                        <p class="text-[#4a4f4d] font-light leading-[1.75] text-base">
                            <?php echo esc_html( $item1_text ); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php if ( $item1_id ) : ?>
                    <div class="overflow-hidden rounded-xl aspect-[21/9]">
                        <?php theResponsiveImage( $item1_id, 'tablet', [
                            'class'   => 'w-full h-full object-cover transition-transform duration-700 hover:scale-105',
                            'loading' => 'lazy',
                            'alt'     => esc_attr( $item1_alt ),
                        ] ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php /* ── Bento 2 — col-span-4 (dark bg + icon + text) ─────── */ ?>
            <div class="about-philosophy-block__bento2 md:col-span-4 bg-[#5f5e5e] text-[#faf7f6] rounded-2xl p-8 lg:p-12 flex flex-col justify-center items-center text-center gap-6">
                <span class="text-5xl opacity-80" aria-hidden="true">♻</span>
                <?php if ( $item2_title ) : ?>
                    <h3 class="font-serif font-light text-2xl md:text-3xl">
                        <?php echo esc_html( $item2_title ); ?>
                    </h3>
                <?php endif; ?>
                <?php if ( $item2_text ) : ?>
                    <p class="font-light leading-[1.75] text-sm opacity-80 max-w-xs">
                        <?php echo esc_html( $item2_text ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php /* ── Bento 3 — col-span-4 (warm bg + text) ───────────── */ ?>
            <div class="about-philosophy-block__bento3 md:col-span-4 bg-[#f4dfcb] rounded-2xl p-8 lg:p-12 flex flex-col justify-end min-h-[24rem]">
                <?php if ( $item3_title ) : ?>
                    <h3 class="font-serif font-light text-2xl md:text-3xl mb-4 text-[#5e4f40]">
                        <?php echo esc_html( $item3_title ); ?>
                    </h3>
                <?php endif; ?>
                <?php if ( $item3_text ) : ?>
                    <p class="font-light leading-[1.75] text-sm text-[#5e4f40] opacity-80">
                        <?php echo esc_html( $item3_text ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php /* ── Bento 4 — col-span-8 (fullscreen image + gradient overlay) ── */ ?>
            <div class="about-philosophy-block__bento4 md:col-span-8 rounded-2xl overflow-hidden relative min-h-[24rem]">
                <?php if ( $item4_id ) : ?>
                    <?php theResponsiveImage( $item4_id, 'tablet', [
                        'class'   => 'absolute inset-0 w-full h-full object-cover transition-transform duration-700 hover:scale-105',
                        'loading' => 'lazy',
                        'alt'     => esc_attr( $item4_alt ),
                    ] ); ?>
                <?php else : ?>
                    <div class="absolute inset-0 bg-stone-200 flex items-center justify-center text-stone-400 text-sm">
                        <?php esc_html_e( 'Chưa có ảnh Bento 4', 'laca' ); ?>
                    </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end p-8 lg:p-12">
                    <?php if ( $item4_title ) : ?>
                        <h3 class="font-serif font-light text-2xl md:text-3xl text-white">
                            <?php echo esc_html( $item4_title ); ?>
                        </h3>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>
