<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

    
////////////////////////////////////////////////////////////////////////////////////////////////////


/* Load LESS  */

function childtheme_scripts() {

wp_enqueue_style('less', get_stylesheet_directory_uri() .'/css/style.less');
add_filter('style_loader_tag', 'my_style_loader_tag_function');

wp_enqueue_script('less', get_stylesheet_directory_uri() .'/scripts/less.min.js', array('jquery'),'2.5.0');

}
add_action('wp_enqueue_scripts','childtheme_scripts', 1);

function my_style_loader_tag_function($tag){   
  return preg_replace("/='stylesheet' id='less-css'/", "='stylesheet/less' id='less-css'", $tag);
}

////////////////////////////////////////////////////////////////////////////////////////////////////

/*function extra_css () {
	wp_register_script( 'custom', get_stylesheet_directory_uri() . '/scripts/wing.js' );
	wp_register_script( 'motio', get_stylesheet_directory_uri() . '/scripts/motio.min.js' );
	wp_enqueue_script( 'custom' );
	wp_enqueue_script( 'motio' );
} 

add_action('wp_print_styles', 'extra_css', 151);*/


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Remove Date from Yoast SEO */

add_filter( 'wpseo_show_date_in_snippet_preview', false);


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Remove Dates from SEO on Pages */


function wpd_remove_modified_date(){
    if( is_page() ){
        add_filter( 'the_time', '__return_false' );
        add_filter( 'the_modified_time', '__return_false' );
        add_filter( 'get_the_modified_time', '__return_false' );
        add_filter( 'the_date', '__return_false' );
        add_filter( 'the_modified_date', '__return_false' );
        add_filter( 'get_the_modified_date', '__return_false' );
    }
}
add_action( 'template_redirect', 'wpd_remove_modified_date' );


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Remove Query String  */


function _remove_script_version( $src ){
  $parsed = parse_url($src);

  if (isset($parsed['query'])) {
    parse_str($parsed['query'], $qrystr);
    if (isset($qrystr['ver'])) {
      unset($qrystr['ver']); 
    }
    $parsed['query'] = http_build_query($qrystr);
  }
  // return http_build_url($parsed); // elegant but not always available

  $src = '';
  $src .= (!empty($parsed['scheme'])) ? $parsed['scheme'].'://' : '';
  $src .= (!empty($parsed['host'])) ? $parsed['host'] : '';
  $src .= (!empty($parsed['path'])) ? $parsed['path'] : '';
  $src .= (!empty($parsed['query'])) ? '?'.$parsed['query'] : '';

  return $src;
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Add Field Visibility Section to Gravity Forms */		

		
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
return true;
}

////////////////////////////////////////////////////////////////////////////////////////////////////


/* SVG Support */	


function bodhi_svgs_disable_real_mime_check( $data, $file, $filename, $mimes ) {
    $wp_filetype = wp_check_filetype( $filename, $mimes );

    $ext = $wp_filetype['ext'];
    $type = $wp_filetype['type'];
    $proper_filename = $data['proper_filename'];

    return compact( 'ext', 'type', 'proper_filename' );
}
add_filter( 'wp_check_filetype_and_ext', 'bodhi_svgs_disable_real_mime_check', 10, 4 );


////////////////////////////////////////////////////////////////////////////////////////////////////


/* Add Category Name to Body Class */	

add_filter('body_class','add_category_to_single');
function add_category_to_single($classes, $class) {
  if (is_single() ) {
    global $post;
    foreach((get_the_category($post->ID)) as $category) {
      // add category slug to the $classes array
      $classes[] = $category->category_nicename;
    }
  }
  // return the $classes array
  return $classes;
}

////////////////////////////////////////////////////////////////////////////////////////////////////


/* Add Tags Shortcode */	


function sc_taglist(){
	
    return get_the_tag_list('',' ','');
}
add_shortcode('tags', 'sc_taglist');