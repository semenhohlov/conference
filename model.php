<?php
namespace Model;
use DB\DB;

class Model {
    private string $tableName;
    private Db $db;
    private bool $isDeleted;
    protected array $fields;
    public array $many;
    public Model $one;
    public $id;
    public function __construct(DB $db, ?int $id=0, ?string $tableName='products')
    {
        $this->id = $id;
        $this->isDeleted = false;
        $this->tableName = $tableName;
        $this->fields = [];
        $this->db = $db;
        // заплоняем поля
        if ($id)
        {
            $sql = 'select * from '.$this->tableName.' where id = '.$id;
            if ($this->db->exec($sql))
            {
                $result = $this->db->fetchAssoc();
                if ($result)
                {
                    foreach($result as $key => $value)
                    {
                        if (is_string($value))
                        {
                            $value = trim($value);
                        }
                        $this->$key = $value;
                        $this->fields[] = $key;
                    }
                }
            } else {
                echo 'DB.Error: '.$this->db->error;
            }
        }
    }
    static public function create(DB $db, $fields)
    {
        // создаем запись
        $tmp = new static($db);
        $flds = [];
        $fldsDots = [];
        foreach($fields as $key => $value)
        {
            $flds[] = $key;
            $fldsDots[] = ':'.$key;
        }
        $sql = 'insert into '.$tmp->tableName.'('.implode(', ', $flds).') values ('.implode(', ', $fldsDots).')';
        $statement = $db->prepare($sql);
        foreach($fields as $key => $value)
        {
            $statement->bindValue(':'.$key, $value);
        }
        $statement->execute();
        return new static($db, $db->lastInsertId());
    }
    static public function getAll(DB $db)
    {
        $result = [];
        $tmp = new static($db);
        if ($db->exec('select id from '.$tmp->tableName))
        {
            $rows = $db->fetchAssocAll();
            foreach($rows as $row )
            {
                $result[] = new static($db, $row['id']);
            }
        } else {
            echo 'DB.Error: '.$db->error;
        }
        return $result;
    }
    static public function getOne(DB $db, $id)
    {
        return new static($db, $id);
    }
    static public function getFirst(DB $db)
    {
        $tmp = new static($db);
        $db->exec('select id from '.$tmp->tableName.' limit 1');
        $row = $db->fetchAssoc();
        return new static($db, $row['id']);
    }
    public function save()
    {
        if ($this->isDeleted)
        {
            // echo 'Данная запись уже удалена<br>';
            return;
        }
        $sql_set = '';
        foreach($this->fields as $key){
            $sql_set .= $key.' = :'.$key.', ';
        }
        $sql_set = substr($sql_set, 0, -2);
        $sql = 'update '.$this->tableName. ' set '.$sql_set.' where id = '.$this->id;
        $statement = $this->db->prepare($sql);
        foreach($this->fields as $key)
        {
            $statement->bindValue(':'.$key, $this->$key);
        }
        $statement->execute();
    }
    public function delete()
    {
        if (!$this->db->exec('delete from '.$this->tableName.' where id = '.$this->id))
        {
            echo 'DB.Error deleting: '.$this->db->error;
        }
        $this->isDeleted = true;
    }
    static public function where(DB $db, string $tableName, string $condition)
    {
        $result = [];
        $sql = 'select id from '.$tableName.' where '.$condition;
        if ($db->exec($sql))
        {
            $rows = $db->fetchAssocAll();
            foreach($rows as $row )
            {
                $result[] = new static($db, $row['id'], $tableName);
            }
        } else {
            echo 'DB.Error: '.$db->error;
        }
        return $result;
    }
    public function hasOne(string $tableName, string $foreignKey)
    {
        $this->one = new Model($this->db, $this->$foreignKey, $tableName);
    }
    // таблица, внешний ключ, вневший id
    public function hasMany(string $tableName, string $foreignKey, $foreignKeyID)
    {
        $condition = $foreignKeyID.' = '.$this->$foreignKey;
        $this->many = Model::where($this->db, $tableName, $condition);
    }
}

class Country extends Model {
    private string $tableName;
    public function __construct(DB $db, ?int $id=0)
    {
        parent::__construct($db, $id, 'countries');
        $this->hasMany('conferences', 'id', 'id');
    }
}
class Conference extends Model {
    private string $tableName;
    public function __construct(DB $db, ?int $id=0)
    {
        parent::__construct($db, $id, 'conferences');
        $this->hasOne('countries', 'country_id');
        $this->date = date("d.m.Y в H:i:s", strtotime($this->conf_date));
    }
}

?>