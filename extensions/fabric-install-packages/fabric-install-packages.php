<?php

/*

  File: /extensions/fabric-install-packages
  Purpose: Load a package configuration file and install the selected plugins

*/

include_once 'spyc.php';

$packages = Spyc::YAMLLoad('extensions/fabric-install-packages/packages.yml');

while ($plugins = current($packages)) {
  echo '<h3>Package: ' . key($packages) . '</h3>';
  echo '<ol>';

  while ($plugin = current($plugins)) {
    echo '<li>' . key($plugins);
      echo '<ul>';
        echo '<li>' . $plugin['url'] . '</li>';
        echo '<li>' . $plugin['repo'] . '</li>';
      echo '</ul>';
    echo '</li>';
    next($plugins);
  }

  echo '</ol>';

  next($packages);
}

?>