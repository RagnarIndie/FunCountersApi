<?php

namespace Test\Counters\Library;


use Test\Counters\Library\Interfaces\IModel;

abstract class PDOModel implements IModel
{
    /**
     * @var integer
     */
    protected $id = 0;

    /**
     * Main application
     * @var \Test\Counters\Library\Application
     */
    protected $application = null;

    /**
     * Default fetch mode for PDO statements
     * @var int
     */
    protected $fetchMode = null;

    abstract function getTableName(): string;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param $value
     */
    public function setId(int $value)
    {
        $this->id = $value;
    }

    /**
     * Gets called model name
     *
     * @return string
     */
    public function getClassName(): string {
        return get_called_class();
    }

    /**
     * PDOModel constructor.
     * @param $application
     */
    public function __construct($application)
    {
        $this->application = $application;
        $this->fetchMode = \PDO::FETCH_OBJ;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findAll(int $page = 1, int $limit = 50) {
        $result = [];

        $query = "SELECT * FROM {$this->getTableName()}";

        if ($page > 0 && $limit > 0) {
            $query .= " LIMIT {$page},{$limit}";
        }

        $statement = $this->application->db->query($query);
        $objects = $statement->fetchAll($this->fetchMode);

        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $this->setObjectAttributes($object);
                array_push($result, clone($this));
            }
        }

        return $result;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        $statement = $this->application->db->prepare("SELECT * FROM {$this->getTableName()} WHERE id=:id");
        $statement->execute(['id' => $id]);

        $result = $statement->fetch($this->fetchMode);

        if ($result) {
            $this->setObjectAttributes($result);
        }
    }

    /**
     * @param string $condition
     */
    public function count(string $condition = '')
    {
        $query = "SELECT count(*) FROM {$this->getTableName()}";

        if (!empty($query)) {
            $query .= ' WHERE '.$condition;
        }

        $result = $this->application->db->prepare($query);
        $result->execute();

        return $result->fetchColumn();
    }

    /**
     * @param $query
     * @return array
     */
    public function query(string $query)
    {
        $result = $this->application->db->prepare($query);
        $result->execute();

        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function columnAll(string $query)
    {
        $result = $this->application->db->prepare($query);
        $result->execute();

        return $result->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function save()
    {
        $isNew = ($this->getId()) ? false : true;
        $data = [];

        foreach ($this as $key => $val) {
            //Skip system fields
            if ($key == 'application' || $key == 'fetchMode' || $key == 'id') {
                continue;
            }

            //Skip empty fields
            if (empty($val)) {
                continue;
            }

            $colName = $this->fromCamelCase($key);
            $data[$colName] = $val;
        }

        if (count($data) > 0) {
            if ($isNew) {
                //Do INSERT
                $fields = array_keys($data);
                $values = array_values($data);
                $fieldList = implode(',', $fields);
                $qStr = str_repeat("?,",count($fields) - 1);
                $query = "INSERT INTO {$this->getTableName()} ($fieldList) VALUES ({$qStr}?)";
                $statement = $this->application->db->prepare($query);
                $statement->execute($values);

                $this->setId($this->application->db->lastInsertId());
            } else {
                //Do UPDATE by id
                $query = "UPDATE {$this->getTableName()} SET";
                $values = [];

                foreach ($data as $name => $value) {
                    $query .= ' '.$name.' = :'.$name.',';
                    $values[':'.$name] = $value;
                }

                $query = substr($query, 0, -1)." WHERE id={$this->getId()};";
                $statement = $this->application->db->prepare($query);
                $statement->execute($values);
            }
        }
    }

    /**
     * Fills current model with results
     *
     * @param $result
     */
    protected function setObjectAttributes($result)
    {
        foreach ($result as $prop => $value) {
            $setterName = "set".$this->toCamelCase($prop, true);

            if (method_exists($this, $setterName)) {
                $this->{$setterName}($value);
            }
        }
    }

    /**
     * Converts underscored string to camelCased
     *
     * @param string $string
     * @param bool $capitalizeFirstCharacter
     * @return string
     */
    protected function toCamelCase(string $string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * Converts camelCase to underscored string
     *
     * @param string $string
     * @return string
     */
    protected function fromCamelCase(string $string) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}