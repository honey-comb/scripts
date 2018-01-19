<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;

class HCPackageDTO extends HCBaseDTO
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    public $packagePath;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $namespaceComposer;

    /**
     * @var string
     */
    public $packageName;

    /**
     * HCPackageDTO constructor.
     * @param array $data
     */
    public function __construct(
        array $data
    )
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getFolderList(): array
    {
        return $this->data['folders'];
    }

    /**
     * @return array
     */
    public function getFilesList(): array
    {
        return $this->data['files'];
    }

    /**
     * @return array
     */
    public function jsonData(): array
    {
        return get_object_vars ($this);
    }
}