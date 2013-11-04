<?php
/**
 * Content Creation
 */

// Add Admin Menu Page
add_action('admin_menu', 'fabric_content_menu');
function fabric_content_menu() {
	add_menu_page('Content Creator', 'Content Creator', 'publish_posts', 'fabric-content-creator', 'fabric_content_creation_page');
}
function fabric_content_creation_page() {
	$message = null;

	//$_POST['fabric-action'] = "textarea-content-paste";
	//$_POST['fabric-content-paste'] = '<ul><li>Portfolio</li><li>Contact</li><li>Agency<ul><li>Team<ul><li>Wordpress<ul><li>Backend<ul><li>Adrian</li><li>Matt</li><li>Chris S</li><li>Aaron</li><li>Kate</li></ul></li><li>Front End<ul><li>Chris C</li></ul></li></ul></li><li>Ruby<ul><li>Will</li><li>Nathan</li></ul></li><li>Management<ul><li>Andy</li><li>Gina</li><li>Dan</li><li>Taylor</li></ul></li></ul></li></ul></li></ul>';

	//echo "<pre>".print_r($data,true)."</pre>";

	//return;

	// Handle POST
	if(isset($_POST['fabric-action']) && $_POST['fabric-action'] == "textarea-content-paste"){
		$htmlcontent = $_POST['fabric-content-paste'];

		if(strlen($htmlcontent)){
			// Replace ol with ul, kinda hacky, might fix it in the dom later
			$htmlcontent = str_replace(array("<ol>","</ol>"),array("<ul>","</ul>"),$htmlcontent);

			// Load DOM
			$content = new DOMDocument();
			$content->preserveWhiteSpace = FALSE;
			$content->loadHTML($htmlcontent);

			// Find root element
			$root = $content->documentElement;

			$collection = array();


			// Loop through each UL in the document
			foreach ($root->getElementsByTagName('body')->item(0)->childNodes AS $node)
			{
				// Handle lists
				handle_ul($node, $collection);
			}

			$message = "success";
			//echo "<h1>Collection</h1>";
			//echo "<pre>".print_r($collection,true)."</pre>";

			// Save to the DB
			update_option("fabric_content_map", json_encode($collection) );
		}
	}

	$existing_content_map = get_option("fabric_content_map");

	if($existing_content_map){
		wp_enqueue_style("content_map",get_template_directory_uri()."/functions/includes/assets/css/content_map.css");
		include "includes/content-creation/map_editor.php";
	} else {
		include "includes/content-creation/main.php";
	}
}

function handle_ul($ul,&$collection){
	if($ul->nodeName == "ul"){
		foreach($ul->childNodes as $li){
			if($li->nodeName == "li"){
				$subul = $li->getElementsByTagName("ul")->item(0);
				if($subul){
					$li->removeChild($subul);
				}

				//echo $li->nodeValue."\n";

				$new_page = (object)array(
					"name" => $li->nodeValue,
					"type" => "page"
				);

				if($subul){
					handle_ul($subul, $new_page->children);
				}

				$collection[] = $new_page;
			}
		}
	}
}


function handle_ul1($ul,$parent_id = null){
	if($ul->nodeName == "ul"){
		foreach($ul->childNodes as $li){
			if($li->nodeName == "li"){
				$subul = $li->getElementsByTagName("ul")->item(0);
				if($subul){
					$li->removeChild($subul);
				}

				//echo $level.$li->nodeValue."\n";

				$new_id = wp_insert_post(array(
							'post_type' => 'page',
							'post_title' => $li->nodeValue,
							'post_parent' => $parent_id,
							'post_status' => 'publish',
							'post_content' => $li->nodeValue." Content Goes here",
						));

				if($subul){
					handle_ul($subul, $new_id);
				}
			}
		}
	}
}

function output_map($content_map){
	echo '<ul>';
	foreach($content_map as $page){
		echo '<li class="node type-'.$page->type.'">';
			echo '<span class="node-title">'.$page->name."</span>";
			if(isset($page->children)){
				output_map($page->children);
			}
		echo "</li>";
	}
	echo "</ul>";
}