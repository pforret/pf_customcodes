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
 * @package           Pf_customcodes
 *
 * @wordpress-plugin
 * Plugin Name:       PF Custom Codes (for ACF/CPT UI)
 * Plugin URI:        https://github.com/pforret/pf_customcodes
 * Description:       Wordpress plugin for working with/displaying Custom Fields (ACF) and Custom Types/Taxonomies (CPT UI)
 * Version:           0.1.0
 * Author:            Peter Forret
 * Author URI:        http://blog.forret.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pf_customcodes
 * Domain Path:       /languages
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
    add_shortcode( 'pf_post_cfields', 'pf_post_cfields');


}

/**
 * Show all custom taxonomies and all terms within each taxonomy.
 *
 * works with custom taxonomies from CPT UI plugin
 *
 * @since    1.0.0
 */

function pf_all_taxos($atts){
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

    $styleattrib="";
    if($style){
        $styleattrib=" style='$style'";
    }

    $args = array(
        'public'   => true,
        '_builtin' => false
    );
    $output = 'names'; // or objects
    $operator = 'and'; // 'and' or 'or'
    $taxonomies = get_taxonomies( $args, $output, $operator );
    $links=Array();
    $total=Array();
    if ( $taxonomies ) {
        foreach ( $taxonomies  as $taxonomy ) {
            if($filter_only AND !in_array($taxonomy,$filter_only))  continue;
            if($filter_except AND in_array($taxonomy,$filter_except))  continue;
            $links[$taxonomy]=Array();
            $total[$taxonomy]=0;
            $terms = get_terms( array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));
            $termlist=Array();
            foreach($terms as $term){
                $term_name=$term->name;
                $term_link=get_term_link($term);
				$count="";
				if($with_count){
					$count=" <sup>(" . $term->count . ")</sup>";
				}
				$total[$taxonomy]+=$term->count;
                $links[$taxonomy][]="<a href='$term_link'>$term_name</a>$count";
            }
        }
    }
    $html="";
    switch($format){
        case "dl":
<<<<<<< HEAD
			$html.="<dl $styleattrib>";
					foreach($links as $taxonomy => $links2){
						$html.="<dt>" . ucfirst($taxonomy) . ": </dt><dd>";
						$html.="<dd>" . implode(" &bull; ",$links2) . "</dd>\n";
					}
			$html.="</dl>";
=======
	        $html.="<dl $styleattrib>";
            foreach($links as $taxonomy => $links2){
                $html.="<dt>" . ucfirst($taxonomy) . ": </dt><dd>";
		        $html.="<dd>" . implode(" &bull; ",$links2) . "</dd>\n";
            }
	        $html.="</dl>";
>>>>>>> 6ea2ce447eb9981a12f0d22bbbbab96b368f8cc2
            break;

        case "table":
            foreach($links as $taxonomy => $links2){
                $html.="<table $styleattrib>";
                $html.="<tr><th>" . ucfirst($taxonomy) . ": </th></tr>";
                foreach($links2 as $link){
                    $html.="<tr><td>$link</td></tr>";
                }
                $html.="</table>";
            }
            break;

        case "bar":
			$ColourValues = Array( 
				"FF8888", "88FF88", "8888FF", "FFFF88", "FF88FF", "88FFFF",  
				"808888", "888088", "888880", "808088", "800080", "008080", "808080", 
				"C00000", "00C000", "0000C0", "C0C000", "C000C0", "00C0C0", "C0C0C0", 
				"400000", "004000", "000040", "404000", "400040", "004040", "404040", 
				"200000", "002000", "000020", "202000", "200020", "002020", "202020", 
				"600000", "006000", "000060", "606000", "600060", "006060", "606060", 
				"A00000", "00A000", "0000A0", "A0A000", "A000A0", "00A0A0", "A0A0A0", 
				"E00000", "00E000", "0000E0", "E0E000", "E000E0", "00E0E0", "E0E0E0", 
				);

            foreach($links as $taxonomy => $links2){
                $html.="<div $styleattrib>";
				$legend="<b>" . ucfirst($taxonomy) . ": </b>";
				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'hide_empty' => true,
				));
				$i=0;
				foreach($terms as $term){
					$term_name=$term->name;
					$term_link=get_term_link($term);
					$width=round(500*$term->count/$total[$taxonomy]);
					$color=$ColourValues[$i];
					$percent="";
					if($with_count){
						$percent=round(100*$term->count/$total[$taxonomy])."%";
					}

                    $html.="<div style='float: left; height: 20px; width: ${width}px; background: #$color ; font-size: .75em; text-align: center'>$percent</div>\n";
					$legend.="<span style='background: #$color'>&nbsp;&nbsp;&nbsp;</span> <a href='$term_link'>$term_name</a> &bull; ";
					$i++;
				}
                $html.="<div style='clear: both'></div></div>";
				$html.="<p>$legend</p>";
            }
            break;

        case "p":
        default:
            foreach($links as $taxonomy => $links2){
                $html.="<p $styleattrib>";
                $html.="<b>" . ucfirst($taxonomy) . "</b>: ";
                $html.=implode(" &bull; ",$links2);
                $html.="</p>";
            }
    }
    return $html;
}

