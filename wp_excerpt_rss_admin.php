<?php
/**
 * wp_excerpt_rss admin page.
 */
/**
 * save form options
 *
 * @action admin_init
 */
add_action("admin_init", "wp_excerpt_rss_init");
function wp_excerpt_rss_init() {
    global $plugin_page;
    if(isset($_POST["wp_excerpt_rss_save"])) {
        $status = "error";
        if(_wp_excerpt_rss_admin_update_options()) {
            $status = "updated";
        }
        wp_redirect("admin.php?page={$plugin_page}&status={$status}");
        exit;
    }
    elseif(isset($_GET["status"]) && !empty($_GET["status"])) {
        add_action("admin_notices", "wp_excerpt_rss_admin_notices");
    }
}


/**
 * display success or failure notices
 *
 * @action admin_notcies
 */
function wp_excerpt_rss_admin_notices() {
    $state = $_GET["status"];
?>
<div class="<?php echo $state; ?> faded">
<?php if($state == "error"): ?>
<p>設定に変更がなかったため、設定は更新されませんでした。</p>
<?php else: ?>
<p>設定の更新が完了しました。</p>
<?php endif; ?>
<p>
<a href="<?php bloginfo("rss2_url"); ?>" target="_blank">RSS2フィードを見る</a>
<a href="<?php bloginfo("atom_url"); ?>" target="_blank">ATOMフィードを見る</a>
<a href="<?php bloginfo("rss_url"); ?>" target="_blank">RSSフィードを見る</a>
<a href="<?php bloginfo("rdf_url"); ?>" target="_blank">RDFフィードを見る</a>
</p>
</div>
<?php
}


/**
 * add admin menu
 *
 * @action amin_menu
 */
add_action("admin_menu", "wp_excerpt_rss_admin_menu");
function wp_excerpt_rss_admin_menu() {
    if(function_exists("add_menu_page")) {
        add_menu_page(
            "wp_excerpt_rss Admin panel", "wp_excerpt_rss",
            10, "wp_excerpt_rss", "wp_excerpt_rss_admin_setting"
        );
    }
}


/**
 * wp_excerpt_rss admin setting page
 *
 * @called wp_excerpt_rss_admin_menu
 */
function wp_excerpt_rss_admin_setting() {
?>
<div class="wrap">
<form action="" method="post">
<?php wp_nonce_field("themes"); ?>
<?php screen_icon("options-general"); ?>
<h2>wp_excerpt_rss設定</h2>
<p>wp_excerpt_rssの設定を行います。</p>


<h3>利用する画像サイズ</h3>
<p>RSSに利用する画像のサイズを指定します。</p>
<?php foreach(_wp_excerpt_rss_admin_get_image_sizes() as $name => $size): ?>
<input type="radio" id="wp_excerpt_rss_image_size_<?php
    echo esc_attr($name);
?>" name="wp_excerpt_rss_image_size" value="<?php
    _e($name);
?>" <?php
    if($name == get_option("wp_excerpt_rss_image_size", "thumbnail")) {
        echo ' checked="checked"';
    }
?>/>&nbsp;<label for="wp_excerpt_rss_image_size_<?php
    echo esc_attr($name);
?>"><?php
    _e(esc_attr($name));
?>(<?php echo $size["width"]; ?> x <?php echo $size["height"]; ?>)</label><br />
<?php endforeach; ?>
<!-- End image size setting fields -->


<p><input type="submit" class="button-primary" name="wp_excerpt_rss_save" value="<?php
    _e("Save");
?>" /></p>

</div>
<?php
}


/**
 * get all image sizes.
 *
 * @return Array   registerd image sizes.
 */
function _wp_excerpt_rss_admin_get_image_sizes() {
    global $_wp_additional_image_sizes;
    $default_sizes = array(
        "thumbnail" => array(
            "width"  => get_option("thumbnail_size_w"),
            "height" => get_option("thumbnail_size_h"),
        ),
        "medium" => array(
            "width"  => get_option("medium_size_w"),
            "height" => get_option("medium_size_h"),
        ),
        "large" => array(
            "width"  => get_option("large_size_w"),
            "height" => get_option("large_size_h"),
        ),
        /*"full" => array(
            "width"  => "image max width",
            "height" => "image max height",
        ),*/
   );

    $image_sizes = $_wp_additional_image_sizes;
    if(empty($image_sizes))
        $image_sizes = array();

    $sizes = array_merge($default_sizes, $image_sizes);
    return $sizes;
}


/**
 * Update wp_excerpt_rss plugin options
 *
 * @return Bool
 */
function _wp_excerpt_rss_admin_update_options() {
    if(strtolower($_SERVER["REQUEST_METHOD"]) !== "post")
        return false;

    $image_size = update_option(
        "wp_excerpt_rss_image_size", $_POST["wp_excerpt_rss_image_size"]
    );

    return $image_size;
}



