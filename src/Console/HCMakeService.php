<?php

declare(strict_types = 1);

namespace HoneyComb\Scripts\Console;

use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Controllers\HCScriptsFormsController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsModelsController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsRoutesController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsServiceController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class HCMakeService
 * @package HoneyComb\Scripts\Console
 */
class HCMakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc-make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating a standardized service';

    /**
     * Configuration directory
     *
     * @var string
     */
    private $configDirectory = '_hc_scripts_configuration';

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * Configuration for services creation
     *
     * @var HCServiceDTO
     */
    private $config;


    /**
     * HCScanRolePermissionsCommand constructor.
     * @param HCScriptsHelper $helper
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(HCScriptsHelper $helper)
    {
        parent::__construct();
        $this->helper = $helper;
    }

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->loadConfiguration();
        $this->createService();
    }

    /**
     * Loading configuration file, first available in configDirectory
     */
    private function loadConfiguration()
    {
        $allFiles = File::allFiles($this->configDirectory);

        foreach ($allFiles as $file) {
            $filePath = (string)$file;

            if (strpos($filePath, '.done') === false) {

                $config = json_decode(file_get_contents($filePath), true);

                if ($config == null) {
                    $this->helper->abort($file->getFilename() . ' has Invalid JSON format.');
                }

                $this->config = new HCServiceDTO($config, $this->helper);

                break;
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function createService()
    {
        (new HCScriptsRoutesController($this->helper))->generate($this->config);
        (new HCScriptsModelsController($this->helper))->generate($this->config);
        (new HCScriptsFormsController($this->helper))->generate($this->config);
        (new HCScriptsServiceController($this->helper))->generate($this->config);

        $this->config->getTranslation()->generate();
    }
}
