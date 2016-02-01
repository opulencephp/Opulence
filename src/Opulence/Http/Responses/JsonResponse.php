<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Responses;

use ArrayObject;
use InvalidArgumentException;

/**
 * Defines a JSON response
 */
class JsonResponse extends Response
{
    /**
     * @param mixed $content The content of the response
     * @param int $statusCode The HTTP status code
     * @param array $headers The headers to set
     * @throws InvalidArgumentException Thrown if the content is not of the correct type
     */
    public function __construct($content = [], int $statusCode = ResponseHeaders::HTTP_OK, array $headers = [])
    {
        parent::__construct($content, $statusCode, $headers);

        $this->headers->set("Content-Type", ResponseHeaders::CONTENT_TYPE_JSON);
    }

    /**
     * @inheritdoc
     * @param mixed $content The content to set
     * @throws InvalidArgumentException Thrown if the input could not be JSON encoded
     */
    public function setContent($content)
    {
        if ($content instanceof ArrayObject) {
            $content = $content->getArrayCopy();
        }

        $json = json_encode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Failed to JSON encode content: " . json_last_error_msg());
        }

        parent::setContent($json);
    }
} 