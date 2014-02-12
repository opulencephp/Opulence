<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Creates API keys
 */
namespace API\V1\Authentication\Tokens\Factories\APIKey;
use API\V1\Authentication\Tokens;

require_once(__DIR__ . "/../../Token.php");

class Factory
{
    /** The number of characters to include in our key */
    const NUM_CHARS = 32;
    /** The lifetime, in seconds, of the key */
    const LIFETIME = 86400;

    /**These are the characters that comprise our key */
    private static $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * Generates an API key
     *
     * @return Tokens\Token A new API key
     */
    public function createAPIKey()
    {
        // Start with the current timestamp to minimize chance of key collision
        $tokenString = time();
        $numPossibleChars = count(self::$chars);

        for($charIter = 0;$charIter < $numPossibleChars;$charIter++)
        {
            $tokenString .= self::$chars[rand(0, $numPossibleChars - 1)];
        }

        $hashedTokenString = hash("sha256", $tokenString);
        // Set the expiration to some time far in the future
        $expiration = new \DateTime("now", new \DateTimeZone("UTC"));
        $expiration->setTimestamp(time() + self::LIFETIME);

        return new Tokens\Token($hashedTokenString, $expiration);
    }
} 