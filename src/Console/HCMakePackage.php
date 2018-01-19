<?php

declare(strict_types = 1);

namespace HoneyComb\Scripts\Console;

use HoneyComb\Scripts\DTO\HCPackageDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class HCProjectSize
 * @package HoneyComb\Core\Console
 */
class HCMakePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc-make:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating initial structure for package';

    /**
     * Directory name where all development packages are stored
     *
     * @var string
     */
    protected $rootDirectory = 'development';

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * Configuration for package creation
     *
     * @var HCPackageDTO
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

        $this->helper->createDirectory($this->rootDirectory);
    }

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->config = new HCPackageDTO($this->getConfiguration());

        $this->getDirectory();
    }

    private function getDirectory()
    {
        $directoryList = [];

        // get packages list without /src/hc-config.json file
        foreach (File::directories($this->rootDirectory) as $directory) {
            $developmentPackages = File::directories($directory);

            foreach ($developmentPackages as $package) {
                if (!file_exists($package . '/src/hc-config.json')) {
                    array_push($directoryList, $package);
                }
            }
        }

        if (empty($directoryList)) {
            $this->helper->abort('You must create your package directory first. .i.e.: honey-comb/package-name inside ' . $this->rootDirectory . ' directory');
        }

        $packageDirectory = $this->choice('Please select package directory', $directoryList);

        // setting package name
        $this->config->packageName = str_replace($this->rootDirectory . '/', '', $packageDirectory);

        // formatting string with proper uppercase
        $upperCaseFormat = ucwords($this->config->packageName, "/-");

        // setting package namespace
        $this->config->namespace = $this->helper->stringOnly(str_replace('/', '\\', $upperCaseFormat));

        // setting composer namespace
        $this->config->namespaceComposer = str_replace(['\\', '/'], '\\\\', $upperCaseFormat . '\\');
        $this->config->namespaceComposer = str_replace('-', '', $this->config->namespaceComposer);
    }

    /**
     * Getting configuration for new package
     *
     * @return array
     * @throws \Exception
     */
    private function getConfiguration(): array
    {
        $path = __DIR__ . '/../config/make-package.json';

        if (!file_exists($path)) {
            $this->helper->abort('Missing package configuration file');
        }

        $config = json_decode(file_get_contents($path), true);

        $this->validateConfig($config);

        return $config;
    }

    /**
     * Validating configuration
     *
     * @param array $config
     * @throws \Exception
     */
    private function validateConfig(array $config)
    {
        if (!isset($config['folders']))
            $this->helper->abort('No folders to create');

    }
}
