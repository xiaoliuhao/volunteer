<?php
/**
 * Created by PhpStorm.
 * User: hawkenliu
 * Date: 2017/11/12
 * Time: 19:38
 */

/**
 * Class MYSQL_DB MYSQL
 */
class MYSQL_DB{
    private static $conn;
    private $host;
    private $user;
    private $passwd;
    private $dbname;
    /**
     * 单例模式连接数据库
     * @return bool|mysqli
     */
    public function getConn(){
        if(self::$conn){
            return self::$conn;
        }else {
            self::$conn = mysqli_connect($this->host, $this->user, $this->passwd, $this->dbname);
            if (self::$conn) {
                mysqli_select_db(self::$conn, $this->dbname);
                mysqli_query(self::$conn, "set names utf8");
                return self::$conn;
            } else {
                $this->error(__METHOD__,'connect');
                return false;
            }
        }
    }
    /**
     * MYSQL_DB constructor.
     * @param $dbname
     * @param string $host
     * @param string $user
     * @param string $passwd
     */
    function __construct($dbname, $host='202.202.43.139', $user = 'liu', $passwd= 'qq470401911'){
        $this->dbname = $dbname;
        $this->host   = $host;
        $this->user   = $user;
        $this->passwd = $passwd;
        self::$conn = $this->getConn();
        mysqli_select_db(self::$conn,$dbname);
    }
    /**
     * 查询
     * @param $table
     * @param $needs
     * @param $wheres
     * @return array 二维数组
     */
    public function select($table, $needs, $wheres){
        $sql = "select {$needs} from {$table} where ";
        //where条件
        foreach ($wheres as $key => $value){
            $sql .= $key."='{$value}' and ";
        }
        $sql = substr($sql, 0, -4);
        $data = $this->exeSql($sql);
        return $data;
    }
    /**
     * 插入数据
     * @param $table
     * @param array $key_values
     * @return array|bool|int
     */
    public function insert($table,array $key_values){
        if(empty($key_values)){
            return 0;
        }
        $sql = "insert into {$table} ";
        $keys = "(";
        $values = "values(";
        foreach($key_values as $key => $value){
            $keys .= "{$key},";
            $values .= "'{$value}',";
        }
        $sql = $sql.substr($keys, 0, -1).") ".substr($values, 0, -1).")";
        $result = $this->exeSql($sql);
        return $result;
    }
    /**
     * 更新数据
     * @param $table
     * @param array $new_datas
     * @param array $wheres
     * @return array|bool
     */
    public function update($table, array $new_datas,array $wheres){
        $set_sql   = "set ";
        $where_sql = "where ";
        foreach ($wheres as $key => $value){
            $where_sql .= $key."='{$value}' and ";
        }
        $where_sql = substr($where_sql, 0, -4);
        foreach ($new_datas as $key=>$value){
            $set_sql .= "{$key} = '{$value}', ";
        }
        $set_sql = substr($set_sql, 0, -2);
        $sql = "update {$table} {$set_sql} {$where_sql}";
        $bool = $this->exeSql($sql);
        return $bool;
    }

    /**
     * 执行sql语句
     * @param $sql
     * @return array|bool
     */
    function exeSql($sql){
        $sqltype = strtolower(substr(trim($sql),0,6));//截取sql语句前6个字符，判断sql语句类型,并转换成小写
        $result = mysqli_query(self::$conn, $sql) or die(mysqli_error(self::$conn));
        // mysqli_close(self::$conn);
        $rows = array();
        if("select"==$sqltype){
            if(false==$result){
                $this->error($sqltype, $sql);
                return false;
            }else if(0==mysqli_num_rows($result)){
                return false;
            }else{
                while($reslut_array=mysqli_fetch_array($result,MYSQLI_ASSOC)){
                    $rows[]=$reslut_array;
                }
                return $rows;
            }
        }else if("update"==$sqltype || "insert"==$sqltype || "delete"==$sqltype){
            if($result){
                return true;
            }else{
                // var_dump($sql);
                $this->error($sqltype, $sql.mysqli_error(self::$conn));
                return false;
            }
        }
    }
    /**
     * 添加错误日志
     * @param $function
     * @param $str
     */
    public function error($function, $str){
        $file_path = dirname(__FILE__).'/error/sql_error_log.txt';
        $file = fopen($file_path, "a+");
        $str = date("Y-m-d H:i:s")."\r\n{$function}  {$str}\r\n";
        fwrite($file, $str);
        fclose($file);
    }
    /**
     * 获取当前连接的数据库名称
     * @return mixed
     */
    public function getname(){
        return $this->dbname;
    }
    /**
     * 更换链接的数据库
     * @param $dbname
     * @return mixed
     */
    public function setname($dbname){
        mysqli_select_db(self::$conn, $dbname);
        return $this->dbname = $dbname;
    }
}