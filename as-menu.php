<?php

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Plugin Name:       AS-Menu
 * Plugin URI:        https://github.com/aloisiosmelo/as-menu
 * Description:       Simple drop-down menu plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Aloisio Soares
 * Author URI:       https://github.com/aloisiosmelo
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       as-banner
 * Domain Path:       /languages
 */

class ASMenu extends WP_Widget {

    function __construct() {
        $widget_ops = array( 'description' => __('This widget creates a drop-down menu.') );
        parent::__construct( 'custom_menu_widget-1', __('AS Menu'), $widget_ops );
    }

    function widget($args, $instance) {

        if(! empty($instance['nav_menu'])){
            $nav_menu = wp_get_nav_menu_object($instance['nav_menu']);
        } else {
            return;
        }

        $instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        echo $args['before_widget'];

        if ( !empty($instance['title']) ) echo $args['before_title'] . $instance['title'] . $args['after_title'];

        // Menu Load with custom css class
        wp_nav_menu(
            [
                'menu' => $nav_menu,
                'theme_location' => 'primary-as',
                'menu_class'     => 'primary-as-menu',
            ]
        );

        echo $args['after_widget'];
    }

    function update( $new_instance, $old_instance ) {
        $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
        $instance['nav_menu'] = (int) $new_instance['nav_menu'];
        return $instance;
    }

    function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

        if ( !$menus ) {
            echo '<p>'. sprintf( __('No menu is found. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
            return;
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
            <select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
                <?php
                foreach ( $menus as $menu ) {
                    $selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
                    echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }
}

add_action( 'widgets_init', 'asmenu_register_widgets' );
function asmenu_register_widgets() {
    register_widget( 'ASMenu' );

    // CSS and JS
    wp_register_style('asmenu_namespace', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('asmenu_namespace');
    wp_register_script( 'asmenu_namespace', plugins_url('script.js',__FILE__ ));
    wp_enqueue_script('asmenu_namespace');
}