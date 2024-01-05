<?php
/**
 * Template Name: Media Template
 */
// Query all media files
get_header();

// Simplified code to retrieve media files and display them in a table
function display_media_table()
{
    $media_files = get_media_files(); // Implement get_media_files() to retrieve media
    if (empty($media_files)) {
        echo "No media files found.";
    } else {
        echo "<table>";
        echo "<tr><th>File Name</th><th>File Size</th><th>Action</th></tr>";
        foreach ($media_files as $media) {
            echo "<tr>";
            echo "<td>{$media->post_title}</td>";
            echo "<td>" . size_format(filesize(get_attached_file($media->ID))) . "</td>";
            echo "<td><button class='delete-media' data-media-id='{$media->ID}'>Delete</button></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

function get_media_files()
{
    $args = array(
        'post_type' => 'attachment',
        // This limits the query to media files
        'post_status' => 'inherit',
        'posts_per_page' => -1,
        // Retrieve all media files
    );

    $media_files = get_posts($args);

    return $media_files;
}

// Usage example:
$media_files = get_media_files();


display_media_table();

get_footer();