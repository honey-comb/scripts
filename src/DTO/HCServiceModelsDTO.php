<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Starter\DTO\HCBaseDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use Illuminate\Support\Facades\DB;

class HCServiceModelsDTO extends HCBaseDTO
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
     * HCServiceModelsDTO constructor.
     * @param array $models
     * @param HCScriptsHelper $helper
     */
    public function __construct(array $models, HCScriptsHelper $helper)
    {
        $this->helper = $helper;
        $autoModels = [];

        foreach ($models as $model) {
            if (in_array('translations', $model['use'])) {
                $autoModels[] = ($this->createTranslationsModel($model));
            }
        }

        $models = array_merge($models, $autoModels);

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
            'tableName' => $model['tableName'] . '_translations',
            'modelName' => $model['modelName'] . 'Translations',
            'use' => [],
        ];
    }
}
