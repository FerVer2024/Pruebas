<?php
error_reporting(0);
session_start();include('../../tiempo.php');
foreach ($_POST as $indice=>$cadena) {//toma todas las var
$$indice = $cadena;
}

//******************************IMPORTANTE****************//

//si hay que cambiar ver donde dice FECHAS_VACAS_CONTROL


//VER LOS CARTELES QUE DICEN " //HABILITAR DESPUES DE JUNIO  ", hay que volver todo al estado natural
// como lo son las fecha de -06-02 por 04-30
// y en donde se ha comentado, se debe volver a poner

//*********************************************************//


//para que se puedan ingresar cualquier dia, sin controlar los 7, 14 etc.
//se anularon los errores: 50100, 50101, 50103 y 50104. 05-04-2023. Por Rositter

include ("../../includes/cnn.php");

//function establecerPeriodosVacaciones() {


   //controlar periodo de vacaciones cuando para el mes de octubre del año actual

    //$anio_proceso_inicio = date("Y").'-01-01';
    //$anio_proceso_intermedio = date("Y").'-10-01';
    //$anio_proceso_fin = date("Y").'-12-31';



//pasada la fecha dejar nuevamente -04-30 en donde dice -04-30
//HABILITAR DESPUES DE JUNIO

    //FECHAS_VACAS_CONTROL

    ///control por fecha ingresada por personal, busca las fechas del año en curso
   /* $sql_control = "SELECT
                      fecha_intermedio_inicio,
                      fecha_intermedio_fin
                    FROM
                      rh_vacaciones_parametro_fechas
                      WHERE YEAR(auditoria_fecha) = YEAR(CURDATE())
                    ORDER BY
                      auditoria_fecha DESC,
                      auditoria_hora DESC
                    LIMIT 1";

    $consulta_control = mysql_query($sql_control, $conexion);
    $control = mysql_fetch_array($consulta_control);

    if ($control){
        // si lo encuentra toma la fecha mas actual
        $_SESSION["mes_anio_inicio_tercio"] = $control["fecha_intermedio_inicio"];
        $_SESSION["mes_anio_fin_tercio"] = $control["fecha_intermedio_fin"];
    }else{
        // si no lo encuentra, se setea con la fecha del sistema
        $_SESSION["mes_anio_inicio_tercio"] = date("Y").'-04-30';
        $_SESSION["mes_anio_fin_tercio"] = date("Y").'-09-30';
    }*/


     $_SESSION["mes_anio_inicio_tercio"] = date("Y").'-04-30'; /*ESTAS CON LAS UNICAS FECHAS QUE SE TOCAN PARA VALIDAR LO QUE SE MUESTRA*/
     $_SESSION["mes_anio_fin_tercio"] = date("Y").'-09-30';    /*ESTAS CON LAS UNICAS FECHAS QUE SE TOCAN PARA VALIDAR LO QUE SE MUESTRA*/
    //////////////////////////fin del control


    if (date("Y-m-d") >= date("Y").'-01-01' and date("Y-m-d") <= date("Y").'-04-30'){
    $_SESSION["periodo_anterior"] = date('y') - 1;
    $_SESSION["periodo_actual"] = date('y') - 1;
    }

    if (date("Y-m-d") > date("Y").'-04-30' and date("Y-m-d") <= date("Y").'-09-30'){
      $_SESSION["periodo_anterior"] = date('y') - 1;
      $_SESSION["periodo_actual"] = date('y') - 1;
    }

    if (date("Y-m-d") > date("Y").'-09-30' and date("Y-m-d") <= date("Y").'-12-31'){


       if (date("Y-m-d") <= $_SESSION["mes_anio_fin_tercio"]) {
           $_SESSION["periodo_anterior"] = date('y') - 1;
           $_SESSION["periodo_actual"] = date('y');
       }else{
           $_SESSION["periodo_anterior"] = date('y') ;
           $_SESSION["periodo_actual"] = date('y');
       }

    }




    // Definir el periodo anterior y actual para vacaciones

    $fecha_parcial = substr($_SESSION["mes_anio_inicio_tercio"], -6);
    $_SESSION["fecha_parcial"] = $fecha_parcial;
    $_SESSION["fecha_tope_vacaciones"] = '20'.($_SESSION["periodo_actual"] + 1) . $fecha_parcial;
    $_SESSION["fecha_fin_vacaciones"] = '20'.($_SESSION["periodo_actual"] + 2) . $fecha_parcial;

    $fecha_parcial_intermedio_tercio = substr($_SESSION["mes_anio_fin_tercio"], -6);
    $_SESSION["fecha_intermedia_vacaciones"] = '20'.($_SESSION["periodo_actual"] + 1) . $fecha_parcial_intermedio_tercio;




//VERIFICA EL INGRESO DEL PERSONAL A LAS VACACIONES

function verificarVacaciones($conexion, $usuario) {
    // Consulta para verificar si el usuario tiene permitido ingresar vacaciones
    $sql_control = "SELECT * FROM rh_acceso WHERE username = '" . $usuario . "' AND vacaciones BETWEEN '1' AND '3'";
    $consulta_control = mysql_query($sql_control, $conexion);
    $control = mysql_fetch_array($consulta_control);

    // Si el usuario no está habilitado para ingresar vacaciones, mostrar un mensaje de error y redirigir
    if (!$control) {
        print '<script>
        alert("PRG_01. Usuario NO HABILITADO a ingresar vacaciones");
        window.location.href = "../../principal.php";
        </script>';
        exit; // Terminar la ejecución del script después de redirigir
    }
}

function establecerCentroCostoPersonal($conexion, $usuario) {
    // Consulta para obtener el centro de costo personal del usuario
    $sql_c = "SELECT cc_nro FROM users WHERE username = '" . $usuario . "'";
    $consulta_c = mysql_query($sql_c, $conexion);
    $controla = mysql_fetch_array($consulta_c);

    // Verificar y establecer el centro de costo personal en la sesión. 6010
    $_SESSION["centro_costo_personal"] = ($controla["cc_nro"] == 'AAAA') ? 'AAAA' : '';

}



function obtenerNivelImputacion($datos) {
  include ("../../includes/cnn.php");
 $respuesta=new xajaxResponse();
    extract($datos);

    // Consulta para obtener el centro de costos del empleado
    $sql_m = "SELECT CENCOS_PER FROM rh_maestro WHERE LEGAJOS = '" . $legajo . "'";
    $consulta_m = mysql_query($sql_m, $conexion);

    // Verificar si se encontró el centro de costos y obtener el nivel de imputación
    if ($consulta_m && $con_maestro = mysql_fetch_array($consulta_m)) {
        // Consulta para obtener el nivel de imputación del usuario en el centro de costos
        $sql_ra = "SELECT * FROM rh_acceso WHERE username = '" . $_SESSION["us2"] . "' AND centro = '" . $con_maestro["CENCOS_PER"] . "'";
        $consulta_ra = mysql_query($sql_ra, $conexion);

        // Verificar si se encontró el nivel de imputación y establecerlo en la sesión
        if ($consulta_ra && $con_acceso = mysql_fetch_array($consulta_ra)) {
            $_SESSION["nivel_usuario"] = $con_acceso["imputacion_nivel"];
            $_SESSION["acceso_usuario_cc"] = $con_acceso["centro"];
            $_SESSION["maestro_empleado_cc"] = $con_maestro["CENCOS_PER"];
            $_SESSION["acceso_vacaciones"] = $con_acceso["vacaciones"];
            $_SESSION["acceso_nivel"] = $con_acceso["imputacion_nivel"];
        }else{
           $_SESSION["nivel_usuario"] = '';
            $_SESSION["acceso_usuario_cc"] = '';
            $_SESSION["maestro_empleado_cc"] = '';
            $_SESSION["acceso_vacaciones"] = '';
            $_SESSION["acceso_nivel"] = '';
        }
    }

    return $respuesta;
}



// Verificar si el usuario tiene permitido ingresar vacaciones
verificarVacaciones($conexion, $_SESSION["us2"]);

// Establecer el centro de costo personal del usuario
establecerCentroCostoPersonal($conexion, $_SESSION["us2"]);


// Llamar a la función para obtener el nivel de imputación
//obtenerNivelImputacion($conexion, $datos, $_SESSION["us2"]);

//FIN DEL CONTROL DE ACCESO




require_once( "../../includes/xajax/xajax_core/xajax.inc.php" );


function calcularPeriodoUso($datos) {
     include ("../../includes/cnn.php");
     $respuesta=new xajaxResponse();
     extract($datos);

    // Inicializamos la variable $var_periodo_en_uso
    $_SESSION["var_periodo_en_uso"] = '';

    if ($datos['dias'] <> ''){
        //si la fecha es superior al control de tope, se debe tomar el año anterior al año en curso
        if (date("Y-m-d") > $_SESSION["fecha_tope_vacaciones"]){
            $_SESSION["periodo_anterior"] = $_SESSION["periodo_anterior"] + 1;
        }else{
            $sql_vac="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'  group by VACACIONES_PERIODO";
            $consulta_vac=mysql_query($sql_vac,$conexion);
            $vacas= mysql_fetch_array($consulta_vac);

            if ($vacas["resta_vac"] == '0'){
                $_SESSION["periodo_anterior"] = $_SESSION["periodo_anterior"] + 1;
            }
        }


        $sql_te = "SELECT (t1.TERCERA_PARTE -  VAR_CANT_TOMADA_TERCIO) AS RESTA_TERCIO,t1.PERIODO, t1.LEGAJOS FROM
                               (SELECT SUM(CANTIDAD_TOMADA) AS VAR_CANT_TOMADA_TERCIO, PERIODO, TERCERA_PARTE,LEGAJOS
                          FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."') AS t1";
        $consulta_vt=mysql_query($sql_te,$conexion);
        $tercio= mysql_fetch_array($consulta_vt);

        //verifica que el periodo anterior se haya cumplido el tercio o no tener nada para tomar como referencia el año anterior en curso

        if (!$tercio or $tercio["RESTA_TERCIO"] > 0){

            //Se puede dar que los tercios tengan resto, pero las vacaciones ya han sido tomadas todas
            if ($_SESSION["TERCIO"] == 'NO'){
                $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."'  group by VACACIONES_PERIODO";
            }else{
                $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'  group by VACACIONES_PERIODO";

            }

        }else{
            $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."'  group by VACACIONES_PERIODO";
        }

        $consulta_p=mysql_query($sql_p,$conexion);


        $x = $datos["dias"];

        //bucle recorriendo los periodos y mientras la cantidad de dias a tomar sea mayor a cero
        while ($vac_periodo= mysql_fetch_array($consulta_p) and $x > 0){

            //si la cantidad del periodo es mayor a cero
            if ($vac_periodo["resta_vac"] > 0){

                $dias_cant = $vac_periodo["resta_vac"];

                if ($dias_cant >= $x){

                    $var_periodo_en_uso = $vac_periodo['VACACIONES_PERIODO'];
                    //para saber en que periodo estoy parado a la hora de controlar el periodo a usar
                    $_SESSION["var_periodo_en_uso"] = $var_periodo_en_uso;

                }
            }
        }

    }

}






function RutinaGeneraTerciosTodos($datos){
  include ("../../includes/cnn.php");

    $respuesta=new xajaxResponse();
    extract($datos);

        $sql_p="SELECT LEGAJOS  FROM rh_maestro";
        $consulta_p=mysql_query($sql_p,$conexion);
        while ($maestro= mysql_fetch_array($consulta_p)){

         //conocer la cantidad que le corresponde de vacaciones
         $sql_p1="SELECT VACACIONES_CANTIDAD  FROM rh_vacaciones WHERE LEGAJOS = '".$maestro["LEGAJOS"]."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."'";
         $consulta_p1=mysql_query($sql_p1,$conexion);
         $vac_periodo= mysql_fetch_array($consulta_p1);


        // tercios de vacaciones
        $sql_t="SELECT LEGAJOS  FROM rh_vacaciones_tercios where LEGAJOS = '".$maestro["LEGAJOS"]."' AND PERIODO = '".$_SESSION["periodo_actual"]."' and PROCESO = 'INICIO'";
        $consulta_t=mysql_query($sql_t,$conexion);
        $vac_tercios_alta= mysql_fetch_array($consulta_t);


        if (!$vac_tercios_alta){
    $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$maestro["LEGAJOS"]."' AND PERIODO = '".$_SESSION["periodo_actual"]."' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);


                    if (!$vac_periodo_tercios){

                                  //SOLO SE PUEDE FRACCIONAR EN 10 Y 12, MENOR SE TIENE QUE TOMAR TODA LA FRACCION

                                  switch ($vac_periodo["VACACIONES_CANTIDAD"]) {

                                    case '14':
                                    $var_cantidad = '5';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    $toma_2 = '5';
                                    break;

                                    case '21':
                                    $var_cantidad = '7';
                                    $toma_3 = '7';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    case '28':
                                    $var_cantidad = '9';
                                    $toma_4 = '9';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    case '35':
                                    $var_cantidad = '12';
                                    $toma_5 = '12';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    default:
                                    $var_cantidad = $vac_periodo["VACACIONES_CANTIDAD"];
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    }

                                     /*if ($vac_periodo["VACACIONES_CANTIDAD"] < '7'){
                                     $var_cantidad = $vac_periodo["VACACIONES_CANTIDAD"];
                                     }*/

                                 $erNum = -1; // Número interno de error.
                                 $erStr = ""; // Descripción del error
                                 mysql_query("BEGIN", $conexion);
                                 if ($erNum == -1){
                                 $sql_vt = "INSERT INTO rh_vacaciones_tercios (
                                       LEGAJOS,
                                       PERIODO,
                                       TERCERA_PARTE,
                                       CANTIDAD_TOMADA,
                                       FECHA_PROCESO,
                                       HORA_PROCESO,
                                       USUARIO,
                                       FECHA_SALIDA,
                                       FECHA_REGRESA,
                                       PROCESO
                                       )
                                      VALUES (
                                       '".$maestro["LEGAJOS"]."',
                                       '".$_SESSION["periodo_actual"]."',
                                       '".$var_cantidad."',
                                       '0',
                                       '".date("Y-m-d")."',
                                       '".date("H:i:s")."',
                                       'personal',
                                       '0000-00-00',
                                       '0000-00-00',
                                       'INICIO'
                                       )"; //GRABA LOS REGISTROS
                //$respuesta->alert($sql_vt);
                                       $alta_vt = mysql_query($sql_vt); //COMPLETA EL TRAMITE

                                 }
                                  if(mysql_errno($conexion) != 0){
                                   $erNum = 80123;
                                   }

                                    if($erNum == -1){
                                    mysql_query("COMMIT", $conexion);
                                   }else{
                                    mysql_query("ROLLBACK", $conexion);
                                    $mensaje_error = "Error: '".$erNum;
                                    $respuesta->alert($mensaje_error);
                                  }
                             }
        }
       }



       $respuesta->alert("Tercios Generados");

	return $respuesta;

}



function VerFecha( $datos ){
$respuesta=new xajaxResponse();
include ("../../includes/cnn.php");
extract($datos);
$parte = explode('-',$datos["fecha_ant_salida"]);
$fecha_anterior = $parte[2].'-'.$parte[1].'-'.$parte[0];




     if (strlen($datos["fecha_ant_salida"]) == 10 and $datos["legajo"] <> ""){
                            if ($fecha_anterior < $datos["oculto_fecha_ingreso"] or $fecha_anterior < date("Y-m-d")){
                              $respuesta->alert("La fecha de salida no puede ser menor a la fecha ingreso o de hoy. USUARIO NO AUTORIZADO");
                              $respuesta->Assign("btn_grabar","style.visibility","hidden");
                            }else{
                               if ($_SESSION["acceso"] > 0 and  $_SESSION["acceso"] < 3){
                               $respuesta->Assign("btn_grabar","style.visibility","visible");
                               }else{
                               $respuesta->Assign("btn_grabar","style.visibility","hidden");
                               }
                            }

     }else{
       $respuesta->Assign("btn_grabar","style.visibility","hidden");
     }




return $respuesta;
}

function VerBoton( $datos ){
$respuesta=new xajaxResponse();
include ("../../includes/cnn.php");
extract($datos);
$parte = explode('-',$datos["fecha_ant_salida"]);
$fecha_anterior = $parte[2].'-'.$parte[1].'-'.$parte[0];



     if (strlen($datos["fecha_ant_salida"]) == 10 and $datos["legajo"] <> ""){
                            if ($fecha_anterior < $datos["oculto_fecha_ingreso"] or $fecha_anterior < date("Y-m-d")){
                              $respuesta->Assign("btn_grabar","style.visibility","hidden");
                            }else{
                               if ($_SESSION["acceso"] > 0 and  $_SESSION["acceso"] < 3){
                               $respuesta->Assign("btn_grabar","style.visibility","visible");
                               }else{
                               $respuesta->Assign("btn_grabar","style.visibility","hidden");
                               }
                            }

     }else{
       $respuesta->Assign("btn_grabar","style.visibility","hidden");
     }




return $respuesta;
}

function ValidaLegajo( $datos ){
    include ("../../includes/cnn.php");
    $respuesta=new xajaxResponse();
    extract($datos);


     $respuesta->call("xajax_obtenerNivelImputacion(xajax.getFormValues('formu3'))");


    $_SESSION["var_tercera_parte_vacaciones"] = '';
    $_SESSION["var_periodo_vacaciones"] = '';

    if ($_SESSION["us2"] == 'fprotopopov' or $_SESSION["us2"] == 'infor'){
                //rutina que genera los tercios de todo el personal. 05-04-2023
                //$respuesta->call("xajax_RutinaGeneraTerciosTodos(xajax.getFormValues('formu3'))");
    }


    if ($lista_sector <> 'SI'){
        $_SESSION["vac_periodo"] = $_SESSION["periodo_anterior"];
        $respuesta->Assign("div_mostrar_lista","innerHTML", "");
    }

    if ($lista_sector == 'NO'){
       $respuesta->Assign("lista_anio","style.visibility","visible");
    }

    //1. Localiza el legajo, si lo encuentra valida los demas datos
    $error = '';
    $suma_todo = '';
    if ($datos["legajo"] <> "") {
        $error = '';
        $respuesta->Assign("oculto_vinculo", "value", "");
                   $sql_a="SELECT * FROM rh_maestro WHERE LEGAJOS = '".$datos["legajo"]."'";
                   $consulta_a=mysql_query($sql_a,$conexion);
                   $row= mysql_fetch_array($consulta_a);

                   $sql_control="select * from rh_acceso WHERE username = '".$_SESSION["us2"]."' and centro = '".$row["CENCOS_PER"]."'";
                   $consulta_control=mysql_query($sql_control,$conexion);
                   $control= mysql_fetch_array($consulta_control);
                   if (!$control){
                     $error = "Usuario no autorizado a ingresar vacaciones en este legajo";
                     $respuesta->alert($error);
                     $respuesta->redirect("vacaciones_sector.php");
                     
                   }




                  if (!$row){
                   if ($row["FECHACONT"]<= date("Y-m-d")){
                       $respuesta->alert("Legajo NO EXISTENTE");
                       $respuesta->redirect("vacaciones_sector.php");
                   }
                  }

                 if ($row["FECHACONT"] <> '0000-00-00'){
                   if ($row["FECHACONT"]<= date("Y-m-d")){
                       $respuesta->alert("Legajo con Fecha Fin de Contrato");
                       $respuesta->redirect("vacaciones_sector.php");
                   }
                  }





         if ($row["CENCOS_PER"] == '6111' or $row["CENCOS_PER"] == '6112' or $row["CENCOS_PER"] == '6113'){

          //ACA SE PUDE VERIFICAR PARA QUE SI GENERÓ VACACIONES PARA EL PERIODO QUE TIENE ASOCIADO EN ESE AÑO, NO ESTE
          $respuesta->alert("No es posible ingresar vacaciones por estar en el C.Costo: ".$row["CENCOS_PER"].".");
          $respuesta->redirect("vacaciones_sector.php");
         }




         if ($_SESSION["acceso_vacaciones"] > '0'){

          if ($_SESSION["acceso_vacaciones"] == '3'){
            $respuesta->Assign("btn_grabar","style.visibility","hidden");
          }
          if ($_SESSION["acceso_vacaciones"] == '1' or $_SESSION["acceso_vacaciones"] == '2'){
            $respuesta->Assign("btn_grabar","style.visibility","visible");
          }

          $_SESSION["acceso"] = $_SESSION["acceso_vacaciones"];
          $_SESSION["acceso_centro"] = $_SESSION["acceso_usuario_cc"];

          $respuesta->Assign("num_campos_vac", "value", "0");
	      $respuesta->Assign("cant_campos_vac", "value", "0");


          if ($row){
              if ($error <> ''){
                $respuesta->Assign("btn_grabar","style.visibility","hidden");
                $respuesta->alert($error);
              }



                $respuesta->assign("legajo","value",$row["LEGAJOS"]);
                $respuesta->assign("apellido","value",utf8_encode($row["APELLIDO"]));
                $respuesta->assign("nro_costo","value",$row["CENCOS_PER"]);
                $respuesta->Assign("oculto_fecha_ingreso", "value", $row["FECHAING"]);

                    $respuesta->assign("fecha_ant_salida","value","");
                    $respuesta->assign("dias","value","");

                $respuesta->call("xajax_RutinaGeneraTercios(xajax.getFormValues('formu3'))");
                $respuesta->call("xajax_RutinaGeneraVacaciones(xajax.getFormValues('formu3'))");
                $respuesta->call("xajax_MostrarDatos(xajax.getFormValues('formu3'))");
                $respuesta->call("xajax_MostrarVacaciones(xajax.getFormValues('formu3'))");






          }else{

          //BLANQUEA LA TABLA

          $respuesta->Assign("num_campos", "value", "0");
	      $respuesta->Assign("cant_campos", "value", "0");
          $respuesta->Assign("num_campos_vac", "value", "0");
	      $respuesta->Assign("cant_campos_vac", "value", "0");


          $respuesta->alert("El Legajo NO EXISTE");
          $respuesta->assign("legajo","value",'');
          $respuesta->assign("apellido","value",'');
          $respuesta->assign("nro_costo","value",'');
          $respuesta->assign("dias_acumulados","value",'');
          $respuesta->assign("ver_datos","value",'');
          $respuesta->assign("legajo","focus()",'');
          $respuesta->Assign("btn_grabar","style.visibility","hidden");

          }




      }else{
        $respuesta->alert("Usuario no autorizado a ingresar vacaciones en este legajo");
        $respuesta->redirect("vacaciones_sector.php");
      }
        return $respuesta;
    }

 }

