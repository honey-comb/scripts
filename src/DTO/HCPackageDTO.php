<?php

namespace HoneyComb\Scripts\DTO;

class HCPackageDTO
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    public $packageName;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $namespaceComposer;

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
}