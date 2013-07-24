<?php

/*

  File: fabric-extensions.php
  Purpose: Find and install all Fabric extensions

*/



$extensions_dir = 'extensions/';
$extensions_path = '' . $extensions_dir;

// Find all Fabric extensions
$extension_initializer_files = get_extensions_initalizer_files($extensions_path);

// Install all Fabric extensions
install_extensions($extension_initializer_files);



/*

  Function: get_extensions_initalizer_files
  Purpose: Return an array of paths to all Fabric extension initalizer files

*/

function get_extensions_initalizer_files() {

  global $extensions_path;
   
  $extension_initializer_files = array();

  $directory_listing = array_filter(glob($extensions_path . '*'), 'is_dir');

  foreach ($directory_listing as $key => $extension_directory)
  {
    // The extension initalizer file should have the same name as the directory it's in
    $extension_initializer_file = $extension_directory . '/' . str_replace($extensions_path,'',$extension_directory) . '.php';

    // Add extensions that have initializer files
    if (file_exists($extension_initializer_file)) {
      $extension_initializer_files[] = str_replace($extensions_path, '', $extension_initializer_file);
    }
  }
   
  return $extension_initializer_files; 
}

/*

  Function: install_extensions
  Purpose: Execute an array of extension initializer files

*/

function install_extensions($extension_initializer_files) {
  global $extensions_path;
  global $extensions_dir;

  foreach ($extension_initializer_files as $initalizer_file) {
    echo '<h1>' . $initalizer_file . '</h1>';
    require_once($extensions_dir . $initalizer_file);
    echo '<hr />';
  }
}

?>