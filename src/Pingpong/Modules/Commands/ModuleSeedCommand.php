<?php namespace Pingpong\Modules\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleSeedCommand extends Command {

	use ModuleCommandTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run database seeder from the specified module or from all modules.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $this->module = $this->laravel['modules'];

        $module = Str::studly($this->argument('module')) ?: $this->getModuleName();

        if($module)
		{
			if($this->module->has($module))
			{
				$this->dbseed($module);

				return $this->info("Module [$module] seeded.");
			}
			
			return $this->error("Module [$module] does not exists.");
		}

		foreach ($this->module->all() as $name)
        {
			$this->dbseed($name);
		}

		return $this->info("All modules seeded.");
	}

	/**
	 * Seed the specified module.
	 *
	 * @parama string  $name
	 * @return array
	 */
	protected function dbseed($name)
	{
		$params 	= [
			'--class' => $this->option('class')? $this->option('class') : Str::studly($name) . 'DatabaseSeeder'
		];
		if($option = $this->option('database'))
		{
			$params['--database'] = $option;
		}
		$this->call('db:seed', $params);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
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
			array('class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', null),
			array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'),
		);
	}

}
