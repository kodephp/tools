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
     * 数组深度合并
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 合并后的数组
     */
    public static function deepMerge(array $array1, array $array2): array
    {
        $stack = [[&$array1, &$array2]];
        while (!empty($stack)) {
            [$arr1, $arr2] = array_pop($stack);
            foreach ($arr2 as $key => $value) {
                if (is_array($value) && isset($arr1[$key]) && is_array($arr1[$key])) {
                    $stack[] = [[&$arr1[$key], &$value]];
                } else {
                    $arr1[$key] = $value;
                }
            }
        }
        return $array1;
    }

    /**
     * 多维数组分组
     * @param array $array 数组
     * @param string $key 分组键名
     * @return array 分组后的数组
     */
    public static function group(array $array, string $key): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[$item[$key]][] = $item;
        }
        return $result;
    }

    /**
     * 多维数组统计
     * @param array $array 数组
     * @param string $key 统计键名
     * @return array 统计结果
     */
    public static function count(array $array, string $key): array
    {
        $result = [];
        foreach ($array as $item) {
            if (isset($result[$item[$key]])) {
                $result[$item[$key]]++;
            } else {
                $result[$item[$key]] = 1;
            }
        }
        return $result;
    }

    /**
     * 多维数组求和
     * @param array $array 数组
     * @param string $key 求和键名
     * @return float 求和结果
     */
    public static function sum(array $array, string $key): float
    {
        $sum = 0;
        foreach ($array as $item) {
            $sum += $item[$key];
        }
        return $sum;
    }

    /**
     * 多维数组求平均值
     * @param array $array 数组
     * @param string $key 求平均值键名
     * @return float 平均值
     */
    public static function avg(array $array, string $key): float
    {
        $count = count($array);
        if ($count == 0) {
            return 0;
        }
        return self::sum($array, $key) / $count;
    }

    /**
     * 多维数组求最大值
     * @param array $array 数组
     * @param string $key 求最大值键名
     * @return float 最大值
     */
    public static function max(array $array, string $key): float
    {
        $max = PHP_INT_MIN;
        foreach ($array as $item) {
            if ($item[$key] > $max) {
                $max = $item[$key];
            }
        }
        return $max;
    }

    /**
     * 多维数组求最小值
     * @param array $array 数组
     * @param string $key 求最小值键名
     * @return float 最小值
     */
    public static function min(array $array, string $key): float
    {
        $min = PHP_INT_MAX;
        foreach ($array as $item) {
            if ($item[$key] < $min) {
                $min = $item[$key];
            }
        }
        return $min;
    }

    /**
     * 多维数组转JSON
     * @param array $array 数组
     * @param int $options JSON选项
     * @return string JSON字符串
     */
    public static function toJson(array $array, int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($array, $options);
    }

    /**
     * JSON转多维数组
     * @param string $json JSON字符串
     * @return array 数组
     */
    public static function fromJson(string $json): array
    {
        return json_decode($json, true);
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
     * 多维数组排序
     * @param array $array 数组
     * @param array $keys 排序键列表
     * @param array $orders 排序顺序列表
     * @return array 排序后的数组
     */
    public static function multiSort(array $array, array $keys, array $orders = []): array
    {
        // Create a copy to avoid mutating the original array
        $sorted = $array;
        
        usort($sorted, function ($a, $b) use ($keys, $orders) {
            foreach ($keys as $index => $key) {
                $hasA = array_key_exists($key, $a);
                $hasB = array_key_exists($key, $b);
                
                // Handle cases where one item has the key and the other doesn't
                if (!$hasA && !$hasB) {
                    continue;
                } elseif (!$hasA) {
                    // Treat missing values as less than existing values by default
                    return ($orders[$index] ?? 'asc') === 'asc' ? -1 : 1;
                } elseif (!$hasB) {
                    return ($orders[$index] ?? 'asc') === 'asc' ? 1 : -1;
                }
                
                $valA = $a[$key];
                $valB = $b[$key];
                
                // Strict equality check first
                if ($valA === $valB) {
                    continue;
                }
                
                $order = $orders[$index] ?? 'asc';
                $isAscending = $order === 'asc';
                
                // Type-safe comparison
                if (is_string($valA) && is_string($valB)) {
                    $result = strcmp($valA, $valB);
                } else {
                    $result = $valA < $valB ? -1 : 1;
                }
                
                return $isAscending ? $result : -$result;
            }
            return 0;
        });
        
        return $sorted;
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
     * 多维数组去重
     * @param array $array 数组
     * @param string $key 去重键名
     * @return array 去重后的数组
     */
    public static function multiUnique(array $array, string $key): array
    {
        $result = [];
        $temp = [];
        foreach ($array as $item) {
            if (!in_array($item[$key], $temp)) {
                $temp[] = $item[$key];
                $result[] = $item;
            }
        }
        return $result;
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

    /**
     * 数组切片
     * @param array $array 数组
     * @param int $offset 偏移量
     * @param int|null $length 长度
     * @return array 切片后的数组
     */
    public static function slice(array $array, int $offset, ?int $length = null): array
    {
        return array_slice($array, $offset, $length, true);
    }

    /**
     * 数组过滤
     * @param array $array 数组
     * @param callable|null $callback 回调函数
     * @return array 过滤后的数组
     */
    public static function filter(array $array, ?callable $callback = null): array
    {
        if ($callback === null) {
            return array_filter($array, function ($value) {
                return !empty($value);
            });
        }
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 数组归约
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @param mixed $initial 初始值
     * @return mixed 归约结果
     */
    public static function reduce(array $array, callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($array, $callback, $initial);
    }

    /**
     * 数组查找
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return mixed|null 查找结果
     */
    public static function find(array $array, callable $callback): mixed
    {
        if (PHP_VERSION_ID >= 80400) {
            return array_find($array, $callback);
        }
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * 数组查找键名
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return string|int|null 键名
     */
    public static function findKey(array $array, callable $callback): string|int|null
    {
        if (PHP_VERSION_ID >= 80400) {
            return array_find_key($array, $callback);
        }
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * 数组是否存在满足条件的元素
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否存在
     */
    public static function some(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 数组是否所有元素都满足条件
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否都满足
     */
    public static function every(array $array, callable $callback): bool
    {
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 数组分割
     * @param array $array 数组
     * @param int $size 分块大小
     * @return array 分割后的数组
     */
    public static function chunk(array $array, int $size): array
    {
        return array_chunk($array, $size, true);
    }

    /**
     * 数组合并
     * @param array ...$arrays 数组列表
     * @return array 合并后的数组
     */
    public static function merge(array ...$arrays): array
    {
        return array_merge(...$arrays);
    }

    /**
     * 数组合并（保留键名）
     * @param array ...$arrays 数组列表
     * @return array 合并后的数组
     */
    public static function mergeRecursive(array ...$arrays): array
    {
        return array_merge_recursive(...$arrays);
    }

    /**
     * 数组差集
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 差集
     */
    public static function diff(array $array1, array $array2): array
    {
        return array_diff($array1, $array2);
    }

    /**
     * 数组差集（带键名）
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 差集
     */
    public static function diffKey(array $array1, array $array2): array
    {
        return array_diff_key($array1, $array2);
    }

    /**
     * 数组交集
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 交集
     */
    public static function intersect(array $array1, array $array2): array
    {
        return array_intersect($array1, $array2);
    }

    /**
     * 数组交集（带键名）
     * @param array $array1 第一个数组
     * @param array $array2 第二个数组
     * @return array 交集
     */
    public static function intersectKey(array $array1, array $array2): array
    {
        return array_intersect_key($array1, $array2);
    }

    /**
     * 数组填充
     * @param int $start 起始索引
     * @param int $count 数量
     * @param mixed $value 填充值
     * @return array 填充后的数组
     */
    public static function fill(int $start, int $count, mixed $value): array
    {
        return array_fill($start, $count, $value);
    }

    /**
     * 数组键名
     * @param array $array 数组
     * @return array 键名数组
     */
    public static function keys(array $array): array
    {
        return array_keys($array);
    }

    /**
     * 数组值
     * @param array $array 数组
     * @return array 值数组
     */
    public static function values(array $array): array
    {
        return array_values($array);
    }

    /**
     * 数组反转
     * @param array $array 数组
     * @param bool $preserveKeys 是否保留键名
     * @return array 反转后的数组
     */
    public static function reverse(array $array, bool $preserveKeys = false): array
    {
        return array_reverse($array, $preserveKeys);
    }

    /**
     * 数组打乱
     * @param array $array 数组
     * @return array 打乱后的数组
     */
    public static function shuffle(array $array): array
    {
        shuffle($array);
        return $array;
    }

    /**
     * 数组随机获取一个元素
     * @param array $array 数组
     * @return mixed 随机元素
     */
    public static function random(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }
        return $array[array_rand($array)];
    }

    /**
     * 数组随机获取多个元素
     * @param array $array 数组
     * @param int $num 数量
     * @return array 随机元素数组
     */
    public static function randomMany(array $array, int $num): array
    {
        if (empty($array) || $num <= 0) {
            return [];
        }
        $keys = array_rand($array, min($num, count($array)));
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }
        return $result;
    }

    /**
     * 数组第一个元素
     * @param array $array 数组
     * @return mixed 第一个元素
     */
    public static function first(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }
        if (PHP_VERSION_ID >= 80400) {
            return array_first($array);
        }
        return reset($array);
    }

    /**
     * 数组最后一个元素
     * @param array $array 数组
     * @return mixed 最后一个元素
     */
    public static function last(array $array): mixed
    {
        if (empty($array)) {
            return null;
        }
        if (PHP_VERSION_ID >= 80400) {
            return array_last($array);
        }
        return end($array);
    }

    /**
     * 数组弹出第一个元素
     * @param array $array 数组
     * @return mixed 第一个元素
     */
    public static function shift(array &$array): mixed
    {
        return array_shift($array);
    }

    /**
     * 数组弹出最后一个元素
     * @param array $array 数组
     * @return mixed 最后一个元素
     */
    public static function pop(array &$array): mixed
    {
        return array_pop($array);
    }

    /**
     * 数组头部添加元素
     * @param array $array 数组
     * @param mixed ...$values 元素列表
     * @return int 新数组长度
     */
    public static function unshift(array &$array, mixed ...$values): int
    {
        return array_unshift($array, ...$values);
    }

    /**
     * 数组尾部添加元素
     * @param array $array 数组
     * @param mixed ...$values 元素列表
     * @return int 新数组长度
     */
    public static function push(array &$array, mixed ...$values): int
    {
        return array_push($array, ...$values);
    }

    /**
     * 数组删除指定元素
     * @param array $array 数组
     * @param mixed $value 元素值
     * @param bool $strict 是否严格比较
     * @return array 删除后的数组
     */
    public static function remove(array $array, mixed $value, bool $strict = false): array
    {
        $keys = array_keys($array, $value, $strict);
        foreach ($keys as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * 数组删除指定键名
     * @param array $array 数组
     * @param string|int $key 键名
     * @return array 删除后的数组
     */
    public static function removeKey(array $array, string|int $key): array
    {
        unset($array[$key]);
        return $array;
    }

    /**
     * 数组是否包含指定元素
     * @param array $array 数组
     * @param mixed $value 元素值
     * @param bool $strict 是否严格比较
     * @return bool 是否包含
     */
    public static function contains(array $array, mixed $value, bool $strict = false): bool
    {
        return in_array($value, $array, $strict);
    }

    /**
     * 数组是否包含指定键名
     * @param array $array 数组
     * @param string|int $key 键名
     * @return bool 是否包含
     */
    public static function containsKey(array $array, string|int $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * 数组是否为空
     * @param array $array 数组
     * @return bool 是否为空
     */
    public static function isEmpty(array $array): bool
    {
        return empty($array);
    }

    /**
     * 数组是否为关联数组
     * @param array $array 数组
     * @return bool 是否为关联数组
     */
    public static function isAssoc(array $array): bool
    {
        if (empty($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * 数组是否为索引数组
     * @param array $array 数组
     * @return bool 是否为索引数组
     */
    public static function isIndexed(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * 数组键名转换
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return array 转换后的数组
     */
    public static function mapKeys(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $callback($key, $value);
            $result[$newKey] = $value;
        }
        return $result;
    }

    /**
     * 数组值转换
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return array 转换后的数组
     */
    public static function mapValues(array $array, callable $callback): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $callback($value, $key);
        }
        return $result;
    }

    /**
     * 数组组合
     * @param array $keys 键名数组
     * @param array $values 值数组
     * @return array 组合后的数组
     */
    public static function combine(array $keys, array $values): array
    {
        return array_combine($keys, $values);
    }

    /**
     * 数组填充键名
     * @param array $keys 键名数组
     * @param mixed $value 填充值
     * @return array 填充后的数组
     */
    public static function fillKeys(array $keys, mixed $value): array
    {
        return array_fill_keys($keys, $value);
    }

    /**
     * 数组翻转
     * @param array $array 数组
     * @return array 翻转后的数组
     */
    public static function flip(array $array): array
    {
        return array_flip($array);
    }

    /**
     * 数组列提取
     * @param array $array 数组
     * @param string $columnKey 列键名
     * @param string|null $indexKey 索引键名
     * @return array 提取后的数组
     */
    public static function column(array $array, string $columnKey, ?string $indexKey = null): array
    {
        return array_column($array, $columnKey, $indexKey);
    }

    /**
     * 数组是否存在满足条件的元素
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否存在
     */
    public static function any(array $array, callable $callback): bool
    {
        if (PHP_VERSION_ID >= 80400) {
            return array_any($array, $callback);
        }
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 数组是否所有元素都满足条件
     * @param array $array 数组
     * @param callable $callback 回调函数
     * @return bool 是否都满足
     */
    public static function all(array $array, callable $callback): bool
    {
        if (PHP_VERSION_ID >= 80400) {
            return array_all($array, $callback);
        }
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return false;
            }
        }
        return true;
    }
}
