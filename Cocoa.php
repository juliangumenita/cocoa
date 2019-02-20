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

    private static function keys($name, $parameters){
      $temp = [];
      foreach ($parameters as $key => $value) {
        if(strpos($key, $name . "::") === 0){
          $bean = substr($key, strlen($name) + 2);
          $temp[$bean] = $value;
        }
      }
      return $temp;
    }

    public static function get($name, $parameters = [], $options = "default"){
      $options = self::options($options);
      $file = @file_get_contents($options["dir"] . $name . "." . $options["ext"]);
      /* Getting the contents. */
      $component = self::between($file, "@{$options["state"]}", "{$options["state"]}@");
      foreach ($parameters as $key => $param) {
        $component = str_replace("{{{$key}}}", $param, $component);
      }
      /* Replacing all of the parameters. */
      $component = preg_replace("/@{{[\s\S]+?}}/", null, $component);
      /* Replacing all of the unused versions. */

      $cocoas = self::between($component, "[[", "]]", true);
      foreach ($cocoas as $cocoa) {
        $package = explode("@", $cocoa);

        $cocoaName = $package[0];
        $cocoaVersion = array_key_exists(1, $package) ? $package[1] : "default";
        $cocoaParameters = self::keys($cocoaName, $parameters);

        $component = str_replace("[[{$cocoa}]]", self::get($cocoaName, $cocoaParameters, $cocoaVersion), $component);
      }
      /* Getting the state. */
      $component = preg_replace("/{{[\s\S]+?}}/", null, $component);
      /* Cleaning all unused parameters. */
      return $component;
    }

  }
?>