function DiasTomados($datos){
include ("../../includes/cnn.php");
     $respuesta=new xajaxResponse();
     extract($datos);
 function DiasFecha($fecha,$dias,$operacion){
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}

function dateDiff($start, $end) {

$start_ts = strtotime($start);

$end_ts = strtotime($end);

$diff = $end_ts - $start_ts;

return round($diff / 86400);

}




if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
   $ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
   $ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
   $ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
   $ip = $_SERVER['REMOTE_ADDR'];
else
   $ip = "unknown";


$obtiene_ip = explode (", ",$ip);
$ip_local   = $obtiene_ip[0];
$maquina    = gethostbyaddr ($ip_local); //nombre de la maquina
include ("../../includes/aud_ip.php");
$audi_fecha = date("Y-m-d"); //fecha actual
$audi_hora = date("H:i:s");  //hora actual
$audi_aplic = "vacaciones_bajas.php ¬  RRHH / Vacaciones bajas";
$audi_user = $_SESSION["us2"];

$erNum = -1; // Número interno de error.
$erStr = ""; // Descripción del error



      $fec = explode("-",$oculto_fecha_inicio);
      $dia = $fec[0];
      $mes = $fec[1];
      $anio = $fec[2];
      $fecha_anterior = $anio.'-'.$mes.'-'.$dia;

      $mes_anio_fichada = "rh_fichadas_20".substr($anio,2,2);


      $mes_aus = "rh_aus_20".substr($anio,2,2);
      $mes_jus = "rh_jus_20".substr($anio,2,2);
      $ll_tarde = "rh_l_t_20".substr($anio,2,2);


       $cant_dias = $datos["dias"];
       $cuento_dias = 1;
       $salida = "";
       $salida_2 = "";
       $no_pasa_5 = "";
       while ($cuento_dias <= $cant_dias){


           $fecha_salida = $fecha_anterior;
           //le resto un dia para que comience del dia a tomar
           $n_toma_1 = $cuento_dias - 1;
           //sumo cantidad de dias
           $inicio=strtotime($fecha_salida);
           $dias=($n_toma_1*86400);
           $fecha_regresa = date("Y-m-d",$inicio+$dias);


                 $sql_liq_aus="SELECT FEC_AUS, DATE_FORMAT(FEC_AUS,'%d/%m/%Y') AS FECHA_AUSENTE FROM $mes_jus WHERE LEGAJOS = '".$legajo."' AND FEC_AUS  between '".$fecha_salida."' and '".$fecha_regresa."' and PAGO = '2'";
                 $consulta_liq_aus=mysql_query($sql_liq_aus,$conexion);

                 //$respuesta->alert($sql_liq_aus);
                 while($liq_aus= mysql_fetch_array($consulta_liq_aus)){

                   $fechas_localizadas = $fechas_localizadas.','.$liq_aus["FECHA_AUSENTE"];
                   $no_pasa_5 = 1;
                 }



                 //busco los dias posibles que ya fueron asignados en esa fecha
                 $sql="SELECT * FROM rh_vacaciones_tomadas WHERE LEGAJOS = '".$datos["legajo"]."' AND '".$fecha_regresa."' >= TOMADAS_SALIDA AND '".$fecha_regresa."' <= TOMADAS_REGRESO";
                 $consulta=mysql_query($sql,$conexion);
                 $cf= mysql_fetch_array($consulta);



                 if ($cf){
                   $salida = "No se puede asignar vacaciones en este periodo\n";
                 }
                 //ver si ficho en ese periodo
                 $sql_fic="SELECT * FROM $mes_anio_fichada WHERE LEGAJO = '".$datos["legajo"]."' AND  FECHA = '".$fecha_regresa."'";
                 $consulta_fic=mysql_query($sql_fic,$conexion);
                 $fichada= mysql_fetch_array($consulta_fic);
                 if ($fichada){
                  $salida_2 = "Fecha contiene fichada";

                 }else{

                 }
                 $cuento_dias = $cuento_dias + 1;
       }


                include ("vacaciones_control_periodo.php");



                //controlar la fecha de finalizacion segun el año que está tomando de periodo
                $anio_actual = date("Y"); // Obtiene el año actual
                // Incrementa el año actual en 1 y establece la fecha tope al 30 de septiembre


                //despues cambiar a -04-30
                //FECHAS_VACAS_CONTROL
                $fecha_tope_atras = ($anio_actual + 1).$_SESSION["fecha_parcial"];

                $fecha_tope_actual = ($anio_actual + 2).$_SESSION["fecha_parcial"];


                //$respuesta->alert( $fecha_tope_atras.'   /   '.$fecha_tope_actual);

                if ($_SESSION["var_periodo_en_uso"] == date("y") - 1 and $fecha_regresa > $fecha_tope_atras){

                    $salida = "No se puede asignar vacaciones en este periodo\n";
                }

                if ($_SESSION["var_periodo_en_uso"] == date("y")  and $fecha_regresa > $fecha_tope_actual){

                    $salida = "No se puede asignar vacaciones en este periodo\n";
                }

                //$respuesta->alert($salida);
                //return $respuesta;



               if ($salida_2 <> ""){
               $respuesta->alert($salida_2);
               }

               if ($salida <> ""){
               $respuesta->alert($salida);
               }else{

               $cant_dias = $datos["dias"];
               $cuento_dias = 1;


               if ($no_pasa_5 == ""){

                   while ($cuento_dias <= $cant_dias){


                       $fecha_salida = $fecha_anterior;
                       //le resto un dia para que comience del dia a tomar
                       $n_toma_1 = $cuento_dias - 1;
                       //sumo cantidad de dias
                       $inicio=strtotime($fecha_salida);
                       $dias=($n_toma_1*86400);
                       $fecha_regresa = date("Y-m-d",$inicio+$dias);







                        $cuento_dias = $cuento_dias + 1;
                   }
                 }

                 if ($no_pasa_5 == ""){

                 // $_SESSION["periodo_anterior"] = $oculto_periodo;

                 // $respuesta->alert("dias tomados ".$_SESSION["periodo_anterior"].' - '.$_SESSION["estado"].' - '.$oculto_periodo_vac);

                 //$respuesta->alert($fecha_salida.' - '.$oculto_periodo_vac.' - '.(date("y") - 1));

                                  //FECHAS_VACAS_CONTROL

                                 //LA VARIABLE DE SESION -> $_SESSION["var_periodo_en_uso"], VIENE DE PHP: "vacaciones_control_periodo.php"
                                 if (date("Y-m-d") > date("Y").'-01-01' && date("Y-m-d") <= $_SESSION["mes_anio_inicio_tercio"] && $_SESSION["var_periodo_en_uso"] == (date("Y") - 1)) {

                                      // Verificar si la fecha de salida es mayor que el último día del año actual
                                      if ($fecha_salida > date("Y").'-12-31') {
                                          $respuesta->alert("Cod: 755. No es posible ingresar vacaciones en la fecha seleccionada");
                                          return $respuesta;
                                      }
                                  }


                                 if (date("Y-m-d") > $_SESSION["mes_anio_inicio_tercio"] and date("Y-m-d") <= date("Y").'-12-31' and $_SESSION["var_periodo_en_uso"] < date("y")){
                                      if ($fecha_salida > $_SESSION["mes_anio_fin_tercio"]){
                                         $respuesta->alert("Cod: 756. No es posible ingresar vacaciones en la fecha seleccionada");
                                         return $respuesta;
                                      }
                                 }

                                // return $respuesta;


                 $respuesta->call("xajax_GrabarVacaciones(xajax.getFormValues('formu3'))");
                 }else{
                 $respuesta->alert("No es posible ingresar vacaciones. Legajo contiene Justificaciones en la fecha seleccionada");
                 }
    }

     return $respuesta;
}

function MostrarVacaciones($datos){
   include ("../../includes/cnn.php");
    //MUESTRA LOS DATOS EN LAS FILAS DE LA TABLA. SON LOS DATOS INGRESADOS ANTES DE GUARDAR E LA BASE DE DATOS
    $respuesta=new xajaxResponse();
     extract($datos);
    //ingresa las filas


 function DiasFecha($fecha,$dias,$operacion){
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}



 $itemlist_resultado.= '<table width="95%">
     <tr>
      <td  bgcolor="#ECE9D8" class="centro">PERIODO</td>
      <td  bgcolor="#ECE9D8" class="centro">TOMADA</td>
      <td  bgcolor="#ECE9D8" class="centro">SALIDA</td>
      <td  bgcolor="#ECE9D8" class="centro">REGRESA</td>
      <td  bgcolor="#ECE9D8" class="centro">CANCELAR</td>
     </tr>
     ';


    $sql_m="SELECT * FROM rh_maestro WHERE LEGAJOS = '".$datos["legajo"]."'";
    $consulta_m=mysql_query($sql_m,$conexion);
    $maestro= mysql_fetch_array($consulta_m);

    //$anio_ingreso = substr($maestro["FECHAING"],2,2);
    $anio_de_ingreso = substr($maestro["FECHAING"],0,4);

     $sql="SELECT * FROM rh_vacaciones_tomadas WHERE LEGAJOS = '".$datos["legajo"]."' order by TOMADAS_SALIDA desc";
     $consulta=mysql_query($sql,$conexion);

    $n = 0;
    $i = 0;

     while ($cf= mysql_fetch_array($consulta)){

      //control de acceso
         $sql_control="select * from rh_acceso WHERE username = '".$_SESSION["us2"]."' and centro = '".$maestro["CENCOS_PER"]."'";
         $consulta_control=mysql_query($sql_control,$conexion);
         $control= mysql_fetch_array($consulta_control);

         //$respuesta->alert($sql_control);



     $n = $n + 1;
      $fec = explode("-",$cf["TOMADAS_SALIDA"]);
      $dia = $fec[2];
      $mes = $fec[1];
      $anio = $fec[0];
      $fecha_salida = $dia.'-'.$mes.'-'.$anio;

      $fec2 = explode("-",$cf["TOMADAS_REGRESO"]);
      $dia_t = $fec2[2];
      $mes_t = $fec2[1];
      $anio_t = $fec2[0];
      $fecha_regresa = $dia_t.'-'.$mes_t.'-'.$anio_t;

      	$id_campos_vac = $cant_campos_vac = $num_campos_vac+1;

          $num_campos = $num_campos + 1;
          $resto = $resto = $num_campos%2;

          if ($resto==1) {
          $color = "#F7F5EE";
          }
          if ($resto==0) {
          $color = "#FCFCF8";
          }


     $fecha_actual = date("Y-m-d");
     //$fecha_inicial = DiasFecha($fecha_actual,360,"restar");
     //POR PEDIDO DE ROSSITER, EL 10-01-2022
     $fecha_inicial = DiasFecha($fecha_actual,450,"restar");



            if ($control["imputacion_nivel"] > '0' and $control["imputacion_nivel"] >= '3'){

                if ($cf["TOMADAS_SALIDA"] > date("Y-m-d")){
                 $linea = '';
                $linea = '<img style="cursor:pointer" src="b_drop.png" width="16" height="16" alt="Borrar" onclick="if(confirm(\'Realmente desea Borrar Definitivamente?\')){xajax_BajaVacaciones('.$cf["TOMADAS_ID"].');}"/>';
                }else{
                    $linea = '';
                }


            }else{
              $linea = '';
            }


        $anio_periodo_vacaciones = "20".$cf["PERIODO"];


        if ($anio_periodo_vacaciones >= $anio_de_ingreso){

         $itemlist_resultado.='<tr class="">
         <td align="center" class="centro" style="background:'.$color.'">'. $cf["PERIODO"].'</td>
         <td align="center" style="background:'.$color.'">'.$cf["TOMADAS_CANTIDAD"].'</td>
         <td align="center" style="background:'.$color.'">'.$fecha_salida.'</td>
         <td align="center" style="background:'.$color.'">'.$fecha_regresa.'</td>
         <td align="center" style="background:'.$color.'">'.$linea.'</td>
         </tr>
        ';
        }

     }
        $itemlist_resultado.= '</table>';
        $respuesta->Assign("ver_dias_vacaciones","innerHTML",$itemlist_resultado);
        return $respuesta;

}

function BajaVacaciones($nro_id){
	$respuesta = new xajaxResponse();



    include ("../../includes/cnn.php");
if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
   $ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
   $ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
   $ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
   $ip = $_SERVER['REMOTE_ADDR'];
else
   $ip = "unknown";


 function DiasFecha($fecha,$dias,$operacion){
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}

function dateDiff($start, $end) {

$start_ts = strtotime($start);

$end_ts = strtotime($end);

$diff = $end_ts - $start_ts;

return round($diff / 86400);

}


$obtiene_ip = explode (", ",$ip);
$ip_local   = $obtiene_ip[0];
$maquina    = gethostbyaddr ($ip_local); //nombre de la maquina
include ("../../includes/aud_ip.php");
$audi_fecha = date("Y-m-d"); //fecha actual
$audi_hora = date("H:i:s");  //hora actual
$audi_aplic = "vacaciones_sector.php ¬  RRHH / Vacaciones";
$audi_user = $_SESSION["us2"];

$erNum = -1; // Número interno de error.
$erStr = ""; // Descripción del error


            mysql_query("BEGIN", $conexion);

     $sql="SELECT * FROM rh_vacaciones_tomadas WHERE TOMADAS_ID = '".$nro_id."'";
     $consulta=mysql_query($sql,$conexion);
     $cf= mysql_fetch_array($consulta);



     $fecha_ingresada = $cf["TOMADAS_SALIDA"];
     $fecha_fin = $cf["TOMADAS_REGRESO"];

     $parte_desde = explode('-',$fecha_ingresada);
     $fec_inicio = $parte_desde[2].'-'.$parte_desde[1].'-'.$parte_desde[0];

     $parte_hasta = explode('-',$fecha_fin);
     $fec_fin = $parte_hasta[2].'-'.$parte_hasta[1].'-'.$parte_hasta[0];

     $sql_liq_re="SELECT * FROM rh_liq_aus_reintegro WHERE FECHA_AUS BETWEEN '".$fecha_ingresada."' AND '".$fecha_fin."' AND PAGO = '2' and LEGAJO = '".$cf["LEGAJOS"]."'";
     $consulta_liq_re=mysql_query($sql_liq_re,$conexion);
     $liq_re= mysql_fetch_array($consulta_liq_re);



     if ($liq_re){
       $alerta = "Legajo ".$liq_re["LEGAJO"].": Tiene reintegro de hs pagadas \n en el periodo ".$fec_inicio." al ".$fec_fin.". \n  \n No se puede dar de baja vacaciones \n en un periodo donde existe reintegro de hs.";
       $respuesta->alert($alerta);

     }else{

     $respuesta->call("mensaje_espere");

     $cant = $cf["TOMADAS_CANTIDAD"];
     $periodo = $cf["PERIODO"];
     $leg = $cf["LEGAJOS"];

     $sql_v="SELECT * FROM rh_vacaciones  WHERE LEGAJOS = '".$leg."' and VACACIONES_PERIODO = '".$periodo."'";
     $consulta_v=mysql_query($sql_v,$conexion);
     $vac= mysql_fetch_array($consulta_v);

     $resta = abs($vac["VACACIONES_TOMADAS"] - $cant);


                   include ("vacaciones_trigger.php");
                  // mysql_query("BEGIN", $conexion);
                 if ($erNum == -1){
                 $sql_u="UPDATE rh_vacaciones SET  VACACIONES_TOMADAS = '".$resta."' WHERE LEGAJOS = '".$leg."' and VACACIONES_PERIODO = '".$periodo."'";
                 mysql_query($sql_u,$conexion);
                 }
                 if(mysql_errno($conexion) != 0){
                           $erNum = 70100;
                 }


                 //*************** PARA LOS TERCIOS


                      $sql_v_c="select LEGAJOS, PERIODO, TOMADAS_SALIDA, TOMADAS_REGRESO FROM rh_vacaciones_tomadas WHERE TOMADAS_ID = '".$nro_id."'";
                      $consulta_v_c=mysql_query($sql_v_c,$conexion);
                      $vac_control= mysql_fetch_array($consulta_v_c);



                      $sql_vt="SELECT * FROM rh_vacaciones_tercios  WHERE LEGAJOS = '".$vac_control["LEGAJOS"]."' and PERIODO = '".$vac_control["PERIODO"]."' and FECHA_SALIDA = '".$vac_control["TOMADAS_SALIDA"]."' AND FECHA_REGRESA = '".$vac_control["TOMADAS_REGRESO"]."'";
                      $consulta_vt=mysql_query($sql_vt,$conexion);
                      if ($vac_tercios= mysql_fetch_array($consulta_vt)){


                           if ($erNum == -1){
                          $sql_t="UPDATE rh_vacaciones_tercios SET CANTIDAD_TOMADA = '0', CANTIDAD_BORRADA = '".$vac_tercios["CANTIDAD_TOMADA"]."', PROCESO = 'DELETE' WHERE LEGAJOS = '".$vac_control["LEGAJOS"]."' and PERIODO = '".$periodo."' AND FECHA_SALIDA = '".$vac_control["TOMADAS_SALIDA"]."' AND FECHA_REGRESA = '".$vac_control["TOMADAS_REGRESO"]."'";


                          mysql_query($sql_t,$conexion);

                          }
                           if(mysql_errno($conexion) != 0){
                           $erNum = 70000;
                           }
                      }

                  ///****************** FIN TERCIOS ***********************





                  if ($erNum == -1){
                 $texto = "UPDATE rh_vacaciones SET  VACACIONES_TOMADAS = ".$resta." WHERE LEGAJOS = ".$leg." and VACACIONES_PERIODO = ".$periodo;
                 $sql_aud = "insert into auditoria (auditoria_aplicacion,auditoria_operacion,auditoria_tabla,username,auditoria_ip,auditoria_maquina,auditoria_fecha,auditoria_hora,auditoria_operacion_txt)values('".$audi_aplic."','UPDATE','rh_VACACIONES','".$audi_user."','".$ip."','".$maquina."','".$audi_fecha."','".$audi_hora."','".$texto."')";

                 $alta_audi = mysql_query($sql_aud);
                 }

                 if(mysql_errno($conexion) != 0){
                 $erNum = 70001;
                 }



                $sql_trigger8 = "SET @HISTORICO_BAJA='NO'";
                $res_trigger8 = mysql_query($sql_trigger8, $conexion);

                if ($erNum == -1){
                 $sql_d="delete FROM rh_vacaciones_tomadas WHERE TOMADAS_ID = '".$nro_id."'";



                 $del=mysql_query($sql_d,$conexion);
                }
                 if(mysql_errno($conexion) != 0){
                 $erNum = 70002;
                 }

                 if(mysql_errno($conexion) != 0){
                 $erNum = 3;
                 }
                 $texto = "delete FROM vacaciones_tomadas WHERE TOMADAS_ID = ".$nro_id;
                 $sql_aud = "insert into auditoria (auditoria_aplicacion,auditoria_operacion,auditoria_tabla,username,auditoria_ip,auditoria_maquina,auditoria_fecha,auditoria_hora,auditoria_operacion_txt)values('".$audi_aplic."','DELETE','rh_VACACIONES_TOMADAS','".$audi_user."','".$ip."','".$maquina."','".$audi_fecha."','".$audi_hora."','".$texto."')";

                $alta_audi = mysql_query($sql_aud);

                 if(mysql_errno($conexion) != 0){
                 $erNum = 4;
                 }

                   if ($erNum == -1){
                  //borro si existe en rh_aus_jus_reintegro
                  $sql_liq_aus="DELETE FROM rh_liq_aus_reintegro WHERE LEGAJO = '".$leg."' AND FECHA_AUS BETWEEN '".$fecha_ingresada."' and '".$fecha_fin."' AND PAGO = '1'";
                  $del_liq=mysql_query($sql_liq_aus,$conexion);
                  //FIN BORRADO
                   }
                   if(mysql_errno($conexion) != 0){
                   $erNum = 70003;
                   }


                //verifica los ausentes en el codigo de ale
          if ($fecha_ingresada < date('Y-m-d')){
          $i = 0;
          $dias_dif = dateDiff($fecha_ingresada,$fecha_fin);
          if ($dias_dif <> 0){
          $dias_dif = $dias_dif ;
          }else{
          $dias_dif = 1;
          }


         if ($fecha_ingresada <> $fecha_fin){
                //recorre los dias en que comienza y termina el control de ausente
             if ($fecha_ingresada <> ''){
                for ($i;$i<=$dias_dif;$i++){
                     $fecha_final = DiasFecha($fecha_ingresada,$i,"sumar");
                     if ($fecha_ingresada <= $fecha_fin){
                      $numero_parte = explode("-",$fecha_final);
                      $fecha_final=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                        $legajo = $leg;
                          $leg = $leg;
                        $fecha_a_procesar = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];
                        include("justifica_calcula_ausente.php");
                     }
                }
             }
         }else{
                 if ($fecha_ingresada <> ''){
                      $numero_parte = explode("-",$fecha_fin);
                      $fecha_fin=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                        $legajo = $leg;
                          $leg = $leg;
                       $fecha_a_procesar = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];
                        include("justifica_calcula_ausente.php");
                 }
         }
         }
         //fin del control de ausentes





     if($erNum == -1){
                  mysql_query("COMMIT", $conexion);
                  $respuesta->call("xajax_ValidaLegajo(xajax.getFormValues('formu3'))");

     $respuesta->alert("Dias de vacaciones, han sido retirados");

      }else{
                  mysql_query("ROLLBACK", $conexion);
                  $respuesta->alert("Error: ".$erNum);
    }




    }



	return $respuesta;

}

function RutinaGeneraTercios($datos){
  include ("../../includes/cnn.php");
    //MUESTRA LOS DATOS EN LAS FILAS DE LA TABLA. SON LOS DATOS INGRESADOS ANTES DE GUARDAR E LA BASE DE DATOS
    $respuesta=new xajaxResponse();
    //ingresa las filas
    extract($datos);

     $erNum = -1; // Número interno de error.
                                 $erStr = ""; // Descripción del error
                                 mysql_query("BEGIN", $conexion);



    $anio_proceso_inicio = date("Y").'-01-01';
    //$anio_proceso_intermedio = date("Y").'-10-01';
    $anio_proceso_fin = date("Y").'-12-31';

   // $var_cantidad = '';

   if (date("Y-m-d") >= $anio_proceso_inicio and date("Y-m-d") <= date("Y").'-09-30'){
       // Definir el periodo anterior y actual para vacaciones
       $_SESSION["periodo_actual_tercio"] = date('y') - 1;   //que se toma como vacaciones vigentes

   }

   if (date("Y-m-d") > date("Y").'-09-30' and date("Y-m-d") <= $anio_proceso_fin){
       $_SESSION["periodo_actual_tercio"] = date('y') ;   //que se toma como vacaciones vigentes
   }


      $sql_mae="SELECT FECHAING  FROM rh_maestro WHERE LEGAJOS = '".$legajo."'";
      $consulta_mae=mysql_query($sql_mae,$conexion);
      $row= mysql_fetch_array($consulta_mae);


      $sql_p="SELECT VACACIONES_CANTIDAD  FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual_tercio"]."'";
      $consulta_p=mysql_query($sql_p,$conexion);
      $vac_periodo= mysql_fetch_array($consulta_p);



                  //me fijo la cantidad de dias que le corresponde, para poder generar los tercios

                  $v_anio = $_SESSION["periodo_actual_tercio"];

                  $anio_s = '20'.$v_anio;

                  //CALCULO LOS DÍAS DE VACACIONES

                  $fecha_ing = $row["FECHAING"];
                  $fecha_hoy = $anio_s."-12-31";//hasta fin del año en curso

                  $anio_ing = substr($fecha_ing,0,4);
                  $mes_ing  = substr($fecha_ing,5,2);
                  $dia_ing  = substr($fecha_ing,8,2);

                  $anio_hoy = substr($fecha_hoy,0,4);
                  $mes_hoy  = substr($fecha_hoy,5,2);
                  $dia_hoy  = substr($fecha_hoy,8,2);



                  $aa_h  = $anio_hoy;
                  $mm_h  = $mes_hoy;
                  $dd_h  = $dia_hoy;

                  $aa_i  = $anio_ing;
                  $mm_i  = $mes_ing;
                  $dd_i  = $dia_ing;

                  $aa  = $aa_h-$aa_i;
                  $mm  = $mm_h-$mm_i;
                  $dd  = $dd_h-$dd_i;


                 if ($dd>29 and $dd<60){
                    $mm = $mm+1;
                    $dd = $dd-30;
                  }
                  if ($dd>59 and $dd<91){
                    $mm = $mm+2;
                    $dd = $dd-60;
                  }

                  if ($mm>11 and $mm<24){
                    $aa = $aa+1;
                    $mm = $mm-12;
                  }
                  if ($mm>23 and $mm<37){
                    $aa = $aa+2;
                    $mm = $mm-24;
                  }




                  IF ($aa == 0){
                      IF ($mm>=6){
                         $vcant = 14;
                      }ELSE{
                         $vcant  = (int)((($mm*25)+$dd)/20);
                      }
                  }else{


                       IF ($aa> 0 AND $aa < 5){
                          $vcant  = 14;
                       }

                       IF ($aa>4 AND $aa<10){
                          $vcant  = 21;

                       }
                       IF ($aa>9 AND $aa<20){
                          $vcant  = 28;

                       }
                       IF ($aa>=20){
                          $vcant  = 35;
                       }
                  }

                  if ($vcant > 35){
                     $vcant = 0;
                  }

      //fin del control de cantidad de dias



    $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_actual_tercio"]."' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);




                    if (!$vac_periodo_tercios){

                                  //SOLO SE PUEDE FRACCIONAR EN 10 Y 12, MENOR SE TIENE QUE TOMAR TODA LA FRACCION

                                  switch ($vcant) {

                                    case '14':
                                    $var_cantidad = '5';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    $toma_2 = '5';
                                    break;

                                    case '21':
                                    $var_cantidad = '7';
                                    $toma_3 = '7';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    case '28':
                                    $var_cantidad = '9';
                                    $toma_4 = '9';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    case '35':
                                    $var_cantidad = '12';
                                    $toma_5 = '12';
                                    $_SESSION["cant_parte"] = $var_cantidad;
                                    break;

                                    default:
                                    $var_cantidad = $vac_periodo["VACACIONES_CANTIDAD"];
                                    }

                                     if ($vac_periodo["VACACIONES_CANTIDAD"] > '0' and $vac_periodo["VACACIONES_CANTIDAD"] < '7'){
                                     //$var_cantidad = $vac_periodo["VACACIONES_CANTIDAD"];
                                     }


                                 if ($erNum == -1){

                                 $sql_vt = "INSERT INTO rh_vacaciones_tercios (
                                       LEGAJOS,
                                       PERIODO,
                                       TERCERA_PARTE,
                                       CANTIDAD_TOMADA,
                                       FECHA_PROCESO,
                                       HORA_PROCESO,
                                       USUARIO,
                                       FECHA_SALIDA,
                                       FECHA_REGRESA,
                                       PROCESO
                                       )
                                      VALUES (
                                       '".$datos["legajo"]."',
                                       '".$_SESSION["periodo_actual_tercio"]."',
                                       '".$var_cantidad."',
                                       '0',
                                       '".date("Y-m-d")."',
                                       '".date("H:i:s")."',
                                       '".$_SESSION["us2"]."',
                                       '0000-00-00',
                                       '0000-00-00',
                                       'INICIO'
                                       )"; //GRABA LOS REGISTROS

                                       $alta_vt = mysql_query($sql_vt); //COMPLETA EL TRAMITE

                                 }
                                  if(mysql_errno($conexion) != 0){
                                   $erNum = 80000;
                                   }

                                    if($erNum == -1){
                                    mysql_query("COMMIT", $conexion);
                                   }else{
                                    mysql_query("ROLLBACK", $conexion);
                                    $mensaje_error = "Error: '".$erNum;
                                    $respuesta->alert($mensaje_error);
                                  }
                             }

	return $respuesta;

}

