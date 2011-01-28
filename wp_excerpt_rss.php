<?php
/**
 * Plugin Name: wp_excerpt_rss
 * Author Name: Nully
 * Author URI: http://blog.nully.org/
 * Description: RSS2のフィードを抜粋表示にし、投稿サムネイルを表示します。
 * Plugin Version: 1.0
 */
// admin page required
require_once dirname(__FILE__). "/wp_excerpt_rss_admin.php";


/**
 * Do filter the_content_feed.
 * rss(wp-includes/feed-rss.php) is don't uses the_content_feed().
 * user manualy fix to the_excerpt_rss() to the_content_rss().
 *
 * @param  $content String  filterd RSS content text.
 * @return String   RSS feed content 
 */
add_filter("the_content_feed", "wp_excerpt_rss");
function wp_excerpt_rss($content) {
    $image = null;

    if(function_exists("get_the_post_thumbnail") && has_post_thumbnail(get_the_ID(), "post-thumbnail")) {
        $image = get_the_post_thumbnail(get_the_ID(), "post-thumbnail");
    }
    elseif(($att = wp_excerpt_rss_get_attachment(get_the_ID()))) {
        $image = $att;
    }
    ob_start();
    the_excerpt_rss();
    $content = ob_get_clean();

    if(!is_null($image))
        $content = "<p>". $image. "</p>". $content;

    return $content;
}


/**
 * Get attachmented post attachment.
 * 
 * @param $post_id  int  post id
 * @return String   HTML img tag.
 */
function wp_excerpt_rss_get_attachment($post_id) {
    $attachments = get_children(array(
        "post_parent" => $post_id,
        "post_type" => "attachment",
        "post_mime_type" => "image"
    ));

    if(empty($attachments))
        return false;

    $aid = array();
    $morder = array();
    foreach($attachments as $key => $data) {
        $aid[$key] = $data->ID;
        $morder[$key] = $data->menu_order;
    }

    array_multisort($aid, SORT_ASC, $morder, SORT_DESC, $attachments);
    $attachment = array_pop($attachments);
    $image_size = get_option("wp_excerpt_rss_image_size", "thumbnail");


    return wp_get_attachment_image($attachment->ID, $image_size);
}


