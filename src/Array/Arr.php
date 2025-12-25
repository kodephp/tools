<?php

namespace Kode\Array;

class Arr
{
    /**
     * 数组转树形结构
     * @param array $list 数组
     * @param string $idField ID字段名
     * @param string $parentIdField 父ID字段名
     * @param string $childrenField 子节点字段名
     * @return array 树形结构
     */
    public static function tree(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $childrenField = 'children'): array
    {
        $tree = [];
        $map = [];
        foreach ($list as $item) {
            $map[$item[$idField]] = $item;
            $map[$item[$idField]][$childrenField] = [];
        }
        foreach ($list as $item) {
            if (isset($map[$item[$parentIdField]]) && $item[$idField] != $item[$parentIdField]) {
                $map[$item[$parentIdField]][$childrenField][] = &$map[$item[$idField]];
            } else {
                $tree[] = &$map[$item[$idField]];
            }
        }
        return $tree;
    }

    /**
     * 树形结构转数组
     * @param array $tree 树形结构
     * @param string $childrenField 子节点字段名
     * @return array 数组
     */
    public static function list(array $tree, string $childrenField = 'children'): array
    {
        $list = [];
        $stack = $tree;
        while (!empty($stack)) {
            $node = array_shift($stack);
            $children = $node[$childrenField] ?? [];
            unset($node[$childrenField]);
            $list[] = $node;
            foreach ($children as $child) {
                array_unshift($stack, $child);
            }
        }
        return $list;
    }

    /**
     * 数组转层级结构
     * @param array $list 数组
     * @param string $idField ID字段名
     * @param string $parentIdField 父ID字段名
     * @param string $levelField 层级字段名
     * @return array 层级结构
     */
    public static function level(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $levelField = 'level'): array
    {
        $map = [];
        $result = [];
        foreach ($list as $item) {
            $map[$item[$idField]] = $item;
            $map[$item[$idField]][$levelField] = 1;
        }
        foreach ($list as $item) {
            if (isset($map[$item[$parentIdField]]) && $item[$idField] != $item[$parentIdField]) {
                $map[$item[$idField]][$levelField] = $map[$item[$parentIdField]][$levelField] + 1;
            }
            $result[] = $map[$item[$idField]];
        }
        return $result;
    }

    /**
     * 数组转路径结构
     * @param array $list 数组
     * @param string $idField ID字段名
     * @param string $parentIdField 父ID字段名
     * @param string $nameField 名称字段名
     * @param string $pathField 路径字段名
     * @param string $pathSeparator 路径分隔符
     * @return array 路径结构
     */
    public static function path(array $list, string $idField = 'id', string $parentIdField = 'parent_id', string $nameField = 'name', string $pathField = 'path', string $pathSeparator = '/'): array
    {
        $map = [];
        $result = [];
        foreach ($list as $item) {
            $map[$item[$idField]] = $item;
            $map[$item[$idField]][$pathField] = $item[$nameField];
        }
        foreach ($list as $item) {
            if (isset($map[$item[$parentIdField]]) && $item[$idField] != $item[$parentIdField]) {
                $map[$item[$idField]][$pathField] = $map[$item[$parentIdField]][$pathField] . $pathSeparator . $item[$nameField];
            }
            $result[] = $map[$item[$idField]];
        }
        return $result;
    }

    /**
     * 获取数组中的值，支持点语法和数组嵌套键
     * @param array $array 数组
     * @param string|int|array $key 键名，可以是点语法字符串或数组
     * @param mixed $default 默认值
     * @return mixed 值
     */
    public static function get(array $array, string|int|array $key, mixed $default = null): mixed
    {
        if (is_array($key)) {
            foreach ($key as $segment) {
                if (!is_array($array) || !array_key_exists($segment, $array)) {
                    return $default;
                }
                $array = $array[$segment];
            }
            return $array;
        }

        if (str_contains((string) $key, '.')) {
            foreach (explode('.', (string) $key) as $segment) {
                if (!is_array($array) || !array_key_exists($segment, $array)) {
                    return $default;
                }
                $array = $array[$segment];
            }
            return $array;
        }

        return $array[$key] ?? $default;
    }

    /**
     * 设置数组中的值
     * @param array $array 数组
     * @param string|int $key 键名
     * @param mixed $value 值
     * @return array 修改后的数组
     */
    public static function set(array $array, string|int $key, mixed $value): array
    {
        $array[$key] = $value;
        return $array;
    }

    /**
     * 检查数组中是否存在键名
     * @param array $array 数组
     * @param string|int $key 键名
     * @return bool 是否存在
     */
    public static function has(array $array, string|int $key): bool
    {
        return isset($array[$key]);
    }

    /**
     * 获取数组中的指定键值
     * @param array $array 数组
     * @param array $keys 键名列表
     * @return array 结果
     */
    public static function only(array $array, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $result[$key] = $array[$key];
            }
        }
        return $result;
    }

    /**
     * 排除数组中的指定键值
     * @param array $array 数组
     * @param array $keys 键名列表
     * @return array 结果
     */
    public static function except(array $array, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * 数组过滤
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return array 过滤后的数组
     */
    public static function filter(array $array, callable $callback): array
    {
        return array_filter($array, $callback);
    }

    /**
     * 数组映射
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return array 映射后的数组
     */
    public static function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }

    /**
     * 数组排序
     * @param array $array 数组
     * @param string $key 排序键
     * @param string $order 排序顺序（asc/desc）
     * @return array 排序后的数组
     */
    public static function sort(array $array, string $key, string $order = 'asc'): array
    {
        usort($array, function ($a, $b) use ($key, $order) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            $result = $a[$key] < $b[$key] ? -1 : 1;
            return $order === 'desc' ? -$result : $result;
        });
        return $array;
    }

    /**
     * 数组去重
     * @param array $array 数组
     * @param bool $strict 是否严格比较
     * @return array 去重后的数组
     */
    public static function unique(array $array, bool $strict = true): array
    {
        return array_unique($array, $strict ? SORT_REGULAR : SORT_STRING);
    }

    /**
     * 数组分页
     * @param array $array 数组
     * @param int $page 页码
     * @param int $size 每页大小
     * @return array 分页后的数组
     */
    public static function paginate(array $array, int $page = 1, int $size = 10): array
    {
        $offset = ($page - 1) * $size;
        return array_slice($array, $offset, $size);
    }

    /**
     * 数组扁平化
     * @param array $array 数组
     * @return array 扁平化后的数组
     */
    public static function flatten(array $array): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, self::flatten($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }
}