function RutinaGeneraVacaciones($datos){

    include ("../../includes/cnn.php");
    $respuesta=new xajaxResponse();
    extract($datos);

    mysql_query("BEGIN", $conexion);

    $erNum = -1;

           $legajo = $datos["legajo"];

           //si el año en curso no se creo los dias de vacaciones, se lo crea el sistema automaticamente
           $sql_a="SELECT * FROM rh_maestro WHERE LEGAJOS = '".$legajo."'";
           $consulta_a=mysql_query($sql_a,$conexion);
           $row= mysql_fetch_array($consulta_a);

           $parte_fecha = explode('-',$row["FECHAING"]);
           $anio_s = $parte_fecha[0];

           $anio_actual = date("Y");

           $resta_fecha = $anio_actual - 10;//que me muestre 10 años antes

           if ($resta_fecha >= $row["FECHAING"]){//el año no puede ser menor al año de ingreso
            $anio_s = $resta_fecha;
           }else{
            $anio_s = $parte_fecha[0];
           }

  for ($anio_s;$anio_s <= $anio_actual; $anio_s++){ //recorro los años hasta el actual


  $v_anio = substr($anio_s,2,2);

           $sql_c="SELECT *  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO = '".$v_anio."'";
           $consulta_c=mysql_query($sql_c,$conexion);
           $vac_c= mysql_fetch_array($consulta_c);





                  //CALCULO LOS DÍAS DE VACACIONES, ANTIG RECONOCIDA Y ANTIG RECONOCIDA 2

                  $fecha_ing = $row["FECHAING"];
                  $fecha_hoy = $anio_s."-12-31";//hasta fin del año en curso

                  $anio_ing = substr($fecha_ing,0,4);
                  $mes_ing  = substr($fecha_ing,5,2);
                  $dia_ing  = substr($fecha_ing,8,2);

                  $anio_hoy = substr($fecha_hoy,0,4);
                  $mes_hoy  = substr($fecha_hoy,5,2);
                  $dia_hoy  = substr($fecha_hoy,8,2);

                  //$anios = $anio_hoy - $anio_ing;

                  $aa_h  = $anio_hoy;
                  $mm_h  = $mes_hoy;
                  $dd_h  = $dia_hoy;

                  $aa_i  = $anio_ing;
                  $mm_i  = $mes_ing;
                  $dd_i  = $dia_ing;

                    $aa  = $aa_h-$aa_i;
                    $mm  = $mm_h-$mm_i;
                    $dd  = $dd_h-$dd_i;








                 if ($dd>29 and $dd<60){
                    $mm = $mm+1;
                    $dd = $dd-30;
                  }
                  if ($dd>59 and $dd<91){
                    $mm = $mm+2;
                    $dd = $dd-60;
                  }

                  if ($mm>11 and $mm<24){
                    $aa = $aa+1;
                    $mm = $mm-12;
                  }
                  if ($mm>23 and $mm<37){
                    $aa = $aa+2;
                    $mm = $mm-24;
                  }


                 /*$aa_t = $aa;
                 $mm_t = $mm;
                 $dd_t = $dd;*/

                //CALCULO DE ANTIGUEDAD RECONOCIDA PARA VACACIONES
                  $sql_antig="SELECT CODIGO,LEGAJOS,DESDE, HASTA,FECHA_ANTIG_RECONOCIDA,
                              TIMESTAMPDIFF(MONTH,DESDE,HASTA) AS mes,
                              TIMESTAMPDIFF(YEAR,DESDE,HASTA) AS anio
                              FROM rh_antig_reconocida WHERE LEGAJOS = '".$legajo."' AND (CODIGO = '2' OR CODIGO = '3' OR CODIGO = '4')";

                  $consulta_antig=mysql_query($sql_antig,$conexion);



                  $antig = 0;


                  while ($antig_rec= mysql_fetch_array($consulta_antig)){

                   //CALCULO DE ANTGUEDAD RECONOCIDA
                   //Obtiene objetos Date. Fecha actual
                    //sumo los dias a partir del año reconocido para calcular las vacaciones

                    $antiguedad_reconocida = substr($antig_rec["FECHA_ANTIG_RECONOCIDA"],2,2);



                    //formateo el año para la comparación entre 1900 y 2000
                    if ($v_anio > 60 and $v_anio <= 99){
                       $anio_ingreso = '19'.$v_anio;
                    }
                    if ($v_anio > 0 and $v_anio <= 30){
                       $anio_ingreso = '20'.$v_anio;
                    }



                      if ($vac_c["VACACIONES_PERIODO"] >= $antiguedad_reconocida){
                        //if ($antig_rec["mes_rec"] < 1){
                        $dia_hasta = substr($antig_rec["HASTA"],8,2);
                        $mes_hasta = (substr($antig_rec["HASTA"],5,2));
                        $anio_hasta = substr($antig_rec["HASTA"],0,4);


                        $anio_desde = substr($antig_rec["DESDE"],0,4);
                        $mes_desde  = substr($antig_rec["DESDE"],5,2);
                        $dia_desde  = substr($antig_rec["DESDE"],8,2);

                        $unidad_anios = 12 * $antig_rec["anio"];//cantidad de meses en los años encontrados

                        $mm_r = $antig_rec["mes"] - $unidad_anios;



                        //sacar la diferencia de dias
                        if ($dia_desde > $dia_hasta){
                        $dd_r  = 31 - ($dia_desde-$dia_hasta);
                        }
                        if ($dia_hasta > $dia_desde){
                        $dd_r  = $dia_hasta - $dia_desde;
                        }
                        if ($dia_hasta == $dia_desde){
                        $dd_r  = 0;
                        }

                        $aa_r = $antig_rec["anio"];

                        $codigo = $antig_rec["CODIGO"];



                         if (($aa == 0 and $mm >= 6) or $aa > 0){

                             $dd = $dd + $dd_r;
                             $mm = $mm + $mm_r;
                             $aa = $aa + $aa_r;


                         }else{
                                   if ($codigo == '4'){
                                   $dd = $dd + $dd_r;
                                   $mm = $mm + $mm_r;
                                   $aa = $aa + $aa_r;
                                   }
                         }




                        if ($dd>29 and $dd<60){
                          $mm = $mm+1;
                          $dd = $dd-30;
                        }
                        if ($dd>59 and $dd<91){
                          $mm = $mm+2;
                          $dd = $dd-60;
                        }

                        if ($mm>11 and $mm<24){
                          $aa = $aa+1;
                          $mm = $mm-12;
                        }
                        if ($mm>23 and $mm<37){
                          $aa = $aa+2;
                          $mm = $mm-24;
                        }

                     }
                  }





                   //FIN CALCULO ANTGUEDAD RECONOCIDA

                  IF ($aa == 0){
                      IF ($mm>=6){
                         $vcant = 14;
                      }ELSE{
                         $vcant  = (int)((($mm*25)+$dd)/20);
                      }
                  }else{


                       IF ($aa> 0 AND $aa < 5){
                          $vcant  = 14;
                       }

                       IF ($aa>4 AND $aa<10){
                          $vcant  = 21;

                       }
                       IF ($aa>9 AND $aa<20){
                          $vcant  = 28;

                       }
                       IF ($aa>=20){
                          $vcant  = 35;
                       }
                  }

                  if ($vcant > 35){
                     $vcant = 0;
                  }



          if (!$vac_c){


             if (date("m") >= '10' and $v_anio == date("y")){
                if ($erNum == -1){
                  $sql_per = "INSERT INTO rh_vacaciones (LEGAJOS,VACACIONES_PERIODO,VACACIONES_CANTIDAD, VACACIONES_TOMADAS)VALUES ('".$legajo."','".$v_anio."','".$vcant."','0')"; // NO PUDO

                  $alta_vac = mysql_query($sql_per); //COMPLETA EL TRAMITE
               }
                   if(mysql_errno($conexion) != 0){
                        $erNum = 2100;
                   }


             }else{
               if ($v_anio <> date("y")){
               //si no encuentra las vacaciones del periodo fuera del actual
                 if ($erNum == -1){
               $sql_per = "INSERT INTO rh_vacaciones (LEGAJOS,VACACIONES_PERIODO,VACACIONES_CANTIDAD, VACACIONES_TOMADAS)VALUES ('".$legajo."','".$v_anio."','".$vcant."','0')";

               $alta_vac = mysql_query($sql_per); //COMPLETA EL TRAMITE
                }
                   if(mysql_errno($conexion) != 0){
                        $erNum = 2101;
                   }
               }
             }

          }
          if ($vac_c){
          //se debe controlar si se han ingresado dias complementarios para hacer la cuenta... es lo que falta,
          //sino modifica la cantidad por las vacaciones que le corresponde

           if ($erNum == -1){
          $sql_per_m = "UPDATE rh_vacaciones SET VACACIONES_CANTIDAD = '".$vcant."' WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$v_anio."'"; // NO PUDO
          $modi_vac = mysql_query($sql_per_m); //COMPLETA EL TRAMITE
          }
                   if(mysql_errno($conexion) != 0){
                        $erNum = 2102;
                   }

          }

}

        if($erNum == -1){
                  mysql_query("COMMIT", $conexion);
        }else{
                  mysql_query("ROLLBACK", $conexion);
        }


        return $respuesta;
}



function MostrarDatos($datos){
   include ("../../includes/cnn.php");
    //MUESTRA LOS DATOS EN LAS FILAS DE LA TABLA. SON LOS DATOS INGRESADOS ANTES DE GUARDAR E LA BASE DE DATOS
    $respuesta=new xajaxResponse();
    //ingresa las filas
    extract($datos);

    $_SESSION["anio_de_lista"] = '';




     $itemlist_resultado.= '
     <TABLE width="95%">
	<TR>
		<TH COLSPAN=3 bgcolor="#FFFFFF">VACACIONES</TH>
		<TH COLSPAN=2 bgcolor="#FFFFFF">TERCIOS DE VACACIONES</TH>
	</TR>
	<TR>
		<TH bgcolor="#ECE9D8">PERIODO</TH> <TH bgcolor="#ECE9D8">CORRESPONDEN</TH> <TH bgcolor="#ECE9D8">QUEDAN</TH>
		<TH bgcolor="#ECE9D8">CORRESPONDE</TH> <TH bgcolor="#ECE9D8">TOMADOS</TH> <!--<TH bgcolor="#ECE9D8">A TOMAR</TH>-->
	</TR>




     ';


           $legajo = $datos["legajo"];

           //si el año en curso no se creo los dias de vacaciones, se lo crea el sistema automaticamente
           $sql_a="SELECT * FROM rh_maestro WHERE LEGAJOS = '".$legajo."'";
           $consulta_a=mysql_query($sql_a,$conexion);
           $row= mysql_fetch_array($consulta_a);

           $parte_fecha = explode('-',$row["FECHAING"]);
           $anio_s = $parte_fecha[0];

           $anio_actual = date("Y");

           $resta_fecha = $anio_actual - 2;//que me muestre 10 años antes

           if ($resta_fecha >= $row["FECHAING"]){//el año no puede ser menor al año de ingreso
            $anio_s = $resta_fecha;
           }else{
            $anio_s = $parte_fecha[0];
           }



           for ($anio_s;$anio_s <= $anio_actual; $anio_s++){ //recorro los años hasta el actual

                $v_anio = substr($anio_s,2,2);

                $sql_c="SELECT *  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO >= '".$v_anio."'";

                $consulta_c=mysql_query($sql_c,$conexion);
                $vac_c= mysql_fetch_array($consulta_c);



                  //CALCULO LOS DÍAS DE VACACIONES, ANTIG RECONOCIDA Y ANTIG RECONOCIDA 2

                  $fecha_ing = $row["FECHAING"];
                  $fecha_hoy = $anio_s."-12-31";//hasta fin del año en curso

                  $anio_ing = substr($fecha_ing,0,4);
                  $mes_ing  = substr($fecha_ing,5,2);
                  $dia_ing  = substr($fecha_ing,8,2);

                  $anio_hoy = substr($fecha_hoy,0,4);
                  $mes_hoy  = substr($fecha_hoy,5,2);
                  $dia_hoy  = substr($fecha_hoy,8,2);



                  $aa_h  = $anio_hoy;
                  $mm_h  = $mes_hoy;
                  $dd_h  = $dia_hoy;

                  $aa_i  = $anio_ing;
                  $mm_i  = $mes_ing;
                  $dd_i  = $dia_ing;

                  $aa  = $aa_h-$aa_i;
                  $mm  = $mm_h-$mm_i;
                  $dd  = $dd_h-$dd_i;


                 if ($dd>29 and $dd<60){
                    $mm = $mm+1;
                    $dd = $dd-30;
                  }
                  if ($dd>59 and $dd<91){
                    $mm = $mm+2;
                    $dd = $dd-60;
                  }

                  if ($mm>11 and $mm<24){
                    $aa = $aa+1;
                    $mm = $mm-12;
                  }
                  if ($mm>23 and $mm<37){
                    $aa = $aa+2;
                    $mm = $mm-24;
                  }


                //CALCULO DE ANTIGUEDAD RECONOCIDA PARA VACACIONES
                  $sql_antig="SELECT CODIGO,LEGAJOS,DESDE, HASTA,FECHA_ANTIG_RECONOCIDA,
                              TIMESTAMPDIFF(MONTH,DESDE,HASTA) AS mes,
                              TIMESTAMPDIFF(YEAR,DESDE,HASTA) AS anio
                              FROM rh_antig_reconocida WHERE LEGAJOS = '".$legajo."' AND (CODIGO = '2' OR CODIGO = '3' OR CODIGO = '4')";

                  $consulta_antig=mysql_query($sql_antig,$conexion);



                  $antig = 0;


                  while ($antig_rec= mysql_fetch_array($consulta_antig)){

                   //CALCULO DE ANTGUEDAD RECONOCIDA
                   //Obtiene objetos Date. Fecha actual
                    //sumo los dias a partir del año reconocido para calcular las vacaciones

                    $antiguedad_reconocida = substr($antig_rec["FECHA_ANTIG_RECONOCIDA"],2,2);

                    //formateo el año para la comparación entre 1900 y 2000
                    if ($v_anio > 60 and $v_anio <= 99){
                       $anio_ingreso = '19'.$v_anio;
                    }
                    if ($v_anio > 0 and $v_anio <= 30){
                       $anio_ingreso = '20'.$v_anio;
                    }



                   if ($vac_c["VACACIONES_PERIODO"] >= $antiguedad_reconocida){
                        //if ($antig_rec["mes_rec"] < 1){
                        $dia_hasta = substr($antig_rec["HASTA"],8,2);
                        $mes_hasta = (substr($antig_rec["HASTA"],5,2));
                        $anio_hasta = substr($antig_rec["HASTA"],0,4);


                        $anio_desde = substr($antig_rec["DESDE"],0,4);
                        $mes_desde  = substr($antig_rec["DESDE"],5,2);
                        $dia_desde  = substr($antig_rec["DESDE"],8,2);

                        $unidad_anios = 12 * $antig_rec["anio"];//cantidad de meses en los años encontrados

                        $mm_r = $antig_rec["mes"] - $unidad_anios;



                        //sacar la diferencia de dias
                        if ($dia_desde > $dia_hasta){
                        $dd_r  = 31 - ($dia_desde-$dia_hasta);
                        }
                        if ($dia_hasta > $dia_desde){
                        $dd_r  = $dia_hasta - $dia_desde;
                        }
                        if ($dia_hasta == $dia_desde){
                        $dd_r  = 0;
                        }

                        $aa_r = $antig_rec["anio"];

                        $codigo = $antig_rec["CODIGO"];



                         if (($aa == 0 and $mm >= 6) or $aa > 0){

                             $dd = $dd + $dd_r;
                             $mm = $mm + $mm_r;
                             $aa = $aa + $aa_r;


                             //$ver = "a. Periodo: ".$anio_s." anios :".$aa." mes: ".$mm." dias: ".$dd;
                             //$respuesta->alert($ver);

                         }else{
                                   if ($codigo == '4'){
                                   $dd = $dd + $dd_r;
                                   $mm = $mm + $mm_r;
                                   $aa = $aa + $aa_r;
                                    //$ver = "b. Periodo: ".$anio_s." anios :".$aa." mes: ".$mm." dias: ".$dd;
                                    //$respuesta->alert($ver);
                                   }
                         }




                        if ($dd>29 and $dd<60){
                          $mm = $mm+1;
                          $dd = $dd-30;
                        }
                        if ($dd>59 and $dd<91){
                          $mm = $mm+2;
                          $dd = $dd-60;
                        }

                        if ($mm>11 and $mm<24){
                          $aa = $aa+1;
                          $mm = $mm-12;
                        }
                        if ($mm>23 and $mm<37){
                          $aa = $aa+2;
                          $mm = $mm-24;
                        }

                     }
                  }





                   //FIN CALCULO ANTGUEDAD RECONOCIDA

                  IF ($aa == 0){
                      IF ($mm>=6){
                         $vcant = 14;
                      }ELSE{
                         $vcant  = (int)((($mm*25)+$dd)/20);
                      }
                  }else{


                       IF ($aa> 0 AND $aa < 5){
                          $vcant  = 14;
                       }

                       IF ($aa>4 AND $aa<10){
                          $vcant  = 21;

                       }
                       IF ($aa>9 AND $aa<20){
                          $vcant  = 28;

                       }
                       IF ($aa>=20){
                          $vcant  = 35;
                       }
                  }

                  if ($vcant > 35){
                     $vcant = 0;
                  }



                  if (!$vac_c){
                       //PARA PERIODOS NUEVOS
                       if (date("m") >= '10' and $v_anio == date("y")){
                       $sql_per = "INSERT INTO rh_vacaciones (LEGAJOS,VACACIONES_PERIODO,VACACIONES_CANTIDAD)VALUES ('".$legajo."','".$v_anio."','".$vcant."')";
                       $alta_vac = mysql_query($sql_per); //COMPLETA EL TRAMITE
                       }
                       if ($v_anio < date("y")){
                         $sql_per = "INSERT INTO rh_vacaciones (LEGAJOS,VACACIONES_PERIODO,VACACIONES_CANTIDAD)VALUES ('".$legajo."','".$v_anio."','".$vcant."')";
                         $alta_vac = mysql_query($sql_per); //COMPLETA EL TRAMITE
                       }

                  }
                  if ($vac_c){
                       //se debe controlar si se han ingresado dias complementarios para hacer la cuenta... es lo que falta,
                       //sino modifica la cantidad por las vacaciones que le corresponde
                       $sql_per_m = "UPDATE rh_vacaciones SET VACACIONES_CANTIDAD = '".$vcant."' WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$v_anio."'";
                       $modi_vac = mysql_query($sql_per_m); //COMPLETA EL TRAMITE

                  }

           }//DEL FOR


             //FECHAS_VACAS_CONTROL
             //if (date("Y-m-d") <= $_SESSION["fecha_fin_vacaciones"]){
             if (date("Y-m-d") <= date("Y").'-09-30'){
                  //$respuesta->alert("paso 01");
             $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO >= '".$_SESSION["periodo_anterior"]."'  GROUP BY VACACIONES_PERIODO";
             }else{
                  //$respuesta->alert("paso 02");

                  if (date("Y-m-d") <= $_SESSION["mes_anio_fin_tercio"]){

                         if (date("Y-m-d") < $_SESSION["fecha_tope_vacaciones"]){
                                         $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO > '".$_SESSION["periodo_anterior"]."'  GROUP BY VACACIONES_PERIODO";
                                           //$respuesta->alert("paso 03");
                                         //controlar que los tercios se hayan cumplido
                                         $sql_tercio="SELECT SUM(TERCERA_PARTE - CANTIDAD_TOMADA) AS RESTA FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$legajo."'  AND PERIODO  = '".$_SESSION["periodo_anterior"]."' AND PROCESO = 'INSERT' ORDER BY VACACIONES_ID DESC";
                                         $consulta_tercio=mysql_query($sql_tercio,$conexion);
                                         $control_tercio= mysql_fetch_array($consulta_tercio);

                                         if ($control_tercio["RESTA"] == '0'){

                                          //$respuesta->alert("paso 04");
                                               //lo fuerzo a tomar el año actual, asi no muestra nada
                                             $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO = '".date("y")."'  GROUP BY VACACIONES_PERIODO";
                                         }else{
                                             $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO >= '".$_SESSION["periodo_anterior"]."'  GROUP BY VACACIONES_PERIODO";
                                         }

                        }
                  }else{
                      $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO >= '".$_SESSION["periodo_anterior"]."'  GROUP BY VACACIONES_PERIODO";
                  }


             }

             //si se llega a la fecha tope de vacaciones del perido de dos años antes
             if ($_SESSION["periodo_anterior"] == (date("y") - 2) and    date("Y-m-d") > $_SESSION["fecha_tope_vacaciones"]){
                 $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO > '".$_SESSION["periodo_anterior"]."'  GROUP BY VACACIONES_PERIODO";
                   //$respuesta->alert("paso 05");
                 //controlar que los tercios se hayan cumplido
                 $sql_tercio="SELECT SUM(TERCERA_PARTE - CANTIDAD_TOMADA) AS RESTA FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$legajo."'  AND PERIODO  = '".$_SESSION["periodo_actual"]."' AND PROCESO = 'INSERT' ORDER BY VACACIONES_ID DESC";
                 $consulta_tercio=mysql_query($sql_tercio,$conexion);
                 $control_tercio= mysql_fetch_array($consulta_tercio);

                 if ($control_tercio["RESTA"] == '0'){

                 // $respuesta->alert("paso 06");
                       //lo fuerzo a tomar el año actual, asi no muestra nada
                     $sql="SELECT sum(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS total, VACACIONES_PERIODO, VACACIONES_CANTIDAD  FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO = '".date("y")."'  GROUP BY VACACIONES_PERIODO";
                 }

              }


              //$respuesta->alert($sql);

            $consulta=mysql_query($sql,$conexion);



   $vacaciones = 0;
   $num_campos = 0;
   $vacaciones_t = 0;
   $vacaciones_a = 0;
   $muestro = "";
   WHILE ($vac= mysql_fetch_array($consulta)){



     if ($vac["total"] > 0){

       //lo uso para saber la cantidad de filas a mostrar
       $total_anio = $vac["total"];
       $periodo = $vac["VACACIONES_PERIODO"];
       $periodo = str_pad($periodo,2,'0', STR_PAD_LEFT);
       $total_anio = str_pad($total_anio,2,'0', STR_PAD_LEFT);
       $registros = str_pad($registros,2,'0', STR_PAD_LEFT);
       $corresponde = $vac["VACACIONES_CANTIDAD"];
        $id_campos = $cant_campos = $num_campos+1;



        $id_campos = $id_campos + 1;
        $num_campos = $num_campos + 1;

        $respuesta->Assign("num_campos","value", $id_campos);
    	$respuesta->Assign("cant_campos" ,"value", $id_campos);


                //colocado pedido por personal el 03-12-2020
                //los siguientes legajos no deben ver las vacaciones del periodo 2020
                $_SESSION["formulario"] = "vacaciones_alta_0";
                include("no_mostrar_vacaciones.php");
                //fin control legajos

         //PARA MOSTRAR LOS TERCIOS
         //if ($periodo == $_SESSION["periodo_anterior"]){
                   switch ($vac["VACACIONES_CANTIDAD"]) {

                                    case '14':
                                    $var_mostrar_tercios = '5';

                                    break;

                                    case '21':
                                    $var_mostrar_tercios = '7';

                                    break;

                                    case '28':
                                    $var_mostrar_tercios = '9';

                                    break;

                                    case '35':
                                    $var_mostrar_tercios = '12';

                                    break;

                                    }

                                 if ($vac["VACACIONES_CANTIDAD"] <= '7'){
                                  $var_mostrar_tercios = $vac["VACACIONES_CANTIDAD"];
                                 }

         //FIN PARA MOSTRAR LOS TERCIOS



                $linea_quedan = '<input size="5" class="centro" style="background-color: #F7F5EE" type="text" id="n_queda_'.$id_campos.'"  readonly="readonly" name="n_queda_'.$id_campos.'" value="'.$total_anio.'"/><input type="hidden" id="oculto_cant_'. $id_campos .'" name="oculto_cant[]" value="'. $id_campos .'" />';

                 //no muestra el resto de vacaciones a tomar


                    //tercios
                    $cuenta_tercios_parcial = "";
                    $cuenta_tercios_restan = "";
                    $sql_t="SELECT SUM(vat.CANTIDAD_TOMADA) AS total_tercios, vat.PERIODO, vat.TERCERA_PARTE  FROM rh_vacaciones_tercios AS vat
WHERE vat.LEGAJOS = '".$legajo."' AND vat.PERIODO = '".$periodo."'  GROUP BY vat.PERIODO, vat.`LEGAJOS`";

                    $consulta_t=mysql_query($sql_t,$conexion);
                    if ($vac_tercios= mysql_fetch_array($consulta_t)){
                        $cuenta_tercios_parcial = $vac_tercios["total_tercios"];
                        $cuenta_tercios_restan = $vac_tercios["TERCERA_PARTE"] - $cuenta_tercios_parcial;
                    }
                    //fin tercios




                //$resta_general_vacaciones = "";

                $var_fecha = date("Y-m-d");
                $var_fecha_fin = date("Y").'-12-31';//fin de año
                $var_fecha_abril = $_SESSION["fecha_tope_vacaciones"]; //ej: 30-04-2024
                $var_fecha_intermedia = $_SESSION["fecha_intermedia_vacaciones"]; // Ej: 30-09-2024
                $var_anio = $periodo; //periodo que se recorre
                $resta_general_vacaciones = $vac["total"];//resto las vacaciones que  corresponde, menos las tomadas. Si el resultado es 0, quiere decir que se tomó vacaciones
                $resta_tercio = $cuenta_tercios_restan; // resto el total del tercio, menos las tomadas

               // $respuesta->alert($periodo.' | '.$var_fecha_abril.' | '.$var_fecha_intermedia);
               // $respuesta->alert($var_fecha_abril);

                 $muestro = '';
                 $resta_general_vacaciones = "";
                if ( date("y") - 2 == $periodo){ // si son vacaciones anteriores
                $i = $i + 1;
                   if ($cuenta_tercios_restan == 0){ //si completó el tercio
                     $muestro = "no";
                     $linea_quedan = "";
                   }else{
                      $muestro = "si";
                      $linea_quedan = "";
                   }

                }


                if(date("y") - 1 == $periodo){
                   //si tiene vacaciones asignadas
                   if ($resta_general_vacaciones == 0){
                                //si la fecha acual es menor al YYYY-04-30
                                if ($var_fecha > $var_fecha_abril){
                                     //si tiene tercio
                                     if ($resta_tercio > 0){
                                          // si la fecha actual es menor o igual a YYYY-09-30
                                          if ($var_fecha <= $var_fecha_intermedia){
                                               $muestro = "si";
                                               $linea_quedan = "";
                                          }else{
                                               $muestro = "no";
                                          }
                                     }else{

                                          $muestro = "no";
                                     }
                                }else{
                                       if ($resta_tercio > 0){
                                           if (date("Y-m-d") >= date("Y").'-10-01'){
                                           $linea_quedan = "";
                                           }
                                           $muestro = "si";
                                       }else{

                                          if ($var_fecha <= $var_fecha_intermedia){
                                                  $var_fecha_inter = date('Y-m-d', strtotime($var_fecha_intermedia . ' -1 year'));
                                                  //le resto un año porque está seteado para un año mas
                                                  if (date("Y-m-d") < date("Y").'-10-01'){
                                                        if (($var_fecha > $var_fecha_intermedia  and $var_fecha <= $var_fecha_fin) and $resta_tercio == 0){
                                                          $muestro = "no";
                                                        }else{
                                                          $muestro = "si";
                                                        }
                                                   }
                                                   if (date("Y-m-d") >= date("Y").'-10-01'){
                                                        if (($var_fecha > $var_fecha_inter  and $var_fecha <= $var_fecha_fin) and $resta_tercio == 0){
                                                          $muestro = "no";
                                                        }else{
                                                          $muestro = "si";
                                                        }
                                                   }

                                          }else{
                                                   $muestro = "no";

                                          }
                                       }
                                }
                        }else{
                               $muestro = "no";
                        }
                }//del if, periodo del año, menos 1



                if(date("y") == $periodo){ //año en curso
                   if ($resta_general_vacaciones == 0){  //si tiene vacaciones, es que da CERO

                                if ($var_fecha > $var_fecha_abril){
                                     if ($resta_tercio > 0){
                                          if ($var_fecha <= $var_fecha_intermedia){
                                               $muestro = "si";
                                               $linea_quedan = "";
                                          }else{
                                               $muestro = "no";
                                          }
                                     }else{

                                          $muestro = "no";
                                     }
                                }else{
                                       if ($resta_tercio > 0){
                                           if (date("Y-m-d") >= date("Y").'-10-01'){
                                           //$linea_quedan = "";
                                           }
                                           $muestro = "si";
                                       }else{

                                          if ($var_fecha <= $var_fecha_intermedia){
                                                  $var_fecha_inter = date('Y-m-d', strtotime($var_fecha_intermedia . ' -1 year'));
                                                  //le resto un año porque está seteado para un año mas
                                                  if (date("Y-m-d") < date("Y").'-10-01'){
                                                        if (($var_fecha > $var_fecha_intermedia  and $var_fecha <= $var_fecha_fin) and $resta_tercio == 0){
                                                          $muestro = "no";
                                                        }else{
                                                          $muestro = "si";
                                                        }
                                                   }
                                                   if (date("Y-m-d") >= date("Y").'-10-01'){

                                                        if ($var_fecha > $_SESSION["fecha_tope_vacaciones"]  and  $resta_tercio == 0){
                                                          $muestro = "no";
                                                        }else{
                                                          $muestro = "si";
                                                        }
                                                   }

                                          }else{
                                                   $muestro = "no";

                                          }
                                       }
                                }
                        }else{
                                 $muestro = "no";
                        }
                }//del if, periodo del año


                  //$respuesta->alert($periodo.' - '.$muestro);


                 if ($total_anio > 0  and $muestro == "si" and $periodo <> ''){



                       if ($linea_quedan <> ''){
                              $itemlist_resultado.='<tr class="">
                               <td align="center" class="centro" style="background:'.$color.'"><input size="5" class="centro"  style="background-color: #F7F5EE" type="text" id="n_periodo_'.$id_campos.'" readonly="readonly" name="n_periodo_'.$id_campos.'" value="'.$periodo.'"/></td>
                               <td align="center" style="background:'.$color.'"><input size="5" class="centro"  style="background-color: #F7F5EE" type="text" id="n_corresp_'.$id_campos.'" readonly="readonly" name="n_corresp_'.$id_campos.'" value="'.$corresponde.'"/></td>
                               <td align="center" style="background:'.$color.'">'.$linea_quedan.'</td>
                               <td align="center" style="background:'.$color.'">'.$var_mostrar_tercios.'</td>
                               <td align="center" style="background:'.$color.'">'.$cuenta_tercios_parcial.'</td>
                              <!-- <td align="center" style="background:'.$color.'">'.$cuenta_tercios_restan.'</td>-->
                               </tr>
                              ';

                              $cuenta = $cuenta + $total_anio;
                       }else{
                              $itemlist_resultado.='<tr class="">
                               <td align="center" class="centro" style="background:'.$color.'"><input size="5" class="centro"  style="background-color: #F7F5EE" type="text" id="n_periodo_'.$id_campos.'" readonly="readonly" name="n_periodo_'.$id_campos.'" value="'.$periodo.'"/></td>
                               <!--<td align="center" style="background:'.$color.'"><input size="5" class="centro"  style="background-color: #F7F5EE" type="text" id="n_corresp_'.$id_campos.'" readonly="readonly" name="n_corresp_'.$id_campos.'" value="'.$corresponde.'"/></td>-->
                              <td align="center" style="background:'.$color.'"></td>
                               <td align="center" style="background:'.$color.'">'.$linea_quedan.'</td>
                               <td align="center" style="background:'.$color.'">'.$var_mostrar_tercios.'</td>
                               <td align="center" style="background:'.$color.'">'.$cuenta_tercios_parcial.'</td>
                              <!-- <td align="center" style="background:'.$color.'">'.$cuenta_tercios_restan.'</td>-->
                               </tr>
                              ';

                               $cuenta = ($linea_quedan + $var_mostrar_tercios) - $cuenta_tercios_parcial;

                                //10-06-2024
                               if ($vac["total"] < $cuenta){
                                 $cuenta = $vac["total"];
                               }
                       }


                $vacaciones = $cuenta;



                }
                $muestro = "";

         }
   }//DEL WHILE DE vac

        $itemlist_resultado.= '</table>';
        $respuesta->Assign("ver_datos","innerHTML",$itemlist_resultado);
        $respuesta->assign("dias_acumulados","value",$vacaciones);

        //si no tiene dias para tomarse no muestra el boton de grabar
        if ($vacaciones == 0){
        $respuesta->Assign("btn_grabar","style.visibility","hidden");
        }

          switch ($datos["nro_costo"]) {
                      //centro de costo no habilitado para ingresar vacaciones
                      case '6111' :
                      $respuesta->Assign("btn_grabar","style.visibility","hidden");
                      break;

                      case '6112' :
                      $respuesta->Assign("btn_grabar","style.visibility","hidden");
                      break;

                      case '6113':
                      $respuesta->Assign("btn_grabar","style.visibility","hidden");
                      break;

                      default:
                            //si la categoria empieza en 15 no mostrar boton

                            if (substr($row["CATEG"],0,2) == '15'){
                            $respuesta->Assign("btn_grabar","style.visibility","hidden");
                            }else{
                              if ($_SESSION["acceso"] == '3'){
                              $respuesta->Assign("btn_grabar","style.visibility","hidden");
                              }else{
                              $respuesta->Assign("btn_grabar","style.visibility","visible");
                              }
                            }
        }

        $respuesta->Assign("btn_grabar","style.visibility","hidden");

        return $respuesta;
 }





