<?php
require 'pcss.class.php';

$file = isset($_REQUEST['pcss']) ? $_REQUEST['pcss'] : 'style.pcss'; //pcss file to render; be sure to validate.

$options = array(
  'indent' => 1, //indent spaces for pretty formatting of css.  Default: 1
  'indent_char' => "\t", //indent character.  Default: "\t"
  'pcss_includes' => array('pcss.includes.php'), //this file will be included at the top of your pcss file.  Use for global functions across all your .pcss files.
  'sort_properties' => true //alphabetically sort the properties.  Defaults to true.
);

$pcss = new pcss($file,$options);

$pcss->render();  //output resulting pcss file w/ css header
