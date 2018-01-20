<?php

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\Console\HCMakePackage;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCScriptsRoutesController extends Controller
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
    public function generate(HCServiceDTO $config)
    {
        $this->config = $config;

        $this->generateAdmin();
        $this->generateApi();
        $this->generateFront();
        $this->updateHCConfig();
    }

    /**
     * Generating Admin routes
     */
    private function generateAdmin()
    {
        $routes = $this->config->getRoutesApi();

        if (empty($routes)) {
            return;
        }

        if ($this->config->isPackage()) {
            $this->rootDirectory = HCMakePackage::ROOT_DIRECTORY;
            $destination = $this->getFullPath('Admin');
        } else {
            $this->rootDirectory = '';
            $destination = '';
        }

        $idActions = $this->getIdsActions($this->config->getRoutesAdmin(), 'admin');
        $urlActions = $this->getUrlActions($this->config->getRoutesAdmin(), 'admin');

        if ($idActions == "" && $urlActions == "") {
            $actionList = "";
        } else {
            $actionList = $this->helper->createFileFromTemplate("", "service/routes/admin/actions.url.hctpl",
                [
                    "idActions" => $idActions,
                    "urlActions" => $urlActions,
                ], false);
        }

        $this->helper->createFileFromTemplate($destination, 'service/routes/route.admin.hctpl',
            [
                "index" => $this->getIndex($this->config->getRoutesAdmin(), 'admin'),
                "actions" => $actionList
            ]);
    }

    private function generateApi()
    {
    }

    private function generateFront()
    {
    }

    private function getFullPath(string $directory)
    {
        return $this->getDirectory($directory) . $this->getFileName();
    }

    private function getFileName()
    {
        return 'routes.' . $this->config->getRoutesPrefix() . 's.php';
    }

    private function getDirectory($directory)
    {
        return $this->rootDirectory . DIRECTORY_SEPARATOR . $this->config->getDirectory() . DIRECTORY_SEPARATOR . 'Routes/' . $directory . '/';
    }

    /**
     * Getting index route
     *
     * @param array $actions
     * @param string $prefix
     * @return string
     */
    private function getIndex (array $actions, string $prefix): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.index.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_list";
        }

        return $php;
    }

    /**
     * Getting all actions which will be required for /{id}/
     *
     * @param array $actions
     * @param string $prefix
     * @return string
     */
    private function getIdsActions(array $actions, string $prefix): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.single.hctpl",
                $this->config->jsonSerialize(), false);
        }

        if (in_array("update", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.update.hctpl",
                $this->config->jsonSerialize(), false);
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.patch.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_update";
        }

        if ($php != "") {
            $php = $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/actions.id.hctpl",
                ["actions" => $php], false);
        }

        return $php;
    }

    /**
     * Getting all actions which will be required for /{id}/
     *
     * @param array $actions
     * @param string $prefix
     * @return string
     */
    private function getUrlActions(array $actions, string $prefix): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.list.hctpl",
                $this->config->jsonSerialize(), false);
        }

        if (in_array("create", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.create.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_create";
        }

        if (in_array("delete", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.delete.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_delete";
        }

        if (in_array("delete_force", $actions)) {
            $php .= $this->helper->createFileFromTemplate("",
                "service/routes/" . $prefix . "/action.delete.force.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_delete_force";
        }

        if (in_array("merge", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.merge.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_merge";
        }

        if (in_array("clone", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.clone.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_clone";
        }

        if (in_array("restore", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/" . $prefix . "/action.restore.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_". $prefix. "_restore";
        }

        return $php;
    }

    /**
     * Updating hc-config file
     */
    private function updateHCConfig()
    {
        $directory = $this->rootDirectory . DIRECTORY_SEPARATOR . $this->config->getDirectory();

        $hcConfig = $this->helper->getHCConfig ($directory);

        $po = [

            "name" => "admin.acl." . $this->config->getRoutesPrefix(),
            "controller" => $this->config->getNamespace() . $this->config->getServiceName() . "Controller",
            "actions" => $this->permissions
        ];

        $hcConfig['acl']['permissions'][] = $po;

        $this->helper->setHCConfig($directory, $hcConfig);
    }
}