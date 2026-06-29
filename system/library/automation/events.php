<?php

if (!class_exists('Events')) {
    /**
     * Events
     */
    //TODO: add async and multi-thread support
    class Events
    {
        private static array $data = [];

        public function __get(string $k):mixed {
            return isset(self::$data[$k]) ? self::$data[$k] : null;
        }

        public function __set(string $k, mixed $v) {
            throw new ErrorException("Can't assign values of properties directly in ". __CLASS__);
        }

        public function __isset(string $k):bool {
            return isset(self::$data[$k]);
        }

        static public function once(string $event_name, callable $fn) {
            Events::on($event_name, $fn, true);
        }

        /**
         * @see https://wiki.php.net/rfc/first_class_callable_syntax
         * */
        static public function on(string $event_name, callable $fn, bool $once = false) {
            if (!isset(self::$data[$event_name]) || !is_array(self::$data[$event_name])) self::$data[$event_name] = [];
            
            Events::emit("events", [
                "event"=>$event_name,
                "callback"=>$fn,
                "once"=>$once,
            ]);

            self::$data[$event_name][] = [
                'function' => $fn, 
                'once' => $once,
            ];
        }

        static public function off(string $event_name) {
            self::$data[$event_name] = [];
        }

        static public function emit(string $event_name, ...$args) {
            if (!isset(self::$data[$event_name]) || count(self::$data[$event_name])===0) {
                return null;
            }

            Events::emit("call", [
                "event"=>$event_name,
                "args"=>$args,
            ]);

            foreach (self::$data[$event_name] as $key => $ev) {
                if (isset($ev["function"]) && !is_null($ev['function'])) {
                    call_user_func_array($ev['function'], $args);
                
                    if (isset($ev["once"]) && $ev["once"]) {
                        unset(self::$data[$event_name][$key]);
                    }

                    Events::emit("called", [
                        "event"=>$event_name,
                        "function"=>$ev['function'],
                        "args"=>$args,
                    ]);
                }
            }
            self::$data[$event_name]['done'] = true;
        }
    } //end class
} //end if