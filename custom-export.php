<?php
/**
* Plugin Name: Custom Export
* Plugin URI: https://developer.wordpress.org/
* Description: This is the very first plugin I ever created.
* Version: 1.0
* Author: WordPress.org`
* Author URI: https://developer.wordpress.org/
**/


register_activation_hook( __FILE__, 'admin_post_list_add_export_button' );

register_deactivation_hook( __FILE__, 'admin_post_list_add_export_button' );

function admin_post_list_add_export_button( $which ) {
    global $typenow;
  
    if ( 'post' === $typenow && 'top' === $which ) {
        ?>
        <input type="submit" name="export_all_posts" class="button button-primary" value="<?php _e('Export All Posts'); ?>" />
        <?php
    }
}
 
add_action( 'manage_posts_extra_tablenav', 'admin_post_list_add_export_button', 20, 1 );


function func_export_all_posts() {
    if(isset($_GET['export_all_posts'])) {
        $arg = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
  
        global $post;
        $arr_post = get_posts($arg);
        if ($arr_post) {
  
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="wp-posts.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
  
            $file = fopen('php://output', 'w');
  
            fputcsv($file, array('Post Id', 'Post Title', 'URL', 'Post Image', 'Categories', 'Tags', 'Author' , 'Date'));
  
            foreach ($arr_post as $post) {
                setup_postdata($post);
				
				$image= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
                  
                $categories = get_the_category();
                $cats = array();
                if (!empty($categories)) {
                    foreach ( $categories as $category ) {
                        $cats[] = $category->name;
                    }
                }
  
                $post_tags = get_the_tags();
                $tags = array();
                if (!empty($post_tags)) {
                    foreach ($post_tags as $tag) {
                        $tags[] = $tag->name;
                    }
                }
  
                fputcsv(
					$file, 
					array(
						get_the_ID(), get_the_title(), get_the_permalink(), $image[0], implode(",", $cats), implode(",", $tags), the_author(), 
						get_the_date()
					)
				);
            }
  
            exit();
        }
    }
}
 
add_action( 'init', 'func_export_all_posts' );

?>