function ValidaFechaSalidaPersonal( $datos ){
 extract($datos);
 $respuesta = new xajaxResponse();
 include ("../../includes/cnn.php");

 $_SESSION["var_tercera_parte_vacaciones"] = 'N';


 $dias_a_tomar = 0;
 $err1 = '';
 $err2 = '';
 $err3 = '';
 $err10 = '';
 $err11 = '';
 $err12 = '';
 $err14 = '';
 $encabezado = '';
$_SESSION["TERCIO"]= "";
   //Funcion que suma o resta n dias a una fecha
function DiasFecha($fecha,$dias,$operacion){
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}            $semana = array(Domingo,Lunes,Martes,Miercoles,Jueves,Viernes,Sabado);

 $_SESSION["tercios"]="";


 $sql_m="SELECT * from rh_maestro WHERE LEGAJOS = '".$datos["legajo"]."'";
 $consulta_m=mysql_query($sql_m,$conexion);
 $maestro = mysql_fetch_array($consulta_m);
 $turno_maestro = $maestro["TURNO"];
 $diagrama = $maestro["DIAGRAMA"];

 if ($turno_maestro == 'NO' or ($turno_maestro == 'SI' and $diagrama == '3-1-3') or $turno_maestro == ''){

   $toma_1 = '7';
   $toma_2 = '14';
   $toma_3 = '21';
   $toma_4 = '28';
   $toma_5 = '35';
   $tomar_dias = utf8_encode("Tomar períodos de días según corresponda\n");
   $limite = '8';
   $hace_turno = "NO";
 }
  if ($turno_maestro == 'SI' or ($turno_maestro == 'SI' and $diagrama == '2-2-4')){
   //cambio del 12-08-2020
   $toma_1 = '7';
   $toma_2 = '14';
   $toma_3 = '21';
   $toma_4 = '28';
   $toma_5 = '35';
   $tomar_dias = utf8_encode("Tomar períodos de días según corresponda\n");
   $limite = '8';
   $hace_turno = "SI";
 }


   // $respuesta->alert($oculto_cant);

 //foreach($oculto_cant as $id){

 include ("vacaciones_control_periodo.php");

           $fec_sal = explode('-',$datos["fecha_ant_salida"]);
           $fecha_de_salida = str_pad($fec_sal[2], 4, "20", STR_PAD_LEFT).'-'.$fec_sal[1].'-'.$fec_sal[0];
           $dias_a_tomar = $dias_a_tomar + $datos['dias'];
           $dia_salida = $datos['fecha_ant_salida'];
           $cantidades_dias = $datos['dias'];
           $dias_queda = $datos['dias_acumulados'];



           include ("vacaciones_control_periodo.php");


          //me fijo el dia anterior que se desea tomar para que si quiere seguir de vacaciones y no cae lunes el dia pueda tomarselo. El dia anterior debe ser el inmediato al dia a tomar
            //Ej.: en el primer periodo tomo del 02/06/2015 al 04/06/2015, el usuario ingresa el 05/06/2015 para seguier las vacaciones, entonces debo verificar que el periodo anterior termine efectivamente el 04/06/2015, caso contrario no lo dejo cargar.
           $fecha_anterior_inmediato = DiasFecha($fecha_de_salida,1,"restar");


           $sql_vt="SELECT * from rh_vacaciones_tomadas WHERE LEGAJOS = '".$datos["legajo"]."' AND TOMADAS_REGRESO = '".$fecha_anterior_inmediato."'";
           $consulta_vt=mysql_query($sql_vt,$conexion);
           $vac_tomadas= mysql_fetch_array($consulta_vt);
           if ($vac_tomadas){
             $seguir_vacaciones = 'SI';
           }else{
             $seguir_vacaciones = 'NO';
           }
          //fin control dias



            //$respuesta->alert($fecha_de_salida.' < '.$_SESSION["fecha_tope_vacaciones"].' and '.$_SESSION["var_periodo_en_uso"].' == '.$_SESSION["periodo_anterior"]);
            $_SESSION["estado"] = '';

            if ($fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"] and $_SESSION["var_periodo_en_uso"] == $_SESSION["periodo_anterior"]){

               $sql_te = "SELECT (t1.TERCERA_PARTE -  VAR_CANT_TOMADA_TERCIO) AS RESTA_TERCIO,t1.PERIODO, t1.LEGAJOS FROM
                               (SELECT SUM(CANTIDAD_TOMADA) AS VAR_CANT_TOMADA_TERCIO, PERIODO, TERCERA_PARTE,LEGAJOS
                          FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."') AS t1";
               $consulta_vt=mysql_query($sql_te,$conexion);
               $tercio= mysql_fetch_array($consulta_vt);

                        // $respuesta->alert($sql_te);

               if (!$tercio or $tercio["RESTA_TERCIO"] > 0){

                                    // $respuesta->alert( $tercio["RESTA_TERCIO"]);
                $sql_vt="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS, LEGAJOS FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'";
                $consulta_cc=mysql_query($sql_vt,$conexion);
                $tomadas_vac= mysql_fetch_array($consulta_cc);

                     if ($tomadas_vac["VACACIONES_CANTIDAD"] == $tomadas_vac["VACACIONES_TOMADAS"]){
                       $_SESSION["periodo_anterior"] = date("y") - 1;
                       $_SESSION["TERCIO"] = "NO";

                     }else{
                        //HABILITAR DESPUES DE JUNIO
                        $_SESSION["TERCIO"] = "SI";  //HABILITAR DESPUES DE JUNIO



                        //$respuesta->alert($_SESSION["periodo_anterior"].' - '.$_SESSION["var_periodo_en_uso"]);

                        //FECHAS_VACAS_CONTROL
                        include ("vacaciones_control_periodo.php");
                        if (date("Y-m-d") > $_SESSION["mes_anio_inicio_tercio"] and date("Y-m-d") <= date("Y").'-12-31' and $_SESSION["var_periodo_en_uso"] < date("y")){

                         include("vacaciones_validaciones_tercios.php"); //HABILITAR DESPUES DE JUNIO

                        }





                     }

               }else{
                 $_SESSION["periodo_anterior"] = date("y") - 1;
               }

             }else{





                     $sql_vt="SELECT (VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA_VAC, VACACIONES_CANTIDAD, VACACIONES_TOMADAS, LEGAJOS FROM rh_vacaciones where LEGAJOS = '".$legajo."' and VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'";
                     $consulta_cc=mysql_query($sql_vt,$conexion);
                     $tomadas_vac= mysql_fetch_array($consulta_cc);

                     if ($tomadas_vac["RESTA_VAC"] > 0){

                    $sql_te = "SELECT (t1.TERCERA_PARTE -  VAR_CANT_TOMADA_TERCIO) AS RESTA_TERCIO,t1.PERIODO, t1.LEGAJOS FROM
                               (SELECT SUM(CANTIDAD_TOMADA) AS VAR_CANT_TOMADA_TERCIO, PERIODO, TERCERA_PARTE,LEGAJOS
                          FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."') AS t1";
                    $consulta_vt=mysql_query($sql_te,$conexion);
                    $tercio= mysql_fetch_array($consulta_vt);


                            if ($tercio["RESTA_TERCIO"] == 0){
                                 // $respuesta->alert("COMPLETO");
                                  $_SESSION["estado"] = "TERCIO COMPLETO";
                            }
                            if ($tercio["RESTA_TERCIO"] > 0){
                                  //$respuesta->alert("FALTA TOMAR");
                                  $_SESSION["estado"] = "FALTA TOMAR TERCIO";
                            }
                    }else{
                      //cuando se tomó todas las vacaciones
                      //$respuesta->alert("COMPLETO");
                      $_SESSION["estado"] = "TERCIO COMPLETO";
                      $_SESSION["periodo_anterior"] = date("y") - 1;
                      $respuesta->Assign("oculto_periodo_vac","values",date("y") - 1);
                    }


             }




             // $dias_a_tomar, son los dias que ingreso


             //aca juega el tercio


             //$respuesta->alert($fecha_de_salida.' > '.$_SESSION["fecha_tope_vacaciones"]);

             //return $respuesta;

              //if ($fecha_de_salida > $_SESSION["fecha_tope_vacaciones"] and $fecha_de_salida <= $_SESSION["fecha_finaliza_tercios"]){
              if ($fecha_de_salida > $_SESSION["fecha_tope_vacaciones"]){




                          $sql_p="SELECT
                                    SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA,
                                    LEGAJOS,
                                    PERIODO,
                                    TERCERA_PARTE
                                  FROM
                                    rh_vacaciones_tercios
                                  WHERE LEGAJOS = '".$legajo."'
                                    AND PERIODO = '".$_SESSION["periodo_actual"]."'
                                  GROUP BY LEGAJOS,
                                    PERIODO ";
    //FIN BUSCO  AÑOS PARA ATRAS



                        $consulta_p=mysql_query($sql_p,$conexion);

                         while ($vac_periodo= mysql_fetch_array($consulta_p)){
                         //si la cantidad del periodo es mayor a cero
                             if ($vac_periodo["CANTIDAD_TOMADA"] <> $vac_periodo["TERCERA_PARTE"]){

                              $limite = $vac_periodo["TERCERA_PARTE"];

                                 $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA from rh_vacaciones WHERE VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);

                                 //$respuesta->alert($sql_val);

                                   switch ($valida["VACACIONES_CANTIDAD"]) {

                                    case '14':
                                    $var_cantidad = '5';
                                    break;

                                    case '21':
                                    $var_cantidad = '7';
                                    break;

                                    case '28':
                                    $var_cantidad = '9';
                                    break;

                                    case '35':
                                    $var_cantidad = '12';
                                    break;

                                    }


                                 //$respuesta->alert($fecha_de_salida);

                                 //FECHAS_VACAS_CONTROL

                                 include("vacaciones_validaciones_tercios.php");

                                 if (date("Y-m-d") >= date("Y") . '-01-01' && date("Y-m-d") <= $_SESSION["mes_anio_inicio_tercio"] && $_SESSION["var_periodo_en_uso"] < date("y")) {
                                      if ($fecha_de_salida > date("Y") . '-12-31') {
                                          $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                          $err14 = "Cod: 507. NO ES POSIBLE ASIGNAR VACACIONES.\n";
                                      }


                                      if ($fecha_de_salida >  $_SESSION["mes_anio_fin_tercio"]) {
                                          $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                          $err14 = "Cod: 508. NO ES POSIBLE ASIGNAR VACACIONES.\n";
                                      }

                                  }


                                 if (date("Y-m-d") > $_SESSION["mes_anio_inicio_tercio"] and date("Y-m-d") <= date("Y").'-12-31' and $_SESSION["var_periodo_en_uso"] < date("y")){
                                      //if ($fecha_de_salida > date("Y-12-31")){

                                      //$respuesta->alert($fecha_de_salida.' > '.$_SESSION["mes_anio_fin_tercio"]);

                                      if ($fecha_de_salida > $_SESSION["mes_anio_fin_tercio"]){
                                           $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                           $err14 = "Cod: 506. NO ES POSIBLE ASIGNAR VACACIONES.\n";

                                      }
                                 }


                                    //en el caso que no se haya tomado vacaciones en 2023
                                    if ($var_cantidad <> $cantidades_dias and $vac_periodo["CANTIDAD_TOMADA"] == 0){
                                       //si la cantidad a ingresar es mayor a la que tengo de resto de las vacas del tercio
                                       if ($cantidades_dias > $var_cantidad ){
                                       $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                       $err14 = "Cod:500. Debe tomar el total del tercio: ".$var_cantidad." dias.\n";
                                       }

                                       //10-06-2024
                                       if ($cantidades_dias < $var_cantidad and $var_cantidad == '7'){
                                       $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                       $err14 = "Cod:505. Debe tomar el total del tercio: ".$var_cantidad." dias.\n";
                                       }
                                    }
                                    //si te tomó todas las vacaciones 2023
                                    if ($valida["RESTA"] == 0){
                                       $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                      $err14 = "Cod:501. No tiene dias para tomar.\n";
                                    }

                                    if ($valida["RESTA"] > $var_cantidad and $var_cantidad <> $cantidades_dias){

                                      //$_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                      //$err14 = "Cod:502. Debe tomar el total del tercio: ".$var_cantidad." dias.\n";
                                      include ("vacaciones_validaciones_tercios.php");
                                    }

                                    if ($valida["RESTA"] < $var_cantidad and $cantidades_dias <> $valida["RESTA"]){
                                      //$_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                      //$err14 = "Cod:503. Debe tomar el total del resto: ".$valida["RESTA"]." dias. Que es del Tercio\n";
                                      include ("vacaciones_validaciones_tercios.php");
                                    }

                                    if ($valida["RESTA"] == $var_cantidad and $cantidades_dias <> $valida["RESTA"]){


                                        //$respuesta->alert($valida["RESTA"].' == '.$var_cantidad.' and '.$cantidades_dias.' <> '.$valida["RESTA"]);

                                      //$_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                      //$err14 = "Cod:504. Debe tomar el total del resto: ".$valida["RESTA"]." dias. Que es del Tercio\n";
                                      include ("vacaciones_validaciones_tercios.php");
                                    }


                                    if ($err14 == ''){
                                    $_SESSION["var_tercera_parte_vacaciones"] = 'S';
                                    }

                                    // puesto 10-06-2024
                                    if ($err14 <> ""){
                                    $_SESSION["var_tercera_parte_vacaciones"] = 'N';
                                    }

                                    //$respuesta->alert($err5);

                                    //$respuesta->alert($valida["RESTA"].' - '.$var_cantidad.' - '.$cantidades_dias);


                             }
                         }//DEL WHILE
             } //DEL 2024-04-30

              /*else{
              //si la fecha supera la fecha de finalización de tercios. 10-06-2024
              $_SESSION["var_tercera_parte_vacaciones"] = 'N';
              $err14 = "Cod:506. Ingreso de Vacaciones excede la fecha permitida\n";

             }*/

                               //$respuesta->alert($err5);
                               //$respuesta->alert($_SESSION["var_tercera_parte_vacaciones"]);

//Luego pornerlo como va, 27-01-2021
//if (strlen($datos['n_salida_'.$id]) == 10){
if (strlen($fecha_ant_salida) == 10){

        //Luego pornerlo como va, 27-01-2021
        //if ($datos['n_salida_'.$id] <> '' or $datos['fecha_ant_salida'] <> ''){
        if ($datos['fecha_ant_salida'] <> ''){


          if ($fecha_de_salida < date("Y-m-d")){
             //me fijo en ausentismo si tiene observaciones
                $ausente = "rh_aus_".substr($fecha_de_salida,0,4);
                $sql_obs = "select * from ".$ausente." where LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_de_salida."'";
                $consulta_obs=mysql_query($sql_obs,$conexion);
                $observa= mysql_fetch_array($consulta_obs);

                 if ($observa["OB"] <> ''){
                  switch ($observa["OB"]) {
                      case 'APELA SANCION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'OBSERVACION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'OBSERVACION D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'AMONESTACION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'AMONESTACION D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'SUSPENDIDO':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'SUSPENDIDO D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;
                  }
                }//FIN OBSERVACIONES
          }



             if ( $cantidades_dias == $toma_1 or  $cantidades_dias == $toma_2 or  $cantidades_dias == $toma_3 or  $cantidades_dias == $toma_4 or  $cantidades_dias == $toma_5){
                //que comience un lunes, si es feriado se corre al dia siguiente
                $dia_semana_inicio = $semana[date(N, strtotime($fecha_de_salida))];//me fijo que cae ese dia

                     //me fijo en ausentismo si tiene observaciones
                $ausente = "rh_aus_".substr($fecha_de_salida,0,4);
                $sql_obs = "select * from ".$ausente." where LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_de_salida."'";
                $consulta_obs=mysql_query($sql_obs,$conexion);
                $observa= mysql_fetch_array($consulta_obs);

                   //$respuesta->alert("mensaje 01");


                 if ($observa["OB"] <> ''){
                  switch ($observa["OB"]) {
                      case 'APELA SANCION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'OBSERVACION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'OBSERVACION D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'AMONESTACION':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'AMONESTACION D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'SUSPENDIDO':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;

                      case 'SUSPENDIDO D':
                      $err7 = "<< FECHA CONTIENE OBSERVACION DE PERSONAL >>";
                      break;
                  }
                }//FIN OBSERVACIONES


                if ($dia_semana_inicio == 'Lunes'){


                              //$respuesta->alert("mensaje 02");

                        if ($hace_turno == 'NO'){

                                  //$respuesta->alert("mensaje 03");

                                  $bus = explode('-',$fecha_de_salida);
                                  $buscar_anio = $bus[2].'-'.$bus[1].'-'.$bus[0];
                                  $respuesta->Assign($dia_salida,'value',$buscar_anio);

                                  $sql_t="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                FROM rh_turnos AS tur
                                LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND   '".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta";
                        $consulta_t=mysql_query($sql_t,$conexion);
                        $turnos= mysql_fetch_array($consulta_t);



                                    if ($turnos["turno"] == ''){
                                      ///controlar franco cuando son dias seguidos
                                      if ($turnos["horario_hs"] == '00:00'){
                                          $err4 = utf8_encode("1970. Verificar Turno y/o Franja Horaria \n");
                                      }
                                    }

                           if ($turnos["horario_hs"] <> '00:00'){
                                $fecha_final = DiasFecha($fecha_de_salida,1,"sumar");
                                 $sql_f="SELECT * from feriados WHERE FEC_FERI like '".$fecha_de_salida."'";
                                        $consulta_f=mysql_query($sql_f,$conexion);
                                        $feriados= mysql_fetch_array($consulta_f);

                                if ($feriados){//si es feridado
                                    $fecha_de_salida = DiasFecha($dia_salida,1,"sumar");
                                    $dia_semana_inicio = $semana[date(N, strtotime($fecha_de_salida))];//me fijo que cae ese dia
                                    //MARTES

                                    $sql_f="SELECT * from feriados WHERE FEC_FERI like '".$fecha_de_salida."'";
                                    $consulta_f=mysql_query($sql_f,$conexion);
                                    $feriados= mysql_fetch_array($consulta_f);
                                    if ($feriados){//si es feridado
                                      $fecha_de_salida = DiasFecha($fecha_de_salida,1,"sumar");
                                      $dia_semana_inicio = $semana[date(N, strtotime($fecha_final))];//me fijo que cae ese dia
                                        //MIERCOLES
                                        $sql_f="SELECT * from feriados WHERE FEC_FERI like '".$fecha_de_salida."'";
                                        $consulta_f=mysql_query($sql_f,$conexion);
                                        $feriados= mysql_fetch_array($consulta_f);
                                            if ($feriados){//si es feridado
                                                $fecha_final = DiasFecha($fecha_de_salida,1,"sumar");
                                            }else{

                                               $bus = explode('-',$fecha_de_salida);
                                                $buscar_anio = $bus[2].'-'.$bus[1].'-'.$bus[0];
                                               $respuesta->Assign($dia_salida,'value',$buscar_anio);

                                            }

                                    }else{
                                      $bus = explode('-',$fecha_de_salida);
                                      $buscar_anio = $bus[2].'-'.$bus[1].'-'.$bus[0];
                                      $respuesta->Assign($dia_salida,'value',$buscar_anio);
                                      $respuesta-> alert(utf8_encode("ATENCION!!! Día ingresado: Feriado, se pasará al día hábil siguiente."));
                                    }

                                }else{



                                }
                             }
                            }else{ //del if de turno SI

                                             //$respuesta->alert("mensaje 04");

                                              $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                             $consulta_c=mysql_query($sql_c,$conexion);
                             $franco= mysql_fetch_array($consulta_c);




                             if($franco){
                                    //si es laborable me fijo el rango anterior que tenga franco
                                     //si es laborable me fijo el rango anterior que tenga franco

                                       $fecha_de_salida_1 = DiasFecha($fecha_de_salida,1,"restar");
                                       $fecha_de_salida_2 = DiasFecha($fecha_de_salida,2,"restar");

                                     //1 dia para atras
                                       $sql_c1="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                       FROM rh_turnos AS tur
                                       LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                       WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                      ('".$fecha_de_salida_1."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                       $consulta_c1=mysql_query($sql_c1,$conexion);
                                       $franco_1_dia_menos= mysql_fetch_array($consulta_c1);


                                          //27-01-2021
                                          if ($franco_1_dia_menos["horario_hs"] <> '00:00'){
                                            if($seguir_vacaciones == 'NO'){
                                            $err8 = utf8_encode("7029. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                            }
                                          }else{
                                               //2 dias para atras
                                               $sql_c2="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                               FROM rh_turnos AS tur
                                               LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                               WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                              ('".$fecha_de_salida_2."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                               $consulta_c2=mysql_query($sql_c2,$conexion);

                                               $franco_2_dia_menos= mysql_fetch_array($consulta_c2);

                                                 if ($franco_2_dia_menos["horario_hs"] <> '00:00'){
                                                 //$err8 = utf8_encode("801. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                                 } //comentado el 15-12-2020
                                          }

                            }//fin control de francos
                            //control que dos dias antes tenga el horario en 00:00  para el franco
                            /* $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)
                                     ORDER BY tur.`turnos_id`";
                             $consulta_c=mysql_query($sql_c,$conexion);
                             $franco= mysql_fetch_array($consulta_c);


                             if($franco){
                                    //si es laborable me fijo el rango anterior que tenga franco
                                    if ($franco["horario_hs"] == '00:00'){
                                    $err8 = utf8_encode("No se puede asignar vacaciones un día de franco \n");
                                    }else{
                                        if ($dia_semana_inicio == 'Sabado' or $dia_semana_inicio == ''){
                                        $err4 = "20. Inicio de vacaciones erroneo. Usuario sin turnos para esta fecha \n";
                                        }
                                    }

                            }//fin control de francos*/
                            }//fin del turno si

                }else{//si NO es lunes

                      if ($hace_turno == 'NO'){

                                 //$respuesta->alert("mensaje 05");

                         if ($dia_semana_inicio == 'Sabado' or $dia_semana_inicio == ''){

                             $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)
                                     ORDER BY tur.`turnos_id`";
                             $consulta_c=mysql_query($sql_c,$conexion);
                             $franco= mysql_fetch_array($consulta_c);
                             //me fijo que si es de Dom a Jue de 22 a 07:15 y tomo de turnos que venga con el campo  con 'S'
                             if ( $seguir_vacaciones == 'NO'){
                                 if ($franco["0"] <> 'S'){
                                 $err4 = utf8_encode("Cod:982. Inicio de vacaciones erroneo.\n");
                                 }
                             }
                         }else{


                        $sql_t="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                FROM rh_turnos AS tur
                                LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND   '".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta";
                        $consulta_t=mysql_query($sql_t,$conexion);
                        $turnos= mysql_fetch_array($consulta_t);



                      if ($turnos["turno"] == ''){

                        ///controlar franco cuando son dias seguidos
                        if ($turnos["horario_hs"] == '00:00'){
                            $err4 = utf8_encode("110. Verificar Turno y/o Franja Horaria \n");
                        }else{
                          if ($dia_semana_inicio <> 'Lunes' and $_SESSION["centro_costo_personal"] <> 'AAAA'){
                             if ( $seguir_vacaciones == 'NO'){
                             $err4 = utf8_encode("ER:340. Debe ser lunes para ingresar vacaciones \n");
                             }
                          }

                        }



                      }

                         }


                      }else{

                      //$respuesta->alert("mensaje 06");

                       //control que dos dias antes tenga el horario en 00:00  para el franco
                             $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)
                                     ORDER BY tur.`turnos_id`";
                             $consulta_c=mysql_query($sql_c,$conexion);
                            $franco= mysql_fetch_array($consulta_c);


                             if($franco){
                                     //si es laborable me fijo el rango anterior que tenga franco
                                     //si es laborable me fijo el rango anterior que tenga franco
                                      //09-12-2020
                                       $fecha_de_salida_1 = DiasFecha($fecha_de_salida,1,"restar");
                                       $fecha_de_salida_2 = DiasFecha($fecha_de_salida,2,"restar");

                                     //1 dia para atras
                                       $sql_c1="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                       FROM rh_turnos AS tur
                                       LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                       WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                      ('".$fecha_de_salida_1."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                       $consulta_c1=mysql_query($sql_c1,$conexion);
                                       $franco_1_dia_menos= mysql_fetch_array($consulta_c1);



                                          if ($franco_1_dia_menos["horario_hs"] <> '00:00'){
                                            if($seguir_vacaciones == 'NO'){
                                              if ($_SESSION["centro_costo_personal"] <> 'AAAA'){
                                              $err8 = utf8_encode("702. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                              }
                                            }
                                          }else{
                                               //2 dias para atras
                                               $sql_c2="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                               FROM rh_turnos AS tur
                                               LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                               WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                              ('".$fecha_de_salida_2."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                               $consulta_c2=mysql_query($sql_c2,$conexion);

                                               $franco_2_dia_menos= mysql_fetch_array($consulta_c2);

                                                 if ($franco_2_dia_menos["horario_hs"] <> '00:00'){
                                                 //$err8 = utf8_encode("801. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                                 } //comentado el 15-12-2020
                                          }





                            }//fin control de francos

                      }//del turno distinto de lunes

                    }//fin del if si NO es lunes

                   //si la cantidad restante entre la que queda y la que se toma es menor a 7 debe tomarse el total
                   if ($dias_queda > $cantidades_dias){//si es distinto del total
                       $resta_dias = $dias_queda - $cantidades_dias;
                       if ($resta_dias < 7){
                       //$err5 = "Cantidad de dias a tomar debe ser igual al total.\n";
                       }

                   }


             }else{ //del if si los dias son 7,14,21,28,35 O 6,12,18,24,30

                 $dia_semana_inicio = $semana[date(N, strtotime($fecha_de_salida))];//me fijo que cae ese dia

                     //$respuesta->alert("mensaje 7");

                 /***********************************************************************************************/
                 //viene de VALIDA_LEGAJO (funcion)
                 //me fijo los dias menores si no es personal
                 if ($_SESSION["centro_costo_personal"] <> 'AAAA'){


                        //$sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO >= '19'  AND VACACIONES_TOMADAS > 0 group by VACACIONES_PERIODO LIMIT 1";
                        $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO >= '".$_SESSION["var_periodo_en_uso"]."' group by VACACIONES_PERIODO limit 1";
    //FIN BUSCO  AÑOS PARA ATRAS

                        $consulta_p=mysql_query($sql_p,$conexion);

                         $resto_cant = 0;
                         $x = $datos["dias"];


                         //$respuesta->alert($sql_p);

                         $v_sale = explode('-',$oculto_fecha_inicio);
                         $j_dias = $v_sale[2].'-'.$v_sale[1].'-'.$v_sale[0];
                         //bucle recorriendo los periodos y mientras la cantidad de dias a tomar sea mayor a cero
                         while ($vac_periodo= mysql_fetch_array($consulta_p) and $x > 0){

                              //$respuesta->alert($vac_periodo["resta_vac"]);

                         //si la cantidad del periodo es mayor a cero
                             if ($vac_periodo["resta_vac"] > 0 and $vac_periodo["VACACIONES_PERIODO"] == $_SESSION["periodo_anterior"]){
                                $dias_cant = $vac_periodo["resta_vac"];


                                //$respuesta->alert($cantidades_dias.' > 2 and '.$cantidades_dias.' < '.$limite);

                                 if ($cantidades_dias > 2 and $cantidades_dias < $limite){

                                   $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA, VACACIONES_PERIODO from rh_vacaciones WHERE VACACIONES_PERIODO = '".$vac_periodo["VACACIONES_PERIODO"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);

                                  //$respuesta->alert($sql_val);


                                         if ($valida["RESTA"] <> $cantidades_dias){//si es distinto del total


                                           if ($valida["RESTA"] > $toma_1){
                                           $err6 = $tomar_dias;

                                           }
                                           if ($valida["RESTA"] > $toma_2){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_3){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_4){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_5){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($cantidades_dias < $toma_1){



                                              //me fijo si hay resto de tercio
                                               $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."' GROUP BY LEGAJOS, PERIODO";
                                               $consulta_vt=mysql_query($sql_vt,$conexion);
                                               $vac_periodo_tercios= mysql_fetch_array($consulta_vt);

                                               $tercio = $vac_periodo_tercios["TERCERA_PARTE"];
                                               $tercio_tomado = $vac_periodo_tercios["CANTIDAD_TOMADA"];
                                               $resta_nro = ($cantidades_dias + $tercio_tomado) - $tercio;
                                               $resta_nro = abs($resta_nro);



                                               $resta_dias =  $dias_cant - $cantidades_dias;
                                              // $respuesta->alert($resta_nro);

                                              // return $respuesta;


                                              if ($fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"]){

                                                 $resta_tomar_tercio = $tercio - $tercio_tomado;
                                                 $suma_calculo_resto = $resta_tomar_tercio + $cantidades_dias;

                                                 //si el resto del tercio, mas, el ingreso de la cantidad de dias, es igual, al resto de vacaciones que
                                                 // quedan por tomar, lo dejo pasar
                                                 if ($suma_calculo_resto == $dias_queda){
                                                   $err6 = "";
                                                 }else{
                                                    //$err6 = utf8_encode("ER:50100. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                     $err6 = "";
                                                 }

                                              }


                                                if ( $fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"] ){
                                                  //$err6 = utf8_encode("ER:5620. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                }else{
                                                      $err6 = "";
                                                      /*
                                                      if ($resta_nro <> 0 ){
                                                        $err6 = utf8_encode("ER:5820. Tomarse el total del tercio \n");
                                                      }else{
                                                        $err6 = "";
                                                      }*/
                                                }

                                           }

                                         }


                                         if ($valida["RESTA"] == $cantidades_dias){
                                           $tomar_dias = '';
                                           $err6 = $tomar_dias;
                                         }

                                         if ($valida["VACACIONES_TOMADAS"] == 0){
                                           if ($cantidades_dias >= 3 and $cantidades_dias <= 5){
                                              $tomar_dias = '';
                                              $err6 = $tomar_dias;
                                           }
                                         }


                                 }else{



                                   $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA, VACACIONES_PERIODO from rh_vacaciones WHERE VACACIONES_PERIODO = '".$vac_periodo["VACACIONES_PERIODO"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);


                                      // $respuesta->alert($sql_val);

                                         if ($valida["RESTA"] <> $cantidades_dias){//si es distinto del total



                                           if ($valida["RESTA"] > $toma_1){
                                           $err6 = $tomar_dias;

                                           }
                                           if ($valida["RESTA"] > $toma_2){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_3){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_4){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_5){

                                           $err6 = $tomar_dias;
                                           }
                                           if ($cantidades_dias < $toma_1){




                                              //me fijo si hay resto de tercio
                                               $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."' GROUP BY LEGAJOS, PERIODO";
                                               $consulta_vt=mysql_query($sql_vt,$conexion);
                                               $vac_periodo_tercios= mysql_fetch_array($consulta_vt);



                                               $tercio = $vac_periodo_tercios["TERCERA_PARTE"];
                                               $tercio_tomado = $vac_periodo_tercios["CANTIDAD_TOMADA"];
                                               $resta_nro = ($cantidades_dias + $tercio_tomado) - $tercio;
                                               $resta_nro = abs($resta_nro);

                                                if ( $fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"]){
                                                  //$err6 = utf8_encode("ER:50101. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                  $err6 = "";
                                                }else{
                                                  if ($resta_nro <> 0){
                                                      $err6 = utf8_encode("ER:50102. Ingresar cantidad de vacaciones validas \n");
                                                  }else{
                                                      $err6 = "";
                                                  }
                                                }

                                           }

                                         }



                                         if ($valida["RESTA"] == $cantidades_dias){
                                           $tomar_dias = '';
                                           $err6 = $tomar_dias;
                                         }



                                         //control para que quede el tercio
                                         if ( $_SESSION["centro_costo_personal"] <> 'AAAA' and $vac_periodo["VACACIONES_PERIODO"] == $_SESSION["periodo_anterior"]){
                                              //rutina de tercios de vacaciones

                                                $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);

                                              $tercio = $vac_periodo_tercios["TERCERA_PARTE"];

                                            // $respuesta->alert($sql_vt);

                                              $resta_nro = ($cantidades_dias + $tercio) - $dias_queda;

                                              $resta_nro = abs($resta_nro);

                                              //$respuesta-> alert($cantidades_dias.' + '.$tercio.' - '.$dias_queda);

                                              //return $respuesta;
                                              //si el resto de dias no es igual a cero
                                              if ($fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"]){//linea 01

                                                 $resta_tomar_tercio = $tercio - $tercio_tomado;
                                                 $suma_calculo_resto = $resta_tomar_tercio + $cantidades_dias;

                                                 //si el resto del tercio, mas, el ingreso de la cantidad de dias, es igual, al resto de vacaciones que
                                                 // quedan por tomar, lo dejo pasar



                                                 $diferencia_resto = $tercio - $vac_periodo_tercios["CANTIDAD_TOMADA"];
                                                 if ($suma_calculo_resto == $dias_queda or $diferencia_resto == 0){

                                                     if ($resta_tomar_tercio == 0 and $dias_queda <> $cantidades_dias){
                                                       // $err6 = utf8_encode("ER:50103. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                       $err6 = "";
                                                       // $respuesta->alert("paso1");
                                                     }else{
                                                        $err6 = "";
                                                       // $respuesta->alert("paso2");
                                                     }
                                                 }else{

                                                    //los dias que me quedan para tomar, le resto los dias que ingreso
                                                    $diferencia_ingreso = $dias_queda - $cantidades_dias;

                                                    //$respuesta->alert($fecha_de_salida);
                                                    if ($diferencia_ingreso <> $diferencia_resto){
                                                    //$err6 = utf8_encode("ER:85620. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                            //si la fecha de salida es menor o igual a la fecha tope, 30-04-2023
                                                            if ($fecha_de_salida <= $_SESSION["fecha_tope_vacaciones"]){

                                                                if ($vac_periodo_tercios["CANTIDAD_TOMADA"] == 0 and $dias_queda <> $cantidades_dias)
                                                           // $respuesta->alert($diferencia_ingreso);
                                                             //$err6 = utf8_encode("ER:50104. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                                              $err6 = "";
                                                           }else{

                                                                $err6 = '';
                                                            }
                                                    }else{
                                                    $err6 = "";
                                                    }
                                                 }

                                              }//fin linea 01
                                            }
                                            //fin control de tercios

                                            //return $respuesta;


                                 }
                             }// de un periodo diferente al actual

                             /***************************************/
                             /***************************************/
                             /******* si es el año actual ***********/
                             /***************************************/
                             /***************************************/

                             //si es el año actual
                                if ($vac_periodo["resta_vac"] > 0 and $vac_periodo["VACACIONES_PERIODO"] == $_SESSION["periodo_actual"]){
                                $dias_cant = $vac_periodo["resta_vac"];




                                //$respuesta->alert($cantidades_dias.' - '.$cantidades_dias.' - '.$limite);

                                 if ($cantidades_dias > 2 and $cantidades_dias < $limite){
                                  $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA from rh_vacaciones WHERE VACACIONES_PERIODO = '".$vac_periodo["VACACIONES_PERIODO"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);





                                     if ($valida["RESTA"] > $toma_1 and $valida["VACACIONES_TOMADAS"] > 0){

                                     $resto_cantidades = $valida["RESTA"] - $cantidades_dias;

                                     if ($var_cantidad <> $resto_cantidades){
                                         $err5 = "Cod:AB1. Cantidad de dias a tomar debe ser mayor.\n";
                                     }


                                     }
                                     if ($valida["RESTA"] < $toma_1 and ($valida["RESTA"] <> $cantidades_dias)){
                                     $err5 = "Cod:AB2. Cantidad de dias a tomar debe ser igual al total.\n";
                                     }
                                     if ($valida["RESTA"] == $toma_1 and ($valida["RESTA"] <> $cantidades_dias)){
                                     $err5 = "Cod:AB3. Cantidad de dias a tomar debe ser igual al total.\n";
                                     }





                                 }else{


                                   $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA, VACACIONES_PERIODO from rh_vacaciones WHERE VACACIONES_PERIODO = '".$vac_periodo["VACACIONES_PERIODO"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);




                                         if ($valida["RESTA"] <> $cantidades_dias){//si es distinto del total
                                           if ($valida["RESTA"] > $toma_1){
                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_2){
                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_3){
                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_4){
                                           $err6 = $tomar_dias;
                                           }
                                           if ($valida["RESTA"] > $toma_5){
                                           $err6 = $tomar_dias;
                                           }
                                           if ($cantidades_dias < $toma_1){
                                           $err6 = utf8_encode("Cod:AB4. Tomarse el total restante o Debe ser igual o mayor a 3 días \n");
                                           }



                                         }



                                         if ($valida["RESTA"] == $cantidades_dias){
                                           $tomar_dias = '';
                                           $err6 = $tomar_dias;
                                         }



                                 }
                             }

                             //fin año actual



                         }
                }/**FIN SI ES DISTINTO A PERSONAL**/
                /***********************************************************************************************/





                  //horario comun
                  if ($hace_turno == 'NO'){

                          if ($dia_semana_inicio == 'Sabado' or $dia_semana_inicio == ''){

                             $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)
                                     ORDER BY tur.`turnos_id`";
                             $consulta_c=mysql_query($sql_c,$conexion);
                             $franco= mysql_fetch_array($consulta_c);
                             //me fijo que si es de Dom a Jue de 22 a 07:15 y tomo de turnos que venga con el campo  con 'S'
                             if ($franco["0"] <> 'S'){
                               //si el dia anterior se ha tomado vacaciones, lo dejo seguir
                                   if ( $seguir_vacaciones == 'NO'){
                                   $err4 = utf8_encode("Cod:002. Inicio de vacaciones erroneo.\n");
                                   }
                                 }
                         }else{


                        $sql_t="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                FROM rh_turnos AS tur
                                LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND   '".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta";
                        $consulta_t=mysql_query($sql_t,$conexion);
                        $turnos= mysql_fetch_array($consulta_t);



                      if ($turnos["turno"] == ''){

                        ///controlar franco cuando son dias seguidos
                        if ($turnos["horario_hs"] == '00:00'){
                            $err4 = utf8_encode("8180. Verificar Turno y/o Franja Horaria \n");
                        }else{
                           if ($dia_semana_inicio <> 'Lunes'){
                             //si el dia anterior se ha tomado vacaciones, lo dejo seguir
                             if ( $seguir_vacaciones == 'NO'){
                               if ($_SESSION["centro_costo_personal"] <> 'AAAA'){
                               $err4 = utf8_encode("ER:351. Debe ser lunes para ingresar vacaciones \n");
                               }
                             }
                          }
                        }



                      }

                         }


                 if ($turnos["horario_hs"] <> '00:00'){

                  $dia_semana_inicio = $semana[date(N, strtotime($fecha_de_salida))];//me fijo que cae ese dia
                  if ($dia_semana_inicio <> 'Sabado' and $dia_semana_inicio <> ''){
                      $sql_f="SELECT * from feriados WHERE FEC_FERI = '".$fecha_de_salida."'";
                      $consulta_f=mysql_query($sql_f,$conexion);
                      $feriados = mysql_fetch_array($consulta_f);
                      if ($feriados){//si es feridado
                          if ($dia_semana_inicio == 'Lunes'){
                                $fecha_de_salida = DiasFecha($dia_salida,1,"sumar");
                                    $dia_semana_inicio = $semana[date(N, strtotime($fecha_de_salida))];//me fijo que cae ese dia
                                    //MARTES

                                    $sql_f="SELECT * from feriados WHERE FEC_FERI like '".$fecha_de_salida."'";
                                    $consulta_f=mysql_query($sql_f,$conexion);
                                    $feriados= mysql_fetch_array($consulta_f);
                                    if ($feriados){//si es feridado
                                      $fecha_de_salida = DiasFecha($fecha_de_salida,1,"sumar");
                                      $dia_semana_inicio = $semana[date(N, strtotime($fecha_final))];//me fijo que cae ese dia
                                        //MIERCOLES
                                        $sql_f="SELECT * from feriados WHERE FEC_FERI like '".$fecha_de_salida."'";
                                        $consulta_f=mysql_query($sql_f,$conexion);
                                        $feriados= mysql_fetch_array($consulta_f);
                                            if ($feriados){//si es feridado
                                                $fecha_final = DiasFecha($fecha_de_salida,1,"sumar");
                                            }else{

                                               $bus = explode('-',$fecha_de_salida);
                                                $buscar_anio = $bus[2].'-'.$bus[1].'-'.$bus[0];
                                               $respuesta->Assign($dia_salida,'value',$buscar_anio);

                                            }

                                    }else{

                                      //$respuesta->alert("mensaje 08");


                                      $respuesta-> alert(utf8_encode("ATENCION!!! Día ingresado: Feriado, se pasará al día hábil siguiente."));

                                    }
                          }else{
                           $err4 = "Inicio de vacaciones erroneo. Feriado\n";
                          }

                      }
                     }
                  }else{


                  }//del if de dia_semana_inicio



                }else{ //fin de, si turno es SI
                         //control que dos dias antes tenga el horario en 00:00  para el franco
                             $sql_c="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                     FROM rh_turnos AS tur
                                     LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                     WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                     ('".$fecha_de_salida."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                             $consulta_c=mysql_query($sql_c,$conexion);
                             $franco= mysql_fetch_array($consulta_c);




                             if($franco){
                                    //si es laborable me fijo el rango anterior que tenga franco
                                     //si es laborable me fijo el rango anterior que tenga franco

                                       $fecha_de_salida_1 = DiasFecha($fecha_de_salida,1,"restar");
                                       $fecha_de_salida_2 = DiasFecha($fecha_de_salida,2,"restar");

                                     //1 dia para atras
                                       $sql_c1="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                       FROM rh_turnos AS tur
                                       LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                       WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                      ('".$fecha_de_salida_1."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                       $consulta_c1=mysql_query($sql_c1,$conexion);
                                       $franco_1_dia_menos= mysql_fetch_array($consulta_c1);




                                          //09-12-2020
                                          if ($franco_1_dia_menos["horario_hs"] <> '00:00'){
                                          $err8 = utf8_encode("701. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                          }else{
                                               //2 dias para atras
                                               $sql_c2="SELECT tur.`turnos_legajo`, tur.`turnos_fecha_desde`, tur.`turnos_fecha_hasta`, hor.6, hor.0, hor.`horario_tipo`,hor.`horario_hs`, hor.`turno`
                                               FROM rh_turnos AS tur
                                               LEFT JOIN rh_horarios AS hor ON (hor.horario_id = tur.horario_id)
                                               WHERE  tur.turnos_legajo = '".$datos["legajo"]."' AND
                                              ('".$fecha_de_salida_2."'  BETWEEN tur.turnos_fecha_desde AND tur.turnos_fecha_hasta)";
                                               $consulta_c2=mysql_query($sql_c2,$conexion);

                                               $franco_2_dia_menos= mysql_fetch_array($consulta_c2);

                                                 if ($franco_2_dia_menos["horario_hs"] <> '00:00'){
                                                 //$err8 = utf8_encode("801. Día anterior al inicio de la fecha de salida de vacaciones, debe ser franco \n");
                                                 } //comentado el 15-12-2020
                                          }

                            }//fin control de francos




                }//de turno
             }//si son distintos a 7,14,21,28,35

        }//si tiene datos la caja
        }else{
            if (strlen($datos['n_salida_'.$id]) > 0 and strlen($datos['n_salida_'.$id]) < 10){
            $err10 = "Fecha ingresada incorrecta  \n";
            }



        }

        if ($cantidades_dias > $dias_queda ){
          $err3 = "Dias supera cantidad del periodo  \n";
        }


 //}//del for que recorre los dias



         if ($dias > $datos["dias_acumulados"]){
          $err2 = "Dias supera el total  \n";
        }

         $sql_con="SELECT * from rh_vacaciones_tomadas where LEGAJOS = '".$datos["legajo"]."' AND TOMADAS_SALIDA = '".$fecha_de_salida."'";
                             $consulta_con=mysql_query($sql_con,$conexion);
                             $control_vac= mysql_fetch_array($consulta_con);


         //controlar que la fecha que se ingresa para el periodo del año actual no supere una fecha estimada


         if ($fecha_de_salida > $_SESSION["fecha_fin_vacaciones"] and $_SESSION["estado"] <> "TERCIO COMPLETO"){
           $err12 = "Cod 01. Fecha supera el rango de vacaciones a tomar\n";
         }

            //busco el periodo en el cual está grabando al momento de pulsar el boton,
            //es lo que me muestra en vacaciones: DIAS A TOMAR

             include("vacaciones_control_periodo.php");

             $var_periodo_en_uso = $_SESSION["var_periodo_en_uso"];

            //si la fecha de salida es superior a la fecha tope y el periodo es dos años menor

            if ($fecha_de_salida > $_SESSION["fecha_tope_vacaciones"] and $var_periodo_en_uso == date("y") - 2){
               $err12 = "Cod 02. Fecha supera el rango de vacaciones a tomar";
            }

           //fin control periodo que tiene que pasar


         if ($control_vac){
          $err11 = "Periodo de vacaciones existente";
         }

              //$respuesta->alert("var ".$_SESSION["var_tercera_parte_vacaciones"]);

              //blanqueo el err6 para que pase cuando es del tercio, asi no toma 7, 14, etc. y toma el valor del tercio
              if ($_SESSION["var_tercera_parte_vacaciones"] == 'S'){
                $err6 = "";
                $err5 = "";
                $err14 = "";
              }


               //si se ingresa una cantidad en el caso de ser tercio, cuya diferencia es igual al tercio, lo permito seguir
               $sql_val="SELECT VACACIONES_CANTIDAD, VACACIONES_TOMADAS,SUM(VACACIONES_CANTIDAD - VACACIONES_TOMADAS) AS RESTA from rh_vacaciones WHERE VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."' and LEGAJOS = '".$datos["legajo"]."' group by VACACIONES_PERIODO";
                                   $consulta_val=mysql_query($sql_val,$conexion);
                                   $valida= mysql_fetch_array($consulta_val);



                                   switch ($valida["VACACIONES_CANTIDAD"]) {

                                    case '14':
                                    $var_cantidad = '5';
                                    break;

                                    case '21':
                                    $var_cantidad = '7';
                                    break;

                                    case '28':
                                    $var_cantidad = '9';
                                    break;

                                    case '35':
                                    $var_cantidad = '12';
                                    break;

                                    }
               $resta_de_dias = $valida["RESTA"] - $cantidades_dias;



               if ($resta_de_dias == $var_cantidad){
                $err6 = "";
                $err5 = "";
               }




        $respuesta->Assign("oculto_cantidad","value", $dias_a_tomar);
        $encabezado = "Se registraron los sig. errores: \n";
        $mostrar_error = $encabezado.$err1.$err2.$err3.$err4.$err5.$err6.$err7.$err8.$err9.$err10.$err11.$err12.$err14;
        if ($err1 <> '' or $err2 <> '' or $err3 <> '' or $err4 <> '' or $err5 <> '' or $err6 <> '' or $err7 <> '' or $err8 <> '' or $err9 <> '' or $err10 <> '' or $err11 <> '' or $err12 <> '' or $err14 <> ''){
          $respuesta->alert($mostrar_error);
        }else{

           $v_salida = explode('-',$fecha_de_salida);
           $sale_fecha = $v_salida[2].'-'.$v_salida[1].'-'.$v_salida[0];



           $respuesta->Assign("oculto_fecha_inicio","value",$sale_fecha);

           $respuesta->Assign("oculto_periodo_vac","value",$_SESSION["periodo_anterior"]);

           $respuesta->call("xajax_DiasTomados(xajax.getFormValues('formu3'))");

        }

  return $respuesta;
}




function GrabarVacaciones($datos){
    extract($datos);
    $respuesta = new xajaxResponse();
    include ("../../includes/cnn.php");

 function DiasFecha($fecha,$dias,$operacion){
  Switch($operacion){
    case "sumar":
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    return $varFecha;
    break;
    case "restar":
    $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
    return $varFecha;
    break;
    default:
    $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
    break;
  }
}

function dateDiff($start, $end) {

$start_ts = strtotime($start);

$end_ts = strtotime($end);

$diff = $end_ts - $start_ts;

return round($diff / 86400);

}

        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
   $ip = getenv("HTTP_CLIENT_IP");
else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
   $ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
   $ip = getenv("REMOTE_ADDR");
else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
   $ip = $_SERVER['REMOTE_ADDR'];
else
   $ip = "unknown";


$obtiene_ip = explode (", ",$ip);
$ip_local   = $obtiene_ip[0];
$maquina    = gethostbyaddr ($ip_local); //nombre de la maquina
include ("../../includes/aud_ip.php");
$audi_fecha = date("Y-m-d"); //fecha actual
$audi_hora = date("H:i:s");  //hora actual
$audi_aplic = "vacaciones_sector.php ¬  RRHH / Vacaciones altas";
$audi_user = $_SESSION["us2"];

$erNum = -1; // Número interno de error.
$erStr = ""; // Descripción del error


$sql_c="SELECT * FROM users WHERE username = '".$_SESSION["us2"]."'";
    $consulta_c=mysql_query($sql_c,$conexion);
    $controla= mysql_fetch_array($consulta_c);





 //control que no se ingrese en fechas ya asignadas
      $fec = explode("-",$oculto_fecha_inicio);
      $dia = $fec[0];
      $mes = $fec[1];
      $anio = str_pad($fec[2], 4, "20", STR_PAD_LEFT);
      $fecha_anterior = $anio.'-'.$mes.'-'.$dia;

      //$mes_anio_fichada = "fichadas_".$mes.substr($anio,2,2);
      $mes_anio_fichada = "rh_fichadas_20".substr($anio,2,2);
      //$mes_aus = "aus".$mes.substr($anio,2,2);
      $mes_aus = "rh_aus_20".substr($anio,2,2);
      $mes_jus = "rh_jus_20".substr($anio,2,2);
      $ll_tarde = "rh_l_t_20".substr($anio,2,2);



 include ("vacaciones_trigger.php");
 mysql_query("BEGIN", $conexion);

    $n_toma_1 = 0;
    $no_pasa_5 = "";


    //falta fecha de alta
    if ($datos['dias'] <> ''){


               //si la fecha es superior al control de tope, se debe tomar el año anterior al año en curso


               if (date("Y-m-d") > $_SESSION["fecha_tope_vacaciones"]){
                $_SESSION["periodo_anterior"] = $_SESSION["periodo_anterior"] + 1;
               }else{
                   $sql_vac="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'  group by VACACIONES_PERIODO";
                   $consulta_vac=mysql_query($sql_vac,$conexion);
                   $vacas= mysql_fetch_array($consulta_vac);

                   if ($vacas["resta_vac"] == '0'){
                   $_SESSION["periodo_anterior"] = $_SESSION["periodo_anterior"] + 1;
                   }


               }


               $sql_te = "SELECT (t1.TERCERA_PARTE -  VAR_CANT_TOMADA_TERCIO) AS RESTA_TERCIO,t1.PERIODO, t1.LEGAJOS FROM
                               (SELECT SUM(CANTIDAD_TOMADA) AS VAR_CANT_TOMADA_TERCIO, PERIODO, TERCERA_PARTE,LEGAJOS
                          FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."') AS t1";
               $consulta_vt=mysql_query($sql_te,$conexion);
               $tercio= mysql_fetch_array($consulta_vt);

               //$respuesta->alert($sql_te);
               //return $respuesta;


               //verifica que el periodo anterior se haya cumplido el tercio o no tener nada para tomar como referencia el año anterior en curso

               if (!$tercio or $tercio["RESTA_TERCIO"] > 0){

                     //Se puede dar que los tercios tengan resto, pero las vacaciones ya han sido tomadas todas
                     if ($_SESSION["TERCIO"] == 'NO'){
                       $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."'  group by VACACIONES_PERIODO";
                     }else{
                       $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_anterior"]."'  group by VACACIONES_PERIODO";

                     }

               }else{
                 $sql_p="SELECT sum( VACACIONES_CANTIDAD - VACACIONES_TOMADAS ) AS resta_vac, VACACIONES_PERIODO, LEGAJOS FROM rh_vacaciones WHERE LEGAJOS = '".$legajo."' AND VACACIONES_PERIODO = '".$_SESSION["periodo_actual"]."'  group by VACACIONES_PERIODO";
               }


              //$respuesta->alert($sql_p);


    $consulta_p=mysql_query($sql_p,$conexion);

     $leg = $legajo;
    $legajo = $datos["legajo"];

   $resto_cant = 0;
   $x = $datos["dias"];





   $v_sale = explode('-',$oculto_fecha_inicio);
   $j_dias = $v_sale[2].'-'.$v_sale[1].'-'.$v_sale[0];
   //bucle recorriendo los periodos y mientras la cantidad de dias a tomar sea mayor a cero
   while ($vac_periodo= mysql_fetch_array($consulta_p) and $x > 0){

   $var_tercera_parte_tomada = 'N';
   //si la cantidad del periodo es mayor a cero
   if ($vac_periodo["resta_vac"] > 0){
      $dias_cant = $vac_periodo["resta_vac"];


             if ( $_SESSION["centro_costo_personal"] <> 'AAAA'){ //linea if 6




               //ACA CONTROLA EL AÑO 2022
               if ($vac_periodo["VACACIONES_PERIODO"] == $_SESSION["periodo_anterior"] and $j_dias <= $_SESSION["fecha_tope_vacaciones"]){ //linea if 5




                //verifica la fecha y si corresponde tomarse vacaciones con el tercio, no lo controlo
                    $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."'  and PROCESO <> 'INSERT' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);

                    //$respuesta->alert($sql_vt);

                    if (!$vac_periodo_tercios){//linea if 4
                             $respuesta->alert("01. No es posible asignar vacaciones del Periodo. Depto. Personal");
                             $respuesta->redirect("vacaciones_sector.php");
                             mysql_query("ROLLBACK", $conexion);
                             return $respuesta;
                    }else{
                       //linea if 3
                       if ($vac_periodo_tercios["CANTIDAD_TOMADA"] == $vac_periodo_tercios["TERCERA_PARTE"] and $vac_periodo_tercios["TERCERA_PARTE"] > 0){
                         $respuesta->alert("H1. Tercios usados para vacaciones del Periodo. Depto. Personal");
                         $respuesta->redirect("vacaciones_sector.php");
                         mysql_query("ROLLBACK", $conexion);
                         return $respuesta;
                       }else{

                               $sql_vtc="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."'  and PROCESO = 'INSERT' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vtc=mysql_query($sql_vtc,$conexion);
                    $vac_periodo_tercios_c= mysql_fetch_array($consulta_vtc);

                               $suma_tercios_t = $vac_periodo_tercios_c["CANTIDAD_TOMADA"] + $datos['dias'];

                               //$respuesta->alert($sql_vtc);

                               if ($suma_tercios_t > $vac_periodo_tercios_c["TERCERA_PARTE"] and $vac_periodo_tercios_c["CANTIDAD_TOMADA"] > 0){


                                   if ($vac_periodo_tercios_c["CANTIDAD_TOMADA"] <> $vac_periodo_tercios_c["TERCERA_PARTE"]){
                                       $respuesta->alert("H4. Debe ingresar Tercios de forma correcta. Depto. Personal");
                                       $respuesta->redirect("vacaciones_sector.php");
                                       mysql_query("ROLLBACK", $conexion);
                                       return $respuesta;
                                   }

                               }



                         if ($vac_periodo_tercios["TERCERA_PARTE"] <> $datos['dias'] and $vac_periodo_tercios["CANTIDAD_TOMADA"] > 0){//linea if 2

                            //si los dias que me restan tomar de vacaciones es menor al tercio, lo dejo pasar, sino muestro error
                               $sql_vtc="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."'  and PROCESO = 'INSERT' GROUP BY LEGAJOS, PERIODO";
                               $consulta_vtc=mysql_query($sql_vtc,$conexion);
                               $vac_periodo_tercios_c= mysql_fetch_array($consulta_vtc);

                               $suma_tercios = $vac_periodo_tercios_c["CANTIDAD_TOMADA"] + $datos['dias'];



                                  if ($suma_tercios > $vac_periodo_tercios["TERCERA_PARTE"]){//linea if 1
                                  $respuesta->alert("H2. Debe ingresar Tercios (total) despues del ".$_SESSION["fecha_tope_vacaciones"].". Si ya ha sido usado, no podra disponer del mismo. Depto. Personal");
                                  $respuesta->redirect("vacaciones_sector.php");
                                  mysql_query("ROLLBACK", $conexion);
                                  return $respuesta;
                                  }else{
                                    if ($vac_periodo_tercios["TERCERA_PARTE"] <> $suma_tercios and $vac_periodo_tercios["CANTIDAD_TOMADA"] > 0){
                                      $respuesta->alert("H3. Debe ingresar el total de vacaciones pasada la fecha ".$_SESSION["fecha_tope_vacaciones"].". Depto. Personal");
                                      $respuesta->redirect("vacaciones_sector.php");
                                      mysql_query("ROLLBACK", $conexion);
                                      return $respuesta;
                                    }
                                  }//fin linea if 1
                         }//fin linea if 2
                       }// fin linea if 3

                    }//fin linea if 4
                    $var_tercera_parte_tomada = 'S';
               }//fin del 2023-04-30. linea if 5

             }// fin linea if 6


               //$respuesta->alert($var_tercera_parte_tomada);


       if ($dias_cant >= $x){





                  //las sesiones vienen de la function ValidaLegajo
                  if ($_SESSION["centro_costo_personal"] == 'AAAA' and  $_SESSION["nivel_usuario"] == '3'){
                     $var_valida_rrhh = 'S';
                     $var_valida = 'S';
                  }else{
                     $var_valida_rrhh = 'N';
                     $var_valida = 'N';
                  }

                  if ($_SESSION["nivel_usuario"] == '3' or $_SESSION["nivel_usuario"] == '4' or $_SESSION["nivel_usuario"] == '5'){
                     $var_valida_rrhh = 'S';
                     $var_valida = 'S';
                  }



                     //fin control dias



       //si los dias a tomar es mayor a los dias del periodo
       //resto los dias del periodo - los dias a tomar
       $resto_cant = $dias_cant - $x ;
       $dias_cant = $resto_cant;
       //seteo la variable $x para que finalice el bucle
       $fecha_salida = $j_dias;
       $diferencia = $vac_periodo["resta_vac"] - $resto_cant;
       //le resto un dia para que comience del dia a tomar
       $n_toma_1 = $diferencia - 1;
       //sumo cantidad de dias
       $inicio=strtotime($fecha_salida);
       $dias=($n_toma_1*86400);
       $fecha_regresa = date("Y-m-d",$inicio+$dias);
       //fin suma






               //ACA CONTROLA EL AÑO 2022
               if ($vac_periodo["VACACIONES_PERIODO"] == $_SESSION["periodo_anterior"] and $fecha_regresa <= $_SESSION["fecha_tope_vacaciones"]){
                //verifica la fecha y si corresponde tomarse vacaciones con el tercio, no lo controlo
                    $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_anterior"]."' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);



                    if (!$vac_periodo_tercios){
                             $respuesta->alert("J2. No es posible asignar vacaciones del Periodo. Depto. Personal");
                             $respuesta->redirect("vacaciones_sector.php");
                             mysql_query("ROLLBACK", $conexion);
                             return $respuesta;
                    }else{

                              $cant_parte = $vac_periodo_tercios["TERCERA_PARTE"];
                              $tercio_parte = $datos["dias"];

                       if ($vac_periodo_tercios["CANTIDAD_TOMADA"] == $vac_periodo_tercios["TERCERA_PARTE"] and $vac_periodo_tercios["TERCERA_PARTE"] > 0){
                          if ($vac_periodo_tercios_c["CANTIDAD_TOMADA"] <> $vac_periodo_tercios_c["TERCERA_PARTE"]){
                            $respuesta->alert("J3. Tercios usados para vacaciones del Periodo. Depto. Personal");
                            $respuesta->redirect("vacaciones_sector.php");
                            mysql_query("ROLLBACK", $conexion);
                            return $respuesta;
                          }
                       }else{



                         if ($vac_periodo_tercios["TERCERA_PARTE"] <> $datos['dias'] and $fecha_regresa > $_SESSION["fecha_tope_vacaciones"]){


                           // $respuesta->alert($j_dias);

                           //$respuesta->alert('010 '.$vac_periodo_tercios["TERCERA_PARTE"].'<>'.$datos['dias'].' and '.$fecha_regresa.' > '.$_SESSION["fecha_tope_vacaciones"]);



                         if ($j_dias <= $_SESSION["fecha_tope_vacaciones"] and $fecha_regresa > $_SESSION["fecha_tope_vacaciones"]){
                           $respuesta->alert("J6. No puede ingresar vacaciones pasada la fecha ".$_SESSION["fecha_tope_vacaciones"].". Depto. Personal");
                                $respuesta->redirect("vacaciones_sector.php");
                                mysql_query("ROLLBACK", $conexion);
                                return $respuesta;
                         }








                              //si los dias que me restan tomar de vacaciones es menor al tercio, lo dejo pasar, sino muestro error
                            if ($x > $vac_periodo_tercios["TERCERA_PARTE"] and $vac_periodo_tercios["TERCERA_PARTE"] > 0){
                           $respuesta->alert("J4. Verifique la fecha de salida y la cantidad ingresada. Depto. Personal");
                            $respuesta->redirect("vacaciones_sector.php");
                            mysql_query("ROLLBACK", $conexion);
                            return $respuesta;
                            }else{
                              if ($x <> $datos["dias"]){

                              //$respuesta->alert($x.' - '.$datos["dias"]);
                                $respuesta->alert("J5. Debe ingresar el total de vacaciones pasada la fecha ".$_SESSION["fecha_tope_vacaciones"].". Depto. Personal");
                                $respuesta->redirect("vacaciones_sector.php");
                                mysql_query("ROLLBACK", $conexion);
                                return $respuesta;
                              }
                            }
                         }
                       }

                    }
               }//fin del 2024-04-30

                      //ACA CONTROLA EL AÑO 2023
               if ($fecha_regresa > $_SESSION["fecha_tope_vacaciones"]){
                //verifica la fecha y si corresponde tomarse vacaciones con el tercio, no lo controlo
                    $sql_vt="SELECT SUM(CANTIDAD_TOMADA) AS CANTIDAD_TOMADA, LEGAJOS, PERIODO, TERCERA_PARTE FROM rh_vacaciones_tercios WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$_SESSION["periodo_actual"]."' GROUP BY LEGAJOS, PERIODO";
                    $consulta_vt=mysql_query($sql_vt,$conexion);
                    $vac_periodo_tercios= mysql_fetch_array($consulta_vt);



                    if (!$vac_periodo_tercios){
                             $respuesta->alert("P2. No es posible asignar vacaciones del Periodo. Depto. Personal");
                             $respuesta->redirect("vacaciones_sector.php");
                             mysql_query("ROLLBACK", $conexion);
                             return $respuesta;
                    }else{

                              $cant_parte = $vac_periodo_tercios["TERCERA_PARTE"];
                              $tercio_parte = $datos["dias"];

                       if ($vac_periodo_tercios["CANTIDAD_TOMADA"] == $vac_periodo_tercios["TERCERA_PARTE"] and $vac_periodo_tercios["TERCERA_PARTE"] > 0){
                         $respuesta->alert("P3. Tercios usados para vacaciones del Periodo. Depto. Personal");
                         $respuesta->redirect("vacaciones_sector.php");
                         mysql_query("ROLLBACK", $conexion);
                         return $respuesta;
                       }else{



                         if ($vac_periodo_tercios["TERCERA_PARTE"] <> $datos['dias'] and $fecha_regresa > $_SESSION["fecha_tope_vacaciones"]){


                           // $respuesta->alert($j_dias);

                           // $respuesta->alert('011 '.$vac_periodo_tercios["TERCERA_PARTE"].'<>'.$datos['dias'].' and '.$fecha_regresa.' > '.$_SESSION["fecha_tope_vacaciones"]);



                         if ($j_dias <= $_SESSION["fecha_tope_vacaciones"] and $fecha_regresa > $_SESSION["fecha_tope_vacaciones"]){
                           $respuesta->alert("P6. No puede ingresar vacaciones pasada la fecha ".$_SESSION["fecha_tope_vacaciones"].". Depto. Personal");
                                $respuesta->redirect("vacaciones_sector.php");
                                mysql_query("ROLLBACK", $conexion);
                                return $respuesta;
                         }








                              //si los dias que me restan tomar de vacaciones es menor al tercio, lo dejo pasar, sino muestro error
                            if ($x > $vac_periodo_tercios["TERCERA_PARTE"] and $vac_periodo_tercios["TERCERA_PARTE"] > 0){
                           $respuesta->alert("P4. Verifique la fecha de salida y la cantidad ingresada. Depto. Personal");
                            $respuesta->redirect("vacaciones_sector.php");
                            mysql_query("ROLLBACK", $conexion);
                            return $respuesta;
                            }else{
                              if ($x <> $datos["dias"]){

                              //$respuesta->alert($x.' - '.$datos["dias"]);
                                $respuesta->alert("P5. Debe ingresar el total de vacaciones pasada la fecha ".$_SESSION["fecha_tope_vacaciones"].". Depto. Personal");
                                $respuesta->redirect("vacaciones_sector.php");
                                mysql_query("ROLLBACK", $conexion);
                                return $respuesta;
                              }
                            }
                         }
                       }

                    }
               }//fin del 2024-04-30 AÑO 2023



       //busco si esta bloquedao o con fin de contrato
           $sql_b="SELECT FECHACONT, FECHABLOQUEO FROM rh_maestro WHERE legajos = '".$datos["legajo"]."'  AND '".$fecha_regresa."' >= FECHABLOQUEO AND
(FECHABLOQUEO <> '0000-00-00' AND  `FECHABLOQUEO` IS NOT NULL)
                   UNION
SELECT FECHACONT, FECHABLOQUEO  FROM rh_maestro WHERE legajos = '".$datos["legajo"]."' AND '".$fecha_regresa."' > FECHACONT AND
(FECHACONT <> '0000-00-00' AND  `FECHACONT` IS NOT NULL)";
           $consulta_b=mysql_query($sql_b,$conexion);
           $bloqueo= mysql_fetch_array($consulta_b);




           if ($bloqueo){
             $no_pasa_5 = 1;
           }

            /*control que no permita eliminar vacaciones cuando está justificado ese día y pasado a liquidar*/

                 $sql_liq_aus="SELECT FEC_AUS, DATE_FORMAT(FEC_AUS,'%d/%m/%Y') AS FECHA_AUSENTE FROM $mes_jus WHERE LEGAJOS = '".$legajo."' AND FEC_AUS  between '".$fecha_salida."' and '".$fecha_regresa."' and PAGO = '2'";
                 $consulta_liq_aus=mysql_query($sql_liq_aus,$conexion);
                 while($liq_aus= mysql_fetch_array($consulta_liq_aus)){

                   $fechas_localizadas = $fechas_localizadas.','.$liq_aus["FECHA_AUSENTE"];
                   $no_pasa_5 = 1;
                 }



           //fin de control



       if ($no_pasa_5 <> 1){

              //$respuesta->alert("alta ".$_SESSION["var_tercera_parte_vacaciones"]);

              $var_tercera_parte_tomada = $_SESSION["var_tercera_parte_vacaciones"];

        //LOS TERCIOS
        if ($var_tercera_parte_tomada == 'S' ){
         $var_tercera_parte_tomada = 'S';
                        //GRABO EN TABLA DE VACACIONES TERCIOS
                        if ($erNum == -1){



                       if ($j_dias <= $_SESSION["fecha_tope_vacaciones"] and $_SESSION["periodo_anterior"] == $_SESSION["periodo_anterior"]){

                        $sql_vt = "INSERT INTO rh_vacaciones_tercios (
                                       LEGAJOS,
                                       PERIODO,
                                       TERCERA_PARTE,
                                       CANTIDAD_TOMADA,
                                       FECHA_PROCESO,
                                       HORA_PROCESO,
                                       USUARIO,
                                       FECHA_SALIDA,
                                       FECHA_REGRESA,
                                       PROCESO
                                       )
                                      VALUES (
                                       '".$datos["legajo"]."',
                                       '".$vac_periodo['VACACIONES_PERIODO']."',
                                       '".$cant_parte."',
                                       '".$tercio_parte."',
                                       '".date("Y-m-d")."',
                                       '".date("H:i:s")."',
                                       '".$_SESSION["us2"]."',
                                       '".$j_dias."',
                                       '".$fecha_regresa."',
                                       'INSERT'
                                       )"; //GRABA LOS REGISTROS
                       }


                       if ($_SESSION["var_tercera_parte_vacaciones"] == 'S'){



                        $sql_vt = "INSERT INTO rh_vacaciones_tercios (
                                       LEGAJOS,
                                       PERIODO,
                                       TERCERA_PARTE,
                                       CANTIDAD_TOMADA,
                                       FECHA_PROCESO,
                                       HORA_PROCESO,
                                       USUARIO,
                                       FECHA_SALIDA,
                                       FECHA_REGRESA,
                                       PROCESO
                                       )
                                      VALUES (
                                       '".$datos["legajo"]."',
                                       '".$vac_periodo['VACACIONES_PERIODO']."',
                                       '".$cant_parte."',
                                       '".$tercio_parte."',
                                       '".date("Y-m-d")."',
                                       '".date("H:i:s")."',
                                       '".$_SESSION["us2"]."',
                                       '".$j_dias."',
                                       '".$fecha_regresa."',
                                       'INSERT'
                                       )"; //GRABA LOS REGISTROS

                                     // $respuesta->alert($sql_vt);

                       }
                $alta_vt = mysql_query($sql_vt); //COMPLETA EL TRAMITE
                       }
                        if(mysql_errno($conexion) != 0){
                        $erNum = 11000;
                        }
        }else{
         $var_tercera_parte_tomada = 'N';
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


         //control que no grabe fechas por error, que ya estan
        $sql_fecha = "select LEGAJOS from rh_vacaciones_tomadas where TOMADAS_SALIDA = '".$j_dias."' and TOMADAS_REGRESO = '".$fecha_regresa."' and LEGAJOS = '".$legajo."'";
        $consulta_fecha=mysql_query($sql_fecha,$conexion);
        $vacas_control= mysql_fetch_array($consulta_fecha);
        if ($vacas_control){
          $erNum = 15632;
        }

        if ($erNum == -1){
        $sql_c = "INSERT INTO rh_vacaciones_tomadas (
                                       LEGAJOS,
                                       PERIODO,
                                       TOMADAS_CANTIDAD,
                                       TOMADAS_SALIDA,
                                       TOMADAS_REGRESO,
                                       AUTORIZA,
                                       AUTORIZA_RRHH,
                                       AUTORIZA_USUARIO,
                                       AUTORIZA_FECHA,
                                       AUTORIZA_HORA,
                                       AUTORIZA_MAQUINA,
                                       NIVEL
                                       )
                                      VALUES (
                                       '".$datos["legajo"]."',
                                       '".$vac_periodo['VACACIONES_PERIODO']."',
                                       '".$diferencia."',
                                       '".$j_dias."',
                                       '".$fecha_regresa."',
                                       '".$var_valida."',
                                       '".$var_valida_rrhh."',
                                       '".$audi_user."',
                                       '".$audi_fecha."',
                                       '".$audi_hora."',
                                       '".$maquina."',
                                       '".$_SESSION["acceso_nivel"]."'
                                       )"; //GRABA LOS REGISTROS



                $alta_c = mysql_query($sql_c); //COMPLETA EL TRAMITE
        }
                        if(mysql_errno($conexion) != 0){
                        $erNum = 11001;
                        }

                if ($_SESSION["nivel_usuario"] == '3' or $_SESSION["nivel_usuario"] == '4' or $_SESSION["nivel_usuario"] == '5'){
                //verifica los ausentes en el codigo de ale
                $fecha_ingresada = $j_dias;
                $fecha_fin = $fecha_regresa;



                if ($fecha_ingresada < date('Y-m-d')){



                $i = 0;


               $var_diferencia = $diferencia - 1;

               if ($fecha_ingresada <> $fecha_fin){
                      //recorre los dias en que comienza y termina el control de ausente
                   if ($fecha_ingresada <> ''){
                      for ($i;$i<=$var_diferencia;$i++){
                           $fecha_final = DiasFecha($fecha_ingresada,$i,"sumar");
                           if ($fecha_ingresada <= $fecha_fin){
                            $numero_parte = explode("-",$fecha_final);
                            $fecha_final=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                              $fecha_regresa = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];


                               $mes_aus = "rh_aus_20".substr($numero_parte[0],2,2);

                               //busco los dias posibles ausentes
                               if ($var_valida == 'S' and $var_valida_rrhh == 'S'){

                                   //ausentismo
                                   $sql_aus="SELECT * FROM ".$mes_aus." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                   $consulta_aus=mysql_query($sql_aus,$conexion);
                                   $aus= mysql_fetch_array($consulta_aus);



                                   if ($aus){

                                   //BORRO EL AUSENTISMO, NO LA JUSTIFICACION
                                    if ($erNum == -1){
                                    $sql_jus="DELETE FROM ".$mes_aus." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                    $consulta_c=mysql_query($sql_jus,$conexion);
                                      if(mysql_errno($conexion) != 0){
                                      $erNum = 12001;
                                      }
                                    }

                                               if ($erNum == -1){
                                               $sql_aud = "INSERT INTO auditoria_ausentismo (
                                                           auditoria_operacion,
                                                           auditoria_legajo,
                                                           auditoria_fec_aus,
                                                           auditoria_aplicacion,
                                                           username,
                                                           auditoria_ip,
                                                           auditoria_maquina,
                                                           auditoria_fecha,
                                                           auditoria_hora
                                                           )
                                                          VALUES (
                                                           'BORRA',
                                                           '".$datos["legajo"]."',
                                                           '".$fecha_regresa."',
                                                           'VACACIONES_ALTA_0',
                                                           '".$audi_user."',
                                                           '".$ip."',
                                                           '".$maquina."',
                                                           '".$audi_fecha."',
                                                           '".$audi_hora."'
                                                           )"; // NO PUDO
                                                          $alta_audi = mysql_query($sql_aud);
                                                          if(mysql_errno($conexion) != 0){
                                                            $erNum = 12002;
                                                          }
                                               }


                               }//fin de ausentismo

                               //llegada tarde

                               //busco los dias posibles ausentes
                                 $ll_tarde = "rh_l_t_20".substr($numero_parte[0],2,2);


                                 $sql_lt="SELECT * FROM ".$ll_tarde." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                 $consulta_lt=mysql_query($sql_lt,$conexion);
                                 $llt= mysql_fetch_array($consulta_lt);

                                 if ($llt){

                                     //BORRO EL AUSENTISMO, NO LA JUSTIFICACION
                                      if ($erNum == -1){
                                      $sql_llt="DELETE FROM ".$ll_tarde." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                      $consulta_llt=mysql_query($sql_llt,$conexion);

                                                          if(mysql_errno($conexion) != 0){
                                                            $erNum = 15000;
                                                          }
                                      }
                                                 if ($erNum == -1){
                                                 $sql_aud = "INSERT INTO auditoria_ausentismo (
                                                             auditoria_operacion,
                                                             auditoria_legajo,
                                                             auditoria_fec_aus,
                                                             auditoria_aplicacion,
                                                             username,
                                                             auditoria_ip,
                                                             auditoria_maquina,
                                                             auditoria_fecha,
                                                             auditoria_hora
                                                             )
                                                             VALUES (
                                                             'BORRA',
                                                             '".$datos["legajo"]."',
                                                             '".$fecha_regresa."',
                                                             'VACACIONES_ALTA_0',
                                                             '".$audi_user."',
                                                             '".$ip."',
                                                             '".$maquina."',
                                                             '".$audi_fecha."',
                                                             '".$audi_hora."'
                                                             )"; // NO PUDO
                                                            $alta_audi = mysql_query($sql_aud);

                                                            if(mysql_errno($conexion) != 0){
                                                            $erNum = 15001;
                                                            }
                                                   }




                                 }

                               //fin llegada tarde


                            } //fin control de borrado de ausentimos cuando las autorizaciones son S ambas
                           }
                      }
                   }
               }else{
                       if ($fecha_ingresada <> ''){
                            $numero_parte = explode("-",$fecha_fin);
                            $fecha_fin=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                             $legajo = $datos["legajo"];
                              $leg = $datos["legajo"];
                              $fecha_a_procesar = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];
                             $fecha_regresa = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];


                               $mes_aus = "rh_aus_20".substr($numero_parte[0],2,2);

                               //busco los dias posibles ausentes
                               if ($var_valida == 'S' and $var_valida_rrhh == 'S'){

                                   $sql_aus="SELECT * FROM ".$mes_aus." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                   $consulta_aus=mysql_query($sql_aus,$conexion);
                                   $aus= mysql_fetch_array($consulta_aus);


                                   //ausentismo
                                   if ($aus){

                                   //BORRO EL AUSENTISMO, NO LA JUSTIFICACION
                                    if ($erNum == -1){
                                    $sql_jus="DELETE FROM ".$mes_aus." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                    $consulta_c=mysql_query($sql_jus,$conexion);
                                        if(mysql_errno($conexion) != 0){
                                        $erNum = 12003;
                                        }
                                    }

                                               if ($erNum == -1){
                                               $sql_aud = "INSERT INTO auditoria_ausentismo (
                                                           auditoria_operacion,
                                                           auditoria_legajo,
                                                           auditoria_fec_aus,
                                                           auditoria_aplicacion,
                                                           username,
                                                           auditoria_ip,
                                                           auditoria_maquina,
                                                           auditoria_fecha,
                                                           auditoria_hora
                                                           )
                                                          VALUES (
                                                           'BORRA',
                                                           '".$datos["legajo"]."',
                                                           '".$fecha_regresa."',
                                                           'VACACIONES_ALTA_0',
                                                           '".$audi_user."',
                                                           '".$ip."',
                                                           '".$maquina."',
                                                           '".$audi_fecha."',
                                                           '".$audi_hora."'
                                                           )"; // NO PUDO
                                                          $alta_audi = mysql_query($sql_aud);

                                                          if(mysql_errno($conexion) != 0){
                                                            $erNum = 12004;
                                                          }
                                               }


                               }//fin de ausentismo

                                          //llegada tarde

                               //busco los dias posibles ausentes
                                 $ll_tarde = "rh_l_t_20".substr($numero_parte[0],2,2);


                                 $sql_lt="SELECT * FROM ".$ll_tarde." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                 $consulta_lt=mysql_query($sql_lt,$conexion);
                                 $llt= mysql_fetch_array($consulta_lt);

                                 if ($llt){

                                     //BORRO EL AUSENTISMO, NO LA JUSTIFICACION
                                      if ($erNum == -1){
                                      $sql_llt="DELETE FROM ".$ll_tarde." WHERE LEGAJOS = '".$datos["legajo"]."' AND FEC_AUS = '".$fecha_regresa."'";
                                      $consulta_llt=mysql_query($sql_llt,$conexion);

                                                          if(mysql_errno($conexion) != 0){
                                                            $erNum = 15002;
                                                          }
                                      }
                                                 if ($erNum == -1){
                                                 $sql_aud = "INSERT INTO auditoria_ausentismo (
                                                             auditoria_operacion,
                                                             auditoria_legajo,
                                                             auditoria_fec_aus,
                                                             auditoria_aplicacion,
                                                             username,
                                                             auditoria_ip,
                                                             auditoria_maquina,
                                                             auditoria_fecha,
                                                             auditoria_hora
                                                             )
                                                             VALUES (
                                                             'BORRA',
                                                             '".$datos["legajo"]."',
                                                             '".$fecha_regresa."',
                                                             'VACACIONES_ALTA_0',
                                                             '".$audi_user."',
                                                             '".$ip."',
                                                             '".$maquina."',
                                                             '".$audi_fecha."',
                                                             '".$audi_hora."'
                                                             )"; // NO PUDO
                                                            $alta_audi = mysql_query($sql_aud);

                                                            if(mysql_errno($conexion) != 0){
                                                            $erNum = 15003;
                                                            }
                                                   }




                                 }

                               //fin llegada tarde
                            }//fin control de borrado de ausentimos cuando las autorizaciones son S ambas
                       }
               }
               }
               }
               //fin del control de ausentes




                 //saco la cantidad total de vacaciones tomadas
                 $sql_va="SELECT sum( TOMADAS_CANTIDAD ) AS total_vac FROM rh_vacaciones_tomadas WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$vac_periodo['VACACIONES_PERIODO']."'";
                 $consulta_va=mysql_query($sql_va,$conexion);
                 $vac_cant= mysql_fetch_array($consulta_va);
                 $total_cantidad = $vac_cant["total_vac"];
    	         //fin vacaciones tomadas


                if ($erNum == -1){
                $sql="UPDATE rh_vacaciones SET  VACACIONES_TOMADAS = '".$total_cantidad."' WHERE LEGAJOS LIKE '".$datos["legajo"]."' and VACACIONES_PERIODO LIKE '".$vac_periodo['VACACIONES_PERIODO']."'";
                mysql_query($sql,$conexion);
                }
                        if(mysql_errno($conexion) != 0){
                        $erNum = 11002;
                        }



                 //24-02-2021 - controla para la liquidacion
                 if ($erNum == -1){
                  $sql_liq_aus_del="DELETE FROM rh_liq_aus_reintegro WHERE LEGAJO = '".$datos["legajo"]."' AND FECHA_AUS BETWEEN '".$j_dias."' and '".$fecha_regresa."' and PAGO = '1'";
                  $del_liq_del=mysql_query($sql_liq_aus_del,$conexion);
                  }
                        if(mysql_errno($conexion) != 0){
                        $erNum = 11003;
                        }



                 $sql_liq_aus="SELECT * FROM rh_liq_aus_sin_jus WHERE LEGAJO = '".$datos["legajo"]."' AND FECHA BETWEEN '".$j_dias."' and '".$fecha_regresa."'";
                 $consulta_liq_aus=mysql_query($sql_liq_aus,$conexion);
                 while($liq_aus= mysql_fetch_array($consulta_liq_aus)){

                                   if ($erNum == -1){
                                   $sql_aud = "INSERT INTO rh_liq_aus_reintegro (
                                               LEGAJO,
                                               FECHA_AUS,
                                               HORAS,
                                               CENTRO,
                                               PAGO,
                                               OBSERVACIONES,
                                               LIQ_MM_AAAA,
                                               AUDITORIA_USUARIO,
                                               AUDITORIA_PROGRAMA,
                                               AUDITORIA_FECHA,
                                               AUDITORIA_HORA
                                               )
                                              VALUES (
                                               '".$liq_aus["LEGAJO"]."',
                                               '".$liq_aus["FECHA"]."',
                                               '".$liq_aus["HORAS"]."',
                                               '".$liq_aus["CENTRO"]."',
                                               '1',
                                               '".$liq_aus["OBSERVACIONES"]."',
                                               '".$liq_aus["LIQ_MM_AAAA"]."',
                                               '".$_SESSION["us2"]."',
                                               'VACACIONES_ALTA_0',
                                               '".$audi_fecha."',
                                               '".$audi_hora."'
                                               )"; // NO PUDO

                                              $alta_audi = mysql_query($sql_aud);
                                   }
                                              if(mysql_errno($conexion) != 0){
                                              $erNum = 11004;
                                              }

                   }


                //fin control para liquidacion
       ///////////////////////////////////////////
       ///////////////////////////////////////////

       $x = 0;

             }
       }else{
       //si los dias a tomar es menor a los dias del periodo
       //resto los dias a tomar - los dias del periodo
       $resto_cant = $x - $dias_cant;
       //asigno el resto a la var $x para que siga haciendo el control del bucle
       $x = $resto_cant;
       //seteo la variable para que me tome el proximo valor

       $fecha_salida = $j_dias;
       //le resto un dia para que comience del dia a tomar
       $n_toma_1 = $dias_cant - 1;
       //sumo cantidad de dias
       $inicio=strtotime($fecha_salida);
       $dias=($n_toma_1*86400);
       $fecha_regresa = date("Y-m-d",$inicio+$dias);
       //fin suma

       $dias_cant = 0;
       //lo asigno a la var para sumarle 1 dia
       $j_dias = $fecha_regresa;


                 //las sesiones vienen de la function ValidaLegajo
                   if ($_SESSION["centro_costo_personal"] == 'AAAA' and $_SESSION["acceso_usuario_cc"] == $datos["nro_costo"] and $_SESSION["acceso_vacaciones"] == '1'){
                     $var_valida_rrhh = 'S';
                     $var_valida = 'S';
                  }else{
                     $var_valida_rrhh = 'N';
                     $var_valida = 'N';
                  }

                  if ($_SESSION["nivel_usuario"] == '3' or $_SESSION["nivel_usuario"] == '4' or $_SESSION["nivel_usuario"] == '5'){
                     $var_valida_rrhh = 'S';
                     $var_valida = 'S';
                  }




        //busco si esta bloquedao o con fin de contrato
           $sql_b="SELECT FECHACONT, FECHABLOQUEO FROM rh_maestro WHERE legajos = '".$datos["legajo"]."'  AND '".$fecha_regresa."' >= FECHABLOQUEO AND
(FECHABLOQUEO <> '0000-00-00' AND  `FECHABLOQUEO` IS NOT NULL)
                   UNION
SELECT FECHACONT, FECHABLOQUEO  FROM rh_maestro WHERE legajos = '".$datos["legajo"]."' AND '".$fecha_regresa."' > FECHACONT AND
(FECHACONT <> '0000-00-00' AND  `FECHACONT` IS NOT NULL)";
           $consulta_b=mysql_query($sql_b,$conexion);
           $bloqueo= mysql_fetch_array($consulta_b);




           if ($bloqueo){
             $no_pasa_5 = 1;
           }

            /*control que no permita eliminar vacaciones cuando está justificado ese día y pasado a liquidar*/

                 $sql_liq_aus="SELECT FEC_AUS, DATE_FORMAT(FEC_AUS,'%d/%m/%Y') AS FECHA_AUSENTE FROM $mes_jus WHERE LEGAJOS = '".$legajo."' AND FEC_AUS  between '".$fecha_salida."' and '".$fecha_regresa."' and PAGO = '2'";
                 $consulta_liq_aus=mysql_query($sql_liq_aus,$conexion);
                 while($liq_aus= mysql_fetch_array($consulta_liq_aus)){


                   $fechas_localizadas =$fechas_localizadas.','.$liq_aus["FECHA_AUSENTE"];
                   $no_pasa_5 = 1;
                 }



           //fin de control

        if ($no_pasa_5 <> 1){

         //control que no grabe fechas por error, que ya estan
        $sql_fecha = "select LEGAJOS from rh_vacaciones_tomadas where TOMADAS_SALIDA = '".$fecha_salida."' and TOMADAS_REGRESO = '".$fecha_regresa."' and LEGAJOS = '".$datos["legajo"]."'";
        $consulta_fecha=mysql_query($sql_fecha,$conexion);
        $vacas_control= mysql_fetch_array($consulta_fecha);
        if ($vacas_control){
          $erNum = 15633;
        }


       //EN ESTA LINEA DEBO HACER LA CARGA DEL DIA, inicio, fin y cant dias
       //$respuesta->alert($fecha_salida.'-'.$fecha_regresa.' cant: '.$vac_periodo["resta_vac"]);
        ///////////////////////////////////////////
       ///////////////////////////////////////////
       if ($erNum == -1){
        $sql_c = "INSERT INTO rh_vacaciones_tomadas (
                                       LEGAJOS,
                                       PERIODO,
                                       TOMADAS_CANTIDAD,
                                       TOMADAS_SALIDA,
                                       TOMADAS_REGRESO,
                                       AUTORIZA,
                                       AUTORIZA_RRHH,
                                       AUTORIZA_USUARIO,
                                       AUTORIZA_FECHA,
                                       AUTORIZA_HORA,
                                       AUTORIZA_MAQUINA,
                                       NIVEL
                                       )
                                      VALUES (
                                       '".$datos["legajo"]."',
                                       '".$vac_periodo['VACACIONES_PERIODO']."',
                                       '".$vac_periodo["resta_vac"]."',
                                       '".$fecha_salida."',
                                       '".$fecha_regresa."',
                                       '".$var_valida."',
                                       '".$var_valida_rrhh."',
                                       '".$audi_user."',
                                       '".$audi_fecha."',
                                       '".$audi_hora."',
                                       '".$maquina."',
                                        '".$_SESSION["acceso_nivel"]."'
                                       )"; // NO PUDO
                $alta_c = mysql_query($sql_c); //COMPLETA EL TRAMITE
         }
                        if(mysql_errno($conexion) != 0){
                        $erNum = 11005;
                        }

                 //saco la cantidad total de vacaciones tomadas
                 $sql_va="SELECT sum( TOMADAS_CANTIDAD ) AS total_vac FROM rh_vacaciones_tomadas WHERE LEGAJOS = '".$datos["legajo"]."' AND PERIODO = '".$vac_periodo['VACACIONES_PERIODO']."'";
                 $consulta_va=mysql_query($sql_va,$conexion);
                 $vac_cant= mysql_fetch_array($consulta_va);
                 $total_cantidad = $vac_cant["total_vac"];
    	         //fin vacaciones tomadas

                if ($erNum == -1){
                $sql="UPDATE rh_vacaciones SET  VACACIONES_TOMADAS = '".$total_cantidad."' WHERE LEGAJOS LIKE '".$datos["legajo"]."' and VACACIONES_PERIODO LIKE '".$vac_periodo['VACACIONES_PERIODO']."'";

                 mysql_query($sql,$conexion);
                }

                 if(mysql_errno($conexion) != 0){
                        $erNum = 11006;
                        }
               /*  if ($_SESSION["nivel_usuario"] == '3' or $_SESSION["nivel_usuario"] == '4'){
                //verifica los ausentes en el codigo de ale
                $fecha_ingresada = $j_dias;
                $fecha_fin = $fecha_regresa;



                if ($fecha_ingresada < date('Y-m-d')){



                $i = 0;


               $var_diferencia = $vac_periodo["resta_vac"] ;
               $fecha_ingresada = $fecha_salida;
               $fecha_fin = $fecha_regresa;
               if ($fecha_ingresada <> $fecha_fin){
                      //recorre los dias en que comienza y termina el control de ausente
                   if ($fecha_ingresada <> ''){
                      for ($i;$i<=$var_diferencia;$i++){
                           $fecha_final = DiasFecha($fecha_ingresada,$i,"sumar");
                           if ($fecha_ingresada <= $fecha_fin){
                            $numero_parte = explode("-",$fecha_final);
                            $fecha_final=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                              $legajo = $datos["legajo"];
                                $leg = $datos["legajo"];
                              $fecha_a_procesar = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];

                              include("justifica_calcula_ausente.php");
                           }
                      }
                   }
               }else{
                       if ($fecha_ingresada <> ''){
                            $numero_parte = explode("-",$fecha_fin);
                            $fecha_fin=$numero_parte[2]."-".$numero_parte[1]."-".$numero_parte[0];

                              $legajo =$datos["legajo"];
                                $leg = $datos["legajo"];
                              $fecha_a_procesar = $numero_parte[0]."-".$numero_parte[1]."-".$numero_parte[2];

                              include("justifica_calcula_ausente.php");
                       }
               }
               }
               }
               //fin del control de ausentes
                */



                 //24-02-2021 - controla para la liquidacion
                 if ($erNum == -1){
                  $sql_liq_aus_del="DELETE FROM rh_liq_aus_reintegro WHERE LEGAJO = '".$datos["legajo"]."' AND FECHA_AUS BETWEEN '".$fecha_salida."' and '".$fecha_regresa."' and PAGO = '1'";
                  $del_liq_del=mysql_query($sql_liq_aus_del,$conexion);
                 }
                 if(mysql_errno($conexion) != 0){
                         $erNum = 11010;
                 }

                 $sql_liq_aus="SELECT * FROM rh_liq_aus_sin_jus WHERE LEGAJO = '".$datos["legajo"]."' AND FECHA BETWEEN '".$fecha_salida."' and '".$fecha_regresa."'";
                 $consulta_liq_aus=mysql_query($sql_liq_aus,$conexion);
                 while($liq_aus= mysql_fetch_array($consulta_liq_aus)){


                                   if ($erNum == -1){
                                   $sql_aud = "INSERT INTO rh_liq_aus_reintegro (
                                               LEGAJO,
                                               FECHA_AUS,
                                               HORAS,
                                               CENTRO,
                                               PAGO,
                                               OBSERVACIONES,
                                               LIQ_MM_AAAA,
                                               AUDITORIA_USUARIO,
                                               AUDITORIA_PROGRAMA,
                                               AUDITORIA_FECHA,
                                               AUDITORIA_HORA
                                               )
                                              VALUES (
                                               '".$liq_aus["LEGAJO"]."',
                                               '".$liq_aus["FECHA"]."',
                                               '".$liq_aus["HORAS"]."',
                                               '".$liq_aus["CENTRO"]."',
                                               '1',
                                               '".$liq_aus["OBSERVACIONES"]."',
                                               '".$liq_aus["LIQ_MM_AAAA"]."',
                                               '".$_SESSION["us2"]."',
                                               'VACACIONES_ALTA_0',
                                               '".$audi_fecha."',
                                               '".$audi_hora."'
                                               )"; // NO PUDO

                                              $alta_audi = mysql_query($sql_aud);
                                    }
                                              if(mysql_errno($conexion) != 0){
                                                  $erNum = 11007;
                                              }

                   }


                //fin control para liquidacion

        ///////////////////////////////////////////
       ///////////////////////////////////////////
       ///////////////////////////////////////////


        //le sumo un dia para que comience del dia a tomar
        $fecha_salida = $fecha_regresa;
        $n_toma_1 = $dias_cant + 1;
        //sumo cantidad de dias
        $inicio=strtotime($fecha_salida);
        $dias=($n_toma_1*86400);
        $j_dias = date("Y-m-d",$inicio+$dias);
        //fin suma

        }
       }
   }
   }

  if ($no_pasa_5 <> 1){
    if($erNum == -1){
                  mysql_query("COMMIT", $conexion);


      $respuesta->alert('Datos guardados correctamente');
      $respuesta->call("xajax_ValidaLegajo(xajax.getFormValues('formu3'))");

    }else{
       mysql_query("ROLLBACK", $conexion);

       if ($erNum <> '15632' and $erNum <> '15633'){
        $respuesta->alert('Error '.$erNum);
        $respuesta->call("xajax_ValidaLegajo(xajax.getFormValues('formu3'))");
       }

    }
  }else{
     mysql_query("ROLLBACK", $conexion);
   $respuesta->alert('No es posible asignar Vacaciones: Fecha fin de contrato / Fecha bloqueda /'."\n".'Liquidacion realizada'.$fechas_localizadas);
  }

}


 return $respuesta;
}



