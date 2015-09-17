<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the file-based cache bridge
 */
namespace Opulence\Cache;

class FileBridge implements ICacheBridge
{
    /** @var string The path to the files */
    private $path = "";

    /**
     * @param string $path The path to the files
     */
    public function __construct($path)
    {
        $this->path = rtrim($path, "/");

        if(!file_exists($this->path))
        {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        $parsedData = $this->parseData($key);
        $incrementedValue = (int)$parsedData["d"] - $by;
        $this->set($key, $incrementedValue, $parsedData["t"]);

        return $incrementedValue;
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        @unlink($this->getPath($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        foreach(glob("{$this->path}/*") as $file)
        {
            if(is_file($file))
            {
                @unlink($file);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->parseData($key)["d"];
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        $parsedData = $this->parseData($key);

        // We want to return true even if the data is null
        // So, we look at the lifetime property
        return $parsedData["t"] !== 0;
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        $parsedData = $this->parseData($key);
        $incrementedValue = (int)$parsedData["d"] + $by;
        $this->set($key, $incrementedValue, $parsedData["t"]);

        return $incrementedValue;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        file_put_contents($this->getPath($key), $this->serialize($value, $lifetime));
    }

    /**
     * Gets the path for a given key
     *
     * @param string $key The key to get
     * @return string The path to the key
     */
    protected function getPath($key)
    {
        return $this->path . "/" . md5($key);
    }

    /**
     * Runs garbage collection on a key, if necessary
     *
     * @param string $key The key to run garbage collection on
     * @return array The array of data after running any garbage collection
     */
    protected function parseData($key)
    {
        if(file_exists($this->getPath($key)))
        {
            $rawData = json_decode(file_get_contents($this->getPath($key)), true);
            $parsedData = ["d" => unserialize($rawData["d"]), "t" => $rawData["t"]];
        }
        else
        {
            $parsedData = ["d" => null, "t" => 0];
        }

        if(time() > $parsedData["t"])
        {
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
    protected function serialize($data, $lifetime)
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
    protected function unserialize($data)
    {
        $unserializedData = json_decode($data, true);
        $unserializedData["d"] = unserialize($unserializedData["d"]);

        return $unserializedData;
    }
}