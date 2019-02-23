<?php
  class Cocoa{
    private static $options = [
      "state" => "default",
      "dir" => "cocoa/",
      "ext" => "cocoa"
    ];

    private static function options($options){
      if(is_array($options)){
        return [
          "state" =>
            (array_key_exists("state", $options))
            ? $options["state"]
            : self::$options["state"],
          "dir" =>
            (array_key_exists("dir", $options))
            ? $options["dir"]
            : self::$options["dir"],
          "ext" =>
            (array_key_exists("ext", $options))
            ? $options["ext"]
            : self::$options["ext"]
        ];
      } else {
        return [
          "state" => $options,
          "dir" => self::$options["dir"],
          "ext" => self::$options["ext"]
        ];
      }
    }

    private static function between($string, $start, $end, $multiple = false){
      $result = [];
      foreach (explode($start, $string) as $key => $value) {
        if(strpos($value, $end) !== false){
          $result[] = substr($value, 0, strpos($value, $end));;
        }
      }
      return ($multiple) ? $result : $result[0];
      /*
      * Get a string
      * between two values.
      */
    }

    public static function get($name, $parameters = [], $options = "default", $beans = []){
      $options = self::options($options);
      $file = @file_get_contents($options["dir"] . $name . "." . $options["ext"]);
      /* Getting the contents. */

      $component = self::between($file, "@{$options["state"]}", "{$options["state"]}@");
      foreach ($parameters as $key => $param) {
        $component = str_replace("{{{$key}}}", $param, $component);
      }
      /* Replacing all of the variables. */

      $beans = self::between($component, "[[", "]]", true);
      foreach ($beans as $bean) {
        $package =
          array_key_exists($beans[$bean])
          ? $beans[$bean]
          : [ "state" => "default", "parameters" => [], "beans" => []];

        $beanParameters = array_key_exists($package["parameters"]) ? $package["parameters"] : [];
        $beanState = array_key_exists($package["state"]) ? $package["state"] : "default";
        $beanCococas = array_key_exists($package["beans"]) ? $package["beans"] : [];

        $component = str_replace("[[{$cocoa}]]", self::get($bean, $beanParameters, $beanState, $beanCococas), $component);
      }
      /* Replacing all of the "beans". */

      $component = preg_replace("/{{[\s\S]+?}}/", null, $component);
      /* Cleaning all unused parameters. */

      return $component;
    }

  }
?>
