<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Makes an entity class
 */
namespace Opulence\Framework\Console\Commands;

class MakeEntityCommand extends MakeCommand
{
    /**
     * @inheritdoc
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:entity")
            ->setDescription("Creates an entity class");
    }

    /**
     * @inheritdoc
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/Entity.template";
    }
}