$xajax=new xajax();


$xajax->register(XAJAX_FUNCTION, "ValidaLegajo");
$xajax->register(XAJAX_FUNCTION, "MostrarDatos");
$xajax->register(XAJAX_FUNCTION, "MostrarVacaciones");
$xajax->register(XAJAX_FUNCTION, "GrabarVacaciones");
$xajax->register(XAJAX_FUNCTION, "DiasTomados");
$xajax->register(XAJAX_FUNCTION, "VerFecha");
$xajax->register(XAJAX_FUNCTION, "VerBoton");
$xajax->register(XAJAX_FUNCTION, "BajaVacaciones");
$xajax->register(XAJAX_FUNCTION, "ValidaFechaSalidaPersonal");
$xajax->register(XAJAX_FUNCTION, "RutinaGeneraVacaciones");
$xajax->register(XAJAX_FUNCTION, "RutinaGeneraTercios");
$xajax->register(XAJAX_FUNCTION, "RutinaGeneraTerciosTodos");

$xajax->register(XAJAX_FUNCTION, "obtenerNivelImputacion");
$xajax->register(XAJAX_FUNCTION, "calcularPeriodoUso");






$xajax->processRequest();
$xajax->configure('javascript URI','../../includes/xajax/');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php $xajax->printJavascript( "../../includes/xajax/" ) ?>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" href="estilo.css" type="text/css">
<link rel="stylesheet" href="../../includes/jquery/css/smoothness/jquery-ui-1.8.11.custom.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript" src = "valida_datos.js"></script>
<SCRIPT LANGUAGE="JavaScript" src = "ajax_consulta.js"></script>
<script LANGUAGE="JavaScript1.1">

