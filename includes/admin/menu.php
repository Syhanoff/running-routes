<?php
// Регистрация Custom Post Type "Running Route"
function rr_register_route_post_type()
{
  $labels = array(
    'name'               => _x('Running Routes', 'post type general name', 'running-routes'),
    'singular_name'      => _x('Running Route', 'post type singular name', 'running-routes'),
    'menu_name'          => _x('Running Routes', 'admin menu', 'running-routes'),
    'name_admin_bar'     => _x('Running Route', 'add new on admin bar', 'running-routes'),
    'add_new'            => _x('Add New', 'running route', 'running-routes'),
    'add_new_item'       => __('Add New Running Route', 'running-routes'),
    'new_item'           => __('New Running Route', 'running-routes'),
    'edit_item'          => __('Edit Running Route', 'running-routes'),
    'view_item'          => __('View Running Route', 'running-routes'),
    'all_items'          => __('All Running Routes', 'running-routes'),
    'search_items'       => __('Search Running Routes', 'running-routes'),
    'parent_item_colon'  => __('Parent Running Routes:', 'running-routes'),
    'not_found'          => __('No running routes found.', 'running-routes'),
    'not_found_in_trash' => __('No running routes found in Trash.', 'running-routes')
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'running-route'),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array('title', 'editor', 'thumbnail'),
    'menu_icon'          => 'dashicons-location'
  );

  register_post_type('running_route', $args);
}
add_action('init', 'rr_register_route_post_type');
