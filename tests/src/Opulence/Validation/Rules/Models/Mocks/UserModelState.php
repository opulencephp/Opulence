<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Validation\Rules\Models\Mocks;

use Opulence\Validation\IValidator;
use Opulence\Validation\Models\ModelState;

/**
 * Mocks the user model state for use in testing
 */
class UserModelState extends ModelState
{
    /**
     * @inheritdoc
     * @param User $model
     */
    protected function getModelProperties($model) : array
    {
        return [
            'id' => $model->getId(),
            'email' => $model->getEmail(),
            'name' => $model->getName()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function registerFields(IValidator $validator)
    {
        $validator->field('id')
            ->integer();
        $validator->field('name')
            ->required();
        $validator->field('email')
            ->email();
    }
}
