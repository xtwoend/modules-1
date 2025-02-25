<?php namespace Pingpong\Modules\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleDisableCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the specified module.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $module = $this->argument('module');

        if($this->laravel['modules']->active($this->argument('module')))
        {
            $this->laravel['modules']->disable($module);

            $this->info("Module [{$module}] disabled successful.");
        }
        else
        {
            $this->comment("Module [{$module}] has already disabled.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('module', InputArgument::REQUIRED, 'Module name.'),
        );
    }


}