/**
 * Show all custom fields for this post.
 *
 * works with custom fields from ACF plugin
 *
 * @since    1.0.0
 */

function pf_post_cfields($atts){
    extract( shortcode_atts( array(
        'only' => '',
        'except' => '',
        'format' => 'p',
        'style' => '',
        'sort' => 0,
        'img_width' => 250,
    ), $atts ) );

    $filter_only=false;
    if($only)   $filter_only=explode(",",$only);

    $filter_except=false;
    if($except)   $filter_except=explode(",",$except);

    $styleattrib="";
    if($style){
        $styleattrib=" style='$style'";
    }
    
    $fieldobjs=get_field_objects();
    $fields=Array();
    if( $fieldobjs ) {
        if($sort)   ksort($fieldobjs);
        foreach( $fieldobjs as $field_name => $fieldobj )
        {
            if($filter_only AND !in_array($field_name,$filter_only))  continue;
            if($filter_except AND in_array($field_name,$filter_except))  continue;

            switch($fieldobj['type']){
                case "url":
                    if(!$fieldobj['value'])	continue;
                    $link_url=$fieldobj['value'];
                    $link_short=str_replace(Array("http://","https://"),"",$link_url);
                    $link_short=preg_replace("(\?.*$)","",$link_short);
                    $fields[]=Array(
                        "label" => $fieldobj['label'],
                        "value" => "<a href='$link_url'>$link_short</a>",
                        "type" => $fieldobj['type'],
                    );
                    break;

                case "true_false":
                    if($fieldobj['value']==1){
                        $boolean="<b style='color:green'>Yes</b>";
                    } else {
                        $boolean="<i>No</i>";
                    }
                    $fields[]=Array(
                        "label" => $fieldobj['label'],
                        "value" => $boolean,
                        "type" => $fieldobj['type'],
                    );
                    break;

                case "image":
                    if(!$fieldobj['value'])	continue;
                    $fields[]= [
                        "label" => $fieldobj['label'],
                        "value" => "<img width='$img_width' src='" . $fieldobj['value'] . "' />",
                        "type" => $fieldobj['type'],
                    ];
                    break;

                default:
                    if(!$fieldobj['value'])	continue;
                    $fields[]=Array(
                        "label" => $fieldobj['label'],
                        "value" => $fieldobj['value'],
                        "type" => $fieldobj['type'],
                    );
            }
        }
    }
    if($fields){
        $html="";
        switch($format){
            case "dl":
                $html.="<dl $styleattrib>";
                foreach($fields as $field){
                    $html.="<dt>" . $field["label"] . ": </dt>";
                    $html.="<dd>" . $field["value"] . "</dd>\n";
                }
                $html.="</dl>\n";
                break;

            case "table":
                $html.="<table $styleattrib>";
                foreach($fields as $field){
                    $html.="<tr><th>" . $field["label"] . "</th><td>" . $field["value"] . "</td></tr>";
                }
                $html.="</table>";
                break;

            case "p":
            default:
                foreach($fields as $field){
                    $html.="<p $styleattrib>";
                    $html.="<b>" . $field["label"] . "</b>: " . $field["value"];
                    $html.="</p>";
                }
        }
        return $html;
    } else {
        return "";
    }
}

run_pf_customcodes();
