<?php
/* 
 * Functions WPMU Global Search
 * Author: Alicia García Holgado
 */

function wpmu_globalsearch_form($page) { ?>
    <form class="globalsearch_form" method="get" action="<?php echo get_bloginfo('url').'/'.$page.'/'; ?>">
		<div>
		    <p><?php _e('Search across all blogs', 'wpmu-global-search') ?></p>
		    <input class="globalsearch_vbox" name="uws" type="text" value="" size="16" tabindex="1" />
		    <input type="submit" class="button" value="<?php _e('Search', 'wpmu-global-search')?>" tabindex="2" />
	    </div>
    </form>
<?php
}

function wpmu_globalsearch_horizontal_form($page) { ?>
    <form class="globalsearch_form" method="get" action="<?php echo get_bloginfo('url').'/'.$page.'/'; ?>">
	    <div>
		    <span><?php _e('Search across all blogs', 'wpmu-global-search') ?>&nbsp;</span>
		    <input class="globalsearch_hbox" name="uws" type="text" value="" size="16" tabindex="1" />
		    <input type="submit" class="button" value="<?php _e('Search', 'wpmu-global-search') ?>" tabindex="2" />
	    </div>
    </form>
<?php
}

function wpmu_globalsearch_get_the_content($s) {
    $content = $s->post_content;
    apply_filters('the_content', $content);

    $output = '';
    if ( post_password_required($s) ) {
		$label = 'wpmu-global-search_'.$s->blog_id.'pwbox_'.$s->ID;
        $output = '<form action="' . get_blog_option($s->blog_id, 'siteurl') . '/wp-pass.php" method="post">
        <p>' . __("This post is password protected. To view it please enter your password below:", 'wpmu-global-search') . '</p>
        <p><label for="' . $label . '">' . __("Password:", 'wpmu-global-search') . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __("Submit", 'wpmu-global-search') . '" /></p>
        </form>
        ';
        return apply_filters('the_password_form', $output);
	}

    return $content;
}

function wpmu_globalsearch_get_edit_link($s, $before = '', $after = '') {
    if ( $s->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $s->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $s->ID ) )
			return;
	}

    $context = 'display';
	switch ( $s->post_type ) :
	case 'page' :
		if ( !current_user_can( 'edit_page', $s->ID ) )
			return;
		$file = 'page';
		$var  = 'post';
		break;
	case 'attachment' :
		if ( !current_user_can( 'edit_post', $s->ID ) )
			return;
		$file = 'media';
		$var  = 'attachment_id';
		break;
	case 'revision' :
		if ( !current_user_can( 'edit_post', $s->ID ) )
			return;
		$file = 'revision';
		$var  = 'revision';
		$action = '';
		break;
	default :
		if ( !current_user_can( 'edit_post', $s->ID ) )
			return;
		$file = 'post';
		$var  = 'post';
		break;
	endswitch;

	$editlink = apply_filters( 'get_edit_post_link', 'http://'.$s->domain.$s->path.'wp-admin/'.$file.'.php?action=edit&amp;'.$var.'='.$s->ID, $s->ID, $context );
    
    $link = '<a class="post-edit-link" href="' . $editlink . '" title="' . attribute_escape( __( 'Edit post' , 'wpmu-global-search') ) . '">' . __('Edit', 'wpmu-global-search') . '</a>';
	return $before . apply_filters( 'edit_post_link', $link, $s->ID ) . $after;
}

function wpmu_globalsearch_get_comments_link($s, $css_class = '') {
    global $wpcommentsjavascript, $wpcommentspopupfile;

	$number = $s->comment_count;

	if ( 0 == $number && 'closed' == $s->comment_status && 'closed' == $s->ping_status ) {
		echo '<span' . ((!empty($css_class)) ? ' class="' . $css_class . '"' : '') . '>' . __('Comments off', 'wpmu-global-search') . '</span>';
		return;
	}

	if ( post_password_required() ) {
		echo __('Enter your password to view comments', 'wpmu-global-search');
		return;
	}

	echo '<a href="';
	if ( $wpcommentsjavascript ) {
		if ( empty( $wpcommentspopupfile ) )
			$home = get_blog_option($s->blog_id, 'home');
		else
			$home = get_blog_option($s->blog_id, 'siteurl');
		echo $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $s->ID;
		echo '" onclick="wpopen(this.href); return false"';
	} else { // if comments_popup_script() is not in the template, display simple comment link
		if ( 0 == $number )
			echo get_blog_permalink( $s->blog_id, $s->ID ) . '#respond';
		else
			echo get_blog_permalink( $s->blog_id, $s->ID ) . '#comments';
		echo '"';
	}

	if ( !empty( $css_class ) ) {
		echo ' class="'.$css_class.'" ';
	}
	$title = attribute_escape( $s->post_title );

	echo apply_filters( 'comments_popup_link_attributes', '' );

	echo ' title="' . sprintf( __('Comment on %s', 'wpmu-global-search'), $title ) . '">';
	
    if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), __('% Comments', 'wpmu-global-search'));
	elseif ( $number == 0 )
		$output = __('No Comments', 'wpmu-global-search');
	else // must be one
		$output = __('1 Comment', 'wpmu-global-search');

	echo apply_filters('comments_number', $output, $number);
	echo '</a>';
}
?>
