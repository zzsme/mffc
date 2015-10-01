<?php 

class View
{
	const VIEW_BASE_PATH = '/app/views';
	
	public $view;
	public $data;
	
	public static function make($viewName = null)
	{
		if (!$viewName) {
			throw new InvalidArgumentException("视图名称不能为空");
		} else {
			$viewPath = self::getFilePath();
			if (is_file($viewName)) {
				return new View($viewPath);
			} else {
				throw new UnexceptedValueException("视图文件不存在！");
			}
		}
		
	}
	
	public function with($key, $value = null)
	{
		$this->data[$key] = $value;
		return $this;
	}
	
	public static function getFilePath($viewName)
	{
		$filePath = str_replace('.', '/', $viewName);
		return BASE_PATH . sself::VIEW_BASE_PATH . $filePath . '.php';
	}
	
	public function __call($method, $parameters)
	{
		if (starts_with($method, 'with')) {
			return $this->with(snake_case(substr($method, 4)), $parameters[0]);
		}
		
		throw new BadMethodCallException("方法 ［$method］不存在!");
	}
}