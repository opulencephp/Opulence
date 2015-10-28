<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file parameters
 */
namespace Opulence\HTTP\Requests;

use Opulence\HTTP\Collection;

class Files extends Collection
{
    /**
     * @inheritdoc
     */
    public function add($name, $value)
    {
        $this->values[$name] = new UploadedFile(
            $value["tmp_name"],
            $value["name"],
            $value["size"],
            $value["type"],
            $value["error"]
        );
    }

    /**
     * @inheritdoc
     * @return UploadedFile|mixed
     */
    public function get($name, $default = null)
    {
        return parent::get($name, $default);
    }

    /**
     * @inheritdoc
     * @return UploadedFile[]
     */
    public function getAll()
    {
        return parent::getAll();
    }
}