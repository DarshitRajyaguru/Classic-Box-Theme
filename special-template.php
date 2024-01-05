<?php
/**
 * Template Name: Special Template
 */
get_header();

if (have_posts()) :
	while (have_posts()) : the_post(); ?>	
        <article class="post page">
            <h2><?php the_title(); ?></h2>

                <!-- Info Box -->
                <div class="info-box">
                    <h4>Desclaimer Text</h4>
                    <p>Lorem ipsum dolor sit amet. Nam voluptates deserunt id voluptas accusamus sit laudantium necessitatibus et nemo exercitationem aut commodi maiores ut aliquid voluptatem. Quo laborum veniam in quae minima aut quae accusantium. Eum soluta atque ut expedita nobis eum fuga laboriosam eos aspernatur error a excepturi dolor est optio maiores. Sit maxime laboriosam ex minus possimus qui corporis maiores et quisquam quia.</p>
                </div>

            <?php the_content(); ?>
        </article>
	<?php endwhile;
else :
	echo '<p>No content found</p>';
endif;

get_footer();