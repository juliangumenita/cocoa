<?php
  require "Cocoa.php";
  echo Cocoa::get("article", [
    "title" => "Article Title",
    "[text]" => Cocoa::get("text", [
      "value" => "Some interesting article text."
    ])
  ]);
?>
