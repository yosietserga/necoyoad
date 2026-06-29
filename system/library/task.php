<?php

final class Task {

    /**
     * Task::task_id
     * @param integer $task_id
     * El ID de la tarea actual
     * */
    public $task_id = null;

    /**
     * Task::task
     * @param string $task
     * Nombre de la tarea
     * */
    public $task = "";

    /**
     * Task::type
     * @param string $type
     * El tipo de tarea
     * array (
     *  'send',     // Enviar un email
     *  'sale',     // Recordar la cobranza de una venta
     *  'enquiry',  // Recordar una venta potencial sin concretar
     *  'report',   // Enviar un informe o reporte de la tienda (Informe de ventas, clientes, web stats, etc.)
     *  'backup',   // Realizar un backup de la base de datos y enviar por email el archivo fuente sql
     * );
     * */
    public $type = "send";

    /**
     * Task::params
     * @param mixed $params
     * Parametros de la url que se van a pasar a la tarea
     * */
    public $params = [];

    /**
     * Task::time_interval
     * @param integer $time_interval
     * Cada cuanto se va a repetir la tarea en formato unix
     * */
    public $time_interval = 0;

    /**
     * Task::time_exec
     * @param datetime $time_exec
     * Cuando se va a ejecutar la tarea en formato unix
     * */
    public $time_exec;

    /**
     * Task::time_last_exec
     * @param datetime $time_last_exec
     * Cuando fue la ultima vez que se ejecuto la tarea
     * */
    public $time_last_exec;

    /**
     * Task::run_once
     * @param integer $run_once
     * Si la tarea se ejecuta una sola vez
     * */
    public $run_once = 1;

    /**
     * Task::status
     * @param integer $status
     * Cual es el estado de la tarea
     * array (
     *  1 => 'activado',
     *  0 => 'desactivado',
     * );
     * */
    public $status = 1;

    /**
     * Task::sort_order
     * @param integer $sort_order
     * Cual es el orden o la secuencia de la tarea
     * */
    public $sort_order = 0;

    /**
     * Task::date_start_exec
     * @param datetime $date_start_exec
     * Cuando comienza a ejecutarse la tarea
     * */
    public $date_start_exec;

    /**
     * Task::date_end_exec
     * @param datetime $date_end_exec
     * Cuando termina de ejecutarse la tarea
     * */
    public $date_end_exec;

    /**
     * Task::expire
     * @param $expire
     * Tiempo de espera antes de considerar una tarea como olvidada
     * despues que fue iniciada su ejecucion
     * Por defecto es una hora (3600 segundos)
     * */
    private $expire = 3600;

    /**
     * Task::minute
     * @param $minute
     * Cada cuantos minutos se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� cada minuto
     * */
    private $minute = 0;

    /**
     * Task::hour
     * @param $hour
     * Cada cuantas horas se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� cada hora
     * La hora es en formato 24h
     * */
    private $hour = 0;

    /**
     * Task::day
     * @param $day
     * El d�a del mes en el cual se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� todos los dias
     * */
    private $day = 0;

    /**
     * Task::month
     * @param $month
     * El mes del a�o en el cual se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� todos los meses
     * */
    private $month = 0;

    /**
     * Task::dow
     * @param $dow
     * El d�a de la semana (Day Of Week) en el cual se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� todos los dias de la semana
     * */
    private $dow = 0;

    /**
     * Task::year
     * @param $year
     * Cada cuantos a�os se va a ejecutar la tarea
     * Si su valor es cero, se ejecutar� todos los a�os
     * */
    private $year = 0;

    /**
     * Task::now
     * @param $now
     * Variable que va a contener el tiempo de ahora para
     * comparar con las tareas y determinar si es tiempo de ejecutarla o no 
     * */
    private $now = [];

    /**
     * Task::dateTime
     * @param $dateTime
     * Objeto DateTime
     * */
    private $dateTime;

    /**
     * Task::timeZone
     * @param $timeZone
     * Timezone
     * */
    private $timeZone = 'America/Caracas';

    /**
     * Task::queue
     * @param $queue
     * Task Queue handler
     * */
    private $queue = [];

    /**
     * Task::db
     * @param $db
     * Objeto Database
     * */
    private $db;

    /**
     * Task::data
     * @param $data
     * Objeto Database
     * */
    private $data;

    /**
     * Task::object_id
     * @param $object_id
     * A que objeto le pertenece la tarea
     * */
    public $object_id;

