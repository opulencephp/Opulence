<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the form middleware
 */
namespace Opulence\Forms;

use Closure;
use Opulence\HTTP\Middleware\IMiddleware;
use Opulence\HTTP\Requests\Request;

class Middleware implements IMiddleware
{
    /** @var FormRequest The form request to handle */
    protected $formRequest = null;

    /**
     * @param FormRequest $formRequest The form request to handle
     */
    public function __construct(FormRequest $formRequest)
    {
        $this->formRequest = $formRequest;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$this->formRequest->isValid($request))
        {
            // TODO:  Determine which response to return
        }

        return $next($request);
    }
}