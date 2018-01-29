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

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

/**
 * Class HCScriptsServiceController
 * @package HoneyComb\Scripts\Http\Controllers
 */
class HCScriptsServiceController extends Controller
{
    /**
     * @var HCServiceDTO
     */
    private $config;

    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * HCScriptsRoutesController constructor.
     * @param HCScriptsHelper $helper
     */
    public function __construct(HCScriptsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param HCServiceDTO $config
     */
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        $this->generateService();
    }

    /**
     *
     */
    private function generateService()
    {
        $data = [
            'serviceName' => $this->config->getServiceName(),
            'namespace' => $this->config->getPackageConfig()->getNamespaceForService(),
            'namespaceClean' => $this->config->getPackageConfig()->getNamespace(),
        ];

        $destination = $this->config->getDirectory() . 'Services/' . $data['serviceName'] . 'Service' . '.php';

        $this->helper->createFileFromTemplate($destination, 'service/service.hctpl', $data);
    }
}
