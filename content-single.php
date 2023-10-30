<article class="post page">
    <!--Add the title of the posts or pages -->
    <h2><a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a></h2>

    <!--Get the information about posted by author with date and time
            Also retrieve the data of the categories of posts -->
    <p class="post-info">
        <?php the_time('F jS, Y g:i a'); ?> | By <a
            href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
            <?php the_author(); ?>
        </a> | Posted in
        <?php
        $categories = get_the_category();
        $separator = ', ';
        $output = '';

        if ($categories) {
            foreach ($categories as $category) {
                $output .= '<a href="' . get_category_link($category->term_id) . '">' . $category->cat_name . '</a>' . $separator;
            }
            echo trim($output, $separator);
        }
        ?>
    </p>
    <!-- Get the post thumbnails -->
    <?php the_post_thumbnail('banner-image'); ?>

    <!--Get the content of the posts and pages-->
    <?php the_content(); ?>

    <div class="author-info clearfix">
        <div class="author-image">
            <?php echo get_avatar(get_the_author_meta('ID'), 150); ?>
            <p class="author-name">
                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                    <?php echo get_the_author_meta('nickname'); ?>
                </a>
            </p>
        </div>
        <div class="author-content">
            <h3 class="author-title">Author Information</h3>
            <p class="author-data">
                <?php echo get_the_author_meta('description'); ?>
            </p>
        </div>
    </div>
</article>