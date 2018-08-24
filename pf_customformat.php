<?php
/**
 * Created by PhpStorm.
 * User: forretp
 * Date: 24/08/2018
 * Time: 13:33
 */

class pf_customformat {

	function fmt_taxos($taxos,$format,$style_attrib=""){
		$html="";
		switch($format) {
			case "table":
				$html.="<table $style_attrib>";
				foreach($taxos as $taxo => $terms){
					$html.="<tr>";
					$termlinks=Array();
					foreach($terms as $term => $link){
						$termlinks[]="<a href='$link'>$term</a>";
					}
					$html.="<th>" . ucfirst($taxo) . ": </th>";
					$html.="<td>" . implode(" &bull; ",$termlinks) . "</td>\n";
					$html.="</tr>\n";
				}
				$html.="</table>\n";
				break;

			case "p":
				foreach($taxos as $taxo => $terms){
					$termlinks=Array();
					foreach($terms as $term => $link){
						$termlinks[]="<a href='$link'>$term</a>";
					}
					$html.="<p $style_attrib><b>" . ucfirst($taxo) . "</b>: ";
					$html.=implode(" &bull; ",$termlinks) . "</p>\n";
				}
				break;
				/*
				 *         case "bar":
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
                $html.="<div $style_attrib>";
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


				 */

			case "dl":
			default:
				$html.="<dl $style_attrib>";
				foreach($taxos as $taxo => $terms){
					$termlinks=Array();
					foreach($terms as $term => $link){
						$termlinks[]="<a href='$link'>$term</a>";
					}
					$html.="<dt>" . ucfirst($taxo) . ": </dt>";
					$html.="<dd>" . implode(" &bull; ",$termlinks) . "</dd>\n";
				}
				$html.="</dl>\n";
		}
		return $html;
	}

	function fmt_posts($posts,$format,$style_attrib=""){
		$html="";
		switch($format) {
			case "dl":
				$html .= "<dl $style_attrib>";
				foreach($posts as $post){
					$fieldno=0;
					foreach($post as $label => $value){
						$fieldno++;
						if($fieldno==1){
							$html.="\n<dt>$value</dt>";
						} else {
							$html.="<dd>$value</dd>";
						}
					}
				}
				$html .= "</dl>\n";
				break;

			case "table":
			default:
			$html .= "<table $style_attrib>";
			$html.="<tr>";
			foreach($posts[0] as $label => $value){
					$html.="<th>$label</th>";
			}
			$html.="</tr>\n";
			foreach($posts as $post){
				$html.="<tr>";
				$fieldno=0;
				foreach($post as $label => $value){
					$fieldno++;
					if($fieldno==1){
						$html.="<th>$value</th>";
					} else {
						$html.="<td>$value</td>";
					}
				}
				$html.="</tr>\n";
			}
			$html .= "</table>\n";
		}
		return $html;
	}

	function fmt_fields($fields,$format,$style_attrib=""){
		$html="";
		switch($format){
			case "dl":
				$html.="<dl $style_attrib>";
				foreach($fields as $label => $value){
					$html.="<dt>$label: </dt><dd>$value</dd>\n";
				}
				$html.="</dl>\n";
				break;

			case "ul":
				$html.="<ul $style_attrib>";
				foreach($fields as $label => $value){
					$html.="<li><b>$label</b>: $value</li>";
				}
				$html.="</ul>\n";
				break;

			case "table":
				$html.="<table $style_attrib>";
				foreach($fields as $label => $value){
					$html.="<tr><th>$label</th><td>$value</td></tr>";
				}
				$html.="</table>\n";
				break;

			case "bull":
				$html.="<p $style_attrib>";
				$blocks=Array();
				foreach($fields as $label => $value){
					$blocks[]="<b>$label</b>: $value";
				}
				$html.=implode(" &bull; ",$blocks);
				$html.="</p>\n";
				break;

			case "p":
			case "br":
			default:
				$html.="<p $style_attrib>";
			foreach($fields as $label => $value){
					$html.="<b>$label</b>: $value<br />";
				}
				$html.="</p>\n";
		}
		return $html;


	}

	function fmt_cfield($fieldobj,$img_width='300'){
		switch($fieldobj['type']){
			case "url":
				$url=$fieldobj['value'];
				$short=$this->display_url($url);
				return "<a href='$url'>$short</a>";
				break;

			case "true_false":
				$boolean=$fieldobj['value'];
				return $this->display_boolean($boolean);
				break;

			case "image":
				$url=$fieldobj['value'];
				if(is_array($url)){
					$url=$fieldobj["value"]["url"];
				}
				$bname=basename($url);
				return "<img width='$img_width' alt='$bname' title='$bname' src='$url' />";
				break;

			case "email":
				$email=$fieldobj['value'];
				return "<a href='mailto:$email'>$email</a>";
				break;

			default:
				return $fieldobj['value'];
		}
	}

	function display_url($url){
		$prefixes=Array(
			"http://",
			"https://",
			"ftp://",
			"ssh://",
			"sftp://",
		);
		$short=str_replace($prefixes,"",$url); // remove prefix
		$short=preg_replace("(\?.*$)","",$short); // remove trailing query
		$short=preg_replace("(#.*$)","",$short); // remove trailing anchor
		return $short;
	}

	function display_boolean($value){
		if($value) {
			return "<input type='checkbox' disabled checked > <b style='color:green'>Yes</b>";
		} else {
			return "<input type='checkbox' disabled ><i>No</i>";
		}
	}
}