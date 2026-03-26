<?php
/**
 * Theme footer partial.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package WPEmergeTheme
 */
?>
<!-- footer -->
<footer class="site-footer">
    <?php
    $phone    = function_exists('getOption') ? getOption('phone_number') : '';
    $email    = function_exists('getOption') ? getOption('email')        : '';
    $address  = function_exists('getOption') ? getOption('address')      : '';
    $siteName = get_bloginfo('name');
    $siteDesc = get_bloginfo('description');

    $socials = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'fab fa-facebook-f'],
        'twitter'   => ['label' => 'Twitter',   'icon' => 'fab fa-x-twitter'],
        'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'fab fa-linkedin-in'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'fab fa-instagram'],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'fab fa-youtube'],
        'tiktok'    => ['label' => 'TikTok',    'icon' => 'fab fa-tiktok'],
    ];

    $benefits = [
        [
            'icon'  => 'fas fa-truck',
            'title' => __('Free Shipping', 'laca'),
            'desc'  => __('On all orders over 500k. Fast and reliable delivery nationwide.', 'laca'),
        ],
        [
            'icon'  => 'fas fa-tag',
            'title' => __('Price Promise', 'laca'),
            'desc'  => __('We match any price. Shop with total confidence every time.', 'laca'),
        ],
        [
            'icon'  => 'fas fa-shield-halved',
            'title' => __('3 Years Warranty', 'laca'),
            'desc'  => __('All products backed by our 3-year comprehensive warranty.', 'laca'),
        ],
    ];
    ?>

    <!-- Benefits strip -->
    <div class="footer__benefits">
        <div class="container">
            <div class="footer__benefits-grid">
                <?php foreach ($benefits as $b) : ?>
                    <div class="footer__benefit">
                        <div class="footer__benefit-icon">
                            <i class="<?php echo esc_attr($b['icon']); ?>" aria-hidden="true"></i>
                        </div>
                        <div class="footer__benefit-body">
                            <h4 class="footer__benefit-title"><?php echo esc_html($b['title']); ?></h4>
                            <p class="footer__benefit-desc"><?php echo esc_html($b['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

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
            foreach ($socials as $key => $s) {
                if (function_exists('getOption') && getOption($key)) { $has_social = true; break; }
            }
            if ($has_social) : ?>
            <nav class="footer__socials" aria-label="<?php esc_attr_e('Social media', 'laca'); ?>">
                <?php foreach ($socials as $key => $s) :
                    $url = function_exists('getOption') ? getOption($key) : '';
                    if ($url) : ?>
                        <a class="footer__social-link"
                           href="<?php echo esc_url($url); ?>"
                           target="_blank"
                           rel="noopener noreferrer nofollow"
                           aria-label="<?php echo esc_attr($s['label']); ?>">
                            <i class="<?php echo esc_attr($s['icon']); ?>" aria-hidden="true"></i>
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
                        <i class="fas fa-location-dot" aria-hidden="true"></i>
                        <span><?php echo esc_html($address); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($phone) : ?>
                    <div class="footer__contact-item">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">
                            <?php echo esc_html($phone); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <div class="footer__contact-item">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
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