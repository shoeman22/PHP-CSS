<?php
require 'pcss.class.php';

$file = 'style.pcss'; //pcss file to render

$options = array(
  'indent' => 1, //indent spaces for pretty formatting of css.  Default: 1
  'indent_char' => "\t", //indent character.  Default: "\t"
  'pcss_includes' => array('pcss.includes.php') //this file will be included at the top of your pcss file.  Use for global functions across all your .pcss files.
);

$pcss = new pcss($file,$options);

$pcss->render();  //output resulting pcss file w/ css header