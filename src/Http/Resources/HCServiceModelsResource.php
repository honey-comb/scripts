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
 * E-mail: hello@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

declare(strict_types = 1);

namespace HoneyComb\Scripts\Http\Resources;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class HCServiceModelsResource
 * @package HoneyComb\Scripts\Http\Resources
 */
class HCServiceModelsResource extends ResourceCollection
{
    /**
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * List of available fields
     *
     * @var array
     */
    private $fields;

    /**
     * List of fields which will be ignored for models and forms
     *
     * @var array
     */
    private $skipFields = ['count', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * List of models
     *
     * @var array
     */
    private $models;

    /**
     * HCServiceModelsResource constructor.
     * @param array $models
     * @param HCScriptsHelper $helper
     * @throws \Exception
     */
    public function __construct(Collection $models, HCScriptsHelper $helper)
    {
        parent::__construct($models);

        $this->helper = $helper;
        $autoModels = [];

        foreach ($models as $model) {
            if (in_array('translations', $model['use'])) {
                $autoModels[] = ($this->createTranslationsModel($model));
            }
        }

        $models = array_merge($models->toArray(), $autoModels);

        foreach ($models as $model) {

            $model['fields'] = $this->getTableColumns($model['tableName']);
            $this->models[] = $model;
        }
    }

    /**
     * Getting table columns
     *
     * @param $tableName
     * @return mixed
     */
    private function getTableColumns(string $tableName)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

        if (!count($columns)) {
            $this->helper->abort('Table not found: ' . $tableName);
        } else {
            $columns = DB::select(DB::raw('SHOW COLUMNS FROM ' . $tableName));
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getModels(): array
    {
        return $this->models;
    }

    /**
     * @param array $model
     */
    public function getFieldsForModel(array &$model)
    {
        $fields = array_pluck($model['fields'], 'Field');

        foreach ($fields as $key => $fieldName) {
            if (!in_array($fieldName, $this->getSkipFieldsForModel())) {
                $model['fieldsModel'][] = $fieldName;
            }
        }
    }

    /**
     * gets default model
     *
     * @return mixed|null
     */
    public function getDefaultModel()
    {
        $default = null;

        foreach ($this->models as $model) {
            if ($model['default']) {
                return $default = $model;
            }
        }

        return $default;
    }

    /**
     * @return array
     */
    private function getSkipFieldsForModel(): array
    {
        return $this->skipFields;
    }

    /**
     * @return array
     */
    public function getSkipFieldsForForm(): array
    {
        return array_merge($this->skipFields, ['id']);
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }

    /**
     * Automatically creating translation model
     * @param array $model
     * @return array
     */
    private function createTranslationsModel(array $model): array
    {
        return [
            'tableName' => $model['tableName'] . '_translation',
            'modelName' => $model['modelName'] . 'Translation',
            'use' => ['base'],
        ];
    }
}
