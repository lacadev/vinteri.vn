<?php
/**
 * Single Post Template - Journal Detail
 *
 * Editorial style layout inspired by Stitch design:
 * - Centered header with category, title, author/date
 * - Full-width hero image
 * - 3-column body: share | content | related products
 * - Author bio card
 * - Related articles grid
 *
 * @package lacadev
 */

get_header();

$post_id    = get_the_ID();
$title      = get_the_title();
$categories = get_the_terms($post_id, 'category');
$cat_name   = $categories ? $categories[0]->name : '';
$cat_link   = $categories ? get_term_link($categories[0]) : '#';
$author_id  = get_the_author_meta('ID');
$author     = get_the_author();
$date       = get_the_date('d/m/Y');
$content    = get_the_content();
$reading_time = max(1, round(str_word_count(strip_tags($content)) / 200));
?>

<article class="single-journal">
    <?php theBreadcrumb(); ?>

    <!-- Article Header -->
    <header class="single-journal__header">
        <div class="container">
            <?php if ($cat_name): ?>
                <a href="<?php echo esc_url($cat_link); ?>" class="single-journal__category">
                    <?php echo esc_html($cat_name); ?>
                </a>
            <?php endif; ?>

            <h1 class="single-journal__title"><?php echo esc_html($title); ?></h1>

            <div class="single-journal__meta">
                <span><?php printf(__('By %s', 'laca'), esc_html($author)); ?></span>
                <span class="single-journal__meta-dot"></span>
                <span><?php echo esc_html($date); ?></span>
                <span class="single-journal__meta-dot"></span>
                <span><?php printf(__('%d phút đọc', 'laca'), $reading_time); ?></span>
            </div>
        </div>
    </header>

    <!-- Hero Image -->
    <?php if (has_post_thumbnail()): ?>
        <section class="single-journal__hero">
            <?php echo getResponsivePostThumbnail($post_id, 'full'); ?>
        </section>
    <?php endif; ?>

    <!-- Content Body -->
    <div class="single-journal__body">
        <div class="container">
            <div class="single-journal__grid">

                <!-- Share Sidebar -->
                <aside class="single-journal__share">
                    <div class="single-journal__share-sticky">
                        <h4 class="single-journal__share-label"><?php _e('Chia sẻ', 'laca'); ?></h4>
                        <div class="single-journal__share-links">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" rel="noopener" title="Facebook">Facebook</a>
                            <a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&description=<?php echo urlencode($title); ?>" 
                               target="_blank" rel="noopener" title="Pinterest">Pinterest</a>
                            <a href="mailto:?subject=<?php echo rawurlencode($title); ?>&body=<?php echo urlencode(get_permalink()); ?>" 
                               title="Email">Email</a>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="single-journal__content editorial-content">
                    <?php theContent(); ?>
                </div>

                <!-- Products Sidebar -->
                <?php
                // Get related products (tagged in post or latest)
                $related_products = [];
                if (class_exists('WooCommerce')) {
                    $product_ids = get_post_meta($post_id, '_related_products', true);
                    if (empty($product_ids)) {
                        // Fallback: get latest 3 products
                        $product_query = new WP_Query([
                            'post_type'      => 'product',
                            'posts_per_page' => 3,
                            'post_status'    => 'publish',
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                            'no_found_rows'  => true,
                        ]);
                        $related_products = $product_query->posts;
                        wp_reset_postdata();
                    }
                }
                if (!empty($related_products)): ?>
                    <aside class="single-journal__products">
                        <div class="single-journal__products-inner">
                            <h3 class="single-journal__products-title"><?php _e('Sản phẩm trong bài', 'laca'); ?></h3>
                            <div class="single-journal__products-list">
                                <?php foreach ($related_products as $p):
                                    $product = wc_get_product($p->ID);
                                    if (!$product) continue;
                                    $img_url = get_the_post_thumbnail_url($p->ID, 'woocommerce_thumbnail');
                                    if (!$img_url) $img_url = wc_placeholder_img_src('woocommerce_thumbnail');
                                ?>
                                    <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="single-journal__product-item">
                                        <div class="single-journal__product-img">
                                            <img src="<?php echo esc_url($img_url); ?>" 
                                                 alt="<?php echo esc_attr($product->get_name()); ?>" 
                                                 loading="lazy">
                                        </div>
                                        <h5 class="single-journal__product-name"><?php echo esc_html($product->get_name()); ?></h5>
                                        <span class="single-journal__product-price"><?php echo $product->get_price_html(); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </aside>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Author Bio -->
    <?php
    $author_bio   = get_the_author_meta('description', $author_id);
    $author_avatar = get_avatar_url($author_id, ['size' => 128]);
    $author_role  = '';
    $user_data = get_userdata($author_id);
    if ($user_data && !empty($user_data->roles)) {
        $role_names = ['administrator' => 'Admin', 'editor' => 'Editor', 'author' => 'Author'];
        $author_role = $role_names[$user_data->roles[0]] ?? ucfirst($user_data->roles[0]);
    }
    if ($author_bio): ?>
        <section class="single-journal__author">
            <div class="container">
                <div class="single-journal__author-card">
                    <img src="<?php echo esc_url($author_avatar); ?>" 
                         alt="<?php echo esc_attr($author); ?>" 
                         class="single-journal__author-avatar">
                    <div class="single-journal__author-info">
                        <h4 class="single-journal__author-name"><?php echo esc_html($author); ?></h4>
                        <?php if ($author_role): ?>
                            <p class="single-journal__author-role"><?php echo esc_html($author_role); ?></p>
                        <?php endif; ?>
                        <p class="single-journal__author-bio"><?php echo esc_html($author_bio); ?></p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Related Articles -->
    <?php
    $related_args = [
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'post__not_in'   => [$post_id],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ];
    if ($categories) {
        $related_args['tax_query'] = [[
            'taxonomy' => 'category',
            'terms'    => $categories[0]->term_id,
        ]];
    }
    $related = new WP_Query($related_args);
    if ($related->have_posts()): ?>
        <section class="single-journal__related">
            <div class="container">
                <div class="single-journal__related-header">
                    <div>
                        <span class="single-journal__related-label"><?php _e('Đọc tiếp', 'laca'); ?></span>
                        <h2 class="single-journal__related-heading"><?php _e('Bài viết liên quan', 'laca'); ?></h2>
                    </div>
                    <?php if ($categories): ?>
                        <a href="<?php echo esc_url($cat_link); ?>" class="single-journal__related-viewall">
                            <?php _e('Xem tất cả', 'laca'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="single-journal__related-grid">
                    <?php while ($related->have_posts()): $related->the_post();
                        $rel_cat = get_the_terms(get_the_ID(), 'category');
                    ?>
                        <a href="<?php the_permalink(); ?>" class="single-journal__related-card">
                            <div class="single-journal__related-img">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php echo getResponsivePostThumbnail(get_the_ID(), 'medium_large'); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($rel_cat): ?>
                                <span class="single-journal__related-cat"><?php echo esc_html($rel_cat[0]->name); ?></span>
                            <?php endif; ?>
                            <h3 class="single-journal__related-title"><?php the_title(); ?></h3>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

</article>

<?php get_footer(); ?>