<?php
/**
 * @copyright 2018 innovationbase
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
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

declare(strict_types = 1);

namespace HoneyComb\Scripts\Console;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Resources\HCInitialProjectResource;
use Illuminate\Console\Command;

/**
 * Class HCMakeService
 * @package HoneyComb\Scripts\Console
 */
class HCPrepareProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:pp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preparing project';

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * Configuration for services creation
     *
     * @var HCInitialProjectResource
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
        $this->config = new HCInitialProjectResource($this->getConfiguration());

        $this->createFiles();
    }

    /**
     * Creating files
     */
    private function createFiles()
    {
        foreach ($this->config->getFilesList() as $key => $value) {
            $this->helper->createFileFromTemplate($key, $value, $this->config->jsonData());
        }
    }

    /**
     * Getting configuration for new package
     *
     * @return array
     * @throws \Exception
     */
    private function getConfiguration(): array
    {
        $path = __DIR__ . '/../config/prepare-project.json';

        if (!file_exists($path)) {
            $this->helper->abort('Missing package configuration file');
        }

        $config = json_decode(file_get_contents($path), true);

        return $config;
    }
}
