<?php

namespace HoneyComb\Scripts\Http\Controllers;

use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

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
            "namespace" => $this->config->getPackageConfig()->getNamespaceForModel(),
            "model" => $model['modelName'],
            "table" => $model['tableName'],
            "fields" => '"' . implode('", "', $model['fieldsModel']) . '"'
        ];

        $modelType = "uuid";
        $destination = $this->config->getDirectory() . "Models/" . $data['model'] . '.php';

        $this->helper->createFileFromTemplate($destination,'service/models/' . $modelType . '.hctpl', $data);
    }
}