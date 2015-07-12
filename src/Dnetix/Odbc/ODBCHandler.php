<?php namespace Dnetix\Odbc;

/**
 * Class ODBCHandler
 * Utility class for managing ODBC connections especially with IBS
 *
 * @author Diego Calle
 * @package Dnetix\Odbc
 */
class ODBCHandler {

    private $conn = FALSE;

    private $dns = FALSE;
    private $user;
    private $pass;

    private $_error;

    private $_isexcel = FALSE;

    // Constantes de respuesta de tablas de sistema
    const TABLE_SCHEM = 'TABLE_SCHEM';
    const TABLE_NAME = 'TABLE_NAME';
    const TABLE_TYPE = 'TABLE_TYPE';
    const TABLE_REMARKS = 'REMARKS';

    public function __construct($params = array()) {
        if (isset($params['dns'])) {
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function setDNS($dns) {
        $this->dns = $dns;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function setPass($pass) {
        $this->pass = $pass;
    }

    public function setExcel($bool) {
        $this->_isexcel = $bool;
    }

    public function connect() {
        $this->conn = odbc_connect($this->dns, $this->user, $this->pass);
        if (!$this->conn) {
            $this->_error = odbc_errormsg();
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Retorna un array con las tablas accesibles por el ODBC. Cada array tiene los encabezados
     * KEY => VALUE y los key son las constantes TABLE_*
     */
    public function getTables() {
        $tables = array();
        if ($this->_isConnected()) {
            $result = odbc_tables($this->conn);
            if (!$result) {
                $this->_error = "No fue posible consultar las respuestas";
                return null;
            }
            while ($row = odbc_fetch_array($result)) {
                $tables[] = $row;
            }
        } else {
            $this->_error = "No se encuentra conectado a ningun ODBC";
            return null;
        }
        return $tables;
    }

    /**
     * Obtiene los encabezados o columnas de la tabla ingresada
     * @param $table
     * @return array|null
     */
    public function getHeadersTable($table) {
        if ($this->_isConnected()) {
            $table = $this->parseTable($table);
            $query = 'SELECT * FROM ' . $table;
            $result = odbc_exec($this->conn, $query);
            if (!$result) {
                $this->_error = "No existe la tabla que desea consultar";
                return null;
            }
            $i = 1;
            $columnsNames = array();
            $j = odbc_num_fields($result);
            while ($i <= $j) {
                $columnsNames[] = odbc_field_name($result, $i);
                $i++;
            }
            return $columnsNames;
        } else {
            $this->_error = "No se encuentra conectado a ningun ODBC";
            return null;
        }
    }

    /**
     * Realiza una consulta al ODBC
     * @param $query
     * @return array|null
     */
    public function query($query) {
        if ($this->_isConnected()) {
            $result = odbc_exec($this->conn, $query);
            if (!$result) {
                $this->_error = "No se ha podido realizar la consulta al ODBC";
                return null;
            }
            $results = array();
            while ($row = odbc_fetch_array($result)) {
                $results[] = $row;
            }
            return $results;
        } else {
            $this->_error = "No se encuentra conectado a ningun ODBC";
            return null;
        }
    }

    /**
     * Comprueba que se haya realizado una conexion al ODBC
     */
    private function _isConnected() {
        if ($this->conn) {
            return TRUE;
        } else {
            if ($this->dns) {
                return $this->connect();
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Retorna el nombre de la tabla enviado, si la conexion es a Excel, agrega
     * los caracteres especiales.
     */
    public function parseTable($table) {
        if ($this->_isexcel) {
            $table = '[' . $table . '$]';
        }
        return $table;
    }

    /**
     * Retorna el mensaje de error.
     */
    public function getErrors() {
        return $this->_error;
    }

    public function getODBCErrors() {
        return odbc_errormsg();
    }

    public function getResource() {
        return $this->conn;
    }

}
