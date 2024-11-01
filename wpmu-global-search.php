<?php
/* 
 * Plugin Name: WPMU Global Search
 * Plugin URI: http://grial.usal.es/agora/pfcgrial/wpmu-global-search
 * Description: Adds the ability to search through blogs into your WordPress MU installation.
 * Version: 2.1
 * Requires at least: WordPress MU 2.6
 * Tested up to: WordPress MU 2.9
 * Author: Alicia García Holgado
 * Author URI: http://grial.usal.es/agora/mambanegra
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/*  Copyright 2010  Alicia García Holgado  (email : aliciagh@usal.es)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WPMU_PLUGIN_URL' ) )
      define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL. '/mu-plugins' );
if ( ! defined( 'WPMU_PLUGIN_DIR' ) )
      define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );

load_plugin_textdomain( 'wpmu-global-search', MUPLUGINDIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/langs' );

include_once('includes/functions.php');

function wpmu_globalsearch($myargs = '') {
    if (is_array($myargs)) $args = &$myargs;
    else parse_str($myargs, $args);

    $defaults = array('before_widget'=>'','after_widget'=>'',
    'before_title'=>'<h2>','after_title'=>'</h2>'
    );
    $args = array_merge($defaults, $args);

    widget_wp_wpmu_globalsearch($args);
}

function widget_wp_wpmu_globalsearch($args) {
    extract($args);
    $options = get_option('widget_wp_wpmu_globalsearch');
    $title = empty($options['title']) ? __( 'Global Search', 'wpmu-global-search' ) : attribute_escape($options['title']);
    $page = empty($options['page']) ? __( 'globalsearch', 'wpmu-global-search' ) : attribute_escape($options['page']);

    echo $before_widget . $before_title . $title . $after_title;
    wpmu_globalsearch_form($page);
    echo $after_widget;
}

function widget_wp_wpmu_globalsearch_control() {
    $options = $newoptions = get_option('widget_wp_wpmu_globalsearch');
    if ( $_POST['wpmusearch-submit'] ) {
        $newoptions['title'] = strip_tags(stripslashes( $_POST['wpmusearch-title']));
        $newoptions['page'] = strip_tags(stripslashes( $_POST['wpmusearch-page']));
    }
    if ( $options != $newoptions ) {
        $options = $newoptions;
        update_option( 'widget_wp_wpmu_globalsearch', $options );

    }
    $title = attribute_escape($options['title']);
    $page = attribute_escape($options['page']);
	?>
	
	<p>
		<label for="wpmusearch-title"><?php _e( 'Title:', 'wpmu-global-search' ); ?></label>
		<input class="widefat" id="wpmusearch-title" name="wpmusearch-title" type="text" value="<?php echo $title ?>" />
	</p>
    <p>
    	<label for="wpmusearch-page"><?php _e( 'Search page:', 'wpmu-global-search' ); ?></label>
    	<input class="widefat" id="wpmusearch-page" name="wpmusearch-page" type="text" value="<?php echo $page ?>" />
    </p>
    <input type="hidden" id="wpmusearch-submit" name="wpmusearch-submit" class="button" value="1" />
    
    <?php
}

/**
 * Register the Widget.
 */
add_action('widgets_init', 'widget_wp_wpmu_globalsearch_init');
function widget_wp_wpmu_globalsearch_init() {
    if ( !function_exists('register_sidebar_widget') ) return;
    // Register widget for use
    register_sidebar_widget(array('WPMU Global Search', 'widgets'), 'widget_wp_wpmu_globalsearch');
    register_widget_control(array('WPMU Global Search', 'widgets'), 'widget_wp_wpmu_globalsearch_control');
}

/**
 * Add style file if it exists.
 */