<!-- Original:  Martin Webb (martin@irt.org) -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function right(e) {
if (navigator.appName == 'Netscape' &&
(e.which == 3 || e.which == 2))
return false;
else if (navigator.appName == 'Microsoft Internet Explorer' &&
(event.button == 2 || event.button == 3)) {
alert("Lo siento, no tienes permiso para pulsar con el botón derecho.");
return false;
}
return true;
}

document.onmousedown=right;
document.onmouseup=right;
if (document.layers) window.captureEvents(Event.MOUSEDOWN);
if (document.layers) window.captureEvents(Event.MOUSEUP);
window.onmousedown=right;
window.onmouseup=right;
//  End -->
</script>
<script language="javascript">
var nuevaVentana

function nueva_ventana(url, ancho, alto, barra) {
    izquierda = (screen.width) ? (screen.width-ancho)/2 : 100
    arriba = (screen.height) ? (screen.height-alto)/2 : 100
    opciones = 'toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=' + barra + ',resizable=0,width=' + ancho + ',height=' + alto + ',left=' + izquierda + ',top=' + arriba + ''
    nuevaVentana = window.open(url, 'popUp', opciones)
    nuevaVentana.focus()

}
</script>


<script language="javascript">
function IsNumeric(valor)
{
var log=valor.length; var sw="S";
for (x=0; x<log; x++)
{ v1=valor.substr(x,1);
v2 = parseInt(v1);
//Compruebo si es un valor numérico
if (isNaN(v2)) { sw= "N";}
}
if (sw=="S") {return true;} else {return false; }
}

