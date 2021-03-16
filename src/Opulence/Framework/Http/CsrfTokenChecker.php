<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Http;

use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Sessions\ISession;

/**
 * Defines the CSRF token checker
 */
class CsrfTokenChecker
{
    /** The token input name */
    const TOKEN_INPUT_NAME = '__OPULENCE_CSRF_TOKEN';

    /**
     * Checks if the token is valid
     *
     * @param Request $request The current request
     * @param ISession $session The current session
     * @return bool True if the token is valid, otherwise false
     */
    public function tokenIsValid(Request $request, ISession $session) : bool
    {
        if (!$session->has(self::TOKEN_INPUT_NAME)) {
            $session->set(self::TOKEN_INPUT_NAME, \bin2hex(\random_bytes(16)));
        }

        if ($this->tokenShouldNotBeChecked($request)) {
            return true;
        }

        // Try an input
        $token = $request->getInput(self::TOKEN_INPUT_NAME);

        // Try the X-CSRF header
        if ($token === null) {
            $token = $request->getHeaders()->get('X-CSRF-TOKEN');
        }

        // Try the X-XSRF header
        if ($token === null) {
            $token = $request->getHeaders()->get('X-XSRF-TOKEN');
        }

        if ($token === null) {
            return false;
        }

        return \hash_equals($session->get(self::TOKEN_INPUT_NAME), $token);
    }

    /**
     * Gets whether or not the token should even be checked
     *
     * @param Request $request The current request
     * @return bool True if the token should be checked, otherwise false
     */
    private function tokenShouldNotBeChecked(Request $request) : bool
    {
        return in_array($request->getMethod(), [RequestMethods::GET, RequestMethods::HEAD, RequestMethods::OPTIONS]);
    }
}
