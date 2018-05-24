<?php
/**
 * @copyright 2018 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InteractiveSolutions:
 * E-mail: info@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

declare(strict_types = 1);

namespace HoneyComb\Scripts\Console;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Controllers\HCScriptsControllerController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsFormsController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsModelsController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsRequestController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsRoutesController;
use HoneyComb\Scripts\Http\Controllers\HCScriptsServiceController;
use HoneyComb\Scripts\Http\Resources\HCServiceResource;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
     * @var HCServiceResource
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
     * @throws \Exception
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

                $this->config = new HCServiceResource(new Collection());
                $this->config->setData($config, $this->helper);

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
        (new HCScriptsControllerController($this->helper))->generate($this->config);
        (new HCScriptsRequestController($this->helper))->generate($this->config);

        $this->config->getTranslation()->generate();
    }
}
