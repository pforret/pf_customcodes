<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://blog.forret.com
 * @since             1.0.0
 * @package           pf_customcodes
 *
 * @wordpress-plugin
 * Plugin Name:       PF Custom Codes (for ACF/CPT UI)
 * Plugin URI:        https://github.com/pforret/pf_customcodes
 * Description:       Wordpress plugin for displaying Custom Fields (ACF) and Custom Types/Taxonomies (CPT UI) through shortcodes [pf_all_taxos] [pf_all_posts] [pf_post_cfields]
 * Version:           0.1.1
 * Author:            Peter Forret
 * Author URI:        http://blog.forret.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '0.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pf_customcodes-activator.php
 */
function activate_pf_customcodes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pf_customcodes-activator.php';
	Pf_customcodes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pf_customcodes-deactivator.php
 */
function deactivate_pf_customcodes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pf_customcodes-deactivator.php';
	Pf_customcodes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pf_customcodes' );
register_deactivation_hook( __FILE__, 'deactivate_pf_customcodes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pf_customcodes.php';
require plugin_dir_path( __FILE__ ) . 'pf_customformat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pf_customcodes() {

	$plugin = new Pf_customcodes();
	$plugin->run();

    add_shortcode( 'pf_all_taxos', 'pf_all_taxos' );
    add_shortcode( 'pf_all_posts', 'pf_all_posts' );
	add_shortcode( 'pf_post_cfields', 'pf_post_cfields');
	add_shortcode( 'pf_post_fields', 'pf_post_cfields'); // for backward compatibility


}

/**
 * Show all custom taxonomies and all terms within each taxonomy.
 *
 * works with custom taxonomies from CPT UI plugin
 *
 * @since    1.0.0
 */

function pf_all_taxos($atts){
	$fmt = New pf_customformat();

	$only="";
	$except="";
	$format="";
	$style="";
	$with_count="";

    extract( shortcode_atts( array(
        'only' => '',
        'except' => '',
        'format' => 'p',
        'style' => '',
        'with_count' => 0,
    ), $atts ) );

    $filter_only=false;
    if($only)   $filter_only=explode(",",$only);

    $filter_except=false;
    if($except)   $filter_except=explode(",",$except);

    $style_attrib="";
    if($style){
        $style_attrib=" style='$style'";
    }

    $args = array( 'public'   => true, '_builtin' => false );
    $output = 'objects'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );

    $taxos=Array();
    if ( $taxonomies ) {
        foreach ( $taxonomies  as $taxonomy ) {
            if($filter_only AND !in_array($taxonomy->name,$filter_only))  continue;
            if($filter_except AND in_array($taxonomy->name,$filter_except))  continue;

            $terms = get_terms( array(
                'taxonomy' => $taxonomy->name,
                'hide_empty' => true,
            ));
	        $term_links=Array();
            foreach($terms as $term){
                $term_name=$term->name;
                $term_link=get_term_link($term);
				if($with_count){
					$term_name.=" <sup>(" . $term->count . ")</sup>";
					$term_link.="#" . $term->count;
				}
                $term_links[$term_name]=$term_link;
            }
            $taxos[$taxonomy->labels->name]=$term_links;
        }
    }
    return $fmt->fmt_taxos($taxos,$format,$style_attrib);
}

function pf_all_posts( $atts) {
	$fmt = New pf_customformat();

	$type="";
	$style="";
	$format="";
	$fields="";
	$separator="";
    extract( shortcode_atts( array(
        'type' => '',
		'style'	=> '',
		'format'	=> 'list',
		'fields'	=> '',
    ), $atts ) );

	$style_attrib="";
	if($style){
		$style_attrib=" style='$style'";
	}

	$html="";
	$args = array(
		'posts_per_page'   => 1000,
		'post_type'        => $type,
		'orderby'          => 'name',
		'order'            => 'ASC',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$items = get_posts( $args );

	if($items){
		if($fields){
			$fieldnames=explode(",",$fields);
			foreach($fieldnames as $i => $fieldname){
				$fieldnames[$i]=trim($fieldname);
			}
		}
		$posts=Array();
		foreach($items as $item){
			$post=Array();
			$post_title=$item->post_title;
			$post_link=get_post_permalink($item);
			$value="<a href='$post_link'>$post_title</a>";
			$label=ucfirst($type);
			$post[$label]=$value;
			if($fieldnames){
				foreach($fieldnames as $label){
					$fielddata=get_field_object($label, $item->ID);
					if($fielddata){
						$post[$label]=$fmt->fmt_cfield($fielddata);
					} else {
						$post[$label]="";
					};
				}
			}
			$posts[]=$post;
		}
		//print_r($posts);
		return $fmt->fmt_posts($posts,$format,$style_attrib);
	}
	return false;
}
  
/**
 * Show all custom fields for this post.
 *
 * works with custom fields from ACF plugin
 *
 * @since    1.0.0
 */

function pf_post_cfields($atts){
	$fmt=New pf_customformat();

	$only="";
	$except="";
	$format="";
	$style="";
	$sort="";
	$img_width=150;
    extract( shortcode_atts( array(
        'only' => '',
        'except' => '',
        'format' => 'p',
        'style' => '',
        'sort' => 0,
        'img_width' => 150,
    ), $atts ) );

    $filter_only=false;
    if($only)   $filter_only=explode(",",$only);

    $filter_except=false;
    if($except)   $filter_except=explode(",",$except);

    $style_attrib="";
    if($style){
        $style_attrib=" style='$style'";
    }
    
    $fieldobjs=get_field_objects();
    $fields=Array();
    if( $fieldobjs ) {
        if($sort)   ksort($fieldobjs);
        foreach( $fieldobjs as $field_name => $fieldobj )
        {
            if($filter_only AND !in_array($field_name,$filter_only))  continue;
            if($filter_except AND in_array($field_name,$filter_except))  continue;
			$label=$fieldobj['label'];
            //if(!$fieldobj['value'])	continue;
	        $fields[$label]=$fmt->fmt_cfield($fieldobj,$img_width);
        }
    }
    if($fields){
	    if($sort > 0) ksort($fields);
	    if($sort < 0) krsort($fields);
        return $fmt->fmt_fields($fields,$format,$style_attrib);
    }
    return false;
}

run_pf_customcodes();
