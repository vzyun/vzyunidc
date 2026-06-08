<?php
/**
 * vzyunIDC - 数据库操作类
 */

class DB {
    private static $instance = null;
    private $pdo;
    private $prefix;

    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $this->prefix = DB_PREFIX;
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function table($table) {
        return $this->prefix . $table;
    }

    /**
     * 执行查询
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * 获取单行
     */
    public function getRow($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * 获取多行
     */
    public function getRows($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * 获取单个值
     */
    public function getOne($sql, $params = []) {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * 插入数据
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(',:', $fields);
        $sql = 'INSERT INTO ' . $this->table($table) . ' (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    /**
     * 更新数据
     */
    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = $field . ' = :' . $field;
        }
        $sql = 'UPDATE ' . $this->table($table) . ' SET ' . implode(',', $sets) . ' WHERE ' . $where;
        return $this->query($sql, array_merge($data, $whereParams))->rowCount();
    }

    /**
     * 删除数据
     */
    public function delete($table, $where, $params = []) {
        $sql = 'DELETE FROM ' . $this->table($table) . ' WHERE ' . $where;
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * 分页查询
     */
    public function paginate($table, $where = '', $params = [], $order = 'id DESC', $page = 1, $pageSize = 20) {
        $whereSql = $where ? ' WHERE ' . $where : '';
        $count = $this->getOne('SELECT COUNT(*) FROM ' . $this->table($table) . $whereSql, $params);
        $offset = ($page - 1) * $pageSize;
        $rows = $this->getRows('SELECT * FROM ' . $this->table($table) . $whereSql . ' ORDER BY ' . $order . ' LIMIT ' . $offset . ',' . $pageSize, $params);
        return [
            'total' => (int)$count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => ceil($count / $pageSize),
            'rows' => $rows,
        ];
    }
}
