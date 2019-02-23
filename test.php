<?php
  require "Cocoa.php";
  echo Cocoa::get("article", [
    "title" => "Hello.",
    "text::value" => "This is a value",
    "version" => "big"
  ]);
?>
