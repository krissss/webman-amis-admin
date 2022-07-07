<?php

namespace Kriss\WebmanAmisAdmin\Amis;

use Kriss\WebmanAmisAdmin\Helper\ArrayHelper;
use Kriss\WebmanAmisAdmin\Helper\ConfigHelper;
use support\Container;

/**
 * amis 组件
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/index
 */
class Component
{
    protected array $config = [
        'schema' => [],
    ];
    protected array $schema = [
        'type' => '',
    ];

    public function __construct()
    {
        $componentConfig = ConfigHelper::get('amis.components.' . static::class, []);
        if (is_callable($componentConfig)) {
            $componentConfig = call_user_func($componentConfig);
        }
        $this->config((array)$componentConfig);
        $this->schema($this->config['schema']);
    }

    /**
     * @param array|null $schema
     * @return $this
     */
    public static function make(array $schema = null)
    {
        /** @var static $component */
        $component = Container::get(static::class);
        if ($schema) {
            $component->schema($schema);
        }
        return $component;
    }

    /**
     * @param array $schema
     * @param bool $overwrite
     * @return $this
     */
    public function schema(array $schema, bool $overwrite = false)
    {
        if ($overwrite) {
            $this->schema = $schema;
        } else {
            $this->schema = $this->merge($this->schema, $schema);
        }
        return $this;
    }

    /**
     * @param array $config
     * @param bool $overwrite
     * @return $this
     */
    public function config(array $config, bool $overwrite = false)
    {
        if ($overwrite) {
            $this->config = $config;
        } else {
            $this->config = $this->merge($this->config, $config);
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->deepToArray($this->schema);
    }

    protected function deepToArray(array $arr): array
    {
        $newArr = [];
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                $item = $this->deepToArray($item);
            } elseif ($item instanceof Component) {
                $item = $item->toArray();
            }
            $newArr[$key] = $item;
        }
        return $newArr;
    }

    /**
     * 获取 schema 中的值
     * @param string $schemaKey
     * @param null $default
     * @return array|mixed
     */
    public function get(string $schemaKey, $default = null)
    {
        return ArrayHelper::get($this->toArray(), $schemaKey, $default);
    }

    /**
     * 合并数组
     * @param ...$arrays
     * @return array
     */
    protected function merge(...$arrays): array
    {
        return ArrayHelper::merge(...$arrays);
    }
}