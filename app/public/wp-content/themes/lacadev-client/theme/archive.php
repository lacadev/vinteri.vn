<?php
/**
 * Archive Template - Journal List
 *
 * Editorial layout with:
 * - Featured hero post (image + overlapping info card)
 * - Category filter tabs
 * - Asymmetric editorial grid
 * - Pagination
 *
 * @package lacadev
 */

get_header();

// Get categories for filter tabs
$categories = get_categories(['hide_empty' => true]);
$current_cat = is_category() ? get_queried_object() : null;

// Separate featured (first) post from the grid
$featured_post = null;
$grid_posts    = [];

if (have_posts()) {
    // N+1 prevention
    update_post_caches($GLOBALS['wp_query']->posts, 'post', true, true);
    update_object_term_cache(wp_list_pluck($GLOBALS['wp_query']->posts, 'ID'), 'post');

    $all_posts = $GLOBALS['wp_query']->posts;
    
    // Only show featured hero on page 1
    $paged = get_query_var('paged', 1);
    if ($paged <= 1 && !empty($all_posts)) {
        $featured_post = $all_posts[0];
        $grid_posts = array_slice($all_posts, 1);
    } else {
        $grid_posts = $all_posts;
    }
}
?>

<main class="journal-archive">
    <?php theBreadcrumb(); ?>

    <?php // --- Featured Hero Post --- ?>
    <?php if ($featured_post):
        $fp_id       = $featured_post->ID;
        $fp_cats     = get_the_terms($fp_id, 'category');
        $fp_cat_name = $fp_cats ? $fp_cats[0]->name : '';
        $fp_excerpt  = wp_trim_words(get_the_excerpt($fp_id), 30, '...');
    ?>
        <section class="journal-hero">
            <div class="container">
                <a href="<?php echo esc_url(get_permalink($fp_id)); ?>" class="journal-hero__link">
                    <div class="journal-hero__grid">
                        <div class="journal-hero__image">
                            <?php echo getResponsivePostThumbnail($fp_id, 'full'); ?>
                        </div>
                        <div class="journal-hero__info">
                            <?php if ($fp_cat_name): ?>
                                <span class="journal-hero__cat"><?php echo esc_html($fp_cat_name); ?></span>
                            <?php endif; ?>
                            <h2 class="journal-hero__title"><?php echo esc_html(get_the_title($fp_id)); ?></h2>
                            <p class="journal-hero__excerpt"><?php echo esc_html($fp_excerpt); ?></p>
                            <span class="journal-hero__cta">
                                <?php _e('Đọc bài viết', 'laca'); ?> →
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    <?php endif; ?>

    <?php // --- Category Tabs --- ?>
    <section class="journal-tabs">
        <div class="container">
            <div class="journal-tabs__inner">
                <div class="journal-tabs__list">
                    <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" 
                       class="journal-tabs__item <?php echo !$current_cat ? 'is-active' : ''; ?>">
                        <?php _e('Tất cả', 'laca'); ?>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" 
                           class="journal-tabs__item <?php echo ($current_cat && $current_cat->term_id === $cat->term_id) ? 'is-active' : ''; ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php // --- Editorial Grid --- ?>
    <?php if (!empty($grid_posts)): ?>
        <section class="journal-grid">
            <div class="container">
                <div class="journal-grid__inner">
                    <?php 
                    $total = count($grid_posts);
                    foreach ($grid_posts as $i => $gp):
                        $gp_id   = $gp->ID;
                        $gp_cats = get_the_terms($gp_id, 'category');
                        $gp_cat  = $gp_cats ? $gp_cats[0]->name : '';
                        $gp_excerpt = wp_trim_words(get_the_excerpt($gp_id), 20, '...');

                        // Determine card variant based on position in group of 5
                        $pos = $i % 5;
                        // 0,1,2 = standard 3-col; 3 = wide (2-col); 4 = small (1-col)
                        $card_class = 'journal-card';
                        $aspect = 'journal-card--portrait'; // 3:4
                        if ($pos === 1) $aspect = 'journal-card--square';
                        if ($pos === 2) $aspect = 'journal-card--tall'; // 4:5
                        if ($pos === 3) { $card_class .= ' journal-card--wide'; $aspect = 'journal-card--landscape'; }
                        if ($pos === 4) $aspect = 'journal-card--portrait';
                        // Offset the middle column
                        if ($pos === 1) $card_class .= ' journal-card--offset';
                    ?>
                        <a href="<?php echo esc_url(get_permalink($gp_id)); ?>" 
                           class="<?php echo esc_attr($card_class); ?>">
                            <div class="journal-card__image <?php echo esc_attr($aspect); ?>">
                                <?php echo getResponsivePostThumbnail($gp_id, 'medium_large'); ?>
                            </div>
                            <?php if ($gp_cat): ?>
                                <span class="journal-card__cat"><?php echo esc_html($gp_cat); ?></span>
                            <?php endif; ?>
                            <h3 class="journal-card__title"><?php echo esc_html(get_the_title($gp_id)); ?></h3>
                            <?php if ($pos !== 4): // Hide excerpt on small cards ?>
                                <p class="journal-card__excerpt"><?php echo esc_html($gp_excerpt); ?></p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php // --- No Posts --- ?>
    <?php if (!have_posts()): ?>
        <div class="container">
            <div class="journal-empty">
                <p><?php _e('Chưa có bài viết nào.', 'laca'); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php // --- Pagination --- ?>
    <div class="container">
        <?php thePagination(); ?>
    </div>

</main>

<?php get_footer(); ?>