<?php

if (!class_exists('Hooks')) {
    /**
     * Hooks
     */
    class Hooks
    {

        public const URGENT = 10;
        public const HIGH   = 50;
        public const NORMAL = 100;
        public const LOW    = 200;
        public const LOWEST = 250;

        private array $merged_filters = [];
        public array $current_filter = [];

        private static array $hooks = [];
        private array $data = [];
        private string $name = "";

        public function __construct(string $name)
        {
            $this->name = $name;
            if (!isset(self::$hooks[$name])) {
                self::$hooks[$name] = [
                    'actions' => [],
                    'done' => false,
                ];
            }
        }

        public function __get(string $k):mixed {
            return isset($this->data[$k]) ? $this->data[$k] : null;
        }

        public function __set(string $k, mixed $v) {
            $this->data[$k] = $v;
        }

        public function __isset(string $k):bool {
            return isset($this->data[$k]);
        }

        public function addFilter(string $tag, callable $fn, int $priority = self::NORMAL, bool $once = false) {
            $idx =  $this->_filter_build_unique_id($tag, $fn, $priority);
            
            self::$hooks[$this->name]['actions'][$tag][$priority][$idx] = [
                'function' => $fn, 
                'once' => $once,
            ];

            unset($this->merged_filters[$tag]);

            return true;
        }

        public function removeFilter(string $tag, callable $fn = null, int $priority = self::NORMAL)
        {
            if ($fn) {
                $function_to_remove = $this->_filter_build_unique_id($tag, $fn, $priority);

                $r = isset(self::$hooks[$this->name]['actions'][$tag][$priority][$function_to_remove]);

                if (true === $r) {
                    unset(self::$hooks[$this->name]['actions'][$tag][$priority][$function_to_remove]);
                    if (empty(self::$hooks[$this->name]['actions'][$tag][$priority]))
                        unset(self::$hooks[$this->name]['actions'][$tag][$priority]);
                    unset($this->merged_filters[$tag]);
                }

                return $r;
            } else {
                if (isset(self::$hooks[$this->name]['actions'][$tag])) {
                    if (false !== $priority && isset(self::$hooks[$this->name]['actions'][$tag][$priority]))
                        unset(self::$hooks[$this->name]['actions'][$tag][$priority]);
                    else
                        unset(self::$hooks[$this->name]['actions'][$tag]);
                }

                if (isset($this->merged_filters[$tag]))
                    unset($this->merged_filters[$tag]);

                return true;
            }
        }

        public function hasFilter(string $tag, callable $fn = null)
        {
            $has = !empty(self::$hooks[$this->name]['actions'][$tag]);
            if (false === $fn || false == $has)
                return $has;

            $idx = $this->_filter_build_unique_id($tag, $fn, false);

            if (!$idx) return false;

            foreach ((array) array_keys(self::$hooks[$this->name]['actions'][$tag]) as $priority) {
                if (isset(self::$hooks[$this->name]['actions'][$tag][$priority][$idx]))
                    return $priority;
            }
            return false;
        }

        public function applyFilters($tag, &$value) {
            $args = [];
            // Do 'all' actions first
            if (isset(self::$hooks[$this->name]['actions']['all'])) {
                $this->current_filter[] = $tag;
                $args = func_get_args();
                array_shift($args);
                $this->_call_all_hook($args);
            }
            
            if (!isset(self::$hooks[$this->name]['actions'][$tag])) {
                if (isset(self::$hooks[$this->name]['actions']['all']))
                    array_pop($this->current_filter);
                return $value;
            }
            
            if (!isset(self::$hooks[$this->name]['actions']['all']))
                $this->current_filter[] = $tag;
            
            // Sort
            if (!isset($this->merged_filters[$tag])) {
                ksort(self::$hooks[$this->name]['actions'][$tag]);
                $this->merged_filters[$tag] = true;
            }
            
            reset(self::$hooks[$this->name]['actions'][$tag]);
            
            if (empty($args)) {
                $args = func_get_args();
                array_shift($args);
            }
            
            do {
                foreach ((array) current(self::$hooks[$this->name]['actions'][$tag]) as $the_)
                    if (!is_null($the_['function'])) {
                        $args[1] = $value;
                        $value = call_user_func_array($the_['function'], $args);
                    }
            } while (next(self::$hooks[$this->name]['actions'][$tag]) !== false);

            array_pop($this->current_filter);

            return $value;
        }

        /**
         * ACTIONS
         */
        public function addAction(string $tag, callable $fn, int $priority = self::NORMAL, bool $once = false)
        {
            $tag = "action:". $tag;
            return $this->addFilter($tag, $fn, $priority, $once);
        }

        public function hasAction(string $tag, callable $fn = null)
        {
            $tag = "action:" . $tag;
            return $this->hasFilter($tag, $fn);
        }

        public function removeAction(string $tag, callable $fn, int $priority = self::NORMAL)
        {
            $tag = "action:" . $tag;
            return $this->removeFilter($tag, $fn, $priority);
        }

        public function removeAllActions(string $tag, int $priority = self::NORMAL)
        {
            $tag = "action:" . $tag;
            return $this->removeFilter($tag, null, $priority);
        }

        public function run(string $tag, $arg = '')
        {
            $tag = "action:" . $tag;
            if (!isset(self::$hooks[$this->name]['actions'][$tag])) {
                self::$hooks[$this->name]['actions'][$tag] = [];
            }
            
            // Do 'all' actions first
            if (isset(self::$hooks[$this->name]['actions']['action:all'])) {
                $this->current_filter[] = $tag;
                $all_args = func_get_args();
                $this->_call_all_hook($all_args);
            }
            
            if (!isset(self::$hooks[$this->name]['actions'][$tag])) {
                if (isset(self::$hooks[$this->name]['actions']['action:all']))
                    array_pop($this->current_filter);
                return;
            }
            
            if (!isset(self::$hooks[$this->name]['actions']['action:all']))
                $this->current_filter[] = $tag;
            
            $args =[];
            
            if (is_array($arg) && 1 == count($arg) && isset($arg[0]) && is_object($arg[0])) {
                // array(&$this)
                $args[] = &$arg[0];
            } else {
                $args[] = $arg;
            }
            
            for ($a = 2; $a < func_num_args(); $a++) {
                $args[] = func_get_arg($a);
            }
            
            // Sort
            if (!isset($this->merged_filters[$tag])) {
                ksort(self::$hooks[$this->name]['actions'][$tag]);
                $this->merged_filters[$tag] = true;
            }
            
            reset(self::$hooks[$this->name]['actions'][$tag]);

            do {
                foreach ((array) current(self::$hooks[$this->name]['actions'][$tag]) as $the_) {
                    if ($the_!==false && !is_null($the_['function'])) {
                        $hasToReturn = call_user_func_array($the_['function'], $args);
                        if ($hasToReturn) {
                            return $hasToReturn;
                        }
                    }
                }
            } while (next(self::$hooks[$this->name]['actions'][$tag]) !== false);

            array_pop($this->current_filter);
        }

        public function runAll($args)
        {
            $tag = "action:all";
            reset(self::$hooks[$this->name]['actions'][$tag]);

            foreach (self::$hooks[$this->name]['actions'][$tag] as $the_)
                if (!is_null($the_['function']))
                    call_user_func_array($the_['function'], $args);
        }

        public function did($tag)
        {
            if (!isset(self::$hooks[$this->name]['actions'][$tag]))
                return 0;

            return self::$hooks[$this->name]['actions'][$tag];
        }

        /**
         * HELPERS
         */
        public function getCurrentFilter()
        {
            return end($this->current_filter);
        }

        public function getCurrentAction()
        {
            return $this->current_filter;
        }

        function doingFilter(string $filter = "")
        {
            if (!$filter) {
                return !empty($this->current_filter);
            }
            return in_array($filter, $this->current_filter);
        }

        function doingAction(string $action = "")
        {
            return $this->doingFilter($action);
        }

        private function _filter_build_unique_id(string $tag, callable $fn, int $priority = self::NORMAL)
        {
            static $filter_id_count = 0;

            if (is_string($fn))
                return $fn;

            if (is_object($fn)) {
                // Closures are currently implemented as objects
                $fn = array($fn, '');
            } else {
                $fn = (array) $fn;
            }

            if (is_object($fn[0])) {
                // Object Class Calling
                if (function_exists('spl_object_hash')) {
                    return spl_object_hash($fn[0]) . $fn[1];
                } else {
                    $obj_idx = get_class($fn[0]) . $fn[1];
                    if (!isset($fn[0]->filter_id)) {
                        if (false === $priority)
                            return false;
                        $obj_idx .= isset(self::$hooks[$this->name]['actions'][$tag][$priority]) ? count((array)self::$hooks[$this->name]['actions'][$tag][$priority]) : $filter_id_count;
                        $fn[0]->filter_id = $filter_id_count;
                        ++$filter_id_count;
                    } else {
                        $obj_idx .= $fn[0]->filter_id;
                    }

                    return $obj_idx;
                }
            } else if (is_string($fn[0])) {
                // Static Calling
                return $fn[0] . $fn[1];
            }
        }

        public function _call_all_hook($args)
        {
            reset(self::$hooks[$this->name]['actions']['all']);
            do {
                foreach ((array) current(self::$hooks[$this->name]['actions']['all']) as $the_)
                if (!is_null($the_['function']))
                call_user_func_array($the_['function'], $args);
            } while (next(self::$hooks[$this->name]['actions']['all']) !== false);
        }
    } //end class
} //end if
