<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;

class HCPackageDTO extends HCBaseDTO
{
    /**
     * Official package name
     * @var string
     */
    private $packageName;

    /**
     * Main namespace
     * @var string
     */
    private $namespace;

    /**
     * Forms config
     * @var array
     */
    private $formData;

    /**
     * Admin menu config
     * @var array
     */
    private $adminMenu;

    /**
     * List of routes which will be later loaded and cached
     *
     * @var array
     */
    private $routes;

    /**
     * List of permissions available for this package
     *
     * @var array
     */
    private $permissions;

    /**
     * Default package configuration
     * @var array
     */
    private $data;

    /**
     * HCPackageDTO constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        
        $this->packageName = $data['general']['packageName'];
        $this->namespace = $data['general']['namespace'];

        $this->formData = $data['formData'];
        $this->adminMenu = $data['adminMenu'];

        $this->routes = $data['routes'];
        $this->permissions = $data['acl']['permissions'];
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @return array
     */
    public function getAdminMenu(): array
    {
        return $this->adminMenu;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Getting namespace for controller
     *
     * @param string $serviceName
     * @return string
     */
    public function getNamespaceForAdminController(string $serviceName = "")
    {
        if ($serviceName === "")
            return $this->namespace;

        return $this->namespace . 'Http\Controllers\\Admin\\' . $serviceName . 'Controller';
    }

    /**
     * Getting namespace for model
     *
     * @return string
     */
    public function getNamespaceForModel()
    {
        return $this->namespace . 'Models';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getNamespaceForForm()
    {
        return $this->namespace . 'Forms';
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        // TODO: Implement jsonData() method.
    }
}