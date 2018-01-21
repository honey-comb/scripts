<?php

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\Console\HCMakePackage;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCScriptsFormsController extends Controller
{
    /**
     * @var HCServiceDTO
     */
    private $config;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var array
     */
    private $permissions = [];

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
     */
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        $model = $this->config->getDefaultModel();

        if ($model == null)
            $this->helper->abort("No default table is set.");

        $list = [];

        foreach ($model['fields'] as $field)
        {
            $fieldData = [];

            if (!in_array($field->Field, $this->config->getSkipFields()))
            {
                $fieldData['field'] = $field->Field;

                if ($field->Null == "NO")
                    $fieldData['required'] = '"required" => 1';

                $fieldData['type'] = $this->getFieldType($field->Type);

                $fieldData['label'] = '"label" => trans("' . $this->helper->getPackageConfig() . '"';

                $list[] = $this->helper->createFileFromTemplate("", "service/forms/field.hctpl", $fieldData, false);
            }
        }

        dd($list);
    }

    private function getFieldType(string $type)
    {
        if (strpos($type, 'char') !== false)
        {
            preg_match_all('!\d+!', $type, $matches);

            if ($matches[0][0] > 255)
                return '"textArea"';

            return '"singleLine"';
        }

        if (strpos($type, 'int') !== false)
            return '"number"';

        if (strpos($type, 'text') !== false)
            return '"richText"';

        if (strpos($type, 'date') !== false || strpos($type, 'time') !== false)
            return '"dateTimePicker"';

        return "";
    }
}