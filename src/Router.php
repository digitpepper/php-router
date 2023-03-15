<?php

declare(strict_types = 1);

namespace DP;

class Router
{
	protected $routes = [];

	public function route($pattern, ...$callbacks)
	{
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		$this->routes[$pattern] = $callbacks;
	}

	public function execute($request_method, $url, ...$arguments)
	{
		foreach ($this->routes as $pattern => $callbacks) {
			$found = false;
			$matches = null;
			$result = null;
			foreach ($callbacks as $index => $callback) {
				if ($index === 0) {
					$found = preg_match($pattern, "$request_method $url", $matches);
					if ($found) {
						array_shift($matches);
						$result = call_user_func_array($callback, array_merge($arguments, array_values($matches)));
					} else {
						break;
					}
				} else {
					$result = call_user_func_array($callback, array_merge($arguments, $result));
				}
			}
			if ($found) {
				return $result;
			}
		}
	}
}
