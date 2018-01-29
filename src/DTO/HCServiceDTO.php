<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Starter\DTO\HCBaseDTO;;
use HoneyComb\Scripts\Console\HCMakePackage;
use HoneyComb\Scripts\Helpers\HCScriptsHelper;

class HCServiceDTO extends HCBaseDTO
{
    /**
     * Scripts helper
     * @var HCScriptsHelper
     */
    private $helper;

    /**
     * Is service meant for package of for app
     * @var bool
     */
    private $package;

    /**
     * Directory where service will be stored
     *
     * @var mixed
     */
    private $directory;

    /**
     * Is service multi language
     * @var bool
     */
    private $isMultiLanguage;

    /**
     * Icon for admin menu
     * @var string
     */
    private $icon;

    /**
     * Package configuration
     * @var HCPackageDTO
     */
    private $packageConfig;

    /**
     * Route name which will be used in routes
     * @var string
     */
    private $routeName;

    /**
     * ACL prefix for actions
     * @var string
     */
    private $aclPrefix;

    /**
     * Service name
     *
     * @var string
     */
    private $serviceName;

    /**
     * Service URL
     * @var string
     */
    private $url;

    /**
     * Is new form available
     * @var bool
     */
    private $newForm;

    /**
     * Is edit form available
     * @var bool
     */
    private $editForm;

    /**
     * Setting
     * @var HCServiceActionsDTO
     */
    private $actions;

    /**
     * @var HCServiceModelsDTO
     */
    private $modelConfig;

    /**
     * Translations
     * @var HCTranslationsDTO
     */
    private $translation;


    /**
     * HCServiceDTO constructor.
     * @param array $data
     * @param $helper
     */
    public function __construct(array $data, $helper)
    {
        $this->helper = $helper;

        $this->directory = $data['directory'];
        $this->package = $this->directory === "" ? false : true;
        $this->isMultiLanguage = (bool)$data['multiLanguage'];
        $this->icon = $data['icon'];
        $this->url = $data['url'];
        $this->routeName = $this->helper->stringWithDots(strtolower($this->url));
        $this->aclPrefix = $this->helper->stringWithUnderscore(strtolower($this->directory . '_' . $this->routeName));
        $this->serviceName = $data['serviceName'];

        if (in_array("new", $data["forms"])) {
            $this->newForm = true;
        }

        if (in_array("edit", $data["forms"])) {
            $this->editForm = true;
        }

        $this->packageConfig = new HCPackageDTO(json_decode(file_get_contents($this->getDirectory() . 'hc-config.json'),
            true));

        $this->actions = new HCServiceActionsDTO($data['actions']);
        $this->modelConfig = new HCServiceModelsDTO($data['models'], $this->helper);

        $this->translation = new HCTranslationsDTO($this->modelConfig, $this->helper);
        $this->translation->setRootDirectory($this->packageConfig->getPackageName(), $this->getDirectory());
        $this->translation->setTranslationPrefix($this->getUrl());

        /*dd($this->isEditForm(), $this->isNewForm());

        return;

        $this->models = $data['database'];

        foreach ($this->models as &$value) {
            $value['fields'] = $this->getTableColumns($value['tableName']);
        }

        $this->routesAdmin = $data['services']['admin'];
        $this->routesApi = $data['services']['api'];
        $this->routesFront = $data['services']['front'];*/
    }

    /**
     * @return bool
     */
    public function isPackage(): bool
    {
        return $this->package;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        if ($this->package) {
            return HCMakePackage::ROOT_DIRECTORY . DIRECTORY_SEPARATOR . $this->directory . '/src/';
        }

        return 'app/';
    }

    /**
     * @return bool
     */
    public function isMultiLanguage(): bool
    {
        return $this->isMultiLanguage;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return HCPackageDTO
     */
    public function getPackageConfig(): HCPackageDTO
    {
        return $this->packageConfig;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getAclPrefix(): string
    {
        return $this->aclPrefix;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isNewForm(): bool
    {
        return $this->newForm;
    }

    /**
     * @return bool
     */
    public function isEditForm(): bool
    {
        return $this->editForm;
    }

    /**
     * @return HCServiceModelsDTO
     */
    public function getModelConfig(): HCServiceModelsDTO
    {
        return $this->modelConfig;
    }

    /**
     * @return HCServiceActionsDTO
     */
    public function getActions(): HCServiceActionsDTO
    {
        return $this->actions;
    }

    /**
     * @return HCTranslationsDTO
     */
    public function getTranslation(): HCTranslationsDTO
    {
        return $this->translation;
    }

    /**
     * Updating package permissions
     * @param array $data
     */
    public function updatePackagePermissions(array $data)
    {
        $config = $this->helper->getHCConfig($this->getDirectory());
        $existing = false;

        if (isset($config['acl']['permissions']['name'])) {
            if ($config['acl']['permissions']['name'] == $data['name']) {
                $existing = true;
            }

            if (!$existing) {
                $config['acl']['permissions'] = [$config['acl']['permissions'], $data];
            }
        } else {
            foreach ($config['acl']['permissions'] as $permission) {
                if ($permission['name'] == $data['name']) {
                    $existing = true;
                }
            }

            if (!$existing) {
                $config['acl']['permissions'] = array_merge($config['acl']['permissions'], $data);
            }
        }

        $this->helper->setHCConfig($this->getDirectory(), $config);
    }

    /**
     * Updating package routes
     * @param string $route
     */
    public function updatePackageRoutes(string $route)
    {
        $config = $this->helper->getHCConfig($this->getDirectory());

        if (!in_array($route, $config['routes']))
            $config['routes'][] = $route;

        $this->helper->setHCConfig($this->getDirectory(), $config);
    }

    /**
     * Updating package routes
     * @param string $name
     * @param string $namespace
     */
    public function updateForm(string $name, string $namespace)
    {
        $config = $this->helper->getHCConfig($this->getDirectory());

        if (!isset($config['formData'][$name]))
            $config['formData'][$name] = $namespace;

        $this->helper->setHCConfig($this->getDirectory(), $config);
    }

    /**
     * Updating package routes
     * @param array $adminMenu
     */
    public function updateAdminMenu(array $adminMenu)
    {
        $config = $this->helper->getHCConfig($this->getDirectory());

        $config['adminMenu'][] = $adminMenu;

        $this->helper->setHCConfig($this->getDirectory(), $config);
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}
