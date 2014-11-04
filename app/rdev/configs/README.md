# Configs

## Table of Contents
1. [Introduction](#introduction)
2. [Basic Usage](#basic-usage)
3. [Config Readers](#config-readers)
  1. [PHP Array Config Reader](#php-array-config-reader)
  2. [JSON Config Reader](#json-config-reader)
  3. [YAML Config Reader](#yaml-config-reader)

## Introduction
Sometimes, it's easier to have configuration files to make it easy to swap out features/settings.  **RDev** allows you to create configs to easily read and validate settings.  Using *config readers*, you can read a config from a file or from input to a method.

> **Note:** To make your application as de-coupled as possible, it's strongly recommended that classes are not directly instantiated with configs.  Instead, create a **factory** class that takes a config as a parameter, extracts data from the config, and then instantiates a class with the extracted data.  Using this method decouples classes from their configs.

## Basic Usage
A basic config class is already set up at `RDev\Configs\Config`.  It provides methods for validating, initializing from an array, and converting to an array.  Additionally, it implements `\ArrayAccess`, allowing you to use the config like an array.

##### Validation
By default, `isValid()` returns true.  However, you can override `isValid()` in your custom config class with your own validation logic to prevent invalid configs.  To require certain fields, you can take advantage of `hasRequiredFields()`:
```php
use RDev\Configs;

class MyConfig extends Configs\Config
{
    protected function isValid(array $configArray)
    {
        // The 2nd parameter is an array whose structure our config array must adhere to
        // In this case, all we care about is that the correct keys are set, which is why the values are all set to null
        return $this->hasRequiredFields($configArray, [
            "websiteURL" => null,
            "databaseSettings" => [
                "host" => null,
                "username" => null,
                "password" => null
            ]
        ]);
    }
}

$myConfig = new MyConfig([
    "websiteURL" => "http://www.example.com",
    "databaseSettings" => [
        "host" => "127.0.0.1",
        "username" => "foo",
        "password" => "bar"
    ]
]);
// We can use $myConfig as an array
echo $myConfig["websiteURL"]; // "http://www.example.com"
echo $myConfig["databaseSettings"]["host"]; // "127.0.0.1"
```

##### Converting From an Array
All configs must implement `exchangeArray()`.  In this method, you should call `isValid()`, and then proceed to convert the array into a config, setting the object's config array at the end.  It is a good place to convert certain pieces of data into new objects.  For example, say we allow an SQL driver to be passed in the array, but we also allow the name of a SQL driver class to be passed in.  In `exchangeArray()`, convert the name of the driver class to an actual instance of that class:
```php
class MyConfig extends Configs\Config
{
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid config");
        }

        // If it's already a driver, don't bother converting
        if(!$configArray["driver"] instanceof RDev\Databases\SQL\IDriver)
        {
            // Ensure that it points to a valid class name
            if(!is_string($configArray["driver"]) || !class_exists($configArray["driver"]))
            {
                throw new \RuntimeException("Invalid driver class name");
            }

            $driver = new $configArray["driver"]();

            // Ensure that it implements IDriver
            if(!$driver instanceof RDev\Databases\SQL\IDriver)
            {
                throw new \RuntimeException("Driver does not implement IDriver");
            }

            // Now that we have our instantiated driver, replace the item in the config array with the driver
            $configArray["driver"] = $driver;
        }

        // Set our object's array now that we're done with the conversions
        $this->configArray = $configArray;
    }
}

$myConfigWithDriverObject = new MyConfig(["driver" => new MyDriver()]); // Valid
$myConfigWithDriverClassName = new MyConfig(["driver" => "Fully\\Qualified\\Name\\Of\\My\\Driver\\Class"]); // Valid
echo $myConfigWithDriverClassName["driver"] instanceof RDev\Databases\SQL\IDriver; // "1"
```

## Config Readers
*Config readers* allow you to read configs from files or from input to a method.  They read configs and save them to the config class of your choice, or `RDev\Configs\Config` by default.  If you want to use your own config class, just implement `RDev\Configs\IConfig`.  There are a few config readers already built in, but you can write your own by extending `RDev\Configs\Readers\Reader`.

##### Saving to a Custom Config Class
```php
use RDev\Configs\Readers;

class MyReader extends Readers\Reader
{
    public function readFromFile($path, $configClassName = "RDev\\Configs\\Config")
    {
        // Read from the file...
    }

    public function readFromInput($input, $configClassName = "RDev\\Configs\\Config")
    {
        // Read from the input...
    }
}

$reader = new MyReader();
$config = $reader->readFromFile(PATH_TO_CONFIG_FILE, "Fully\\Qualified\\Name\\Of\\My\\Config\\Class");
echo get_class($config); // "Fully\\Qualified\\Name\\Of\\My\\Config\\Class"
```

#### PHP Array Config Reader
If your configuration is stored in a PHP array, you can use a `PHPArrayReader` to read from it:

##### PHP Config File
```php
<?php
return [
    "websiteURL" => "http://www.example.com"
];
```
##### Reading From the File
```php
use RDev\Configs\Readers;

$reader = new Readers\PHPArrayReader();
$config = $reader->readFromFile(PATH_TO_PHP_CONFIG_FILE);
echo $config["websiteURL"]; // "http://www.example.com"
```
##### Reading From Input
```php
use RDev\Configs\Readers;

$reader = new Readers\PHPArrayReader();
$config = $reader->readFromInput(["host" => "192.168.1.1"]);
echo $config["host"]; // "192.168.1.1"
```

#### JSON Config Reader
If your configuration is stored in JSON, you can use a `JSONReader` to read from it:

##### JSON Config File
```javascript
{
    "websiteURL": "http://www.example.com"
}
```
##### Reading From the File
```php
use RDev\Configs\Readers;

$reader = new Readers\JSONReader();
$config = $reader->readFromFile(PATH_TO_JSON_CONFIG_FILE);
echo $config["websiteURL"]; // "http://www.example.com"
```
##### Reading From Input
```php
use RDev\Configs\Readers;

$reader = new Readers\JSONReader();
$config = $reader->readFromInput('{"host":"192.168.1.1"}');
echo $config["host"]; // "192.168.1.1"
```

#### YAML Config Reader
If your configuration is stored in YAML, you can use a `YAMLReader` to read from it:

##### YAML Config File
```
websiteURL: http://www.example.com
```
##### Reading From the File
```php
use RDev\Configs\Readers;

$reader = new Readers\YAMLReader();
$config = $reader->readFromFile(PATH_TO_YAML_CONFIG_FILE);
echo $config["websiteURL"]; // "http://www.example.com"
```
##### Reading From Input
```php
use RDev\Configs\Readers;

$reader = new Readers\YAMLReader();
$config = $reader->readFromInput('host: 192.168.1.1');
echo $config["host"]; // "192.168.1.1"
```