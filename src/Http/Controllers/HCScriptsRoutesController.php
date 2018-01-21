<?php

namespace HoneyComb\Scripts\Http\Controllers;

use App\Http\Controllers\Controller;
use HoneyComb\Scripts\DTO\HCServiceDTO;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCScriptsRoutesController extends Controller
{
    /**
     * @var HCServiceDTO
     */
    private $config;

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

        $this->generateAdmin();
        $this->generateApi();
        $this->updateHCConfig();
    }

    /**
     * Generating Admin routes
     */
    private function generateAdmin()
    {
        $actions = $this->config->getActions()->getAdmin();

        if (empty($actions)) {
            return;
        }

        $idActions = $this->getIdsActions($this->config->getActions()->getAdmin(), 'admin');

        $urlActions = $this->getUrlActions($this->config->getActions()->getAdmin(), 'admin');

        if ($idActions == "" && $urlActions == "") {
            $actionList = "";
        } else {
            $actionList = $this->helper->createFileFromTemplate("", "service/routes/actions.url.hctpl",
                [
                    "idActions" => $idActions,
                    "urlActions" => $urlActions,
                ], false);
        }

        $this->helper->createFileFromTemplate($this->getFullRoutePath($this->getRoutePath('Admin')), 'service/routes/route.admin.hctpl',
            [
                "index" => $this->getIndex($this->config->getActions()->getAdmin(), 'admin'),
                "actions" => $actionList,
                "url" => $this->config->getUrl()
            ]);
    }
    private function generateApi()
    {
    }

    /**
     * Generating file destination
     * @param string $path
     * @return string
     */
    private function getFullRoutePath(string $path)
    {
        return $this->config->getDirectory() . $path;
    }

    private function getRoutePath($directory)
    {
        return  'Routes/' . $directory . '/routes.' . $this->config->getRouteName() . 's.php';
    }

    /**
     * Getting index route
     *
     * @param array $actions
     * @return string
     */
    private function getIndex (array $actions): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.index.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_list";
        }

        return $php;
    }

    /**
     * Getting all actions which will be required for /{id}/
     *
     * @param array $actions
     * @return string
     */
    private function getIdsActions(array $actions): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.single.hctpl",
                $this->config->jsonSerialize(), false);
        }

        if (in_array("update", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.update.hctpl",
                $this->config->jsonSerialize(), false);
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.patch.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_update";
        }

        if ($php != "") {
            $php = $this->helper->createFileFromTemplate("", "service/routes/actions.id.hctpl",
                ["actions" => $php], false);
        }

        return $php;
    }

    /**
     * Getting all actions which will be required for /{id}/
     *
     * @param array $actions
     * @return string
     */
    private function getUrlActions(array $actions): string
    {
        $php = "";

        if (in_array("list", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.list.hctpl",
                $this->config->jsonSerialize(), false);
        }

        if (in_array("create", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.create.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_create";
        }

        if (in_array("delete", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.delete.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_delete";
        }

        if (in_array("delete_force", $actions)) {
            $php .= $this->helper->createFileFromTemplate("",
                "service/routes/action.delete.force.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_delete_force";
        }

        if (in_array("merge", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.merge.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_merge";
        }

        if (in_array("clone", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.clone.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_clone";
        }

        if (in_array("restore", $actions)) {
            $php .= $this->helper->createFileFromTemplate("", "service/routes/action.restore.hctpl",
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . "_restore";
        }

        return $php;
    }

    /**
     * Updating hc-config file
     */
    private function updateHCConfig()
    {
        $packageConfig = $this->config->getPackageConfig();

        $permission = [

            "name" => "acl." . $this->config->getRouteName(),
            "controller" => $packageConfig->getNamespaceForController($this->config->getServiceName()),
            "actions" => $this->permissions
        ];

        $this->config->updatePackagePermissions($permission);

        if ($this->config->getActions()->getAdmin())
            $this->config->updatePackageRoutes($this->getRoutePath('Admin'));

    }
}