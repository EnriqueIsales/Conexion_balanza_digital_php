<?php

ini_set('max_execution_time', 180);
class mp_modelo_balanza_mantenimiento{
    private $cod_usuario;
    private $cod_empresa;
    private $pdo;

    public function __construct(){
        global $pdo;
        $this->pdo=$pdo;
        $this->cod_empresa=$_SESSION['x6'];
        $this->cod_usuario=$_SESSION['usuario']['cod_usuario'];
    } 


    function get_balanzas(){
        // $qry="SELECT * FROM balanzas WHERE estado =1";
        $qry="  SELECT * FROM(

                SELECT id, nombre, ip, puerto, case when  estado=1 then 'Activa' when estado=0 then 'desabilidata' end as estado, DATE_FORMAT(fecha_ingreso,'%d/%m/%Y %h:%m')fecha_ingreso, usuario_ingreso FROM balanzas WHERE estado =1

                )t1 LEFT JOIN(

                select cod_usuario, nombre as usuario from ui_usuario 

                )t2 on t1.usuario_ingreso=cod_usuario;";
        $qqry=$this->pdo->query($qry);
        $data_info=$qqry->fetchAll(PDO::FETCH_ASSOC);
        if($data_info){
            return $data_info;

        }else{
            return 0;
        }
    }

    function get_balanza_id($data) {

        $id = isset($data['id']) ? intval($data['id']) : 0;

        if ($id > 0) {
            $stmt = $this->pdo->prepare("SELECT * FROM balanzas WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $data_info = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data_info ? $data_info : 0;
        }else{
            
            return 0;
        }
    }

    function verificar_balanza_existente($data) {
        $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
        $ip = isset($data['ip']) ? trim($data['ip']) : '';

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_nombre FROM balanzas WHERE nombre = :nombre AND estado = 1");
        $stmt->execute([':nombre' => $nombre]);
        $result_nombre = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total_ip FROM balanzas WHERE ip = :ip AND estado = 1");
        $stmt->execute([':ip' => $ip]);
        $result_ip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (($result_nombre && $result_nombre['total_nombre'] > 0) || ($result_ip && $result_ip['total_ip'] > 0)) {
            return ['estado' => 3]; // Ya existe nombre o ip
        } else {
            return ['estado' => 1]; // No existe
        }
    }

    function set_insert_balanza($data){
        $nombre=$data['nombre'];
        $ip=$data['ip'];
        $puerto=$data['puerto'];

        $resultado=$this->verificar_balanza_existente($data);

        if($resultado['estado']==1){
            try {
                $this->pdo->beginTransaction();

                    $cod_usuario=$this->cod_usuario;
                    
                    $qry = "INSERT INTO balanzas (nombre, ip, puerto, estado, usuario_ingreso) VALUES ('{$nombre}','{$ip}',{$puerto},1,$cod_usuario)";
                    $qqry=$this->pdo->query($qry);
                    if($qqry){
                        $respuesta['estado']=1;
                        $respuesta['contenido']='Balanza registrada';
                    }else{
                        $respuesta['estado']=0;
                        $respuesta['contenido']='Balanza no registrada';
                    }
                    

                // $this->pdo->rollback();  
                $this->pdo->commit();
            } catch (PDOException $e) {
                $this->pdo->rollback();
                $respuesta['estado']=0;
                $respuesta['contenido']='Error en accion';
                $respuesta['info']='Error: '.$e->getMessage();
            }   
        }else{
            $respuesta['estado']=0;
            $respuesta['contenido']='Nombre o IP ya existe';

        }
        echo json_encode($respuesta);
        
    }


    function get_formulario_editar($parametros) {
        
        $id = $parametros['id'];
        $nombre = $parametros['nombre'];
        $ip =  $parametros['ip'];
        $puerto = intval($parametros['puerto']);
        $estado = $parametros['estado'];
        $cod_usuario=$this->cod_usuario;

        try {
            $this->pdo->beginTransaction();
            $qry = "UPDATE balanzas set nombre='$nombre', ip='$ip', puerto=$puerto, estado=$estado, usuario_modificacion=$cod_usuario , fecha_modificacion=now() WHERE id={$id}";
            $qqry=$this->pdo->query($qry);
            if($qqry){
                $respuesta['estado']=1;
                $respuesta['contenido']='Balanza editada';
            }else{
                $respuesta['estado']=0;
                $respuesta['contenido']='No se edito la balanza';
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollback();
            $respuesta['estado']=0;
            $respuesta['contenido']='Error en accion';
            $respuesta['info']='Error: '.$e->getMessage();
        }
        echo json_encode($respuesta);
    }


    function set_eliminar($id){

        $cod_usuario=$this->cod_usuario;

        try {
            $this->pdo->beginTransaction();
            $qry = "UPDATE balanzas set estado=0, usuario_modificacion=$cod_usuario , fecha_modificacion=now() WHERE id={$id}";
            $qqry=$this->pdo->query($qry);
            if($qqry){
                $respuesta['estado']=1;
                $respuesta['contenido']='Balanza eliminada';
            }else{
                $respuesta['estado']=0;
                $respuesta['contenido']='Balanza no eliminada';
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollback();
            $respuesta['estado']=0;
            $respuesta['contenido']='Error en accion';
            $respuesta['info']='Error: '.$e->getMessage();
        }
        echo json_encode($respuesta);
    }



    function get_dato_balanza($dato){
        if($dato){
            $respuesta=[];

            $ip = $dato['ip'];
            $port = $dato['puerto'];
    

            $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
            if (!$fp) {
                $respuesta['estado'] = 0;
                $respuesta['contenido'] = "Error de conexión: $errstr ($errno)";
                $respuesta['cantidad'] = null;
                echo json_encode($respuesta);
                return;
            }
    
            // Si la balanza requiere comando, envíalo:
            $data = fread($fp, 1024);
            fclose($fp);
    
            // Separar por comas
            $parts = explode(",", $data);
    
            // El peso suele estar en el último elemento
            $peso = trim(str_replace("kg", "", end($parts)));
    

            $respuesta['estado']=1;
            $respuesta['contenido']="Neto Capturado";
            $respuesta['cantidad']=$peso;
            
        }

        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        echo json_encode($respuesta);

    }














}