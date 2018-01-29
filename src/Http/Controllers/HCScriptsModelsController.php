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

use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

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
     */
    public function generate(HCServiceDTO $config)
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
            "fields" => '"' . implode('", "', $model['fieldsModel']) . '"',
            'useNamespaces' => $this->getNameSpaces($model['use']),
            'useClassNames' => $this->getClassNames($model['use']),
        ];

        $modelType = 'uuid';
        $destination = $this->config->getDirectory() . 'Models/' . $data['model'] . '.php';

        if (isset($model['repository']) && $model['repository'] == 1) {
            $this->generateRepository($data);
        }

        $this->helper->createFileFromTemplate($destination, 'service/models/' . $modelType . '.hctpl', $data);
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

                    $value .= ', Ownership';
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

        $destination = $this->config->getDirectory() . 'Repositories/' . $data['repository'] . '.php';
        $this->helper->createFileFromTemplate($destination, 'service/repository.hctpl', $data);
    }
}
