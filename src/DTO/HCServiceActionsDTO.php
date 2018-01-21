<?php

namespace HoneyComb\Scripts\DTO;

use HoneyComb\Core\DTO\HCBaseDTO;

class HCServiceActionsDTO extends HCBaseDTO
{
    /**
     * @var array
     */
    private $admin;

    /**
     * @var array
     */
    private $api;

    /**
     * @var array
     */
    private $front;

    /**
     * HCServiceActionsDTO constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (isset($data['admin']))
            $this->admin = $data['admin'];
        else
            $this->admin = [];

        if (isset($data['api']))
            $this->api = $data['api'];
        else
            $this->api = [];

        if (isset($data['front']))
            $this->front = $data['front'];
        else
            $this->front = [];
    }

    /**
     * @return array
     */
    public function getFront(): array
    {
        return $this->front;
    }

    /**
     * @return array
     */
    public function getApi(): array
    {
        return $this->api;
    }

    /**
     * @return array
     */
    public function getAdmin(): array
    {
        return $this->admin;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}