add_action('wp_print_styles', 'wp_wpmu_globalsearch_css');
function wp_wpmu_globalsearch_css() {
    $styleurl = WPMU_PLUGIN_URL."/".basename( dirname( __FILE__ ) )."/style.css";
	$styledir = WPMU_PLUGIN_DIR."/".basename( dirname( __FILE__ ) )."/style.css";
	
	if(file_exists($styledir))
		wp_enqueue_style('wp_wpmu_globalsearch_css_styles', $styleurl);
}

/**
 * Init search variable.
 */
add_filter('query_vars', 'wpmu_globalsearch_queryvars' );
function wpmu_globalsearch_queryvars( $qvars ) {
  $qvars[] = 'uws';
  return $qvars;
}

/**
 * Shortcode definition.
 */
add_shortcode('page_wpmu_search', 'wp_wpmu_globalsearch_page');
function wp_wpmu_globalsearch_page($atts) {
	global $wp_query, $wpdb;

    $term = apply_filters( 'get_search_query', get_query_var( 'uws' ));
    
    if(!empty($term)) {
       	$request = $wpdb->prepare("SELECT ".$wpdb->base_prefix."v_posts.* from ".$wpdb->base_prefix."v_posts left join ".$wpdb->base_prefix."users on ".$wpdb->base_prefix."users.ID=".$wpdb->base_prefix."v_posts.post_author ".
		"where (post_title LIKE '%%".$term."%%' OR post_content LIKE '%%".$term."%%' OR ".$wpdb->base_prefix."users.display_name LIKE '%%".$term."%%')".
        "AND (post_status = 'publish' OR post_status = 'private') AND post_type = 'post' ORDER BY ".$wpdb->base_prefix."v_posts.post_date DESC, ".$wpdb->base_prefix."v_posts.comment_count DESC");
       	
        $search = $wpdb->get_results($request);
		
		if(empty($search)) { ?>
			<h2 class='globalpage_title center'><?php echo __("Not found", 'wpmu-global-search')." <span class='globalsearch_term'>".$term."</span>."; ?></h2>
			<p class='globalpage_message center'><?php _e("Sorry, but you are looking for something that isn't here.", 'wpmu-global-search') ?></p>
		<?php
        } else { ?>
        	<p><?php echo count($search)." ".__( 'matches with', 'wpmu-global-search' )." <span class='globalsearch_term'>".$term."</span>."; ?></p>
        <?php
            $blogid = '';
            foreach($search as $s) {
                $author = get_userdata($s->post_author);
                if($blogid != $s->blog_id) {
                    $blogid = $s->blog_id; ?>
                    
                    <h2 class='globalblog_title'><?php echo get_blog_option($blogid, 'blogname') ?></h2>
                <?php
                } ?>

                <div <?php post_class('globalsearch_post') ?>>
                	<div class="globalsearch_header">
                    	<h2 id="post-<?php echo $s->ID.$s->blog_id; ?>" class="globalsearch_title"><a href="<?php echo get_blog_permalink( $s->blog_id, $s->ID ); ?>" rel="bookmark" title="<?php echo __('Permanent Link to', 'wpmu-global-search').' '.$s->post_title; ?>"><?php echo $s->post_title ?></a></h2>
                    	<p class="globalsearch_meta">
							<span class="globalsearch_comment"><?php wpmu_globalsearch_get_comments_link($s); ?></span>
							<span class="globalsearch_date"><?php echo date(__('j/m/y, G:i', 'wpmu-global-search') ,strtotime($s->post_date)); ?></span>
							<span class="globalsearch_author"><?php echo '<a href="http://' . $s->domain.$s->path.'author/'.$author->user_nicename . '" title="' . $author->user_nicename . '">' . $author->user_nicename . '</a>'; ?></span>
							<?php echo wpmu_globalsearch_get_edit_link($s, '<span class="globalsearch_edit">', '</span>'); ?>
						</p>
					</div>
					
					<div class="globalsearch_content">
                    	<div class="entry">
                        	<?php echo wpmu_globalsearch_get_the_content($s); ?>
                    	</div>
					</div>
                </div>
            <?php
            }
        }
    } else { ?>
        <h2 class='globalpage_title center'><?php _e("Not found", 'wpmu-global-search') ?></h2>
        <p class='globalpage_message center'><?php _e("Sorry, but you are looking for something that isn't here.", 'wpmu-global-search') ?></p>
    <?php
    }
}

