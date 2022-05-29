<?php

class Database
{
    /**
     * @return null|PDO
     *
     */
    public function connect()
    {
        include 'configs.php';
        $db = null;
        if ($db === null) {
            try {
                $dsn = 'mysql:host='.$dbHost.';dbname='.$dbName.';charset=utf8';
                $db = new PDO($dsn, $dbUserName, $dbPass);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }

    /**
     * @param $primary_id
     * @param $table
     * @param $where
     * @return mixed
     *
     * Toplam kayıt sayısını döndürür
     */
    public function total($primary_id, $table, $where)
    {
        if (is_null($where)) {
            try {
                $query_string = 'SELECT COUNT(' . $primary_id . ') FROM ' . $table . '';
                $r = $this->connect()->prepare($query_string);
                $r->execute();
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch (PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        } else {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value) {
                if ($where_count == 0) {
                    $where_string = 'WHERE ' . $key . ' = :' . $key;
                } else {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            try {
                $query_string = 'SELECT COUNT(' . $primary_id . ') FROM ' . $table . ' ' . $where_string . '';
                $r = $this->connect()->prepare($query_string);
                $r->execute($where);
                $count = $r->fetch(PDO::FETCH_COLUMN);
                return $count;
            } catch (PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }

    /**
     * @param $primary_id
     * @param string $colums
     * @param $table
     * @param $where
     * @param $order_by
     * @param $order_sort
     * @param bool $pageable
     * @param int $adet
     * @param int $sayfa
     * @return mixed
     *
     * Array olarak tüm verileri döndürür.
     */

    public function result($primary_id, $colums = "*", $table, $where, $order_by, $order_sort, $pageable = false, $adet = 0, $sayfa = 0)
    {
        if (is_null($where)) {
            $query_string = 'SELECT ' . $colums . ' FROM ' . $table/*.' ORDER BY '.$order_by.' '.$order_sort.''*/
            ;
            try {
                if ($pageable == true) {
                    $get_total_count = $this->total($primary_id, $table, $where);
                    if ($get_total_count == 0) {
                        return null;
                    } else {
                        $toplam_sayfa = ceil($get_total_count / $adet);
                        $sayfa = (int)$sayfa;
                        if ($sayfa < 1) $sayfa = 1;
                        if ($sayfa > $toplam_sayfa) $sayfa = $toplam_sayfa;
                        $limit = ($sayfa - 1) * $adet;
                        $query = $this->connect()->prepare($query_string . ' LIMIT ' . $limit . ', ' . $adet . '');
                        $query->execute();
                        return $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                } else {
                    $query = $this->connect()->prepare($query_string);
                    $query->execute();
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        } else {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value) {
                if ($where_count == 0) {
                    $where_string = 'WHERE ' . $key . ' = :' . $key;
                } else {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            $query_string = 'SELECT ' . $colums . ' FROM ' . $table . ' ' . $where_string . ' ORDER BY ' . $order_by . ' ' . $order_sort . '';
            try {
                if ($pageable == true) {
                    $get_total_count = $this->total($primary_id, $table, $where);
                    if ($get_total_count == 0) {
                        return null;
                    } else {
                        $toplam_sayfa = ceil($get_total_count / $adet);
                        $sayfa = (int)$sayfa;
                        if ($sayfa < 1) $sayfa = 1;
                        if ($sayfa > $toplam_sayfa) $sayfa = $toplam_sayfa;
                        $limit = ($sayfa - 1) * $adet;
                        $query = $this->connect()->prepare($query_string . ' LIMIT ' . $limit . ', ' . $adet . '');
                        $query->execute($where);
                        return $query->fetchAll(PDO::FETCH_ASSOC);
                    }
                } else {
                    $query = $this->connect()->prepare($query_string);
                    $query->execute($where);
                    return $query->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }

    /**
     * @param string $colums
     * @param $table
     * @param $where
     * @param null $order_by
     * @param null $order_sort
     * @return null
     *
     * Row olarak tek veri döndürür.
     */
    public function row($colums = "*", $table, $where, $order_by = null, $order_sort = null)
    {
        if (is_null($where)) {
            return null;
        } else {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value) {
                if ($where_count == 0) {
                    $where_string = 'WHERE ' . $key . ' = :' . $key;
                } else {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            if ($order_by != null && $order_sort != null) {
                $query_string = 'SELECT ' . $colums . ' FROM ' . $table . ' ' . $where_string . ' ORDER BY ' . $order_by . ' ' . $order_sort;
            } else {
                $query_string = 'SELECT ' . $colums . ' FROM ' . $table . ' ' . $where_string;
            }
            try {
                $query = $this->connect()->prepare($query_string);
                $query->execute($where);
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
            }
        }
    }


    /**
     * @param $table
     * @param $values
     * @return mixed
     *
     * Veri ekleme
     */
    public function insert($table, $values)
    {
        try {
            $values_string = "";
            $colums_string = "";
            $value_count = 0;
            foreach ($values as $key => $value) {
                if ($value_count == 0) {
                    $colums_string = $key;
                    $values_string = ':' . $key;
                } else {
                    $colums_string = $colums_string . ', ' . $key;
                    $values_string = $values_string . ', :' . $key;
                }
                $value_count++;
            }
            $query_string = 'INSERT INTO ' . $table . ' (' . $colums_string . ') VALUES (' . $values_string . ')';
            $query = $this->connect()->prepare($query_string);
            foreach ($values as $key => &$value) {
                $query->bindParam(':' . $key, $value);
            }
            return $query->execute();
        } catch (PDOException $e) {
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * @param $table
     * @param $values
     * @param $where
     * @return mixed
     *
     * Veri düzenleme fonksiyonu
     */
    public function update($table, $values, $where)
    {
        try {
            $values_string = "";
            $value_count = 0;
            foreach ($values as $key => $value) {
                if ($value_count == 0) {
                    $values_string = $key . ' = :' . $key;
                } else {
                    $values_string = $values_string . ', ' . $key . ' = :' . $key;
                }
                $value_count++;
            }
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value) {
                if ($where_count == 0) {
                    $where_string = ' WHERE ' . $key . ' = :' . $key;
                } else {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            $query_string = 'UPDATE ' . $table . ' SET ' . $values_string . $where_string . '';
            $query = $this->connect()->prepare($query_string);
            foreach ($values as $key => &$value) {
                $query->bindParam(':' . $key, $value);
            }
            foreach ($where as $key => &$value) {
                $query->bindParam(':' . $key, $value);
            }
            return $query->execute();
        } catch (PDOException $e) {
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * @param $table
     * @param $where
     * @return mixed
     *
     * Veri silme fonksiyonu
     */
    public function delete($table, $where)
    {
        try {
            $where_string = "";
            $where_count = 0;
            foreach ($where as $key => $value) {
                if ($where_count == 0) {
                    $where_string = ' WHERE ' . $key . ' = :' . $key;
                } else {
                    $where_string = $where_string . ' AND ' . $key . ' = :' . $key;
                }
                $where_count++;
            }
            $query_string = 'DELETE FROM ' . $table . $where_string;
            $query = $this->connect()->prepare($query_string);
            foreach ($where as $key => &$value) {
                $query->bindParam(':' . $key, $value);
            }
            return $query->execute();
        } catch (PDOException $e) {
            trigger_error('Wrong SQL: ' . $query_string . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }

    public function query($sql)
    {
        try {
            $query = $this->connect()->prepare($sql);
            $query->execute();
            if (strpos($sql, 'SELECT') !== false){
                if(strpos($sql, 'COUNT') !== false)
                    return $query->fetch(PDO::FETCH_COLUMN);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }else{
                return "Çalıştı ama geri döndürülücek bi değer yok.";
            }
        } catch (PDOException $e) {
            trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $e->getMessage(), E_USER_ERROR);
        }
    }
}

?>