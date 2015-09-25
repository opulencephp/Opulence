<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file parameters
 */
namespace Opulence\HTTP\Requests;
use Opulence\HTTP\Parameters;

class Files extends Parameters
{
    /**
     * @inheritdoc
     */
    public function add($name, $value)
    {
        $this->parameters[$name] = new UploadedFile(
            $value["tmp_name"],
            $value["name"],
            $value["size"],
            $value["type"],
            $value["error"]
        );
    }

    /**
     * @inheritDoc
     * @return UploadedFile|mixed
     */
    public function get($name, $default = null)
    {
        return parent::get($name, $default);
    }

    /**
     * @inheritDoc
     * @return UploadedFile[]
     */
    public function getAll()
    {
        return parent::getAll();
    }
}