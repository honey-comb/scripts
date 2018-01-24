<?php

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCScriptsServiceController extends Controller
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
     */
    public function generate(HCServiceDTO &$config)
    {
        $this->config = $config;

        $this->generateService ();
    }

    private function generateService()
    {
        $data = [
            "serviceName" => $this->config->getServiceName(),
            "namespace" => $this->config->getPackageConfig()->getNamespaceForService()
        ];

        $destination = $this->config->getDirectory() . 'Services/' . $data['serviceName'] . 'Service'  . '.php';

        $this->helper->createFileFromTemplate($destination, 'service/service.hctpl', $data);
    }
}