/**
 * Builds a view that contains posts from all blogs.
 * Views are built by activate_blog, desactivate_blog, archive_blog, unarchive_blog, delete_blog and wpmu_new_blog hooks.
 */
add_action ('wpmu_new_blog', 'wpmu_globalsearch_build_views_add', 10, 1);
add_action ('delete_blog', 'wpmu_globalsearch_build_views_drop', 10, 1);
add_action ('archive_blog', 'wpmu_globalsearch_build_views_drop', 10, 1);
add_action ('unarchive_blog', 'wpmu_globalsearch_build_views_unarchive', 10, 1);
add_action ('activate_blog', 'wpmu_globalsearch_build_views_activate', 10, 1);
add_action ('deactivate_blog', 'wpmu_globalsearch_build_views_drop', 10, 1);
function wpmu_globalsearch_build_views_drop($trigger) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE blog_id != {$trigger} AND site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"));
    wpmu_globalsearch_build_v_query($blogs);
}

function wpmu_globalsearch_build_views_add($trigger) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC"));
    wpmu_globalsearch_build_v_query($blogs);
}

function wpmu_globalsearch_build_views_activate($trigger) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE (blog_id = {$trigger} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0') OR (site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0') ORDER BY registered DESC"));
    wpmu_globalsearch_build_v_query($blogs);
}

function wpmu_globalsearch_build_views_unarchive($trigger) {
    global $wpdb;

    $blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE (blog_id = {$trigger} AND public = '1' AND deleted = '0' AND mature = '0' AND spam = '0') OR (site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0') ORDER BY registered DESC"));
    wpmu_globalsearch_build_v_query($blogs);
}

function wpmu_globalsearch_build_v_query($blogs) {
    global $wp_query, $wpdb;

    $site = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->site} WHERE id='1'"));
    $posts_select_query    = " (SELECT '1' AS blog_id, '{$site->domain}' AS domain, '{$site->path}' AS path, posts{$site->id}.* FROM {$wpdb->base_prefix}{$site->id}_posts posts{$site->id}) ";
    $postmeta_select_query = " (SELECT '1' AS blog_id, '{$site->domain}' AS domain, '{$site->path}' AS path, postmeta{$site->id}.* FROM {$wpdb->base_prefix}{$site->id}_postmeta postmeta{$site->id}) ";
    $comments_select_query = " (SELECT '1' AS blog_id, '{$site->domain}' AS domain, '{$site->path}' AS path, comments{$site->id}.* FROM {$wpdb->base_prefix}{$site->id}_comments comments{$site->id}) ";
    
    foreach ($blogs as $blog) {
        $posts_select_query    .= " UNION (SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, posts{$blog->blog_id}.* FROM {$wpdb->base_prefix}{$blog->blog_id}_posts posts{$blog->blog_id}) ";
        $postmeta_select_query .= " UNION (SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, postmeta{$blog->blog_id}.* FROM {$wpdb->base_prefix}{$blog->blog_id}_postmeta postmeta{$blog->blog_id}) ";
        $comments_select_query .= " UNION (SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, comments{$blog->blog_id}.* FROM {$wpdb->base_prefix}{$blog->blog_id}_comments comments{$blog->blog_id}) ";
    }
    
    $v_query1  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_posts` AS ".$posts_select_query;
	$wpdb->query($wpdb->prepare($v_query1));
	
	$v_query2  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_postmeta` AS ".$postmeta_select_query;
	$wpdb->query($wpdb->prepare($v_query2));
	
	$v_query3  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_comments` AS ".$comments_select_query;
	$wpdb->query($wpdb->prepare($v_query3));
}

?>
