<?php
class DbManager
{
    private $conn;
    private $table_name;
    private $last_error = '';
    public function __construct($table_name) {
        $this->table_name = $table_name;

        // Create connection
        $this->conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function add($entry) {
        $columns_str = '';
        $values_str = '';
        foreach($entry as $key => $value) {
            $columns_str .= $key . ',';
            $values_str .= "'" . $this->conn->real_escape_string($value) . "',";
        }

        $columns_str = rtrim($columns_str, ',');
        $values_str = rtrim($values_str, ',');

        $sql = "INSERT INTO " . $this->table_name . " ($columns_str)
        VALUES ($values_str)";

        if ($this->conn->query($sql) === TRUE) {
            return $this->conn->insert_id;
        }

        $this->last_error =  "Error: " . $sql . "<br>" . $this->conn->error;
        return false;
    }

    public function update($id, $entry) {

    }

    public function getAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($sql);

        if ($result != false) {
            $all_todos = [];
            foreach ($result->fetch_all(MYSQLI_ASSOC) as $entry) {
                $all_todos[$entry['id']] = $entry;
            }
            return $all_todos;
        }

        $this->last_error = $this->conn->error;

        return [];
    }

    public function getAllByUser($id) {
        $todo_table_name = $this->table_name;
        $sql = "SELECT $todo_table_name.id as todoid, $todo_table_name.task, user.username, user.id
        FROM $todo_table_name
        INNER JOIN user
        ON $todo_table_name.user_id=user.id where user.id = $id;";
        $result = $this->conn->query($sql);

        if ($result != false) {
            $all_todos = $result->fetch_all(MYSQLI_ASSOC);
            print_r($all_todos);
        }
        else {
            echo $this->conn->error;
        }
    }

    public function __desctruct() {
        $this->conn->close();
    }
}