    /**
     * Task::object_type
     * @param $object_type
     * El nombre de la tabla de la BD donde se encuentra el objeto
     * */
    public string $object_type;

    /**
     * Task::types
     * @param $types
     * Tipos de tareas
     * */
    protected $types = array(
        'send', // Enviar un email
        'sale', // Recordar la cobranza de una venta
        'enquiry', // Recordar una venta potencial sin concretar
        'report', // Enviar un informe o reporte de la tienda (Informe de ventas, clientes, web stats, etc.)
        'backup', // Realizar un backup de la base de datos y enviar por email el archivo fuente sql
    );

    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->now['minute'] = date('i');
        $this->now['hour'] = date('h');
        $this->now['day'] = date('d');
        $this->now['month'] = date('m');
        $this->now['year'] = date('Y');
        $this->now['date'] = date('D');
        $this->dateTime = new DateTime(date('Y-m-d h:i:s'), new DateTimeZone($this->timeZone));
    }

    public function __get($key) {
        return $this->data[$key];
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return (isset($this->data[$key]) && !empty($this->data[$key]));
    }

    public function addMinute($min = 1) {
        $this->dateTime->add(new DateInterval('PT' . (int) $min . 'M'));
        $this->time_exec = $this->dateTime->format('Y-m-d h:i:s');
        foreach ($this->queue as $key => $queue) {
            $this->queue[$key]['time_exec'] = $this->time_exec;
        }
    }

    public function addHour($hour = 1) {
        $this->dateTime->add(new DateInterval('PT' . (int) $hour . 'H'));
        $this->time_exec = $this->dateTime->format('Y-m-d h:i:s');
        foreach ($this->queue as $key => $queue) {
            $this->queue[$key]['time_exec'] = $this->time_exec;
        }
    }

    public function addDay($day = 1) {
        $this->dateTime->add(new DateInterval('P' . (int) $day . 'D'));
        $this->time_exec = $this->dateTime->format('Y-m-d h:i:s');
        foreach ($this->queue as $key => $queue) {
            $this->queue[$key]['time_exec'] = $this->time_exec;
        }
    }

    public function addMonth($month = 1) {
        $this->dateTime->add(new DateInterval('P' . (int) $month . 'M'));
        $this->time_exec = $this->dateTime->format('Y-m-d h:i:s');
        foreach ($this->queue as $key => $queue) {
            $this->queue[$key]['time_exec'] = $this->time_exec;
        }
    }

    public function addYear($year = 1) {
        $this->dateTime->add(new DateInterval('P' . (int) $year . 'Y'));
        $this->time_exec = $this->dateTime->format('Y-m-d h:i:s');
        foreach ($this->queue as $key => $queue) {
            $this->queue[$key]['time_exec'] = $this->time_exec;
        }
    }

    public function getCurrentTime() {
        return $this->dateTime->getTimestamp();
    }

    /**
     * Task::addQueue
     * Agrega una tarea a la cola de trabajo
     * @param mixed $data 
     * @return void
     * */
    public function addQueue($data) {
        $this->queue[] = $data;
    }

    /**
     * Task::removeTask
     * Elimina una tarea de la cola de trabajo
     * @param integer $id task_id
     * @return void
     * */
    public function removeTask($id) {
        $this->task = null;
        $this->type = null;
        $this->time_exec = null;
        $this->params = null;
        $this->time_interval = null;
        $this->time_last_exec = null;
        $this->run_once = null;
        $this->status = null;
        $this->sort_order = null;
        $this->date_start_exec = null;
        $this->date_end_exec = null;
        $this->queue = [];
        $this->db->query("DELETE FROM " . DB_PREFIX . "task WHERE task_id = '" . (int) $id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "task_queue WHERE task_id = '" . (int) $id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "task_exec WHERE task_id = '" . (int) $id . "'");
    }

    /**
     * Task::removeQueue
     * Elimina una tarea de la cola de trabajo
     * @param integer $id task_queue_id
     * @return void
     * */
    public function removeQueue($id) {
        foreach ($this->queue as $key => $queue) {
            if (in_array($id, $queue)) {
                unset($this->queue[$key]);
            }
        }
        $this->db->query("DELETE FROM " . DB_PREFIX . "task_queue WHERE task_queue_id = '" . (int) $id . "'");
    }

    /**
     * Task::setQueueDone
     * Establece la tarea como ejecutada de la cola de trabajo
     * @param integer task_queue_id 
     * @return void
     * */
    public function setQueueDone($id) {
        foreach ($this->queue as $key => $queue) {
            if (in_array($id, $queue)) {
                $this->queue[$key]['status'] = 0;
                $this->db->query("UPDATE " . DB_PREFIX . "task_queue SET `status` = 0 WHERE task_queue_id = '" . (int) $id . "'");
            }
        }
    }

    /**
     * Task::setTaskDone
     * Establece la tarea como ejecutada de la cola de trabajo
     * @param integer task_queue_id 
     * @return void
     * */
    public function setTaskDone($id = 0) {
        
        $id = ($id) ? $id : $this->task_id;
        
        if (!$id)
            return false;

        $this->status = 0;
        
        $this->db->query("UPDATE " . DB_PREFIX . "task SET `status` = 0 WHERE task_id = '" . (int) $id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "task_exec WHERE task_id = '" . (int) $id . "'");
        
        if ($this->task_id) {
            foreach ($this->queue as $key => $queue) {
                $this->setQueueDone($queue['task_queue_id']);
            }
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "task_queue SET `status` = 0 WHERE task_id = '" . (int) $id . "'");
        }
        /*
         * verificar si la tarea se repite o tiene un periodo
         * verificar que la fecha final de la ejecuci[on no haya llegado, es decir, que la tarea no haya vencido
         * si todo se cumple, reiniciar la cola de trabajo colocando status 1 a toda la cola
         * set status 1 a la tarea
         * dependiendo del periodo o repetici[on, sumarle el tiempo a time_exec
         * registrar eventos y notificar al administrador
         */
        //TODO: verificar si la tarea se repite y cual es el periodo para reiniciar la tarea
        //TODO: enviar informe de la tarea ejecutada al administrador
        //TODO: si la tarea se reinicia notificar al administrador del reinicio de la tarea
    }

    /**
     * Task::getTaskQueue
     * Devuelve todas las tareas de la cola de trabajo
     * @return void
     * */
    public function getTaskQueue($id = 0) {
        if ($id != 0) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "task_queue WHERE AND task_id = '" . (int) $id . "'");
            return $query->rows;
        } else {
            return $this->queue;
        }
    }

    /**
     * Task::getTaskDos
     * Obtiene todas las tareas pendientes de la cola de trabajo
     * @param integer $id task_id
     * @return void
     * */
    public function getTaskDos($id = 0) {
        if ($id != 0) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "task_queue WHERE status = 1 AND task_id = '" . (int) $id . "'");
            return $query->rows;
        } else {
            $result = [];
            foreach ($this->queue as $key => $queue) {
                if ($queue['status'] == 1) {
                    $result[] = $this->queue[$key];
                }
            }
            return $result;
        }
    }

    /**
     * Task::getTask
     * Devuelve una tarea
     * @param integer $id task_id
     * @return array task and load the object task
     * */
    public function getTask() {
        return $this;
    }

    /**
     * Task::createTask
     * Crea una tarea y la registra en la BD
     * @return integer task_id
     * */
    protected function createTask() {
        //TODO: validar datos de la tarea antes de guardarla
        //if ($this->checkTaskData()) { 
        $this->db->query("INSERT INTO " . DB_PREFIX . "task SET 
                store_id       = '" . (int) $this->store_id . "',
                object_id      = '" . (int) $this->object_id . "',
                object_type    = '" . $this->db->escape($this->object_type) . "',
                task           = '" . $this->db->escape($this->task) . "',
                type           = '" . $this->db->escape($this->type) . "',
                params         = '" . serialize($this->params) . "',
                time_interval  = '" . $this->db->escape($this->time_interval) . "',
                time_exec      = '" . $this->db->escape($this->time_exec) . "',
                time_last_exec = '" . (int) $this->time_last_exec . "',
                run_once       = '" . (int) $this->run_once . "',
                status         = '" . (int) $this->status . "',
                sort_order     = '" . (int) $this->getTaskSortOrder($this->time_exec) . "',
                date_start_exec= '" . $this->db->escape($this->date_start_exec) . "',
                date_end_exec  = '" . $this->db->escape($this->date_end_exec) . "'
           ");

        $task_id = $this->db->getLastId();

        foreach ($this->queue as $key => $queue) {
            if (isset($queue['task_queue_id'])) {
                $this->db->query("REPLACE INTO " . DB_PREFIX . "task_queue SET 
                       task_queue_id    = '" . (int) $queue['task_queue_id'] . "',
                       task_id          = '" . (int) $task_id . "',
                       params           = '" . serialize($queue['params']) . "',
                       time_exec      = '" . $this->db->escape($queue['time_exec']) . "',
                       status           = '" . (int) $queue['status'] . "',
                       sort_order       = '" . (int) $key . "'
                    ");
            } else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "task_queue SET 
                       task_id       = '" . (int) $task_id . "',
                       params           = '" . serialize($queue['params']) . "',
                       time_exec      = '" . $this->db->escape($queue['time_exec']) . "',
                       status         = '" . (int) $queue['status'] . "',
                       sort_order     = '" . (int) $key . "'
                    ");
            }
        }
        return $task_id;
        //}
        return false;
    }

    /**
     * Task::createSendTask
     * Crea una tarea para enviar email
     * @return integer task_id
     * */
    public function createSendTask() {
        $this->type = "send";
        return $this->createTask();
    }

    /**
     * Task::createSaleTask
     * Crea una tarea para recordar una cobranza
     * @return integer task_id
     * */
    public function createSaleTask() {
        $this->type = "sale";
        return $this->createTask();
    }

    /**
     * Task::createBackupTask
     * Crea una tarea para respaldar la BD
     * @return integer task_id
     * */
    public function createBackupTask() {
        $this->type = "backup";
        return $this->createTask();
    }

    /**
     * Task::createReportTask
     * Crea una tarea para enviar un informe
     * @return integer task_id
     * */
    public function createReportTask() {
        $this->type = "report";
        return $this->createTask();
    }

    /**
     * Task::createEnquiryTask
     * Crea una tarea para recordar una venta potencial
     * @return integer task_id
     * */
    public function createEnquiryTask() {
        $this->type = "enquiry";
        return $this->createTask();
    }

    /**
     * Task::checkType
     * Verifica que el tipo de tarea es permitido
     * @param string $type 
     * @return boolean
     * */
    protected function checkType() {
        return in_array($this->type, $this->types);
    }

    /**
     * Task::checkTaskData
     * Verifica que los datos de la tarea hayan sido asignados
     * @return boolean
     * */
    protected function checkTaskData() {
        if (!$this->checkType()) {
            return false;
        }

        if (empty($this->time_exec)) {
            return false;
        }

        if (empty($this->task)) {
            return false;
        }

        return true;
    }

    /**
     * Task::setStore
     * @param int $value 
     * @return void
     * */
    public function setStore($value) {
        $this->store_id = (int) $value;
    }

    /**
     * Task::setName
     * @param string $name 
     * @return void
     * */
    public function setName($name) {
        $this->task = $name;
    }

    /**
     * Task::setObject
     * @param integer $id el ID del objeto
     * @param string $type nombre de la tabla
     * @return void
     * */
    public function setObject($id, $type) {
        $this->object_id = (int) $id;
        $this->object_type = $type;
    }

    /**
     * Task::setParams
     * @param string $value 
     * @return void
     * */
    public function setParams($value) {
        $this->paramas = $value;
    }

    /**
     * Task::setTimeInterval
     * @param string $value 
     * @return void
     * */
    public function setTimeInterval($value) {
        $this->time_interval = $value;
    }

    /**
     * Task::setDateStart
     * @param int $value 
     * @return void
     * */
    public function setDateStart($value) {
        $this->date_start_exec = $value;
    }

    /**
     * Task::setDateEnd
     * @param datetime $value 
     * @return void
     * */
    public function setDateEnd($value) {
        $this->date_end_exec = $value;
    }

    /**
     * Task::setRunOnce
     * @param int $value 
     * @return void
     * */
    public function setRunOnce($value) {
        $this->run_once = (int) $value;
    }

    /**
     * Task::getTaskSortOrder
     * Devuelve el numero de secuencia de la proxima tarea para una fecha
     * @param integer $time_exec fecha de ejecucion en formato unix
     * @return integer next sort order
     * */
    public function getTaskSortOrder($time_exec) {
        $query = $this->db->query("SELECT COUNT(*) AS next FROM " . DB_PREFIX . "task 
           WHERE time_exec = '" . (int) $time_exec . "' 
           GROUP BY task_id
           ORDER BY sort_order DESC");
        return $query->row['next'] + 1;
    }

    /**
     * Task::lock
     * Bloquea todas las tareas en la cola de trabajo menos la que se esta ejecutando actualemten
     * @return void
     * */
    public function lock() {
        
    }

    /**
     * Task::unlock
     * Desbloquea todas las tareas en la cola de trabajo
     * @return void
     * */
    public function unlock() {
        
    }

    /**
     * Task::restart
     * Reinicia una tarea
     * @param integer $task_id
     * @return void
     * */
    public function restart($task_id) {
        
    }

    /**
     * Task::update
     * Actualiza una tarea
     * @param integer $task_id
     * @return void
     * */
    public function update() {
        //if ($this->checkTaskData()) { 
        $this->db->query("UPDATE " . DB_PREFIX . "task SET 
                store_id       = '" . (int) $this->store_id . "',
                object_id      = '" . (int) $this->object_id . "',
                object_type    = '" . $this->db->escape($this->object_type) . "',
                task           = '" . $this->db->escape($this->task) . "',
                type           = '" . $this->db->escape($this->type) . "',
                params         = '" . serialize($this->params) . "',
                time_interval  = '" . $this->db->escape($this->time_interval) . "',
                time_exec      = '" . $this->db->escape($this->time_exec) . "',
                time_last_exec = '" . (int) $this->time_last_exec . "',
                run_once       = '" . (int) $this->run_once . "',
                status         = '" . (int) $this->status . "',
                sort_order     = '" . (int) $this->getTaskSortOrder($this->time_exec) . "',
                date_start_exec= '" . $this->db->escape($this->date_start_exec) . "',
                date_end_exec  = '" . $this->db->escape($this->date_end_exec) . "'
                WHERE task_id = '" . (int) $this->task_id . "'
           ");

        foreach ($this->queue as $key => $queue) {
            if (isset($queue['task_queue_id'])) {
                $this->db->query("REPLACE INTO " . DB_PREFIX . "task_queue SET 
                       task_queue_id    = '" . (int) $queue['task_queue_id'] . "',
                       task_id          = '" . (int) $this->task_id . "',
                       params           = '" . $queue['params'] . "',
                       time_exec      = '" . $this->db->escape($queue['time_exec']) . "',
                       status           = '" . (int) $queue['status'] . "',
                       sort_order       = '" . (int) $key . "'
                    ");
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "task_queue SET 
                       task_id       = '" . (int) $this->task_id . "',
                       params           = '" . $queue['params'] . "',
                       time_exec      = '" . $this->db->escape($queue['time_exec']) . "',
                       status         = '" . (int) $queue['status'] . "',
                       sort_order     = '" . (int) $key . "'
                       WHERE task_queue_id = '" . (int) $key . "'
                    ");
            }
        }
        return $task_id;
        //}
        return false;
    }

    /**
     * Task::hold
     * Pone en espera una tarea para ser ejecutado despues
     * @param integer $task_id
     * @return void
     * */
    public function hold($task_id) {
        
    }

    /**
     * Task::getTimeExec
     * Devuelve la fecha y hora de la ejecucion de la tarea
     * @param integer $task_id
     * @return void
     * */
    public function getTimeExec() {
        
    }

    /**
     * Task::exec
     * Ejecuta la tarea
     * @return mixed
     * */
    public function exec() {
        
    }

    protected function isRunning($task_id) {
        $result = $this->db->query("SELECT * " . DB_PREFIX . "task WHERE task_id='" . (int) $task_id . "' ");
        return (bool) $result['running'];
    }

    public function start($task_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "task_exec WHERE `type` = '" . $this->db->escape($this->params['job']) . "'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "task_exec SET 
        task_id='" . $this->task_id . "',
        `type` = '" . $this->db->escape($this->params['job']) . "',
        status = 1,
        date_added = NOW()");
    }

    protected function stop($task_id) {
        $result = $this->db->query("UPDATE " . DB_PREFIX . "task SET running='0' WHERE task_id='" . (int) $task_id . "' ");
        $result = $this->db->query("INSERT INTO " . DB_PREFIX . "task_queue SET 
            `task_id` = '" . (int) $task_id . "',
            `script` = '" . (int) $task_id . "',
            `execution_time` = '" . (int) $task_id . "',
            `status` = '" . (int) $task_id . "',
            `time_exec` = '" . (int) $task_id . "',
            `next_time_exec` = '" . (int) $task_id . "',
            `date_added` = NOW()
        WHERE task_id='" . (int) $task_id . "' ");
    }

}
