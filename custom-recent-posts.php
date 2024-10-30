<?php  
/*
 * Plugin Name: Custom Recent Posts
 * Plugin URI:  https://wordpress.org/plugins/custom-recent-posts
 * Description: Add a widget that display the Recent post with small thumbnail.
 * Version: 1.0.1
 * Author: AxisThemes
 * Author URI:  http://axistheme.com/
 * License: GPL2 

    Copyright 2014  Md. Bayzid Bostame

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation. 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details. 

*/

// Register Custom Recent Posts widget
add_action( 'widgets_init', 'init_AT_recent_posts' );
function init_AT_recent_posts() { return register_widget('AT_recent_posts'); }

class AT_recent_posts extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'AT_recent_posts',		// Base ID
			'Custom Recent Posts',		// Name
			array(
				'classname'		=>	'AT_recent_posts',
				'description'	=>	__('A widget that display the Recent post with thumbnail.', 'framework')
			)
		);
		$this->register_at_scripts();

	} // end constructor
		function register_at_scripts(){
			wp_enqueue_style( 'custom-recent-posts', plugins_url( 'custom-recent-posts.css' , __FILE__ ) );
		}
	/**
	* This is our Widget
	**/
	function widget( $args, $instance ) {
		global $post;
		extract($args);
		 
		// Widget options
		$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
		$AT 	 = $instance['types']; // Post type(s) 		
	    $types   = explode(',', $AT); // Let's turn this into an array we can work with.
		$number	 = $instance['number']; // Number of posts to show
		
        // Output
		echo $before_widget;
		
	    if ( $title ) echo $before_title . $title . $after_title;
			
		$atq = new WP_Query(array( 'post_type' => $types, 'showposts' => $number ));
		if( $atq->have_posts() ) : 
		?>
		<ul>
		<?php while($atq->have_posts()) : $atq->the_post(); ?>
        <figure class="alignleft small-thum">
			<?php if ((function_exists('has_post_thumbnail')) && (has_post_thumbnail())) { ?>
        	<?php the_post_thumbnail('post-thumbnails'); } ?>
        </figure>
		<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
        <span><i class="fa fa-clock-o"></i> <?php if ( get_the_time('M') ) the_time('M'); else the_ID(); ?> <?php if ( get_the_time('d') ) the_time('d'); else the_ID(); ?>, <?php if ( get_the_time('Y') ) the_time('Y'); else the_ID(); ?></span>
        <div class="clearfix"></div>
		<?php wp_reset_query(); 
		endwhile; ?>
		</ul>
			
		<?php endif; ?>			
		<?php
		// echo widget closing tag
		echo $after_widget;
	}

	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;
		
		//Let's turn that array into something the Wordpress database can store
		$types       = implode(',', (array)$new_instance['types']);

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['types']  = $types;
		$instance['number'] = strip_tags( $new_instance['number'] );
		return $instance;
	}
	
	/**
	* Widget settings
	**/
	function form( $instance ) {	
	
		    // instance exist? if not set defaults
		    if ( $instance ) {
				$title  = $instance['title'];
		        $types  = $instance['types'];
		        $number = $instance['number'];
		    } else {
			    //Defaults value
				$title  = '';
		        $types  = 'post';
		        $number = '5';
		    }
			
			//Let's turn $types into an array
			$types = explode(',', $types);
			
			//Count number of post types for select box sizing
			$at_types = get_post_types( array( 'public' => true ), 'names' );
			foreach ($at_types as $AT ) {
			   $at_ar[] = $AT;
			}
			$i = count($at_ar);
			if($i > 10) { $i = 10;}

			// The widget form
			?>
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('types'); ?>"><?php echo __( 'Select post type(s):' ); ?></label>
			<select name="<?php echo $this->get_field_name('types'); ?>[]" id="<?php echo $this->get_field_id('types'); ?>" class="widefat" style="height: auto;" size="<?php echo $i ?>" multiple>
				<?php 
				$args = array( 'public' => true );
				$post_types = get_post_types( $args, 'names' );
				foreach ($post_types as $post_type ) { ?>
					<option value="<?php echo $post_type; ?>" <?php if( in_array($post_type, $types)) { echo 'selected="selected"'; } ?>><?php echo $post_type;?></option>
				<?php }	?>
			</select>
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __( 'Number of posts to show:' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
			</p>
	<?php 
	}

} 

?>
