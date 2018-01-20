<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;

class HCServiceDTO extends HCBaseDTO
{
    private $directory;
    private $url;
    private $isPackage;
    private $multiLanguage;
    private $icon;

    private $routesAdmin;
    private $routesApi;
    private $routesFront;
    private $routePrefix;
    private $serviceName;
    private $aclPrefix;

    /**
     * HCPackageDTO constructor.
     * @param array $data
     */
    public function __construct(array $data) {

        $this->directory = $data['directory'];
        $this->url = $data['url'];
        $this->multiLanguage = $data['multiLanguage'];
        $this->icon = $data['icon'];
        $this->routePrefix = $data['routePrefix'];
        $this->aclPrefix = $data['aclPrefix'];
        $this->serviceName = $data['serviceName'];
        $this->isPackage = $this->directory === "" ? false : true;

        $this->routesAdmin = $data['services']['admin'];
        $this->routesApi = $data['services']['api'];
        $this->routesFront = $data['services']['front'];
    }

    /**
     * @return mixed
     */
    public function getRoutesApi()
    {
        return $this->routesApi;
    }

    /**
     * @return bool
     */
    public function isPackage(): bool
    {
        return $this->isPackage;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        if ($this->isPackage)
            return $this->directory . '/src';

        return $this->directory;
    }

    /**
     * @return mixed
     */
    public function getRoutesPrefix()
    {
        return $this->routePrefix;
    }

    /**
     * @return mixed
     */
    public function getRoutesAdmin()
    {
        return $this->routesAdmin;
    }

    /**
     * @return mixed
     */
    public function getAclPrefix()
    {
        return $this->aclPrefix;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}