<?php
/**
 * Copyright (c) 2020 kakuilan@163.com All rights reserved
 * User: kakuilan@163.com
 * Date: 2019/5/10
 * Time: 16:18
 * Desc: 数组可迭代对象
 */


namespace Kph\Objects;

use ArrayAccess;
use Countable;
use Iterator;
use JsonSerializable;
use Kph\Interfaces\Arrayable;
use Kph\Interfaces\Jsonable;
use RuntimeException;
use Serializable;


/**
 * Class ArrayObject
 * @package Kph\Objects
 */
class ArrayObject extends BaseObject implements ArrayAccess, JsonSerializable, Serializable, Countable, Iterator, Arrayable, Jsonable {


    /**
     * 数据
     * @var array
     */
    private $__datas = [];


    /**
     * 索引
     * @var int
     */
    private $__index = 0;


    /**
     * ArrayObject constructor.
     * @param array $datas
     */
    public function __construct(array $datas = null) {
        if (!empty($datas)) {
            $this->__datas = $datas;
        }
    }


    /**
     * 魔术get
     * @param $key
     * @return mixed|null
     */
    public function __get($key) {
        return $this->__datas[$key] ?? null;
    }


    /**
     * 魔术set
     * @param $key
     * @param $value
     */
    public function __set($key, $value) {
        $this->__datas[$key] = $value;
    }


    /**
     * 获取键值
     * @param $key
     * @return mixed|null
     */
    public function get($key) {
        return $this->__datas[$key] ?? null;
    }


    /**
     * 设置键值
     * @param $key
     * @param $value
     */
    public function set($key, $value): void {
        $this->__datas[$key] = $value;
    }


    /**
     * 键是否存在
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
        return isset($this->__datas[$offset]);
    }


    /**
     * 获取键值
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        return $this->__datas[$offset] ?? null;
    }


    /**
     * 设置键值
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void {
        $this->__datas[$offset] = $value;
    }


    /**
     * 删除键
     * @param mixed $offset
     */
    public function offsetUnset($offset): void {
        unset($this->__datas[$offset]);
    }


    /**
     * json序列化
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->__datas;
    }


    /**
     * 序列化
     * @return string
     */
    public function serialize(): string {
        return serialize($this->__datas);
    }


    /**
     * 反序列化
     * @param string $serialized
     */
    public function unserialize($serialized): void {
        $this->__datas = unserialize($serialized);
    }


    /**
     * 计算数量
     * @return int
     */
    public function count(): int {
        return count($this->__datas);
    }


    /**
     * 当前元素
     * @return mixed
     */
    public function current() {
        return current($this->__datas);
    }


    /**
     * 下一个元素
     * @return mixed|void
     */
    public function next() {
        $this->__index++;
        return next($this->__datas);
    }


    /**
     * 获取当前元素的键
     * @return int|mixed|string|null
     */
    public function key() {
        return key($this->__datas);
    }


    /**
     * 验证当前位置
     * @return bool
     */
    public function valid(): bool {
        return count($this->__datas) >= $this->__index;
    }


    /**
     * 重置迭代器
     * @return mixed|void
     */
    public function rewind() {
        $this->__index = 0;
        return reset($this->__datas);
    }


    /**
     * 返回数组
     * @return array
     */
    public function toArray(): array {
        return $this->__datas;
    }


    /**
     * 返回json串
     * @param int $options
     * @param int $depth
     * @return mixed|string
     */
    public function toJson(int $options = 0, int $depth = 512): string {
        return json_encode($this->__datas, $options, $depth);
    }


    /**
     * 查找元素
     * @param $needle
     * @param bool $strict
     * @return false|int|string
     */
    public function search($needle, $strict = false) {
        return array_search($needle, $this->__datas, $strict);
    }


    /**
     * 元素位置
     * @param $needle
     * @return false|int|string
     */
    public function indexOf($needle) {
        return $this->search($needle);
    }


    /**
     * 元素最后出现位置
     * @param $needle
     * @return bool|int|string
     */
    public function lastIndexOf($needle) {
        $res = false;
        foreach ($this->__datas as $k => $it) {
            if ($needle == $it) {
                $res = $k;
            }
        }

        return $res;
    }


    /**
     * 删除元素(根据键)
     * @param $key
     * @return bool
     */
    public function delete($key): bool {
        $res = false;
        if (isset($this->__datas[$key])) {
            unset($this->__datas[$key]);
            $res = true;
        }

        return $res;
    }


    /**
     * 移除元素(根据值)
     * @param $value
     * @return ArrayObject
     */
    public function remove($value): ArrayObject {
        $key = $this->search($value);
        if ($key !== false) {
            unset($this->__datas[$key]);
        }

        return $this;
    }


    /**
     * 清空
     */
    public function clear(): void {
        $this->__datas = [];
    }


    /**
     * 是否包含值
     * @param $val
     * @return bool
     */
    public function contains($val): bool {
        return in_array($val, $this->__datas);
    }


    /**
     * 是否存在键
     * @param $key
     * @return bool
     */
    public function exists($key): bool {
        return array_key_exists($key, $this->__datas);
    }


