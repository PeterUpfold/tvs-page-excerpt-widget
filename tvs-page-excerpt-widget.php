<?php
/*
Plugin Name: Test Valley School Page Excerpt Widget
Plugin URI: https://www.testvalley.hants.sch.uk/
Description: Allows a configurable page excerpt to be displayed (with a read more link) as a widget.
Version: 1.0
Author: Mr P Upfold
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
/* Copyright (C) 2016-2020 Test Valley School.
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License version 2
    as published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class TVS_PageExcerptWidget extends WP_Widget {

  public function __construct() {
    parent::__construct(
      'tvs_page_excerpt_widget',
      'Test Valley School Page Excerpt Widget',
      array( 'description' => 'Allows a configurable page excerpt to be displayed (with a read more link) as a widget. e.g. Headteacher\'s welcome' )
    );


    add_action('in_admin_header', function() {
	    // load the find posts div for the purpose of displaying the post selector modal dialogue
	    if ( strpos( $_SERVER['SCRIPT_NAME'], 'widgets.php' ) !== false ) {
		    find_posts_div();
	    }
    });

    add_action( 'admin_enqueue_scripts', function() {
      wp_enqueue_media();
      wp_enqueue_script( 'media' );
      wp_enqueue_script( 'media-upload' );
      wp_enqueue_script( 'media-views' );
      wp_enqueue_script( 'media-editor' );
      wp_enqueue_script( 'media-grid' );

      wp_register_script( 'tvs-page-excerpt-widget',
        plugins_url( 'js/widget-admin.js', __FILE__ ),
        array( 'jquery', 'media', 'wp-ajax-response' ),
        date('Y-m-d-H-i-s', @filemtime( plugin_dir_path( __FILE__ ) . '/js/widget-admin.js' ) ),
        true
      );
      wp_enqueue_script( 'tvs-page-excerpt-widget' );
    });

  }

  public function widget( $args, $instance ) {

    echo $args['before_widget'];

    $excerpt = get_the_excerpt( $instance['post-id'] );

    $excerpt = strip_tags( $excerpt, '<strong><em><br><p>' ); 
    $excerpt = nl2br( $excerpt );
    
    ?><h4 class="widgettitle"><?php echo esc_html( $instance['title'] ); ?></h4>

    <div class="textwidget">
      <div style="float:right; max-width: 200px; text-align:center; margin: 0 15px;">
        <a href="<?php echo get_permalink( $instance['post-id'] ); ?>"><?php echo get_the_post_thumbnail( $instance['post-id'], 'tvs-thumb-200' ); ?></a>
      </div>
      <div>
        <?php echo $excerpt; ?>
      </div>
      <div class="clearfix"></div>
      <p class="read-more">
        <a href="<?php echo get_permalink( $instance['post-id'] ); ?>">Read more ▶▶</a>
      </p>
    </div>


    <?php

    echo $args['after_widget']; 
  }

  public function form( $instance ) {

    $title = !empty( $instance['title'] ) ? $instance['title'] : 'Page Excerpt Widget';
    $post_id = (
      array_key_exists( 'post-id', $instance) && 
      is_numeric( $instance['post-id'] ) 
     ) ? $instance['post-id'] : '0';

    $post_name = array_key_exists('post-name', $instance ) ? $instance['post-name'] : '[not set]';

    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>">
        <?php _e( 'Title' ); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
       <label for="post-name">
	<?php _e('Post/Page', 'tvs-page-excerpt-widget'); ?>
       </label>
       <input class="widefat" id="<?php echo $this->get_field_id( 'post-name' ); ?>" name="<?php echo $this->get_field_name( 'post-name' ); ?>" type="text" value="<?php echo esc_attr( $post_name ); ?>" disabled>
       <button class="button tvs-page-excerpt-find-post" data-widget-title="<?php echo esc_attr( $title ); ?>" data-post-id-field="<?php echo $this->get_field_id( 'post-id' ); ?>" data-post-name-field="<?php echo $this->get_field_id( 'post-name' ); ?>"><?php _e( 'Find post' ); ?></button>
    </p>
    <p>
      <label for="post-id">
	<?php _e('(Fallback) Post ID', 'tvs-page-excerpt-widget'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'post-id' ); ?>" name="<?php echo $this->get_field_name( 'post-id' ); ?>" type="text" value="<?php echo esc_attr( $post_id ); ?>">
    </p>
    <?php
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();

    $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    if ( is_numeric( $new_instance['post-id'] ) ) {
      $instance['post-id'] = intval( $new_instance['post-id'] );
    }

    return $instance;
  }

};

if ( function_exists( 'add_action' ) ) {
  add_action( 'widgets_init', function() {
    register_widget( 'TVS_PageExcerptWidget' );
  });
}
else {
  header( 'HTTP/1.0 403 Forbidden' );
  die( 'This page is not meant to be accessed directly.' );
}
