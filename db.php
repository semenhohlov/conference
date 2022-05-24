<?php
namespace DB;
use \PDO;

class DB {
    var $dbh;
    var $query;
    var $error;
    var $result;
    public function __construct($driver='pgsql', 
        $host='localhost',
        $dbname='test',
        $user='sem',
        $password='123')
    {
        $this->dbh = new PDO($driver.':host='.$host.';dbname='.$dbname, $user, $password);
        $this->query = '';
        $this->result = null;
        $this->error = '';
    }
    public function __destruct()
    {
        // closing connection
        $this->result = null;
        $this->dbh = null;
    }
    public function exec($query)
    {
        $this->query = $query;
        $this->result = null;
        $this->error = '';
        try
        {
            $this->result = $this->dbh->query($this->query);
            return true;
        } catch(PDOException $e)
        {
            $this->error = $e->getMessage();
            return false;
        }
    }
    public function fetchAssoc()
    {
        if ($this->result)
        {
            try
            {
                return $this->result->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return null;
            }
        }
        return null;
    }
    public function fetchAssocAll()
    {
        if ($this->result)
        {
            try
            {
                return $this->result->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return null;
            }
        }
        return null;
    }
    public function prepare($sql)
    {
        return $this->dbh->prepare($sql);
    }
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}

?>