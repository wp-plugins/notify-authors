<?php
/*
Plugin Name: Notify blog authors
Plugin URI: http://www.1pixelout.net/code/author-notify-wordpress-plugin/
Description: Notifies all blog authors when a new post is published.
Version: 1.0
Author: Martin Laine
Author URI: http://www.1pixelout.net
*/

/*  Copyright 2005  Martin Laine  (email : martin@1pixelout.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function notify_users() {
	global $wpdb, $post_ID;
	
	$users = $wpdb->get_results( "SELECT ID FROM $wpdb->users WHERE user_level > 0" );
	$post = $wpdb->get_row( "SELECT post_author, post_title FROM $wpdb->posts WHERE ID = " . $post_ID );
	$authordata = get_userdata( $post->post_author );

	$blogname = get_settings( "blogname" );
	$subject = "New or updated content by " . $authordata->user_nickname;
	
	$text = $authordata->user_nickname . " has posted new content or updated content on the website.";
	$text = $text . "\n\nNew or updated article: " . $post->post_title;
	$text = $text . "\n\nRead it here: " . get_permalink( $post_ID );

	foreach ($users as $user) {
		if($user->ID != $post->post_author) {
			$user_data = get_userdata( $user->ID );
			
			mail( $user_data->user_email,
				  $subject,
				  $text,
				  "From: " . $blogname . " <" . get_settings( "admin_email" ) . ">" );
		}
	}
}

add_action('publish_post', 'notify_users');
?>