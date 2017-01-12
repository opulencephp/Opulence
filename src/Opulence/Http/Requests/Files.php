<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Requests;

use Opulence\Http\Collection;

/**
 * Defines the file parameters
 */
class Files extends Collection
{
    /**
     * @inheritdoc
     */
    public function add(string $name, $value)
    {
        $this->values[$name] = new UploadedFile(
            $value['tmp_name'],
            $value['name'],
            $value['size'],
            $value['type'],
            $value['error']
        );
    }

    /**
     * @inheritdoc
     * @return UploadedFile|mixed
     */
    public function get(string $name, $default = null)
    {
        return parent::get($name, $default);
    }

    /**
     * @inheritdoc
     * @return UploadedFile[]
     */
    public function getAll() : array
    {
        return parent::getAll();
    }
}
