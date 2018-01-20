<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;
use Illuminate\Support\Facades\DB;

class HCServiceDTO extends HCBaseDTO
{
    private $directory;
    private $url;
    private $isPackage;
    private $isMultiLanguage;
    private $icon;

    private $routesAdmin;
    private $routesApi;
    private $routesFront;
    private $routePrefix;
    private $serviceName;
    private $aclPrefix;
    private $namespace;
    private $models;

    /**
     * HCPackageDTO constructor.
     * @param array $data
     */
    public function __construct(array $data) {

        $this->models = $data['database'];

        foreach ($this->models as &$value)
        {
            $value['fields'] = $this->getTableColumns($value['tableName']);
        }

        $this->directory = $data['directory'];
        $this->url = $data['url'];
        $this->isMultiLanguage = $data['multiLanguage'];
        $this->icon = $data['icon'];
        $this->routePrefix = $data['routePrefix'];
        $this->aclPrefix = $data['aclPrefix'];
        $this->serviceName = $data['serviceName'];
        $this->namespace = $data['namespace'];

        $this->isPackage = $this->directory === "" ? false : true;

        $this->routesAdmin = $data['services']['admin'];
        $this->routesApi = $data['services']['api'];
        $this->routesFront = $data['services']['front'];
    }

    /**
     * Getting table columns
     *
     * @param $tableName
     * @return mixed
     */
    private function getTableColumns(string $tableName)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

        if (!count($columns)) {
            $this->abort("Table not found: " . $tableName);
        } else {
            $columns = DB::select(DB::raw('SHOW COLUMNS FROM ' . $tableName));
        }

        return $columns;
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
     * @param string $type
     * @return mixed
     */
    public function getNamespace(string $type = "")
    {

        switch ($type)
        {
            case "c" :

                return $this->namespace . '\Http\Controllers';

            case "m" :

                return $this->namespace . '\Models';
        }
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return mixed
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @return bool
     */
    public function isMultiLanguage(): bool
    {
        return $this->isMultiLanguage;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}