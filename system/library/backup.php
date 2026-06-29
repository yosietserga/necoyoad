<?php
/**
 * Backup
 *
 * @package NecoTienda Standalone
 * @author Yosiet Serga
 * @copyright NecoTienda
 * @version 2012
 * @access public
 */
class Backup
{
    protected $db;
    protected $load;

    public $backup_dir = "";

    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->load = $registry->get('load');

        $this->backup_dir = DIR_BACKUP . date('dmYhis');
        if (!is_dir($this->backup_dir)) {
            mkdir($this->backup_dir,0777);
        }
    }

    public function zipData($source, $destination, $ignore=null, $content=null) {
        if (extension_loaded('zip')) {
            if (file_exists($source)) {
                $zip = new ZipArchive();
                if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
                    $source = realpath($source);
                    if (is_dir($source)) {
                        $iterator = new RecursiveDirectoryIterator($source);
                        // skip dot files while iterating
                        $iterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
                        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
                        foreach ($files as $file) {
                            $file = realpath($file);
                            $path = substr($source, 0, strrpos($source, DIRECTORY_SEPARATOR) + 1);
                            $filename = str_replace($path, '', $file);
                            if (strlen($filename) > 0) {
                                if (is_dir($file)) {
                                    if ((isset($ignore) && !in_array($filename, explode('|', $ignore['directories']))) || !isset($ignore)) {
                                        $zip->addEmptyDir($filename);
                                    }
                                } else if (file_exists($file) && is_file($file)) {
                                    $zip->addFromString($filename, file_get_contents($file));
                                }
                            }
                        }
                    } elseif (is_file($source)) {
                        $zip->addFromString(basename($source), file_get_contents($source));
                    } elseif (isset($content)) {
                        $zip->addFromString($source, $content);
                    }
                }
                return $zip->close();
            }
        }

        return false;
    }

    public function run() {
        $max_execution_time = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        ini_set('max_execution_time', 600);
        ini_set('memory_limit','1024M');

        if (!is_dir(DIR_ROOT . "backups")) {
            mkdir(DIR_ROOT . "backups", '0755');
        }

        $this->zipData(
            DIR_ROOT . 'app/',
            $this->backup_dir . "/backup_". time() ."_app.zip"
        );

        $this->zipData(
            DIR_ROOT . 'system/',
            $this->backup_dir . "/backup_". time() ."_system.zip",
            array(
                'directories'=>
                    'system'. DIRECTORY_SEPARATOR .'cache|'.
                    'system'. DIRECTORY_SEPARATOR .'logs|'.
                    'system'. DIRECTORY_SEPARATOR .'logs'. DIRECTORY_SEPARATOR .'frontend'
            )
        );

        $query = $this->db->getTables();

        foreach ($query->rows as $result) {
            $tables[] = $result['Tables_in_' . DB_DATABASE];
        }

        $output = '';

        foreach ($tables as $table) {
            if (defined('DB_PREFIX')) {
                if (strpos($table, DB_PREFIX) === false) {
                    $status = false;
                } else {
                    $status = true;
                }
            } else {
                $status = true;
            }

            if ($status) {
                $this->db->query('LOCK TABLES `' . $table . '` WRITE');
                $q = $this->db->query('SHOW CREATE TABLE `' . $table . '`');
                $output .= str_replace("CREATE TABLE","CREATE TABLE IF NOT EXISTS",$q->row['Create Table']) . "\n\n";
                $output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";
                $query = $this->db->query("SELECT * FROM `" . $table . "`");
                foreach ($query->rows as $result) {
                    $fields = '';
                    foreach (array_keys($result) as $value) {
                        $fields .= '`' . $value . '`, ';
                    }
                    $values = '';
                    foreach (array_values($result) as $value) {
                        $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
                        $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
                        $value = str_replace('\\', '\\\\',	$value);
                        $value = str_replace('\'', '\\\'',	$value);
                        $value = str_replace('\\\n', '\n',	$value);
                        $value = str_replace('\\\r', '\r',	$value);
                        $value = str_replace('\\\t', '\t',	$value);

                        $values .= '\'' . $value . '\', ';
                    }
                    $output .= 'INSERT IGNORE INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
                }
                $output .= "\n\n";
                $this->db->query('UNLOCK TABLES');
            }
        }

        $sql_filename = $this->backup_dir . "/backup_". time() .".sql.zip";

        $this->zipData(
            'sqldump.sql',
            $sql_filename,
            null,
            $output
        );

        $checksum = sha1_file($sql_filename);
        $fd = fopen ($this->backup_dir . "/backup_". time() .".sql.zip.CKECKSUM", "w");
        fwrite ($fd, $checksum);
        fclose ($fd);

        ini_set('max_execution_time', $max_execution_time);
        ini_set('memory_limit',$memory_limit);
    }
}