<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;
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
            $this->helper->abort("Table not found: " . $tableName);
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
    public function getFieldsForModel (array &$model)
    {
        $fields = array_pluck($model['fields'], 'Field');

        foreach ($fields as $key => $fieldName) {
            if (!in_array($fieldName, $this->getSkipFieldsForModel())) {
                $model['fieldsModel'][] = $fieldName;
            }
        }
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
    private function getSkipFieldsForForm(): array
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
}