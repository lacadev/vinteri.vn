<?php
/**
 * Asset helpers.
 *
 * @package WPEmergeTheme
 */

use WPEmergeTheme\Facades\Theme;
use WPEmergeTheme\Facades\Assets;

/**
 * Enhanced asset loading with performance optimizations
 */
function app_action_theme_enqueue_assets()
{
    $version = wp_get_theme()->get('Version');
    $theme_root_dir = dirname(get_template_directory());
    $theme_root_uri = dirname(get_template_directory_uri());
    
    $dist_path = $theme_root_dir . '/dist/';
    $dist_url  = $theme_root_uri . '/dist/';

    /**
     * Enqueue the built-in comment-reply script for singular pages.
     */
    if (is_singular()) {
        wp_enqueue_script('comment-reply');
    }

    /**
     * Critical JS (inline or very small) - load in head for critical functionality
     */
    if (file_exists($dist_path . 'critical.js')) {
        wp_enqueue_script('theme-critical-js', $dist_url . 'critical.js', [], $version, false);
    }

    /**
     * Vendors bundle (supports dynamically split chunks)
     */
    $vendors_deps = [];
    $vendor_files = ['vendor-swal.js', 'vendor-gsap.js', 'vendor-swiper.js', 'vendors.js'];
    foreach ($vendor_files as $vfile) {
        if (file_exists($dist_path . $vfile)) {
            $handle = 'theme-' . sanitize_title(basename($vfile, '.js'));
            wp_enqueue_script($handle, $dist_url . $vfile, [], $version, true);
            $vendors_deps[] = $handle;
        }
    }

    /**
     * Main JavaScript bundle (deferred)
     */
    Assets::enqueueScript('theme-js-bundle', $dist_url . 'theme.js', $vendors_deps, true);

    /**
     * Conditional assets based on page type
     */
    if (is_home() || is_archive() || is_search()) {
        if (file_exists($dist_path . 'archive.js')) {
            wp_enqueue_script('theme-archive-js', $dist_url . 'archive.js', ['theme-js-bundle'], $version, true);
        }
    }

    if (is_single() && comments_open()) {
        if (file_exists($dist_path . 'comments.js')) {
            wp_enqueue_script('theme-comments-js', $dist_url . 'comments.js', ['theme-js-bundle'], $version, true);
        }
    }

    /**
     * Enqueue styles with preload optimization
     */
    Assets::enqueueStyle('theme-css-bundle', $dist_url . 'styles/theme.css');

    /**
     * Conditional CSS based on page type
     */
    if (is_single()) {
        if (file_exists($dist_path . 'styles/single.css')) {
            wp_enqueue_style('theme-single-css', $dist_url . 'styles/single.css', ['theme-css-bundle'], $version);
        }
    }

    /**
     * Enqueue theme's style.css file to allow overrides for the bundled styles.
     */
    Assets::enqueueStyle('theme-styles', get_template_directory_uri() . '/style.css');

    /**
     * Localize script with minimal data
     */
    wp_localize_script('theme-js-bundle', 'themeData', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('theme_nonce'),
        'isHome' => is_home(),
        'isMobile' => wp_is_mobile(),
        'currentUrl' => get_permalink(),
    ]);

    /**
     * Shop page inline CSS (editorial layout)
     */
    if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        // Dequeue WC price slider script to prevent it from overriding our custom range slider
        add_action( 'wp_enqueue_scripts', function() {
            wp_dequeue_script( 'wc-price-slider' );
            wp_dequeue_script( 'wc-jquery-ui-touchpunch' );
        }, 999 );
    }

    /**
     * Single Product page: inject CSS + enqueue Swiper/Fancybox + remove duplicate WC summary hooks
     */
    if ( function_exists( 'is_product' ) && is_product() ) {

        // ── Enqueue Fancybox CDN only (Swiper already bundled in theme JS) ──
        add_action( 'wp_enqueue_scripts', function() {
            // Fancybox CSS
            wp_enqueue_style( 'fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.css', array(), '5.0.0' );
            // Fancybox JS
            wp_enqueue_script( 'fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5/dist/fancybox/fancybox.umd.js', array(), '5.0.0', true );
        }, 20 );

        // ── Product Gallery init script (Vanilla JS – no Swiper) ──
        add_action( 'wp_footer', function() {
            ?>
            <script>
            (function() {
                document.querySelectorAll('[data-fancybox="product-gallery"]').forEach(function(el) {
                    el.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); }, true);
                });
                function initGallery() {
                    var main = document.getElementById('sp-gallery-main');
                    if (!main) return;
                    var slides  = main.querySelectorAll('.sp-gallery__slide');
                    var total   = slides.length;
                    if (total === 0) return;
                    var current = 0;
                    var counter = document.getElementById('sp-counter-current');
                    var thumbs  = document.querySelectorAll('.sp-gallery__thumb');
                    var prev    = document.getElementById('sp-gallery-prev');
                    var next    = document.getElementById('sp-gallery-next');
                    function goTo(idx) {
                        if (idx < 0) idx = 0;
                        if (idx >= total) idx = total - 1;
                        slides.forEach(function(s) { s.classList.remove('sp-gallery__slide--active'); });
                        slides[idx].classList.add('sp-gallery__slide--active');
                        thumbs.forEach(function(t) { t.classList.remove('sp-gallery__thumb--active'); });
                        if (thumbs[idx]) thumbs[idx].classList.add('sp-gallery__thumb--active');
                        if (counter) counter.textContent = idx + 1;
                        current = idx;
                    }
                    if (prev) prev.addEventListener('click', function() { goTo(current - 1); });
                    if (next) next.addEventListener('click', function() { goTo(current + 1); });
                    thumbs.forEach(function(t) {
                        t.addEventListener('click', function() { goTo(parseInt(this.getAttribute('data-index'))); });
                    });
                    if (typeof Fancybox !== 'undefined') {
                        Fancybox.bind('[data-fancybox="product-gallery"]', {
                            animated: true,
                            Toolbar: { display: { left: [], middle: ['infobar'], right: ['close'] } },
                            Images: { zoom: true }
                        });
                    }
                }
                if (document.readyState === 'complete') { initGallery(); }
                else { window.addEventListener('load', initGallery); }
            })();
            </script>
            <?php
        }, 99 );

        // ── Inline CSS fallback ──
        $dist_css = get_template_directory() . '/dist/css/theme.css';
        if ( ! file_exists( $dist_css ) ) {
            add_action( 'wp_head', function() {
                echo '<style id="sp-editorial-css">
/* ===== SINGLE PRODUCT - EDITORIAL ===== */
.sp-layout{display:grid;grid-template-columns:7fr 5fr;gap:3rem;align-items:start;padding:2.5rem 0 4rem}
@media(max-width:1024px){.sp-layout{grid-template-columns:1fr;gap:2rem}}

/* --- Gallery (Vanilla CSS slider) --- */
.sp-gallery{position:relative}
.sp-gallery__main{position:relative;overflow:hidden;border-radius:.5rem;background:#eae8e4}
.sp-gallery__slide{display:none;aspect-ratio:4/5;width:100%}
.sp-gallery__slide--active{display:block}
.sp-gallery__link{display:block;width:100%;height:100%;cursor:zoom-in}
.sp-gallery__image{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
.sp-gallery__image:hover{transform:scale(1.03)}

/* Navigation arrows */
.sp-gallery__nav{position:absolute;top:50%;z-index:10;width:2.5rem;height:2.5rem;background:rgba(255,255,255,.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transform:translateY(-50%);transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.sp-gallery__nav:hover{background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.12)}
.sp-gallery__nav--prev{left:.75rem}
.sp-gallery__nav--next{right:.75rem}

/* Counter */
.sp-gallery__counter{position:absolute;bottom:.75rem;left:50%;transform:translateX(-50%);z-index:10;background:rgba(0,0,0,.5);color:#fff;font-size:.75rem;letter-spacing:.1em;padding:.25rem .75rem;border-radius:999px;font-variant-numeric:tabular-nums}

/* Thumbnails */
.sp-gallery__thumbs{display:flex;gap:.5rem;margin-top:.75rem}
.sp-gallery__thumb{flex:0 0 auto;width:4.5rem;height:4.5rem;cursor:pointer;border-radius:.375rem;overflow:hidden;opacity:.5;transition:opacity .25s;border:2px solid transparent;padding:0;background:none}
.sp-gallery__thumb--active{opacity:1;border-color:#1a1a1a}
.sp-gallery__thumb:hover{opacity:.8}
.sp-gallery__thumb img{width:100%;height:100%;object-fit:cover;display:block}

/* --- Info column --- */
.sp-info{position:sticky;top:8rem}
@media(max-width:1024px){.sp-info{position:static}}
.sp-info__inner{display:flex;flex-direction:column;gap:1.75rem}
.sp-info__label{font-size:.6875rem;letter-spacing:.2em;text-transform:uppercase;color:#7a7a7a;margin:0}
.sp-info__title{font-family:"Noto Serif",serif;font-size:clamp(2rem,4vw,3rem);font-weight:500;letter-spacing:-.025em;color:#1a1a1a;line-height:1.1;margin:0}
.sp-info__price{font-family:"Noto Serif",serif;font-size:1.5rem;color:#4a3d2f;font-weight:400}
.sp-info__price del{opacity:.45;font-size:1.1rem}
.sp-info__price ins{text-decoration:none}
.sp-info__section-label{font-size:.625rem;letter-spacing:.18em;text-transform:uppercase;font-weight:600;color:#1a1a1a;margin:0 0 .65rem}
.sp-info__description{font-size:.9375rem;color:#6b6b6b;line-height:1.75;font-weight:300}
.sp-info__cart .quantity{display:none}
.sp-info__cart .single_add_to_cart_button,.sp-info__cart button[type=submit]{width:100%;padding:1.1rem 2rem;background:#1a1a1a;color:#fff;border:none;border-radius:999px;font-size:.6875rem;letter-spacing:.18em;text-transform:uppercase;cursor:pointer;transition:background .25s ease;display:flex;align-items:center;justify-content:center;gap:.5rem}
.sp-info__cart .single_add_to_cart_button:hover,.sp-info__cart button[type=submit]:hover{background:#3a3a3a}
.sp-info__availability--out{color:#c0392b;font-size:.875rem;font-weight:500;margin:0}
.sp-info__specs{display:grid;grid-template-columns:repeat(2,1fr);gap:1.25rem 2rem;border-top:1px solid #eee;padding-top:1.5rem}
.sp-info__spec-label{font-size:.625rem;letter-spacing:.18em;text-transform:uppercase;color:#9a9a9a;margin:0 0 .3rem}
.sp-info__spec-value{font-size:.875rem;color:#1a1a1a;margin:0}
.sp-info__meta{font-size:.8125rem;color:#aaa}

/* --- Below (tabs, related) --- */
.sp-below{padding-bottom:5rem}
.sp-below .woocommerce-tabs ul.tabs{list-style:none;padding:0;margin:0 0 2rem;display:flex;gap:2rem;border-bottom:1px solid #eee}
.sp-below .woocommerce-tabs ul.tabs li{padding-bottom:.75rem;margin:0}
.sp-below .woocommerce-tabs ul.tabs li a{font-size:.75rem;letter-spacing:.12em;text-transform:uppercase;color:#aaa;text-decoration:none}
.sp-below .woocommerce-tabs ul.tabs li.active{border-bottom:2px solid #1a1a1a}
.sp-below .woocommerce-tabs ul.tabs li.active a{color:#1a1a1a;font-weight:600}

/* --- Related products --- */
.sp-related{background:#f7f6f4;padding:4rem 2rem;margin:0}
.sp-related__header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:3rem}
.sp-related__label{font-size:.6875rem;letter-spacing:.2em;text-transform:uppercase;color:#7a7a7a;margin:0 0 .4rem}
.sp-related__title{font-family:"Noto Serif",serif;font-size:clamp(1.75rem,3vw,2.5rem);font-weight:500;letter-spacing:-.02em;color:#1a1a1a;margin:0}
.sp-related__view-all{font-size:.6875rem;letter-spacing:.15em;text-transform:uppercase;color:#1a1a1a;text-decoration:none;transition:opacity .2s}
.sp-related__view-all:hover{opacity:.5}
.sp-related__grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem}
@media(max-width:1024px){.sp-related__grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.sp-related__grid{grid-template-columns:1fr}}
.sp-related__card-link{display:block;text-decoration:none;color:inherit}
.sp-related__card-image{overflow:hidden;border-radius:.5rem;background:#fff;aspect-ratio:3/4;margin-bottom:1.25rem}
.sp-related__card-img{width:100%;height:100%!important;object-fit:cover;display:block;transition:transform .7s ease}
.sp-related__card-link:hover .sp-related__card-img{transform:scale(1.05)}
.sp-related__card-info{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem}
.sp-related__card-name{font-family:"Noto Serif",serif;font-size:1.125rem;font-weight:400;color:#1a1a1a;margin:0;line-height:1.3}
.sp-related__card-price{font-size:.9375rem;font-weight:600;color:#1a1a1a;flex-shrink:0}
.sp-related__card--offset{margin-top:3rem}
@media(max-width:1024px){.sp-related__card--offset{margin-top:0}}

/* --- WC overrides --- */
.single-product div.product{display:block!important;flex-direction:unset}
.single-product .woocommerce-product-gallery{display:none!important}
.single-product .summary.entry-summary{padding:0!important;max-width:unset!important}
.sp-layout .summary.entry-summary,.sp-layout .woocommerce-product-gallery{display:none!important;visibility:hidden!important;height:0!important;overflow:hidden!important}
</style>' . "\n";
            }, 5 );
        }

        // Remove WC default single_product_summary hooks that we render manually in template
        add_action( 'wp', function() {
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title',     5  );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating',    10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price',     10 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt',   20 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta',      40 );
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing',   50 );
        }, 1 );

        // Remove WC image gallery & sale flash (we render manually)
        add_action( 'wp', function() {
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images',     20 );
        }, 1 );
    }
}

/**
 * Enqueue admin assets.
 *
 * @return void
 */
function app_action_admin_enqueue_assets()
{
    wp_enqueue_media();

    // Theme::uri() trả về .../lacadev-client/theme/ (nơi đặt style.css)
    // dist/ nằm ở .../lacadev-client/dist/ nên cần dirname() để lên 1 level
    $template_dir = dirname(get_template_directory_uri());

    /**
     * Enqueue styles.
     */
    Assets::enqueueStyle(
        'theme-admin-css-bundle',
        $template_dir . '/dist/styles/admin.css'
    );
    Assets::enqueueStyle(
        'theme-editor-css-bundle',
        $template_dir . '/dist/styles/editor.css'
    );

    /**
     * Enqueue vendors.js if exists (same fix as frontend)
     * CRITICAL: Load in head (false) to ensure it's available before admin.js
     */
    $admin_deps = [];
    $theme_root = dirname(get_template_directory());
    $base_uri = get_template_directory_uri();
    $theme_uri = dirname($base_uri);
    
    $vendor_files = ['vendor-swal.js', 'vendors.js'];
    foreach ($vendor_files as $vfile) {
        $vpath = $theme_root . '/dist/' . $vfile;
        if (file_exists($vpath)) {
            $handle = 'theme-' . sanitize_title(basename($vfile, '.js'));
            $vurl = $theme_uri . '/dist/' . $vfile;
            wp_enqueue_script($handle, $vurl, [], wp_get_theme()->get('Version'), false);
            $admin_deps[] = $handle;
        }
    }

    /**
     * Enqueue scripts.
     */
    Assets::enqueueScript(
        'theme-admin-js-bundle',
        $template_dir . '/dist/admin.js',
        $admin_deps,
        true
    );

    /**
     * Localize admin script data with nonce for AJAX requests and i18n strings
     */
    wp_localize_script('theme-admin-js-bundle', 'ajaxurl_params', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('update_post_thumbnail'),  // Must match backend check_ajax_referer
    ]);

    /**
     * Localize i18n strings for admin JavaScript
     */
    wp_localize_script('theme-admin-js-bundle', 'adminI18n', [
        // Thumbnail removal
        'removeThumbnailTitle' => __('Remove Thumbnail?', 'lacadev'),
        'removeThumbnailText' => __('Are you sure you want to remove this featured image?', 'lacadev'),
        'removeThumbnailConfirm' => __('Yes, remove it', 'lacadev'),
        'removeThumbnailCancel' => __('Cancel', 'lacadev'),
        'removedTitle' => __('Removed!', 'lacadev'),
        'removedText' => __('Featured image has been removed.', 'lacadev'),
        'errorTitle' => __('Error!', 'lacadev'),
        'failedRemove' => __('Failed to remove thumbnail.', 'lacadev'),
        
        // UI labels
        'chooseImage' => __('Choose image', 'lacadev'),
        'setFeaturedImage' => __('Set featured image', 'lacadev'),
    ]);

    /**
     * Localize project chart data — chỉ inject trên trang Dashboard (index.php).
     * Dữ liệu được đọc từ custom post type 'project' nếu đã đăng ký.
     */
    $current_screen = get_current_screen();
    if ($current_screen && $current_screen->id === 'dashboard' && post_type_exists('project')) {
        global $wpdb;

        // byStatus: đếm project theo meta _project_status (Carbon Fields)
        $status_labels = [
            'pending'     => '🕐 Chờ làm',
            'in_progress' => '🔨 Đang làm',
            'done'        => '✅ Đã xong',
            'maintenance' => '🔧 Đang bảo trì',
            'paused'      => '⏸️ Tạm dừng',
        ];

        $status_rows = $wpdb->get_results("
            SELECT
                COALESCE(pm.meta_value, 'pending') AS `key`,
                COUNT(*) AS `count`
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm
                ON p.ID = pm.post_id AND pm.meta_key = '_project_status'
            WHERE p.post_type = 'project'
              AND p.post_status NOT IN ('trash','auto-draft','inherit')
            GROUP BY `key`
        ");

        $by_status = [];
        foreach ($status_rows as $row) {
            $by_status[] = [
                'key'   => $row->key,
                'label' => $status_labels[$row->key] ?? ucfirst($row->key),
                'count' => (int) $row->count,
            ];
        }

        // byMonth: đếm project tạo mới trong 12 tháng gần nhất
        $month_rows = $wpdb->get_results("
            SELECT
                DATE_FORMAT(post_date, '%Y-%m') AS ym,
                COUNT(*) AS cnt
            FROM {$wpdb->posts}
            WHERE post_type = 'project'
              AND post_status NOT IN ('trash','auto-draft','inherit')
              AND post_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY ym
            ORDER BY ym ASC
        ");

        // Lấp đầy các tháng còn thiếu
        $month_map = [];
        foreach ($month_rows as $r) {
            $month_map[$r->ym] = (int) $r->cnt;
        }
        $by_month = [];
        for ($i = 11; $i >= 0; $i--) {
            $ym    = date('Y-m', strtotime("-{$i} months"));
            $label = 'T' . (int) date('n', strtotime("-{$i} months"));
            $by_month[] = [
                'month' => $label,
                'count' => $month_map[$ym] ?? 0,
            ];
        }

        wp_localize_script('theme-admin-js-bundle', 'lacaProjectCharts', [
            'primary'  => carbon_get_theme_option('primary_color_ad') ?: '#2ea2cc',
            'byStatus' => $by_status,
            'byMonth'  => $by_month,
        ]);
    }

    // Enqueue front-end styles in admin area
    //  Assets::enqueueStyle('theme-css-bundle', $template_dir . '/dist/styles/theme.css');

    // Inject dynamic admin colors as CSS variables
    $primary_color_ad = carbon_get_theme_option('primary_color_ad') ?: '#2ea2cc';
    $secondary_color_ad = carbon_get_theme_option('secondary_color_ad') ?: '#1d2327';
    $bg_color_ad = carbon_get_theme_option('bg_color_ad') ?: '#f0f0f1';
    $text_color_ad = carbon_get_theme_option('text_color_ad') ?: '#3c434a';

    $custom_css = "
        :root {
            --primary-color-ad: {$primary_color_ad};
            --secondary-color-ad: {$secondary_color_ad};
            --bg-color-ad: {$bg_color_ad};
            --text-color-ad: {$text_color_ad};
        }
    ";
    wp_add_inline_style('theme-admin-css-bundle', $custom_css);
}

/**
 * Preload critical assets in admin_head
 */
add_action('admin_head', function() {
    $theme_root_uri = dirname(get_template_directory_uri());
    $dist_url = $theme_root_uri . '/dist/';
    
    // Preload important fonts
    $fonts = [
        'fonts/BeVietnamPro-Regular.bbe77399f9.ttf',
        'fonts/BeVietnamPro-SemiBold.fbc3f74acb.ttf',
        'fonts/Quicksand-Regular.61504eaec8.ttf',
    ];

    foreach ($fonts as $font) {
        echo '<link rel="preload" href="' . $dist_url . $font . '" as="font" type="font/ttf" crossorigin>' . "\n";
    }
}, 1);

/**
 * Enqueue login assets.
 *
 * @return void
 */
function app_action_login_enqueue_assets()
{
    $template_dir = dirname(get_template_directory_uri());

    /**
     * Enqueue scripts.
     */
    Assets::enqueueScript(
        'theme-login-js-bundle',
        $template_dir . '/dist/login.js',
        [],
        true
    );

    wp_localize_script('theme-login-js-bundle', 'loginI18n', [
        'userLabel' => __('Ai đang ghé trạm? (Tên / Email)', 'lacadev'),
        'userPlaceholder' => __('Điền tên hoặc email vào đây nhé', 'lacadev'),
        'passLabel' => __('Chìa khóa', 'lacadev'),
        'passPlaceholder' => __('Nhập chìa khóa mở cửa', 'lacadev'),
        'welcomeText' => __('Chào mừng về Trạm Laca!<br/>Cắm sạc, pha trà và bắt đầu nào!', 'lacadev'),
        'forgetPwd' => __('Rớt chìa khoá?', 'lacadev'),
        'backToBlog' => __('← Rời khỏi Trạm', 'lacadev'),
        'language' => get_bloginfo('language')
    ]);

    /**
     * Enqueue styles.
     */
    Assets::enqueueStyle(
        'theme-login-css-bundle',
        $template_dir . '/dist/styles/login.css'
    );
}

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function app_action_editor_enqueue_assets()
{
    $template_dir = dirname(get_template_directory_uri());

    /**
     * Enqueue scripts.
     */
    Assets::enqueueScript(
        'theme-editor-js-bundle',
        $template_dir . '/dist/editor.js',
        [],
        true
    );

    /**
    * Enqueue styles.
    */
    Assets::enqueueStyle(
        'theme-editor-css-bundle',
        $template_dir . '/dist/styles/editor.css'
    );

    // Support for block editor styles (classic and modern)
    add_editor_style($template_dir . '/dist/styles/editor.css');

    // Inject theme colors and fonts as CSS variables for the editor
    $primary_color = getOption('primary_color');
    $secondary_color = getOption('secondary_color');
    $bg_color = getOption('bg_color');
    
    $primary_color_dark = getOption('primary_color_dark');
    $secondary_color_dark = getOption('secondary_color_dark');
    $bg_color_dark = getOption('bg_color_dark');

    $custom_css = "
        :root, .editor-styles-wrapper {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
            --bg-color: {$bg_color};
            --primary-color-dark: {$primary_color_dark};
            --secondary-color-dark: {$secondary_color_dark};
            --bg-color-dark: {$bg_color_dark};
            font-family: 'Quicksand', sans-serif !important;
        }
    ";
    wp_add_inline_style('theme-editor-css-bundle', $custom_css);
}

/**
 * Add favicon proxy.
 *
 * @return void
 * @link WPEmergeTheme\Assets\Assets::addFavicon()
 */
function app_action_add_favicon()
{
    Assets::addFavicon();
}

/**
 * Advanced script optimization with defer/async/preload
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    // Scripts to defer (non-critical)
    // NOTE: theme-vendors-js is NOT deferred - it must load blocking to ensure Swal/dependencies are available
    $defer_scripts = [
        'theme-js-bundle',
        'theme-admin-js-bundle',
        'theme-login-js-bundle',
        'theme-editor-js-bundle',
        'theme-archive-js',
        'theme-comments-js'
    ];

    // Scripts to async (tracking, analytics)
    $async_scripts = [
        'google-analytics',
        'facebook-pixel',
        'hotjar'
    ];

    if (in_array($handle, $defer_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }

    if (in_array($handle, $async_scripts)) {
        return str_replace('<script ', '<script async ', $tag);
    }

    return $tag;
}, 10, 3);

/**
 * Advanced style optimization
 */
add_filter('style_loader_tag', function ($tag, $handle, $href) {
    // Non-critical styles to load asynchronously
    $non_critical_styles = [
        'theme-single-css',
        'fontawesome',
        'google-fonts'
    ];

    // If critical CSS file exists (inlined in header), load main bundle asynchronously
    if (file_exists(get_template_directory() . '/dist/styles/critical.css')) {
        $non_critical_styles[] = 'theme-css-bundle';
    }

    if (in_array($handle, $non_critical_styles)) {
        // Load non-critical CSS asynchronously
        return '<link rel="preload" href="' . $href . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'" id="' . $handle . '">' .
            '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
    }

    return $tag;
}, 10, 3);

/**
 * FIXED: Preload critical assets in wp_head (Agent Skills: Performance)
 */
add_action('wp_head', function() {
    $theme_root_dir = dirname(get_template_directory());
    $theme_root_uri = dirname(get_template_directory_uri());
    
    $dist_path = $theme_root_dir . '/dist/';
    $dist_url  = $theme_root_uri . '/dist/';
    
    // 1. Preload Critical JS
    if (file_exists($dist_path . 'critical.js')) {
        echo '<link rel="preload" href="' . $dist_url . 'critical.js" as="script">' . "\n";
    }

    // 2. Preload Main CSS Bundle (if not using Critical CSS inline)
    if (!file_exists($dist_path . 'styles/critical.css')) {
         echo '<link rel="preload" href="' . $dist_url . 'styles/theme.css" as="style">' . "\n";
    }

    // 3. Preload important fonts (Agent Skills: Performance)
    $fonts = [
        'fonts/BeVietnamPro-Regular.bbe77399f9.ttf',
        'fonts/BeVietnamPro-SemiBold.fbc3f74acb.ttf',
        'fonts/Quicksand-Regular.61504eaec8.ttf',
    ];

    foreach ($fonts as $font) {
        echo '<link rel="preload" href="' . $dist_url . $font . '" as="font" type="font/ttf" crossorigin>' . "\n";
    }
}, 1);

/**
 * Enhanced resource hints for performance
 */
add_filter('wp_resource_hints', function ($hints, $relation_type) {
    if ('preconnect' === $relation_type) {
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://ajax.googleapis.com';
    }

    if ('dns-prefetch' === $relation_type) {
        $hints[] = '//fonts.googleapis.com';
        $hints[] = '//cdnjs.cloudflare.com';
    }

    if ('prefetch' === $relation_type && (is_home() || is_front_page())) {
        // Prefetch likely next pages
        $hints[] = get_permalink(get_option('page_for_posts'));
    }

    return $hints;
}, 10, 2);

// Hook vào action để enqueue assets thông qua function có sẵn thay vì thêm action mới
add_action('wp_enqueue_scripts', 'app_action_theme_enqueue_assets');
add_action('admin_enqueue_scripts', 'app_action_admin_enqueue_assets');
add_action('login_enqueue_scripts', 'app_action_login_enqueue_assets');
add_action('enqueue_block_editor_assets', 'app_action_editor_enqueue_assets'); // For Gutenberg editor
