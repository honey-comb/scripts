<?php

namespace HoneyComb\Scripts\Http\Controllers;

use HoneyComb\Scripts\Console\HCMakePackage;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use Illuminate\Support\Facades\DB;

class HCScriptsModelsController
{
    /**
     * @var HCScriptsHelper
     */
    private $helper;

    private $skipFields = ['count', 'created_at', 'updated_at', 'deleted_at'];

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
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        foreach ($config->getModels() as $model) {
            $this->generateModel($model);
        }
    }

    private function generateModel(array $model)
    {
        $fields = array_pluck($model['fields'], 'Field');

        $data = [
            "namespace" => $this->config->getNamespace('m'),
            "model" => $model['modelName'],
            "table" => $model['tableName'],
        ];

        foreach ($fields as $key => $fieldName) {
            if (!in_array($fieldName, $this->skipFields)) {
                $data['fields'][] = $fieldName;
            }
        }

        if ($this->config->isPackage()) {
            $rootDirectory = HCMakePackage::ROOT_DIRECTORY;
        } else {
            $rootDirectory = '';
        }

        $data['fields'] = '"' . implode('", "', $data['fields']) . '"';

        $modelType = "uuid";

        if (isset($model['type'])) {
            $modelType = $model['type'];
        }

        $this->helper->createFileFromTemplate($rootDirectory . '/' . $this->config->getDirectory() . "/Models/" . $data['model'] . '.php',
            'service/models/' . $modelType . '.hctpl',
            $data);
    }
}