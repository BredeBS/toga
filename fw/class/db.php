<?php

/**
 * Clase DB
 * Permite la conexión con la base de datos
 *
 * @author
 * @copyright Copyright (c) 2008 - 2017, Brede Basualdo Serraino
 * @version 2.2
 * @package AdminPlus
 */
//include_once("../conf.inc.php");
if (!class_exists("Util")) {
    include_once("../clases/clase.util.php");
}
if (!class_exists("Conexion")) {

    class Conexion {

        var $depura;
        var $con;
        protected $_Config;

        public function __construct($a = false) {
            global $_Config;
            $this->_Config = $_Config;
            if ($this->_Config["depuraDB"]) {
                $this->depura = true;
            }
            if ($a) {

                $this->depura = $a;
            }
        }

        /**
         * Realiza un select directo
         * @param string $Campos
         * @param string $Tabla
         * @param string $Condicion
         * @return <type>
         */
        function SeleccionarTodo($Campos, $Tabla, $Condicion) {
            $CamposLeer = "";
            if (is_array($Campos)) {
                $total = count($Campos);
                for ($i = 0; $i < $total; $i++) {
                    $CamposLeer .= $Campos[$i];
                    if ($i < $total - 1) {
                        $CamposLeer.=",";
                    }
                }
            } else {
                $CamposLeer = $Campos;
            }
            $Consulta = "Select " . $CamposLeer . " from " . $Tabla;
            if (trim($Condicion) != "") {
                $Consulta .= " Where " . $Condicion;
            }
            return $this->Execute($Consulta);
        }

        function SeleccionarUno($Campo, $Tabla, $Condicion) {
            $Consulta = "Select " . $Campo . " from " . $Tabla;
            if (trim($Condicion) != "") {
                $Consulta .= " Where " . $Condicion;
            }
            $resultArray = $this->Execute($Consulta);
            if (strpos($Campo, ".") === FALSE) {

            } else {
                $tempo = split("\.", $Campo);
                $Campo = $tempo[1];
            }
            if (count($resultArray) == 0) {
                return "0";
            }
            return $resultArray[0][$Campo];
        }

        /**
         * Extrae un ítem a partir de una consulta SQL
         * @param <type> $Consulta
         * @param <type> $Arreglo
         * @return <type> resultado
         */
        function GetOne($Consulta, $Arreglo = false) {
            $resultado = $this->Execute($Consulta, $Arreglo);
            if (count($resultado) == 0) {
                return "";
            }
            $pos = strrpos($Consulta, " as ");
            if ($pos === false) { // note: three equal signs
                $dato = preg_split('/ /', $Consulta);
                return $resultado[0][$dato[1]];
            } else {
                $dato = preg_split('/\ as /', $Consulta);
                $dato = preg_split('/ /', $dato[1]);
                return $resultado[0][$dato[0]];
            }
        }


        function GetOneMax($Consulta, $Arreglo = false) {
            $resultado = $this->Execute($Consulta, $Arreglo);
            if (count($resultado) == 0) {
                return "";
            }
            return $resultado[0]["dato"];
        }

        function Seleccionar($Consulta) {
            $this->Conectar();


            mysql_set_charset('utf8', $this->con);
            $result = mysql_query($Consulta);
            $resultArray = array();
            while (
            ($resultArray[] = mysql_fetch_assoc($result)) || array_pop($resultArray)
            );
            mysql_free_result($result);
            $this->CerrarConexion();
            return $resultArray;
        }

        function Conectar() {
            global $_Config;
//            p($_GET);
            try {
                $this->con = @mysql_connect($_Config["DB"]["Servidor"], $_Config["DB"]["Usuario"], $_Config["DB"]["Clave"]);
//                p($this->con);
                if (!$this->con) {
                    if (isset($_GET["api"])) {
                        header('Content-type: application/json');
                        $dataFalso = array();
                        $dataFalso["estado"] = false;
                        $dataFalso["mensaje"] = "Error con el Servidor";
                        $dataFalso["codigo"] = 1;
                        echo json_encode($dataFalso);
                        die("");
                    } else
                        die("Error: No se ha iniciado la base de datos");
                }
                $db_selected = @mysql_select_db($_Config["DB"]["DB"], $this->con);
                if (!$db_selected) {
                    if (isset($_GET["api"])) {
                        header('Content-type: application/json');
                        $dataFalso = array();
                        $dataFalso["estado"] = false;
                        $dataFalso["mensaje"] = "Error con el Servidor";
                        $dataFalso["codigo"] = 2;
                        echo json_encode($dataFalso);
                        die("");
                    } else
                        die("Error: La DB seleccionada no se encuentra disponible");
                }
//                }else {
//                    if (isset($_GET["api"])) {
//                        header('Content-type: application/json');
//                        $dataFalso = array();
//                        $dataFalso["estado"] = false;
//                        $dataFalso["mensaje"] = "Error con el Servidor";
//                        $dataFalso["codigo"] = 4;
//                        echo json_encode($dataFalso);
//                        die("");
//                    } else
//                        die("Error: La DB seleccionada no se encuentra disponible");
//                }
            } catch (Exception $e) {
                if (isset($_GET["api"])) {
                    header('Content-type: application/json');
                    $dataFalso = array();
                    $dataFalso["estado"] = false;
                    $dataFalso["mensaje"] = "Error con el Servidor";
                    $dataFalso["codigo"] = 3;
                    echo json_encode($dataFalso);
                    die("");
                } else
                    die("Error: La Base de datos no está disponible");
            }
        }

        function CerrarConexion() {
            mysql_close($this->con);
        }

        function Insertar($Consulta, $up = false) {
            $this->Conectar();

//            mysql_query("SET NAMES 'utf8'");
            $b = mysql_query($Consulta);
            $id = mysql_insert_id();
            if ($up)
                $update = $this->mysql_modified_rows();
            $this->CerrarConexion();

            if ($up)
                return $update;
            return $id;
        }

        function mysql_modified_rows() {
            $dev = array();
            $info_str = mysql_info();
            $a_rows = mysql_affected_rows();
            $a = preg_split("/ /", $info_str);
            if (count($a) == 9) {
                $dev["pareado"] = $a[2];
                $dev["cambio"] = $a[5];
                $dev["advertencias"] = $a[8];
                return $dev;
            }
            return 0;
        }

        function AbreConexion() {

        }

        function depura($val) {
            if (is_int($val)) {
                if ($val == 0) {
                    $this->depura = false;
                } else
                    $this->depura = true;
            }
            else {
                $this->depura = $val;
            }
        }

        function Conexion($val = false) {
            if (is_int($val)) {
                if ($val == 0) {
                    $this->depura = false;
                } else
                    $this->depura = true;
            }
            else {
                $this->depura = $val;
            }
        }

        function d($val) {
            $this->depura($val);
        }

        function DB() {
            global $_Conexion;
            return $_Conexion["BaseDeDatos"];
        }

        function Execute($consulta, $arreglo = false) {
//            p(func_get_args());
            $dato = (explode(" ", $consulta));
            $insert = $dato[0];
            if ($arreglo) {
                $dato = (explode("?", $consulta));
                $Consulta = "";
                $i = 0;
                $largo = count($arreglo);
                foreach ($dato as $item) {
                    if ($i < $largo) {
                        $Consulta.=$item . "'" . (($arreglo[$i])) . "'";
                    } else {
                        $Consulta.=$item;
                    }
                    $i++;
                }
            } else {
                $Consulta = $consulta;
            }
            $Consulta = trim($Consulta);
            if ($this->depura) {
                p($Consulta);
            }
            $insert = strtolower($insert);
            if ($insert == "select" || $insert == "show" || $insert == "desc") {
                return $this->Seleccionar($Consulta);
            } else {
                $up = false;
                if ($insert == "update")
                    $up = true;
                return $this->Insertar($Consulta, $up);
            }
        }

    }

}
if (!function_exists("mysql_real_escape_stringBS")) {

    function mysql_real_escape_stringBS($cadena) {
        return $cadena;
    }

}
?>
