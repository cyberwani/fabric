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

			// Loop through each UL in the document
			foreach ($root->getElementsByTagName('body')->item(0)->childNodes AS $node)
			{
				// Handle lists
				handle_ul($node);
			}

			$message = "success";
		}
	}

	include "includes/content-creation/main.php";
}

function handle_ul($ul,$parent_id = null){
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