var primerslap=false;
var segundoslap=false;
function formateafecha(fecha)
{
var long = fecha.length;
var dia;
var mes;
var ano;

if ((long>=2) && (primerslap==false)) { dia=fecha.substr(0,2);
if ((IsNumeric(dia)==true) && (dia<=31) && (dia!="00")) { fecha=fecha.substr(0,2)+"-"+fecha.substr(3,7); primerslap=true; }
else { fecha=""; primerslap=false;}
}
else
{ dia=fecha.substr(0,1);
if (IsNumeric(dia)==false)
{fecha="";}
if ((long<=2) && (primerslap=true)) {fecha=fecha.substr(0,1); primerslap=false; }
}
if ((long>=5) && (segundoslap==false))
{ mes=fecha.substr(3,2);
if ((IsNumeric(mes)==true) &&(mes<=12) && (mes!="00")) { fecha=fecha.substr(0,5)+"-"+fecha.substr(6,4); segundoslap=true; }
else { fecha=fecha.substr(0,3);; segundoslap=false;}
}
else { if ((long<=5) && (segundoslap=true)) { fecha=fecha.substr(0,4); segundoslap=false; } }
if (long>=7)
{ ano=fecha.substr(6,4);
if (IsNumeric(ano)==false) { fecha=fecha.substr(0,6); }
else { if (long==10){ if ((ano==0) || (ano<1900) || (ano>2100)) { fecha=fecha.substr(0,6); } } }
}

if (long>=10)
{
fecha=fecha.substr(0,10);
dia=fecha.substr(0,2);
mes=fecha.substr(3,2);
ano=fecha.substr(6,4);
// Año no viciesto y es febrero y el dia es mayor a 28
if ( (ano%4 != 0) && (mes ==02) && (dia > 28) ) { fecha=fecha.substr(0,2)+"-"; }
}

if (long>=5)
{
dia=fecha.substr(0,2);
mes=fecha.substr(3,2);
// si el mes tiene 30 no permite entrar
if ( (mes ==04 || mes ==06 || mes ==09 || mes ==11) && (dia > 30) ) {
fecha=fecha.substr(0,2)+"-"; }
}
return (fecha);
}



