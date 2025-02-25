<?php namespace Pingpong\Modules\Commands;

use Pingpong\Modules\Module;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMigrateMakeCommand extends Command {

	use ModuleCommandTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:migration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a new migration for the specified module.';

    /**
     * Create a new command instance.
     *
     * @param Module $module
     * @param File $files
     * @return \Pingpong\Modules\Commands\ModuleMigrateMakeCommand
     */
	public function __construct(Module $module, File $files)
	{
        parent::__construct();

        $this->module  = $module;

        $this->file  = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->moduleName  		=  $this->getModuleName();

		$this->table 		 	=  str_plural(strtolower($this->argument('table')));
		
		$this->migrationName 	=  "create_".snake_case($this->table)."_table";
        $this->className        =  studly_case($this->migrationName);


		if($this->module->has($this->moduleName))
		{
			$this->makeFile();
			
			$this->info("Created : ".$this->getDestinationFile());
			
			return $this->call('dump-autoload');
		}
		return $this->info("Module [$this->moduleName] does not exists.");
	}

	/**
	 * Get filename.
	 *
	 * @return string
	 */
	protected function getFilename()
	{
		return date("Y_m_d_His") . '_' . $this->migrationName.'.php';
	}

	/**
	 * Get fields.
	 *
	 * @return string
	 */
	protected function getFields()
	{
		$result = '';

		if($option = $this->option('fields'))
		{
			$fields = str_replace(" ", "", $option);

			$fields = explode(',', $fields);

			foreach ($fields as $field)
            {
				$result .= $this->setField($field);
			}
		}

		return $result;
	}

	/**
	 * Set field to script.
	 *
	 * @param  string  $option
	 * @return string
	 */
	protected function setField($option)
	{
		$result = '';

		if( ! empty($option) )
		{
			$option = explode(":", $option);

			$result.= '			$table->'.$option[1]."('$option[0]')";
			
			if(count($option) > 0)
			{
				foreach ($option as $key => $o)
                {
					if($key == 0 || $key == 1) continue;
					$result.= "->$o()";		
				}
			}

			$result.= ';'.PHP_EOL;
		}

		return $result;
	}

	/**
	 * Get destination file.
	 *
	 * @return string
	 */
	protected function getDestinationFile()
	{
		return $this->getPath() . $this->formatContent($this->getFilename());
	}

	/**
	 * Get seeder path.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		$path = $this->module->getModulePath($this->moduleName);

		return $path . "database/migrations/";
	}

	/**
	 * Create new file.
	 *
	 * @return 	string
	 */
	public function makeFile()
	{
		return $this->file->put($this->getDestinationFile(), $this->getStubContent());
	}

	/**
	 * Get stub content by given key.
	 *
	 * @return string
	 */
	protected function getStubContent()
	{
		return $this->formatContent($this->file->get(__DIR__ . '/stubs/migration.stub'));
	}

	/**
	 * Replace the specified text from given content.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{migrationName}}', '{{table}}', '{{fields}}'],
			[$this->className, $this->table, $this->getFields()],
			$content
		);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('table', InputArgument::REQUIRED, 'The name of table will be created.'),
			array('module', InputArgument::OPTIONAL, 'The name of module will be created.'),
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
			array('--fields', null, InputOption::VALUE_OPTIONAL, 'The specified fields table.', null),
		);
	}
}
