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

namespace HoneyComb\Scripts\Http\Controllers;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Resources\HCServiceResource;

/**
 * Class HCScriptsModelsController
 * @package HoneyComb\Scripts\Http\Controllers
 */
class HCScriptsModelsController
{
    /**
     * @var HCScriptsHelper
     */
    private $helper;


    /**
     * @var HCServiceResource
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
     * @param HCServiceResource $config
     */
    public function generate(HCServiceResource $config)
    {
        $this->config = $config;

        foreach ($config->getModelConfig()->getModels() as $model) {
            $this->generateModel($model);
        }
    }

    /**
     * Generating model
     *
     * @param array $model
     */
    private function generateModel(array $model)
    {
        $this->config->getModelConfig()->getFieldsForModel($model);

        $data = [
            'namespace' => $this->config->getPackageConfig()->getNamespaceForModel(),
            'model' => $model['modelName'],
            'table' => $model['tableName'],
            "fields" => '\'' . implode('\', \'', $model['fieldsModel']) . '\'',
            'useNamespaces' => $this->getNameSpaces($model['use']),
            'useClassNames' => $this->getClassNames($model['use']),
            'with' => "",
        ];

        if ($this->config->isMultiLanguage() && isset($model['default']) && $model['default'] === 1) {
            $data['with'] = "'translations'";
        }

        $destination = $this->config->getDirectory() . 'Models/' . $data['model'] . '.php';

        if (isset($model['repository']) && $model['repository'] == 1) {
            $this->generateRepository($data);
        }

        $this->helper->createFileFromTemplate($destination,
            'service/models/' . $this->getModelType($model['use'], $this->config->getActions()->getAdmin()) . '.hctpl',
            $data);
    }

    /**
     * @param array $use
     * @return string
     */
    private function getNameSpaces(array $use): string
    {
        $value = '';

        foreach ($use as $item) {
            switch ($item) {
                case 'ownership' :

                    $value .= 'use HoneyComb\\Core\\Models\\Traits\\HCOwnership;' . "\r\n";
                    break;

                case 'translations' :

                    $value .= 'use HoneyComb\\Core\\Models\\Traits\\HCTranslation;' . "\r\n";
                    break;
            }
        }

        return $value;
    }

    /**
     * @param array $use
     * @return string
     */
    private function getClassNames(array $use): string
    {
        $value = '';

        foreach ($use as $item) {
            switch ($item) {
                case 'ownership' :

                    $value .= ', HCOwnership';
                    break;

                case 'translations' :

                    $value .= ', HCTranslation';
                    break;
            }
        }

        if ($value != '') {
            $value = 'use ' . substr($value, 2) . ';';
        }

        return $value;
    }

    /**
     * Generating repository
     *
     * @param $data
     */
    private function generateRepository(array $data)
    {
        $data['repository'] = $data['model'] . 'Repository';
        $data['repositoryNs'] = $this->config->getPackageConfig()->getNamespaceForRepository();
        $data['requestNs'] = $this->config->getPackageConfig()->getNameSpaceForRequest();
        $data['softDelete'] = $this->getActionSoftDelete($data);
        $data['restore'] = $this->getActionRestore($data);
        $data['deleteForce'] = $this->getActionForceDelete($data);

        $destination = $this->config->getDirectory() . 'Repositories/Admin/' . $data['repository'] . '.php';
        $this->helper->createFileFromTemplate($destination, 'service/repository.hctpl', $data);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getActionSoftDelete(array $data)
    {
        $string = "";

        if (in_array("delete", $this->config->getActions()->getAdmin())) {

            if ($this->config->isMultiLanguage()) {
                $template = 'service/repository/action.deleteSoft.m.hctpl';
            } else {
                $template = 'service/repository/action.deleteSoft.hctpl';
            }

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getActionRestore(array $data)
    {
        $string = "";

        if (in_array("restore", $this->config->getActions()->getAdmin())) {

            if ($this->config->isMultiLanguage()) {
                $template = 'service/repository/action.restore.m.hctpl';
            } else {
                $template = 'service/repository/action.restore.hctpl';
            }

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getActionForceDelete(array $data)
    {
        $string = "";

        if (in_array("delete_force", $this->config->getActions()->getAdmin())) {

            if ($this->config->isMultiLanguage()) {
                $template = 'service/repository/action.deleteForce.m.hctpl';
            } else {
                $template = 'service/repository/action.deleteForce.hctpl';
            }

            $string = $this->helper->createFileFromTemplate('', $template, $data, false);
        }

        return $string;
    }

    /**
     * @param array $use
     * @return string
     */
    private function getModelType(array $use, array $actions): string
    {
        if (in_array('uuid', $use)) {
            return 'uuid';
        }

        if (in_array('translation', $use)) {
            return 'translation';
        }

        if (in_array('conn', $use)) {
            return 'base';
        }

        if (in_array('delete_force', $actions)) {
            return 'soft_uuid';
        }

        return 'uuid';
    }
}