    /**
     * 连接
     * @param string $str
     * @return string
     */
    public function join($str = ''): string {
        return implode($str, $this->__datas);
    }


    /**
     * 插入元素
     * @param int $offset 位置
     * @param mixed $val 元素值
     * @return bool
     */
    public function insert($offset, $val): bool {
        if ($offset > $this->count()) {
            return false;
        }

        array_splice($this->__datas, $offset, 0, $val);

        return true;
    }


    /**
     * 是否为空
     * @return bool
     */
    public function isEmpty(): bool {
        return empty($this->__datas);
    }


    /**
     * 求和
     * @return float|int
     */
    public function sum() {
        return array_sum($this->__datas);
    }


    /**
     * 求乘积
     * @return float|int
     */
    public function product() {
        return array_product($this->__datas);
    }


    /**
     * 用回调函数迭代地将数组简化为单一的值
     * @param callable $fn
     * @return mixed
     */
    public function reduce(callable $fn, $initial = null) {
        return array_reduce($this->__datas, $fn, $initial);
    }


    /**
     * 向数组尾部追加元素
     * @param $val
     * @return int
     */
    public function append($val): int {
        return array_push($this->__datas, $val);
    }


    /**
     * 向数组头部追加元素
     * @param $val
     * @return int
     */
    public function prepend($val): int {
        return array_unshift($this->__datas, $val);
    }


    /**
     * 从数组尾部弹出元素
     * @return mixed
     */
    public function pop() {
        return array_pop($this->__datas);
    }


    /**
     * 从数组头部弹出元素
     * @return mixed
     */
    public function shift() {
        return array_shift($this->__datas);
    }


    /**
     * 数组切片
     * @param $offset
     * @param null $length
     * @return ArrayObject
     */
    public function slice($offset, $length = null): ArrayObject {
        return new static(array_slice($this->__datas, $offset, $length));
    }


    /**
     * 随机获取一个元素
     * @return mixed|null
     */
    public function rand() {
        $key = array_rand($this->__datas, 1);
        return $this->__datas[$key] ?? null;
    }


    /**
     * 遍历数组
     * @param callable $fn 处理函数
     * @return ArrayObject
     */
    public function each(callable $fn): ArrayObject {
        if ($this->count() > 0) {
            array_walk($this->__datas, $fn);
        }

        return $this;
    }


    /**
     * 遍历数组,并构建新数组
     * @param callable $fn
     * @return ArrayObject
     */
    public function map(callable $fn): ArrayObject {
        return new static(array_map($fn, $this->__datas));
    }


    /**
     * 所有值
     * @return ArrayObject
     */
    public function values(): ArrayObject {
        return new static(array_values($this->__datas));
    }


    /**
     * 所有键
     * @param null $search_value
     * @param bool $strict
     * @return ArrayObject
     */
    public function keys($search_value = null, $strict = false): ArrayObject {
        if (is_null($search_value)) {
            $keys = array_keys($this->__datas);
        } else {
            $keys = array_keys($this->__datas, $search_value, $strict);
        }

        return new static($keys);
    }


    /**
     * 返回列
     * @param $column_key
     * @param null $index
     * @return ArrayObject
     */
    public function column($column_key, $index = null): ArrayObject {
        return new static(array_column($this->__datas, $column_key, $index));
    }


    /**
     * 去重
     * @param int $sort_flags
     * @return ArrayObject
     */
    public function unique($sort_flags = SORT_STRING): ArrayObject {
        return new static(array_unique($this->__datas, $sort_flags));
    }


    /**
     * 获取重复的元素
     * @param int $sort_flags
     * @return ArrayObject
     */
    public function multiple($sort_flags = SORT_STRING): ArrayObject {
        $arr = array_unique($this->__datas, $sort_flags);
        return new static(array_merge(array_diff_assoc($this->__datas, $arr)));
    }


    /**
     * 排序
     * @param int $sort_flags
     * @return ArrayObject
     */
    public function sort($sort_flags = SORT_REGULAR): ArrayObject {
        sort($this->__datas, $sort_flags);

        return $this;
    }


    /**
     * 反序
     * @param bool $preserve_keys
     * @return ArrayObject
     */
    public function reverse($preserve_keys = false): ArrayObject {
        $this->__datas = array_reverse($this->__datas, $preserve_keys);

        return $this;
    }


    /**
     * 乱序
     * @return ArrayObject
     */
    public function shuffle(): ArrayObject {
        if ($this->count() > 0) {
            shuffle($this->__datas);
        }

        return $this;
    }


    /**
     * 将一个数组分割成多个数组
     * @param $size
     * @param bool $preserve_keys 是否保留键名
     * @return ArrayObject
     */
    public function chunk($size, $preserve_keys = false): ArrayObject {
        return new static(array_chunk($this->__datas, $size, $preserve_keys));
    }


    /**
     * 交换数组中的键和值
     * @return ArrayObject
     */
    public function flip(): ArrayObject {
        return new static(array_flip($this->__datas));
    }


    /**
     * 过滤数组中的元素
     * @param callable $fn
     * @param int $flag
     * @return ArrayObject
     */
    public function filter(callable $fn, $flag = 0): ArrayObject {
        return new static(array_filter($this->__datas, $fn, $flag));
    }


}