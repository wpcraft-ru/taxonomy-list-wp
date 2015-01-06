<?php
/*
Plugin Name: MZ Taxonomy
Description: Taxonomy widget and shorcode. Default taxonomy is category.
Version: 0.1
Author: Maksim Zinchenko
Author URI: https://www.facebook.com/maksim.zinchenko.1
Domain Path: /languages
Text Domain: mz-taxonomy
*/

// Shortcode area begin
function mz_list_taxonomy_cp_shortcode($atts) {
	// get name from $atts (category by default) and echo list of taxonomy
	$name_att = shortcode_atts( array(
                'name' => 'category'
                ), $atts );
	$args = array(
            'taxonomy'=> $name_att['name'],
            'title_li'     => ''
  	);
    echo '<ul>';
	wp_list_categories( $args );
    echo '</ul>';
}
 
function mz_taxonomy_register_shortcode() {
	// shortcode list_taxonomy_cp handler
    add_shortcode( 'list_taxonomy_cp', 'mz_list_taxonomy_cp_shortcode' );
}

// hook shortcode handler to init action 
add_action( 'init', 'mz_taxonomy_register_shortcode' );

// Shortcode area end

// Widget begin

class mz_Taxonomy_Widget extends WP_Widget {
	function mz_Taxonomy_Widget() {
		// Instantiate the parent object
		$widget_options = array(
							'classname'=> 'mz_taxonomy_widget_classname',
							'description'=> __('A simple widget to show selected taxonomy.','mz-taxonomy'),
							);
		$this->WP_Widget('mz_Taxonomy_Widget', __('MZ Taxonomy Widget','mz-taxonomy'), $widget_options);
	}

	function widget( $args, $instance ) {
		// Widget output
		extract($args);
		echo $before_widget;
		$title = apply_filters('widget_title', $instance['title']);
		$selected_taxonomy = empty($instance['selected_taxonomy']) ? '' : $instance['selected_taxonomy'];
		
		if(!empty($title)){ echo $before_title . $title . $after_title; }
		echo '<ul>';
		wp_list_categories('title_li=&taxonomy='.$selected_taxonomy );
		echo '</ul>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		$instance = $old_instance;
		$instance["title"] = strip_tags($new_instance["title"]);
		$instance["selected_taxonomy"] = $new_instance["selected_taxonomy"];
		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
		$default_settings = array(
							'title' => __('Selected Taxonomy','mz-taxonomy'),
							'selected_taxonomy'=>'category',
        );
		$instance = wp_parse_args(
					(array) $instance,
					$default_settings
		);
		$title = $instance['title'];
		$selected_taxonomy = $instance['selected_taxonomy'];
		echo '<label for="'. $this->get_field_id("title") .'">'. __('Title','mz-taxonomy') .'</label><br>';
		echo '<input id="'. $this->get_field_id("title") .'" type="text" name="' .$this->get_field_name('title') . '" value="' . esc_attr($title) . '"><br>';
		echo '<label for="'. $this->get_field_id("selected_taxonomy") . '">'. __('Taxonomy','mz-taxonomy') .'</label><br>';
		echo '<input id="'. $this->get_field_id("selected_taxonomy") . '" type="text" name="' .$this->get_field_name('selected_taxonomy') . '" value="' . esc_attr($selected_taxonomy) . '"><br>';
	}
}

function mz_myplugin_register_widgets() {
	// register widget
	register_widget( 'mz_Taxonomy_Widget' );
}

// hook widget register function to widget_init action
add_action( 'widgets_init', 'mz_myplugin_register_widgets' );

// Widget end

// load plugin translation

function mz-taxonomy_load_plugin_textdomain() {
    load_plugin_textdomain( 'mz-taxonomy', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'mz-taxonomy_load_plugin_textdomain' );
?>
