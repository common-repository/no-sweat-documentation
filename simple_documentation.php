<?php

/*
 Plugin Name: No Sweat Documentation
 Description: A plugin that allows you to create documentation (no sweat) on your WordPress Powered Website.
 Version: 1.0
 Author: Nikhil Vimal
 Author URI: http://nik.techvoltz.com/
 License: GPL2
*/

/*  Copyright 2014  Nikhil Vimal  (email : techvoltz@gmail.com)

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


	//Registers the Documentation Custom Post Type
	function register_no_sweat_docs_cpt() {
	
		
	
		$args = array(
			'labels'                   => array(
			'name'                => ( 'Documentation'),
			'singular_name'       => ( 'Documentation'),
			'add_new'             => ( 'Add New Doc'),
			'add_new_item'        => ( 'Add New Doc'),
			'edit_item'           => ( 'Edit Documentation'),
			'new_item'            => ( 'New Documentation'),
			'view_item'           => ( 'View Documentation'),
			'search_items'        => ( 'Search Docs' ),
			'not_found'           => ( 'No Docs found'),
			'not_found_in_trash'  => ( 'No Docs found in Trash'),
			'parent_item_colon'   => ( 'Parent Documentation:'),
			'menu_name'           => ( 'Documentation'),
			),

			'hierarchical'        => true,
			'description'         => 'Documentation Custom Post Type',
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capabilities'        =>array(
		    'edit_post' => 'edit_no_sweat_doc',
         	'edit_posts' => 'edit_no_sweat_docs',
        	'edit_others_posts' => 'edit_others_no_sweat_docs',
        	'publish_posts' => 'publish_no_sweat_docs',
        	'read_post' => 'read_no_sweat_doc',
        	'read_private_posts' => 'read_private_no_sweat_docs',
        	'delete_post' => 'delete_no_sweat_doc'
        	),
			'supports'            => array(
               'title', 'editor', 'author', 'page-attributes',

				),  
			
			

		);
	
		register_post_type( 'no_sweat_docs_cpt', $args );
	}
	
	add_action( 'init', 'register_no_sweat_docs_cpt' );


    //Modifys the "Enter Title Here" for the Documentation Custom Post Type
	function no_sweat_docs_enter_title( $title ) {
		$screen = get_current_screen();

		if ('no_sweat_docs_cpt' == $screen->post_type) {
			$title = 'Enter Document Title Here';

		}

		return $title;

	}

add_filter('enter_title_here', 'no_sweat_docs_enter_title');


    //Configures the shortcode [all_docs]
	function no_sweat_docs_child_docs_shortcode($atts) {
		extract(shortcode_atts( array(
			'post_parent' => false,
			'title' => '',
 
			),
		$atts ));

		if( ! $post_parent) {
			global $post;
			if(is_object($post)) {
				$post_parent = $post->ID;
			}
			else {
				return false;
			}
		}

		$args = array(
			'depth' => 3,
			'child_of' => $post_parent,
			'title_li' => $title,
			'echo' => 0,
			'post_type' => 'no_sweat_docs_cpt',
			);

		if(empty( $title )) {
			return "<ul>" . wp_list_pages( $args ) . "</ul>";
		   

		}
		else {
			return wp_list_pages( $args );
		}
		

	}

add_shortcode('all_docs', 'no_sweat_docs_child_docs_shortcode' );


//Changes the Publish button in the documentation post type
function no_sweat_docs_pulish_button( $translation, $text ) {
	if( 'no_sweat_docs_cpt' == get_post_type()) {

	
	if( $text == 'Publish') {
		return 'Publish Doc';
	}
	}
	return $translation;


}

add_filter('gettext', 'no_sweat_docs_pulish_button', 10, 2);


//Creates the Documentation Master Role
function no_sweat_docs_doc_master_role() {
	add_role('doc_master', 'Documentation Master', array(
		'read' => true,
        'edit_posts' => true,
        'upload_files' => true,
        )

	);


}

add_action('init', 'no_sweat_docs_doc_master_role');

//Adds Documentation Capabilities to the Documentation Master Role
function add_doc_master_capabilities() {
	$role = get_role('doc_master');

	$role->add_cap( 'edit_no_sweat_doc' ); 
    $role->add_cap( 'edit_no_sweat_docs' ); 
    $role->add_cap( 'edit_others_no_sweat_docs' ); 
    $role->add_cap( 'publish_no_sweat_docs' );
    $role->add_cap('read_no_sweat_doc'); 
    $role->add_cap('read_private_no_sweat_docs'); 
    $role->add_cap('delete_no_sweat_doc'); 
}

add_action('init', 'add_doc_master_capabilities');

//Adds Documentation Capabilities to the admin User Role
function add_doc_role_capabilities_admin() {
	$role = get_role('administrator');

	$role->add_cap( 'edit_no_sweat_doc' ); 
    $role->add_cap( 'edit_no_sweat_docs' ); 
    $role->add_cap( 'edit_others_no_sweat_docs' ); 
    $role->add_cap( 'publish_no_sweat_docs' );
    $role->add_cap('read_no_sweat_doc'); 
    $role->add_cap('read_private_no_sweat_docs'); 
    $role->add_cap('delete_no_sweat_doc'); 
}

add_action('init', 'add_doc_role_capabilities_admin');

//Deactivation Hook that removes the Documentation User Role
function no_sweat_docs_deactivate() {
	remove_role('doc_master');

}

register_deactivation_hook( __FILE__, 'no_sweat_docs_deactivate' );