//formatea el campo
function currencyFormat(fld, milSep, decSep, e) {
var sep = 0;
var key = '';
var i = j = 0;
var len = len2 = 0;
var strCheck = '0123456789';
var aux = aux2 = '';
var whichCode = (window.Event) ? e.which : e.keyCode;
if (whichCode == 13) return true;  // Enter
key = String.fromCharCode(whichCode);  // Get key value from key code
if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
len = fld.value.length;
for(i = 0; i < len; i++)
if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
aux = '';
for(; i < len; i++)
if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
aux += key;
len = aux.length;
if (len > 4) return false;  // agregado mio maximo de caracteres
if (len == 0) fld.value = '';
if (len == 1) fld.value = '0'+ decSep + '0' + aux;
if (len == 2) fld.value = '0'+ decSep + aux;
if (len > 2) {
aux2 = '';
for (j = 0, i = len - 3; i >= 0; i--) {
if (j == 3) {
aux2 += milSep;
j = 0;
}
aux2 += aux.charAt(i);
j++;
}
fld.value = '';
len2 = aux2.length;
for (i = len2 - 1; i >= 0; i--)
fld.value += aux2.charAt(i);
fld.value += decSep + aux.substr(len - 2, len);
}
return false;
}
</SCRIPT>




<script language="javascript">
function inicializa(){
document.getElementById('legajo').focus();
document.formu3.elements['btn_grabar'].style.visibility="hidden";
}

function blanco(){
document.formu3.elements['dias_acumulados'].value="";
}


function fecha_sumada(valor_item) {
  num=document.getElementById('tablaFormulario').rows[valor_item].cells[2].childNodes[0].value;

  f=document.getElementById('tablaFormulario').rows[valor_item].cells[3].childNodes[0].value; ;
  // pasaremos la fecha a formato mm/dd/yyyy
  f=f.split('-');
  f[0] = f[0] -1; //le resto uno asi me muestra a partir del dia que se toma las vacaciones
  if (f[0] > 0){
  f=f[1]+'-'+f[0]+'-'+f[2];
  //
  hoy=new Date(f);
  hoy.setTime(hoy.getTime()+num*24*60*60*1000);
  mes=hoy.getMonth()+1;
  if(mes<9) mes='0'+mes;
  fecha=hoy.getDate()+'/'+mes+'/'+hoy.getFullYear();
  document.getElementById('tablaFormulario').rows[valor_item].cells[4].childNodes[0].value = fecha;
  }
}
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
//pase con enter
<!-- Begin
siguienteCampo = "apellido"; // name of first box on page
netscape = "";
ver1 = navigator.appVersion; len = ver1.length;
for(iln = 0; iln < len; iln++) if (ver1.charAt(iln) == "(") break;
netscape = (ver1.charAt(iln+1).toUpperCase() != "C");

function keyDown(DnEvents) { // handles keypress
// determines whether Netscape or Internet Explorer
k = (netscape) ? DnEvents.which : window.event.keyCode;
if (k == 13) { // enter key pressed
if (siguienteCampo == 'done') return true; // submit, we finished all fields
else { // we're not done yet, send focus to next box
eval('document.formu3.' + siguienteCampo + '.focus()');
return false;
      }
   }
}
document.onkeydown = keyDown; // work together to analyze keystrokes
if (netscape) document.captureEvents(Event.KEYDOWN|Event.KEYUP);
//  End -->
</script>


</script>
<script src="../../includes/jquery/js/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="../../includes/jquery/js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>
<script>
	function mensaje() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!

		$( "#dialog:ui-dialog" ).dialog( "destroy" );

		$( "#dialog-message" ).dialog({
			modal: true,
            resizable: false,
            show: 'fade',
            open: function () {
                $('.ui-dialog').css("font-size", "12px");
            },
            width: 500,
            buttons: {
				Aceptar: function() {

					$( this ).dialog( "close" );
				}
			}
		});
	};

    //muestra mensaje con fondo transparente
function mensaje_espere(){
    document.getElementById("Detalle_mensa").style.visibility="visible";
	$(function() {
	   	$( "#dialog-modal" ).dialog({
			height: 60,
            width: 360,
			modal: true,
            title: 'Espere por favor...!'
 	});
	});
}
function cierra_espere(){
	$(function() {
	   	$( "#dialog-modal" ).dialog( "close" );
	});
    document.getElementById("Detalle_mensa").style.visibility="hidden";
}

function mensaje_usu(){
    document.getElementById("Detalle_mensa3").style.visibility="visible";
	$(function() {
	   	$( "#dialog-modal3" ).dialog({
			height: 60,
            width: 450,
			modal: true,
            title: 'Atencion '
 	});
	});
}


$(function() {
	   	$( "#fecha_ant_salida" ).datepicker({
			showOn: "button",
			buttonImage: "../../images/iconPicDate.gif",
            monthNames: ['Enero 1-', 'Febrero 2-', 'Marzo 3-', 'Abril 4-', 'Mayo 5-', 'Junio 6-', 'Julio 7-', 'Agosto 8-', 'Septiembre 9-', 'Octubre 10-', 'Noviembre 11-', 'Diciembre 12-'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            dateFormat: 'dd-mm-yy',
            firstDay: 1,
			buttonImageOnly: true
            });
	});
	</script>
</head>

<body onLoad="inicializa();">

<form name="formu3"   method="post" id="formu3">
  <h3><span >RRHH / Personal / Vacaciones </span></h3>

<div style="text-align: left; width:80%; z-index:1;"> <!--2-->
      <!-- CAMPOS OCULTOS -->
      <input type="hidden" name="oculto_periodo" id="oculto_periodo">
      <input type="hidden" id="num_campos" name="num_campos" value="0" />
      <input type="hidden" id="cant_campos" name="cant_campos" value="0" />
      <input type="hidden" id="num_campos_vac" name="num_campos_vac" value="0" />
      <input type="hidden" id="cant_campos_vac" name="cant_campos_vac" value="0" />
      <input type="hidden" name="oculto_cantidad" id="oculto_cantidad">
      <input type="hidden" name="oculto_fecha_inicio" id="oculto_fecha_inicio">
      <input type="hidden" name="oculto_periodo_vac" id="oculto_periodo_vac">
      <input name="oculto_legajo" type="hidden" id="oculto_legajo">
      <input type="hidden" name="oculto_fecha_ingreso" id="oculto_fecha_ingreso">

      <!-- FIN CAMPOS OCULTOS -->

     <fieldset class="izquierda" style="margin-top:10px;">

      <table width="100%" border="0">
        <tr>
          <td width="40" valign="middle" class="izquierda">Legajo</td>
          <td width="89" valign="middle" class="izquierda"><input name="legajo" type="text" class="derecha" id="legajo"  "<?php print $legajo; ?>" onblur="xajax_obtenerNivelImputacion(xajax.getFormValues('formu3')),xajax_ValidaLegajo(xajax.getFormValues('formu3')); return false"  onFocus="siguienteCampo ='apellido';" onKeyPress="Numeros(event,'noDec')" size="5" maxlength="4">
          <a href="javascript:nueva_ventana('selecciona_legajo_vacaciones.php',570,220,0)"><img src="../../images/16-zoom.png" alt="Buscar Legajo" width="16" height="16" border="0" longdesc="Buscar"></a></td>
          <td width="143" valign="middle"  class="izquierda">Apellido y Nombre</td>
          <td width="220" valign="middle" class="izquierda"><input name="apellido" type="text"  class="izquierda" id="apellido" value="<?php print $apellido; ?>" size="35" maxlength="25" readonly="readonly"></td>
          <td width="83" valign="middle" class="izquierda">C. de Costo</td>
          <td width="303" valign="middle" class="izquierda"><input name="nro_costo" type="text" class="centro" id="nro_costo" onFocus="siguienteCampo ='mensual';" value="<?php print $nro_costo; ?>"  size="5" maxlength="4" readonly="readonly"></td>
          </tr>
        <tr>
          <td id="td_dias" colspan="6" bgcolor="#F0F0E0" class="izquierda"><div  id="campo">

          <input type="hidden" name="lista_sector" id="lista_sector" value="NO">

          <?php
            if ($_SESSION["centro_costo_personal"] == 'AAAA'){
              print 'VER PERIODOS - <select name="lista_sector" size="1" onchange="xajax_ValidaLegajo(xajax.getFormValues(\'formu3\')); ">
                                                  <option value="SI" >POR A&Ntilde;O</option>
                                                  <option value="NO" >TODOS</option>
                                                  </select>
                                                  <select style="visibility: hidden" name="lista_anio" id="lista_anio" size="1" onchange="xajax_MostrarDatos(xajax.getFormValues(\'formu3\')); "></select>
                                                  ';
            }else{
               print 'PERIODOS - <select name="lista_sector" size="1">
                                                   <option value="NO" >TODOS</option>
                                                   </select>';
            }

          ?>

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vacaciones - Fecha de Salida &nbsp;&nbsp; <label><input name="fecha_ant_salida" type="text" class="centro" id="fecha_ant_salida"  onFocus="siguienteCampo ='dias';" onBlur="xajax_VerFecha(xajax.getFormValues('formu3')); " onKeyPress="Numeros(event,'noDec')" onKeyUp="this.value=formateafecha(this.value);" size="10" maxlength="10"></label>
          &nbsp;&nbsp;Toma Dias &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="dias" onfocus="xajax_VerBoton(xajax.getFormValues('formu3')); " type="text"  onKeyPress="Numeros(event,'noDec')" class="centro" id="dias"  size="3" maxlength="2">
          </div>
          </td>
        </tr>


      </table>
     </fieldset>

      <fieldset id="field_dias_acumulados"  style="margin-top:10px">
        <legend>DIAS ACUMULADOS</legend>
          <span class="LetNegro12">Dias a Tomar</span>
          <input name="dias_acumulados" type="text"  id="dias_acumulados"  size="3" maxlength="2" readonly="readonly">
     </fieldset>




    <div  style="width: 100%; margin-top: 1%">

          <fieldset class="izquierda" id="ver_dias_tomar"   style="width: 48%;  height: 350px; float: left ; margin-left:5px; margin-top:5px;">
              <legend class="centro">DIAS A TOMAR</legend>
              <div id="ver_datos" style="width: 100%; height: 300px; text-align: left; overflow: auto">

              </div>
          </fieldset>


          <fieldset class="izquierda" id="titulos_vacaciones" style="width: 45%; height: 350px; float: right; margin-top:5px;">
               <legend class="centro">DIAS TOMADOS</legend>
               <div id="ver_dias_vacaciones"  style="width: 100%; height: 300px; text-align: left; overflow: auto">

               </div>
          </fieldset>
    </div>

    <div align="center" style="margin-top: 2%">
      <input name="btn_grabar" type="button" class="boton" id="btn_grabar" onClick="xajax_ValidaFechaSalidaPersonal(xajax.getFormValues('formu3'));" value="Guardar d&iacute;as de vacaciones" />
    </div>

    <div align="center">
    <label><h3>En fecha: 27/06/2024, bajo Disposici&oacute;n:DI-2024-19494372-GDEBA-GGEAARS. Procedimiento:IF-2024-18340011-GDEBA-GRHEAARS</h3></label>
    </div>

</div>


</form>


</body>
</html>

