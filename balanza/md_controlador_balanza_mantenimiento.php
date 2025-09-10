<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}
if(empty($_SESSION['x0']) || empty($_SESSION['x1']) || empty($_SESSION['x6'])){
    echo 'Debe iniciar sesión...';
    exit;
}
require_once('../bd_conexion.php');
get_pdo();
require_once('md_modelo_balanza_mantenimiento.php');
$modelo=new mp_modelo_balanza_mantenimiento();
require_once('md_vista_balanza_mantenimiento.php');
$vista=new mp_vista_balanza_mantenimiento();


if(isset($_POST['accion'])){
    $accion=$_POST['accion'];
    switch($accion){
        case 'get_balanzas':
            
            $data=$modelo->get_balanzas();
            $vista->get_tabla_balanza_mantenimiento($data);

            // echo "<pre>";
            // print_r($INFO_GENERAL_RESULTADO);
            // echo "</pre>";
            break;
        case 'get_formulario_nuevo':
            $info=$_POST['info'];
            $data=$modelo->get_balanza_id($info);
            $vista->get_formulario_balanza_mantenimiento($data);
            break;
        case 'set_nuevo':
            $parametros=$_POST['parametros'];
            $data=$modelo->set_insert_balanza($parametros);
            break;
        case 'set_eliminar':
            $parametros=$_POST['parametros'];
            $data=$modelo->set_eliminar($parametros);
            break;

        case 'get_formulario_datos_editar':
            $info=$_POST['info'];
            $data=$modelo->get_balanza_id($info);
            $vista->get_formulario_balanza_mantenimiento($data);
            break;
        case 'get_formulario_editar':
            $parametros=$_POST['parametros'];
            $data=$modelo->get_formulario_editar($parametros);
            break;



        case 'get_datos_balanza':
            $parametros=$_POST['parametros'];
            
            $data_balanza=$modelo->get_balanza_id($parametros);
            $data=$modelo->get_dato_balanza($data_balanza);
            break;


        case 'get_balanzas_activas':
            
            $data_balanza=$modelo->get_balanzas();
            echo json_encode($data_balanza);
            break;


        default:
            $respuesta=[];
            $respuesta['estado']=0;
            $respuesta['contenido']="Accion no registrada($accion) en mp_controlador";
            echo json_encode($respuesta);
    }
    unset($_POST);
    exit;
}


