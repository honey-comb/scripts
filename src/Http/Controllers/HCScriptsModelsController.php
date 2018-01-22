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
            "fields" => '"' . implode('", "', $model['fieldsModel']) . '"',
            "useNamespaces" => $this->getNameSpaces($model['use']),
            "useClassNames" => $this->getClassNames($model['use']),
        ];

        $modelType = "uuid";
        $destination = $this->config->getDirectory() . "Models/" . $data['model'] . '.php';

        $this->helper->createFileFromTemplate($destination, 'service/models/' . $modelType . '.hctpl', $data);
    }

    /**
     * @param array $use
     * @return string
     */
    private function getNameSpaces(array $use): string
    {
        $value = "";

        foreach ($use as $item) {
            switch ($item) {
                case "ownership" :

                    $value .= "use HoneyComb\\Core\\Models\\Traits\\HCOwnership;\r\n";
                    break;

                case "translations" :

                    $value .= "use HoneyComb\\Core\\Models\\Traits\\HCTranslation;\r\n";
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
        $value = "";

        foreach ($use as $item) {
            switch ($item) {
                case "ownership" :

                    $value .= ", Ownership";
                    break;

                case "translations" :

                    $value .= ", HCTranslation";
                    break;
            }
        }

        if ($value != "") {
            $value = "use " . substr($value, 2) . ';';
        }


        return $value;
    }
}