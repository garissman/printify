<?php

namespace Printify;

use ArrayAccess;
use Countable;
use Iterator;

class Collection implements ArrayAccess, Countable, Iterator
{
    public $current_page = 1;
    public $to = 1;
    public $from = 1;
    public $last_page = 1;
    public $total = 1;
    private $_items = [];
    private $position = 0;

    public function __construct(array $items = [])
    {
        $this->_items = $items;
    }

    public function count(): int
    {
        return count($this->_items);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_items[] = $value;
        } else {
            $this->_items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->_items[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->_items[$this->position]);
    }

    public function toArray()
    {
        $items = [];
        foreach ($this->_items as $item) {
            $items[] = $item->toArray();
        }
        return $items;
    }

}