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
 * Class HCScriptsFormsController
 * @package HoneyComb\Scripts\Http\Controllers
 */
class HCScriptsFormsController extends Controller
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
     * @throws \Exception
     */
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        $model = $this->config->getModelConfig()->getDefaultModel();

        if ($model == null) {
            $this->helper->abort("No default table is set.");
        }

        $structure = "";

        foreach ($model['fields'] as $field) {
            $fieldData = [];

            if (!in_array($field->Field, $this->config->getModelConfig()->getSkipFieldsForForm())) {
                $fieldData['field'] = $field->Field;

                if ($field->Null == "NO") {
                    $fieldData['required'] = '"required" => 1,';
                } else {
                    $fieldData['required'] = "";
                }

                $fieldData['type'] = '"type" => ' . $this->getFieldType($field->Type) . ',';
                $fieldData['label'] = '"label" => "' . $this->config->getTranslation()->getLabelFieldForForm($field->Field) . '",';

                $structure .= $this->helper->createFileFromTemplate("", "service/forms/field.hctpl", $fieldData, false);
            }
        }

        $data = [
            "serviceName" => $this->config->getServiceName(),
            "namespace" => $this->config->getPackageConfig()->getNamespaceForForm(),
            "structure" => $structure,
            "routeName" => $this->config->getRouteName(),
        ];

        $this->helper->createFileFromTemplate($this->getFormDestination(), "service/forms/form.hctpl", $data);

        $this->config->updateForm($this->config->getRouteName(),
            $data['namespace'] . '\\' . $data['serviceName'] . 'Form');
    }

    /**
     * @param string $type
     * @return string
     */
    private function getFieldType(string $type)
    {
        if (strpos($type, 'char') !== false) {
            preg_match_all('!\d+!', $type, $matches);

            if ($matches[0][0] > 255) {
                return '"textArea"';
            }

            return '"singleLine"';
        }

        if (strpos($type, 'int') !== false) {
            return '"number"';
        }

        if (strpos($type, 'text') !== false) {
            return '"richText"';
        }

        if (strpos($type, 'date') !== false || strpos($type, 'time') !== false) {
            return '"dateTimePicker"';
        }

        return "";
    }

    /**
     * @return string
     */
    private function getFormDestination()
    {
        return $this->config->getDirectory() . 'Forms/' . $this->config->getServiceName() . 'Form.php';
    }
}
