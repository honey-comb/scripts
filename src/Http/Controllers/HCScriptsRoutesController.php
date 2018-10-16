<?php
/**
 * @copyright 2018 innovationbase
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
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

declare(strict_types = 1);

namespace HoneyComb\Scripts\Http\Controllers;

use HoneyComb\Scripts\Helpers\HCScriptsHelper;
use HoneyComb\Scripts\Http\Resources\HCServiceResource;

/**
 * Class HCScriptsRoutesController
 * @package HoneyComb\Scripts\Http\Controllers
 */
class HCScriptsRoutesController
{
    /**
     * @var HCServiceResource
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
     * @param HCServiceResource $config
     */
    public function generate(HCServiceResource &$config)
    {
        $this->config = $config;

        $this->generateAdmin();
        $this->generateApi();
        $this->updateRoutes();
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

        if ($idActions == '' && $urlActions == '') {
            $actionList = '';
        } else {
            $actionList = $this->helper->createFileFromTemplate('', 'service/routes/actions.url.hctpl',
                [
                    'idActions' => $idActions,
                    'urlActions' => $urlActions,
                ], false);
        }

        $this->helper->createFileFromTemplate($this->getFullRoutePath($this->getRoutePath('Admin')),
            'service/routes/route.admin.hctpl',
            [
                'index' => $this->getIndex($this->config->getActions()->getAdmin(), 'admin'),
                'actions' => $actionList,
                'url' => $this->config->getUrl(),

                'serviceName' => $this->config->getServiceName(),
                'routeName' => $this->config->getRouteName(),
                'aclPrefix' => $this->config->getAclPrefix()
            ]);
    }

    /**
     *
     */
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

    /**
     * @param $directory
     * @return string
     */
    private function getRoutePath($directory)
    {
        return 'Routes/' . $directory . '/routes.' . $this->config->getRouteName() . '.php';
    }

    /**
     * Getting index route
     *
     * @param array $actions
     * @return string
     */
    private function getIndex(array $actions): string
    {
        $php = '';

        if (in_array('list', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.index.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_list';
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
        $php = '';

        if (in_array('list', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.single.hctpl',
                $this->config->jsonSerialize(), false);
        }

        if (in_array('update', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.update.hctpl',
                $this->config->jsonSerialize(), false);
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.patch.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_update';
        }

        if ($php != '') {
            $php = $this->helper->createFileFromTemplate('', 'service/routes/actions.id.hctpl',
                ['actions' => $php], false);
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
        $php = '';

        if (in_array('list', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.list.hctpl',
                $this->config->jsonSerialize(), false);
        }

        if (in_array('create', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.create.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_create';
        }

        if (in_array('delete', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.delete.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_delete';
        }

        if (in_array('delete_force', $actions)) {
            $php .= $this->helper->createFileFromTemplate('',
                'service/routes/action.delete.force.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_delete_force';
        }

        if (in_array('merge', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.merge.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_merge';
        }

        if (in_array('clone', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.clone.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_clone';
        }

        if (in_array('restore', $actions)) {
            $php .= $this->helper->createFileFromTemplate('', 'service/routes/action.restore.hctpl',
                $this->config->jsonSerialize(), false);

            $this->permissions[] = $this->config->getAclPrefix() . '_restore';
        }

        return $php;
    }

    /**
     * Updating hc-config file
     */
    private function updateRoutes()
    {
        $packageConfig = $this->config->getPackageConfig();

        $permission = [

            'name' => 'acl.' . $this->config->getRouteName(),
            'controller' => $packageConfig->getNamespaceForAdminController(true),
            'actions' => $this->permissions,
        ];

        $this->config->updatePackagePermissions($permission);

        if ($this->config->getActions()->getAdmin()) {
            $this->config->updatePackageRoutes($this->getRoutePath('Admin'));

            $adminMenu = [

                'route' => 'admin.' . $this->config->getRouteName() .'.index',
                'parent' => 'admin.index',
                'translation' => $this->config->getTranslation()->getLabelFieldForForm('page_title'),
                'icon' => $this->config->getIcon(),
                'aclPermission' => $this->config->getAclPrefix() . '_list',
            ];

            $this->config->updateAdminMenu($adminMenu);

        }
    }
}
