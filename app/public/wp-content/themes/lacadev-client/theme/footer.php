<?php
/**
 * Theme footer partial.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package WPEmergeTheme
 */

// ---------------------------------------------------------------------------
// SVG fallback defaults (used when Carbon Fields không có data)
// ---------------------------------------------------------------------------
$svg_truck = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v4h-7V8Z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>';

$svg_tag   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2H4a2 2 0 0 0-2 2v8l10 10 10-10L12 2Z"/><circle cx="7" cy="9" r="1.5" fill="currentColor" stroke="none"/></svg>';

$svg_shield= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><polyline points="9 12 11 14 15 10"/></svg>';

// ---------------------------------------------------------------------------
// Benefits: đọc từ Carbon Fields, fallback sang giá trị mặc định
// ---------------------------------------------------------------------------
$cf_benefits = (function_exists('carbon_get_theme_option'))
    ? carbon_get_theme_option('footer_benefits')
    : [];

$default_benefits = [
    [
        'icon_svg'    => $svg_truck,
        'title'       => __('Free Shipping', 'laca'),
        'description' => __('Miễn phí vận chuyển cho đơn hàng trên 500k. Giao nhanh toàn quốc.', 'laca'),
    ],
    [
        'icon_svg'    => $svg_tag,
        'title'       => __('Price Promise', 'laca'),
        'description' => __('Cam kết giá tốt nhất. Mua sắm hoàn toàn an tâm.', 'laca'),
    ],
    [
        'icon_svg'    => $svg_shield,
        'title'       => __('Bảo hành 3 năm', 'laca'),
        'description' => __('Tất cả sản phẩm được bảo hành toàn diện trong 3 năm.', 'laca'),
    ],
];

$benefits = !empty($cf_benefits) ? $cf_benefits : $default_benefits;

// ---------------------------------------------------------------------------
// Contact info
// ---------------------------------------------------------------------------
$phone    = function_exists('getOption') ? getOption('phone_number') : '';
$email    = function_exists('getOption') ? getOption('email')        : '';
$address  = function_exists('getOption') ? getOption('address')      : '';
$siteName = get_bloginfo('name');
$siteDesc = get_bloginfo('description');

// ---------------------------------------------------------------------------
// Social SVGs (inline, no FontAwesome)
// ---------------------------------------------------------------------------
$social_svgs = [
    'facebook' => [
        'label' => 'Facebook',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
    ],
    'twitter'  => [
        'label' => 'Twitter / X',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    ],
    'linkedin' => [
        'label' => 'LinkedIn',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>',
    ],
    'instagram'=> [
        'label' => 'Instagram',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>',
    ],
    'youtube'  => [
        'label' => 'YouTube',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.4a2.78 2.78 0 0 0 1.95-1.97A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#3d3d3d"/></svg>',
    ],
    'tiktok'   => [
        'label' => 'TikTok',
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V9.01a8.16 8.16 0 0 0 4.77 1.52V7.08a4.85 4.85 0 0 1-1-.39z"/></svg>',
    ],
];
?>
<!-- footer -->
<footer class="site-footer">

    <!-- Benefits strip -->
    <?php if (!empty($benefits)) : ?>
    <div class="footer__benefits">
        <div class="container">
            <div class="footer__benefits-grid">
                <?php foreach ($benefits as $b) :
                    // hỗ trợ cả key từ CF ('icon_svg','title','description') lẫn fallback
                    $icon_svg = isset($b['icon_svg']) ? $b['icon_svg'] : '';
                    $b_title  = isset($b['title'])    ? $b['title']    : '';
                    $b_desc   = isset($b['description']) ? $b['description'] : '';
                ?>
                    <div class="footer__benefit">
                        <?php if ($icon_svg) : ?>
                        <div class="footer__benefit-icon">
                            <?php echo wp_kses($icon_svg, [
                                'svg'      => ['xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true],
                                'path'     => ['d' => true, 'fill' => true, 'stroke' => true],
                                'circle'   => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true],
                                'rect'     => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true],
                                'polyline' => ['points' => true],
                                'polygon'  => ['points' => true, 'fill' => true],
                                'line'     => ['x1' => true, 'y1' => true, 'x2' => true, 'y2' => true],
                            ]); ?>
                        </div>
                        <?php endif; ?>
                        <div class="footer__benefit-body">
                            <?php if ($b_title) : ?>
                                <h4 class="footer__benefit-title"><?php echo esc_html($b_title); ?></h4>
                            <?php endif; ?>
                            <?php if ($b_desc) : ?>
                                <p class="footer__benefit-desc"><?php echo esc_html($b_desc); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main area: logo, social, tagline, contact -->
    <div class="footer__main">
        <div class="container">

            <!-- Logo -->
            <div class="footer__brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer__logo">
                    <?php echo esc_html($siteName); ?>
                </a>
            </div>

            <!-- Social icons -->
            <?php
            $has_social = false;
            foreach (array_keys($social_svgs) as $key) {
                if (function_exists('getOption') && getOption($key)) { $has_social = true; break; }
            }
            if ($has_social) : ?>
            <nav class="footer__socials" aria-label="<?php esc_attr_e('Social media', 'laca'); ?>">
                <?php foreach ($social_svgs as $key => $s) :
                    $url = function_exists('getOption') ? getOption($key) : '';
                    if ($url) : ?>
                        <a class="footer__social-link"
                           href="<?php echo esc_url($url); ?>"
                           target="_blank"
                           rel="noopener noreferrer nofollow"
                           aria-label="<?php echo esc_attr($s['label']); ?>">
                            <?php echo $s['svg']; // SVG đã được sanitize thủ công ?>
                        </a>
                    <?php endif;
                endforeach; ?>
            </nav>
            <?php endif; ?>

            <!-- Tagline -->
            <?php if ($siteDesc) : ?>
                <p class="footer__tagline"><?php echo esc_html($siteDesc); ?></p>
            <?php endif; ?>

            <!-- Contact row -->
            <?php if ($address || $phone || $email) : ?>
            <div class="footer__contact">
                <?php if ($address) : ?>
                    <div class="footer__contact-item">
                        <span class="footer__contact-svg" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <span><?php echo esc_html($address); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($phone) : ?>
                    <div class="footer__contact-item">
                        <span class="footer__contact-svg" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.18h3a2 2 0 0 1 2 1.72c.12.96.36 1.91.72 2.81a2 2 0 0 1-.45 2.11L7.91 8.72A16 16 0 0 0 15.28 16.1l.95-.95a2 2 0 0 1 2.11-.45c.9.36 1.85.6 2.81.72A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">
                            <?php echo esc_html($phone); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <div class="footer__contact-item">
                        <span class="footer__contact-svg" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </span>
                        <a href="mailto:<?php echo esc_attr($email); ?>">
                            <?php echo esc_html($email); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Copyright bar -->
    <div class="footer__bottom">
        <div class="container">
            <p class="footer__copyright">
                &copy; <?php echo esc_html(date('Y')); ?>
                <?php echo esc_html($siteName); ?>.
                <?php esc_html_e('All rights reserved.', 'laca'); ?>
            </p>
        </div>
    </div>

</footer>
<!-- footer end -->

</div>
<!-- container-wrapper end -->


<?php wp_footer(); ?>
</body>

</html>