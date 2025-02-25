<?php namespace Pingpong\Modules\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ModuleControllerCommand
 * @package Pingpong\Modules\Commands
 */
class ModuleControllerCommand extends Command {

	use ModuleCommandTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:controller';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate new restful controller for the specified module.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->module 		= $this->laravel['modules'];
		$this->moduleName 	= $this->getModuleName();

		$this->controllerName	= $this->getControllerName();
		
		if($this->module->has($this->moduleName))
		{
			return $this->call('controller:make', $this->getParameters());
		}
		
		return $this->error("Module [$this->moduleName] doest not exists.");
	}

	/**
	 * Get controller name.
	 * 
	 * @return string 
	 */
	public function getControllerName()
	{
		$controller = studly_case($this->argument('controller'));
		
		if( ! str_contains(strtolower($controller), 'controller'))
		{
			$controller = $controller . 'Controller';
		}

		return $controller;
	}

	/**
	 * Get parameters.
	 *
	 * @return array
	 */
	protected function getParameters()
	{
		return [
			'name'		=>  $this->controllerName,
			'--path'	=>	$this->getControllerPath(),
			'--only'	=>	$this->option('only'),
			'--except'	=>	$this->option('except'),
		];
	}

	/**
	 * Get controller path.
	 *
	 * @return string
	 */
	protected function getControllerPath()
	{
        $path = ltrim(str_replace(base_path(), '', $this->module->getPath()), '/');

        return $path . "/{$this->moduleName}/controllers";
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('controller', InputArgument::REQUIRED, 'The name of the controller class.'),
			array('module', InputArgument::OPTIONAL, 'The name of module will be used.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('only', null, InputOption::VALUE_OPTIONAL, 'The methods that should be included'),
			array('except', null, InputOption::VALUE_OPTIONAL, 'The methods that should be excluded'),
		);
	}

}
