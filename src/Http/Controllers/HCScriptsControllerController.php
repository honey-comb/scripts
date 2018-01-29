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

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCScriptsControllerController extends Controller
{
    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * @var HCServiceDTO
     */
    private $config;

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
     * @throws \Exception
     */
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        $data = [
            'namespace' => $this->config->getPackageConfig()->getNamespaceForAdminController(),
            'serviceNs' => $this->config->getPackageConfig()->getNamespaceForService(),
            'serviceName' => $this->config->getServiceName(),
            'translationLabel' => $this->config->getTranslation()->getLabelFieldForForm('page_title'),
            'url' => $this->config->getUrl(),
            'formName' => $this->config->getRouteName(),
            'actionPrefix' => $this->config->getAclPrefix(),
            'columnList' => $this->generateColumnList()
        ];

        $this->helper->createFileFromTemplate($this->config->getDirectory() . '/Http/Controllers/Admin/' . $this->config->getServiceName() . 'Controller.php','service/controller.hctpl', $data);
    }

    private function generateColumnList()
    {
        $model = $this->config->getModelConfig()->getDefaultModel();
        $this->config->getModelConfig()->getFieldsForModel($model);

        $fields = $model['fieldsModel'];
        $columns = '';

        foreach ($fields as $value)
        {
            $columns .= '\''. $value . '\' => $this->headerText(trans(\'' . $this->config->getTranslation()->getLabelFieldForForm($value) .'\')),';
        }

        return $columns;
    }
}