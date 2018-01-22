<?php

declare(strict_types = 1);

namespace HoneyComb\Scripts\Console;

use HoneyComb\Scripts\DTO\HCInitialPackageDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Class HCMakePackage
 * @package HoneyComb\Scripts\Console
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
     * @const string
     */
    const ROOT_DIRECTORY = 'packages';

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * Configuration for package creation
     *
     * @var HCInitialPackageDTO
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

        $this->helper->createDirectory(self::ROOT_DIRECTORY);
    }

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->config = new HCInitialPackageDTO($this->getConfiguration());

        $this->finalizeConfig();
        $this->createStructure();
        $this->createFiles();
    }

    /**
     * Finalizing configuration
     *
     * @throws \Exception
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function finalizeConfig(): void
    {
        $directoryList = [];

        // get packages list without /src/hc-config.json file
        foreach (File::directories(self::ROOT_DIRECTORY) as $directory) {
            $developmentPackages = File::directories($directory);

            foreach ($developmentPackages as $package) {
                if (!file_exists($package . '/src/hc-config.json')) {
                    array_push($directoryList, $package);
                }
            }
        }

        if (empty($directoryList)) {
            $this->helper->abort('You must create your package directory first. .i.e.: honey-comb/package-name inside ' . self::ROOT_DIRECTORY . ' directory');
        }

        // selecting package directory
        $packageDirectory = $this->choice('Please select package directory', $directoryList);

        // setting package name
        $this->config->packageName = $this->ask('Please enter package name');

        // setting package name
        $this->config->packagePath = str_replace(self::ROOT_DIRECTORY . '/', '', $packageDirectory);

        // formatting string with proper uppercase
        $upperCaseFormat = ucwords($this->config->packagePath, "/-");

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
        if (!isset($config['folders'])) {
            $this->helper->abort('No folders to create');
        }
    }

    /**
     * Creating structure for new package
     */
    private function createStructure()
    {
        foreach ($this->config->getFolderList() as $folder) {
            $this->helper->createDirectory(self::ROOT_DIRECTORY . '/' . $this->config->packagePath . '/' . $folder);
        }
    }

    /**
     * Creating files
     */
    private function createFiles()
    {
        foreach ($this->config->getFilesList() as $key => $value) {
            $this->helper->createFileFromTemplate(self::ROOT_DIRECTORY . '/' . $key, $value, $this->config->jsonData());
        }
    }
}
