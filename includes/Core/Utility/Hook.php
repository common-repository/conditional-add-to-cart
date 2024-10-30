<?php

namespace ConditionalAddToCart\Core\Utility;
class Hook {

	public static function isClosure($closure){
		return  (new \ReflectionFunction($closure))->isClosure();
	}
	public static function replaceCallback($searchCallback, $replaceCallback = null) {
    global $wp_filter;
    if(is_null($replaceCallback)){
      $replaceCallback = function(){};
		}
		foreach($wp_filter as $hook => $hookObj){
			foreach($hookObj->callbacks as $priority => $priorityArr){
				foreach($priorityArr as $callbackName => $callbackArr){
					if(in_array($callbackName, (array)$searchCallback)){
						if($wp_filter[$hook]->callbacks[$priority][$callbackName]['function'] === $replaceCallback ||
							static::isClosure($wp_filter[$hook]->callbacks[$priority][$callbackName]['function']) && static::isClosure($replaceCallback)		
						){
							continue;
						}
						$wp_filter[$hook]->callbacks[$priority][$callbackName]['function(backup)'] = $wp_filter[$hook]->callbacks[$priority][$callbackName]['function'];
						$wp_filter[$hook]->callbacks[$priority][$callbackName]['function'] = $replaceCallback;
					}
				}
			}
		}
  }
  
  public static function restoreCallback($callback){
		global $wp_filter;
		foreach($wp_filter as $hook => $hookObj){
			foreach($hookObj->callbacks as $priority => $priorityArr){
				foreach($priorityArr as $callbackName => $callbackArr){
					if(in_array($callbackName, (array)$callback)){
						if(isset($wp_filter[$hook]->callbacks[$priority][$callbackName]['function(backup)'])){
							$wp_filter[$hook]->callbacks[$priority][$callbackName]['function'] 
							= $wp_filter[$hook]->callbacks[$priority][$callbackName]['function(backup)'];
							unset($wp_filter[$hook]->callbacks[$priority][$callbackName]['function(backup)']);
						}
					}
				}
			}
		}
  }


}