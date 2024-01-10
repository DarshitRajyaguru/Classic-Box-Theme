<?php
/*
Template Name: Media Library Table
*/

get_header(); // Include the header
function media_usage_info_table()
{
    $html_output = "<div class='media-section'>";
    $html_output .= "<form method='post' name='' id=''><button type='button' id='deleteButton' class='deleteButton' style='display: none;'>Delete Selected</button>";
    $html_output .= "<table>";
    $html_output .= "<tr>";
    $html_output .= "<th></th>";
    $html_output .= "<th>Image</th>";
    $html_output .= "<th>File Name</th>";
    $html_output .= "<th>File Size</th>";
    $html_output .= "<th>File Count</th>";
    $html_output .= "<th>Image List</th>";
    $html_output .= "<th>Used In</th>";
    $html_output .= "<th>Action</th>";
    $html_output .= "</tr>";

    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
        'post_status' => null,
        'post_parent' => null,
    );
    $attachments = get_posts($args);

    if ($attachments) {
        foreach ($attachments as $attachment) {
            $custom_post_type = get_post_types();
            $usage_info = array();
            $id = $attachment->ID;

            // Check usage in posts and pages
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

            $get_media_list = get_media_list($id);

            $html_output .= "<tr>";
            $html_output .= '<td><input type="checkbox" value="' . $id . '" class="det_checkbox" /></td>';
            $html_output .= '<td><img src="' . esc_html(wp_get_attachment_url($id)) . '" alt="' . esc_html(get_the_title($id)) . '" height="50" width="50"/></td>';
            $html_output .= '<td><a href="' . esc_html(get_the_permalink($id)) . '">' . esc_html(get_the_title($id)) . '</a></td>';
            $html_output .= '<td>' . size_format(filesize(get_attached_file($id))) . '</td>';
            $html_output .= '<td>' . get_media_count($id) . '</td>';
            $html_output .= '<td>';
            if (!empty($get_media_list) && is_array($get_media_list)) {
                foreach ($get_media_list as $filelist) {
                    $html_output .= $filelist . '</br>';
                }
            } else {
                $html_output .= '-';
            }
            $html_output .= '</td>';
            $html_output .= '<td>' . $usage_info_string . '</td>';
            $html_output .= '<td><a href="javascript:void(0)" class="delete-item-button" data-media_id="' . get_the_ID($id) . '">Delete</a></td>';
            $html_output .= '</tr>';
        }
    }
    $html_output .= "</table>";
    $html_output .= "</form>";
    $html_output .= "</div>";

    echo $html_output;
}
// Call the function to display the media usage information
media_usage_info_table();

function get_all_media_usage_info() {
    $media_info_all = array();

    // Step 1: Get all media attachments
    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1,
    );

    $media_attachments = get_posts($args);

    foreach ($media_attachments as $media_attachment) {
        $media_id = $media_attachment->ID;

        // Step 2: Get usage info for each media
        $media_info = get_media_usage_info($media_id);

        // If media is used, add it to the result array
        if (!empty($media_info)) {
            $media_info_all[] = array(
                'media_id' => $media_id,
                'media_title' => $media_attachment->post_title,
                'usage_info' => $media_info,
            );
        }
    }

    return $media_info_all;
}

// Function to get usage info for a specific media ID
function get_media_usage_info($media_id) {
    $usage_info = array();

    // Query all post types
    $post_types = get_post_types(array('public' => true), 'objects');

    // Loop through each post type
    foreach ($post_types as $post_type) {
        $args = array(
            'post_type' => $post_type->name,
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        // Loop through each post
        foreach ($posts as $post) {
            // Check if media ID is present in the post content
            $post_content = $post->post_content;
            if (strpos($post_content, 'wp-image-' . $media_id) !== false) {
                // Identify the page builder
                $page_builder = identify_page_builder($post_content);

                $usage_info[] = array(
                    'post_title' => $post->post_title,
                    'post_type' => $post_type->name,
                    'page_builder' => $page_builder,
                );
            }
        }
    }

    return $usage_info;
}

// Function to identify the page builder (Elementor and Beaver Builder example)
function identify_page_builder($content) {
    $page_builder = 'Unknown';

    // Check for Elementor
    if (strpos($content, 'elementor') !== false) {
        $page_builder = 'Elementor';
    }

    // Check for Beaver Builder
    if (strpos($content, 'fl-builder') !== false) {
        $page_builder = 'Beaver Builder';
    }

    // Check for WPBakery
    if (strpos($content, 'vc_row') !== false) {
        $page_builder = 'WPBakery';
    }

    // Check for Divi
    if (strpos($content, 'et_pb_section') !== false) {
        $page_builder = 'Divi';
    }

    // Check for Thrive Architect
    if (strpos($content, 'tve_leads_form_container') !== false) {
        $page_builder = 'Thrive Architect';
    }

    // Check for Classic Editor
    if (strpos($content, 'wp-editor-area') !== false) {
        $page_builder = 'Classic Editor';
    }

    return $page_builder;
}

// ...

// Example usage: Get usage info for all media
$all_media_usage_info = get_all_media_usage_info();

// Print the results
echo '<pre>';
print_r($all_media_usage_info);
echo '</pre>';


// Example usage: Get usage info for all media
$all_media_usage_info = get_all_media_usage_info();

// Print the results
echo '<pre>';
print_r($all_media_usage_info);
echo '</pre>';

wp_reset_postdata();

get_footer(); // Include the footer