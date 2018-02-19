<?php

namespace CLIDatabaseIterator;

use \Symfony\Component\Console\Helper\ProgressBar;
use \Symfony\Component\Console\Output\ConsoleOutput;

class Iterator extends ProgressBar
{
    public static $dryRun = false;
    public static $dbName;

    /**
     * @var \mysqli
     */
    private $mysql;

    /**
     * @var bool|\mysqli_result
     */
    private $result;

    /**
     * @var ConsoleOutput
     */
    private $out;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * KorbenDb constructor.
     *
     * @param string $sql
     */
    public function __construct($sql)
    {
        $this->out = new ConsoleOutput();

        if (!$this::DRY_RUN) {
            $this->out->writeln('<comment>WARNING! Write access enable!</comment>');
        } else {
            $this->out->writeln('<info>Dry run</info>');
        }

        $this->mysql = new \mysqli('localhost', 'root', 'cyan', $this::$dbName);

        if ($this->mysql->connect_errno) {
            echo "Error: Failed to make a MySQL connection, here is why: \n";
            echo "Errno: " . $this->mysql->connect_errno . PHP_EOL;
            echo "Error: " . $this->mysql->connect_error . PHP_EOL;
            exit;
        }

        /************************/
        $this->result = $this->query($sql);
        /************************/

        if ($this->result->num_rows === 0) {
            $this->out->writeln('<info>Nothing.</info>');
            exit;
        }

        parent::__construct($this->out, $this->result->num_rows + 1);
        $this->setFormat('verbose');
        $this->start();
    }


    /**
     * @param string $sql
     * @param bool $write
     * @return bool|\mysqli_result
     */
    public function query($sql, $write = false)
    {
        if ($write && $this::$dryRun) {
            return false;
        }
        if (!$result = $this->mysql->query($sql)) {
            echo "Query: " . $sql . PHP_EOL;
            echo "Errno: " . $this->mysql->errno . PHP_EOL;
            echo "Error: " . $this->mysql->error . PHP_EOL;
            exit;
        }

        return $result;
    }

    /**
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return $this->mysql->real_escape_string($string);
    }

    /**
     * @return array
     */
    public function fetch()
    {
        $this->advance();
        return $this->result->fetch_assoc();
    }

    /**
     * @param string $str
     * @param string|integer $id
     */
    public function alert($str, $id)
    {
        $this->clear();
        $this->out->writeln("<info>$id</info>:$str");
        $this->display();
    }
}
