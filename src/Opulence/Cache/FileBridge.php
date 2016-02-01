<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cache;

/**
 * Defines the file-based cache bridge
 */
class FileBridge implements ICacheBridge
{
    /** @var string The path to the files */
    private $path = "";

    /**
     * @param string $path The path to the files
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, "/");

        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * @inheritdoc
     */
    public function decrement(string $key, int $by = 1) : int
    {
        $parsedData = $this->parseData($key);
        $incrementedValue = (int)$parsedData["d"] - $by;
        $this->set($key, $incrementedValue, $parsedData["t"]);

        return $incrementedValue;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key)
    {
        @unlink($this->getPath($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        foreach (glob("{$this->path}/*") as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        return $this->parseData($key)["d"];
    }

    /**
     * @inheritdoc
     */
    public function has(string $key) : bool
    {
        $parsedData = $this->parseData($key);

        // We want to return true even if the data is null
        // So, we look at the lifetime property
        return $parsedData["t"] !== 0;
    }

    /**
     * @inheritdoc
     */
    public function increment(string $key, int $by = 1) : int
    {
        $parsedData = $this->parseData($key);
        $incrementedValue = (int)$parsedData["d"] + $by;
        $this->set($key, $incrementedValue, $parsedData["t"]);

        return $incrementedValue;
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value, int $lifetime)
    {
        file_put_contents($this->getPath($key), $this->serialize($value, $lifetime));
    }

    /**
     * Gets the path for a given key
     *
     * @param string $key The key to get
     * @return string The path to the key
     */
    protected function getPath(string $key) : string
    {
        return $this->path . "/" . md5($key);
    }

    /**
     * Runs garbage collection on a key, if necessary
     *
     * @param string $key The key to run garbage collection on
     * @return array The array of data after running any garbage collection
     */
    protected function parseData(string $key) : array
    {
        if (file_exists($this->getPath($key))) {
            $rawData = json_decode(file_get_contents($this->getPath($key)), true);
            $parsedData = ["d" => unserialize($rawData["d"]), "t" => $rawData["t"]];
        } else {
            $parsedData = ["d" => null, "t" => 0];
        }

        if (time() > $parsedData["t"]) {
            $this->delete($key);

            return ["d" => null, "t" => 0];
        }

        return $parsedData;
    }

    /**
     * Serializes the data with lifetime information
     *
     * @param mixed $data The data to serialize
     * @param int $lifetime The lifetime in seconds
     * @return string The serialized data
     */
    protected function serialize($data, int $lifetime) : string
    {
        return json_encode(
            ["d" => serialize($data), "t" => time() + $lifetime]
        );
    }

    /**
     * Unserializes the data from storage
     *
     * @param string $data The data to unserialize
     * @return mixed The serialized data
     */
    protected function unserialize(string $data)
    {
        $unserializedData = json_decode($data, true);
        $unserializedData["d"] = unserialize($unserializedData["d"]);

        return $unserializedData;
    }
}