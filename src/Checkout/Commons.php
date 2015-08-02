<?php

namespace CieloCheckout;

class Commons {

  public function __construct($properties = NULL) {
    if ($properties) {
      $this->set_properties($properties);
    }
  }

  /**
   * @param Array $properties
   *   List of property values to be set.
   *   Index: Property name | Value: Property value.
   */
  public function set_properties($properties) {
    foreach($properties as $property_name => $property_value) {
      if (method_exists($this, "set_{$property_name}")) {
        $method_name = "set_{$property_name}";
        $this->{$method_name}($property_value);
      }
      else {
        if (property_exists($this, $property_name)) {
          $this->{$property_name} = $property_value;
        }
        else {
          throw new \Exception("Property '$property_name' is not recognised.");
        }
      }
    }
  
    $this->property_requirements();
  
    if (method_exists($this, 'validate')) {
      $this->validate();
    }
  }

  /**
   * Checks if each property listed on $this->property_requirements passes
   * a set of callback functions. The callback functions must return a boolean
   * value and accept the property being checked as its single paramenter.
   *
   * Example of how $this->property_requirements should be set:
   *   $property_requirements = [
   *     // The property name which should be checked.
   *     'PropertyName' => [
   *       // The callback function name.
   *       'empty' => [
   *         // Whether or not it should negate.
   *         // i.e. !empty($this->PropertyName) when negate is TRUE.
   *         // If not set, defaults to TRUE.
   *         'negate' => FALSE,
   *       ],
   *       'is_array' => [],
   *     ],
   *   ];
   */
  protected function property_requirements() {
    if (!empty($this->property_requirements)) {
      if (!is_array($this->property_requirements)) {
        throw new \Exception("'property_requirements' is not an array.");
      }
  
      foreach ($this->property_requirements as $property_name => $requitements) {
        if (!is_array($requitements)) {
          throw new \Exception("'property_requirements[$property_name]' is not an array.");
        }
    
        foreach ($requitements as $callback_function_name => $callback_options) {
          if (!is_array($callback_options)) {
            throw new \Exception("'property_requirements[$property_name][$callback_function_name]' is not an array.");
          }
    
          $negate = TRUE;
          foreach ($callback_options as $callback_option => $callback_option_value) {
            switch ($callback_option) {
              case 'negate':
                if (!is_bool($callback_option_value)) {
                  throw new \Exception("'property_requirements[$property_name][$callback_function_name][$callback_option] == {$callback_option_value}'. It should've been set with a boolean value.");
                }
                $negate = $callback_option_value;
              break;
              // Everything else is a typo.
              default:
                throw new \Exception("The '$callback_option' index at 'property_requirements[$property_name][$callback_function_name][$callback_option]' is invalid.");
            }
          }
    
          // Check if $callback_function_name is a language construct rather
          // than a function.
          if (!is_callable($callback_function_name)) {
            $method_name = "construct_{$callback_function_name}";
      
            if (!method_exists($this, $method_name)) {
              throw new \Exception("'$method_name' does not exist.");
            }
            $check_result = $this->{$method_name}($this->{$property_name});
          }
          else {
            $check_result = $callback_function_name($this->{$property_name});
          }
    
          if ($check_result !== $negate) {
            $not = 'not';
            if ($negate) {
              $not = '';
            }
            throw new \Exception("'$property_name' does not pass the check for $not {$callback_function_name}.");
          }
        }
      }
    }
  }

  /**
   * Replaces the language construct empty();
   *
   * @param Any $value
   *   A variable value to be checked.
   *
   * @return Bool
   *   Whether or not $value is empty.
   */
  protected function construct_empty($value) {
    return empty($value);
  }
  
}
