<?php

namespace App\Flickr\Mock;

use ReflectionClass;

abstract class AbstractMocker
{
    public function getResponse(string $fileName): array
    {
        return $this->cleanTextNodes(json_decode(file_get_contents(__DIR__ . '/../Tests/Fixtures/' . $fileName . '.json'), true));
    }

    public function __call(string $name, array $arguments)
    {
        $classname = (new ReflectionClass($this))->getShortName();
        $fileName = str_replace('api', '', strtolower($classname));
        $method = str_replace('get', '', strtolower($name));

        return $this->cleanTextNodes(json_decode(file_get_contents(__DIR__ . '/../Tests/Fixtures/' . $fileName . '_' . $method . '.json'), true));
    }

    /**
     * Normalize text nodes in API results.
     * @param mixed $arr The node to normalize.
     * @return mixed
     * @private
     */
    private function cleanTextNodes($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        } elseif (count($arr) == 0) {
            return $arr;
        } elseif (count($arr) == 1 && array_key_exists('_content', $arr)) {
            return $arr['_content'];
        }
        foreach ($arr as $key => $element) {
            $arr[$key] = $this->cleanTextNodes($element);
        }
        return ($arr);
    }
}
