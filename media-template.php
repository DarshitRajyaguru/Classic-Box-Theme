<?php
/*
Template Name: Media Library Table
*/

get_header(); // Include the header


echo "<div class='media-section'>";
echo "<table>";
echo "<tr>";
echo "<th>Image</th>";
echo "<th>File Name</th>";
echo "<th>File Size</th>";
echo "<th>Used In</th>";
echo "<th>Action</th>";
echo "</tr>";
$args = array(
    'post_type' => 'attachment',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => null,
    //'posts_per_page' => 20,
    // any parent
);
$attachments = get_posts($args);
if ($attachments) {
    foreach ($attachments as $post) {
        //Get an array of all registered post type including Custom Post Type
        $custom_post_type = get_post_types();

        $usage_info = array();
        $id = get_the_ID();

        // Check for usage in posts and pages
        $usage_media = get_posts(
            array(
                'numberposts' => -1,
                'post_type' => $custom_post_type,
                'post_status' => array('publish', 'draft'),
                'meta_query' => array(
                    array(
                        'key' => '_thumbnail_id',
                        'value' => $id,
                        'compare' => '=',
                    )
                )
            )
        );

        foreach ($usage_media as $usage_post) {
            $usage_info[] = '<p><b>' . get_post_type($usage_post) . '</b>: <a href="' . get_the_permalink($usage_post) . '">' . get_the_title($usage_post) . '</a></p>';
        }

        // Check for usage in content
        $content_media = get_posts(
            array(
                'numberposts' => -1,
                'post_type' => $custom_post_type,
                'post_status' => array('publish', 'draft'),
                'suppress_filters' => false,
                's' => 'wp-image-' . $id,
            )
        );

        foreach ($content_media as $content_post) {
            $usage_info[] = '<p><b>' . get_post_type($content_post) . '</b>: <a href="' . get_the_permalink($content_post) . '">' . get_the_title($content_post) . '</a></p>';
        }

        $usage_info_string = implode(' ', $usage_info);

        echo "<tr>";
        echo '<td><img src="' . esc_html(wp_get_attachment_url()) . '" alt="' . esc_html(get_the_title()) . '" height="50" width="50"/></td>';
        echo '<td><a href="' . esc_html(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></td>';
        echo '<td>' . size_format(filesize(get_attached_file(get_the_ID()))) . '</td>';
        echo '<td>' . $usage_info_string . '<br></td>';
        echo '<td><a href="' . get_delete_post_link(get_the_ID()) . '" class="delete-item-button">Delete</a></td>';
        echo '</tr>';
    }
    // echo the_posts_pagination($args) . "bnv";
}
echo "</table>";
echo "</div>";




function check_media_usage()
{
    $media_query = new WP_Query(
        array(
            'post_type' => 'any',
            'posts_per_page' => -1,
        )
    );

    $media_usage = array(); // Store media usage information

    if ($media_query->have_posts()) {
        while ($media_query->have_posts()) {
            $media_query->the_post();

            // Check Elementor content
            if (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID())) {
                $media_usage[get_the_ID()][] = 'Used in Elementor';
            }

            // Check WPBakery Page Builder content
            if (defined('WPB_VC_VERSION') && has_shortcode(get_the_content(), 'vc_row')) {
                $media_usage[get_the_ID()][] = 'Used in WPBakery';
            }

            // Check ACF fields
            if (function_exists('acf')) {
                $acf_fields = get_fields(get_the_ID()); // You need to set the field names you want to check

                if (!empty($acf_fields)) {
                    foreach ($acf_fields as $field_name => $field_value) {
                        if (!empty(is_string($field_value)) && !empty(strpos($field_value, wp_get_attachment_url()) !== false)) {
                            $media_usage[get_the_ID()][] = 'Used in ACF : ' . $field_name;
                            // echo '<pre>';
                            // print_r($media_usage);
                        }
                    }
                }
            }
        }
    }

    // Display media usage information
    foreach ($media_usage as $post_id => $usages) {
        $post_title = get_the_title($post_id);
        $usage_info = implode(', ', $usages);

        echo "Media used in: $post_title ($usage_info)<br>";
    }
}

// Run the function
check_media_usage();

wp_reset_postdata();

get_footer(); // Include the footer
