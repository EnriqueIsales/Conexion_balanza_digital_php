<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}
if(empty($_SESSION['x0']) || empty($_SESSION['x1']) || empty($_SESSION['x6'])){
    echo 'Debe iniciar sesion...';
    exit;
}
//SERVICIO
require_once("../pr_servicio_principal.php");
$servicio=new servicio_principal();
//RUTAS DEL REGISTRO DE RUTAS EN PR_SERVICIO_PRINCIPAL.PHP
$bootstrap_css=$servicio->get_ruta("bootstrap_css_5");
$bootstrap_js=$servicio->get_ruta("bootstrap_js_5");
$materialize=$servicio->get_ruta("materialize-icono");
$jquery=$servicio->get_ruta("jquery");
$alert2=$servicio->get_ruta("alert2");



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MANTENIMIENTO DE BALANZAS</title>

    <link rel="stylesheet" href="<?php echo $bootstrap_css; ?>">
    <link rel="stylesheet" href="<?php echo $materialize; ?>">

    <!-- JS LIBRERIES (ORDEN CRÍTICO) -->
    <script src="<?php echo $jquery; ?>"></script>
    <script src="<?php echo $bootstrap_js; ?>"></script>
    <script src="<?php echo $alert2; ?>"></script>


</head>

<body>


    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Balanzas</h3>
            <div class="row justify-content-md-center">
                <div class="col-md-auto">
                    <button class="btn btn-success btn-sm rounded-circle boton_nuevo" type="button"
                        title="Nuevo registro"><i class="material-icons">add</i></button>
                </div>
            </div>
        </div>



        <div id="contenido_detalle"></div>





        <div id="boton_captura"></div>

    </div>


</body>

</html>

<!-- Modal -->
<div class="modal fade" id="modal_dinamico" tabindex="-1" role="dialog" aria-labelledby="modal_dinamicoLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal_dinamicoLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_contenido">
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

            var set_spinner=`
                <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                <span class="sr-only"></span>
                    </div>
                    </div>
            `;


    get_tabla_balanzas();

    function get_tabla_balanzas() {
        // $('#contenido_spinner').html(set_spinner);
        $.ajax({
            async: true,
            type: 'post',
            url: 'md_controlador_balanza_mantenimiento.php',
            data: {
                /*PARAMETROS A ENVIAR*/
                accion: 'get_balanzas',
                // parametros: parametros
            },
            success: function(data) {

                $('#contenido_detalle').html(data);
            },
            error: function(request, status, error) {
                alert(jQuery.parseJSON(request.responseText).Message);
            },
            timeout: 30 * 60 * 1000 /*ESPERAR 30 MINUTOS*/
        });

    }


            /*NUEVO REGISTRO*/
        $(this).on('click', '.boton_nuevo', function(){
            let balanza_new=0;

            $('#modal_contenido').html(set_spinner);
            //COLOCAR FORMULARIO NUEVO EN EL MODAL
            $.ajax({
                async: true, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                    /*PARAMETROS A ENVIAR*/
                    accion: 'get_formulario_nuevo',
                    info: balanza_new,
                },
                success: function (data) {
                    /*RESULTADO*/
                    $('#modal_contenido').html(data);
                },
                error: function (request, status, error) {
                    alert(jQuery.parseJSON(request.responseText).Message);
                }
            });
            //CAMBIAR TITULO
            $("#modal_dinamicoLabel").html("Agregar Balanza");
            //ABRIR MODAL
            $("#modal_dinamico").modal("show");
        });


        // Solo permitir letras, números, guion medio y guion bajo en el campo nombre_balanza
        $(document).on('input', '#nombre_balanza', function() {
            let valor = $(this).val();
            // Reemplaza cualquier caracter que no sea letra, número, guion medio o guion bajo
            valor = valor.replace(/[^a-zA-Z0-9\-_]/g, '');
            $(this).val(valor);
        });

        $(document).on('input', '#ip', function() {
            let valor = $(this).val();
            // Reemplaza cualquier caracter que no sea letra, número, guion medio o guion bajo
            valor = valor.replace(/[^0-9\.-_]/g, '');
            $(this).val(valor);
        });


        $(this).on('submit', '#formulario_nuevo', function(e){
            e.preventDefault();
            /*INPUST DEL FORMULARIO*/
            var nombre= $('#nombre_balanza').val();
            var ip= $('#ip').val();
            var puerto= $('#puerto').val();
            var estado= $('#estado').val();
            /*PARAMETROS*/
            var parametros={
                nombre: nombre,
                ip: ip,
                puerto: puerto,
                estado: estado,
            };

            console.log(parametros);
            

            $.ajax({
                async: true, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                    /*PARAMETROS A ENVIAR*/
                    accion: 'set_nuevo',
                    parametros: parametros
                },
                success: function (data) {
                    let dataJ=JSON.parse(data);
                    //console.log(data);
                    /*RESULTADO*/
                    if(dataJ){
                        Swal.fire(
                            'Resultado',
                            dataJ['contenido'],
                            dataJ['estado']===1?'success':'error'
                        );

                        $('#modal_dinamico').modal('hide');
                        get_tabla_balanzas();
                    }
                },
                error: function (request, status, error) {
                    alert(jQuery.parseJSON(request.responseText).Message);
                }
            });
        });



        $(this).on('click', '#eli_edit', function(){
            var registro_id=$(this).attr('id_balanza');
            
            if(registro_id){
                var confirmacion=confirm("¿Desea eliminar la Balanza, ID: "+registro_id+" ?");
                if(confirmacion){
                    /*ELIMINAR REGISTRO*/
                    $.ajax({
                        async: true, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                            /*PARAMETROS A ENVIAR*/
                            accion: 'set_eliminar',
                            parametros: registro_id
                        },
                        success: function (data) {
                            let dataJ=JSON.parse(data);
                            if(dataJ){
                                Swal.fire(
                                    'Resultado',
                                    dataJ['contenido'],
                                    dataJ['estado']===1?'success':'error'
                                );

                                $('#modal_dinamico').modal('hide');
                                get_tabla_balanzas();
                            }

                        },
                        error: function (request, status, error) {
                            alert(jQuery.parseJSON(request.responseText).Message);
                        }
                    });

                }
            }

        });


        $(this).on('click', '#ba_edit', function(){
            var registro_id=$(this).attr('id_balanza');
            console.log(registro_id);
            
            $('#modal_contenido').html(set_spinner);
            //COLOCAR FORMULARIO NUEVO EN EL MODAL
            var parametros={
                id: registro_id,
            };

            $.ajax({
                async: true, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                    /*PARAMETROS A ENVIAR*/
                    accion: 'get_formulario_datos_editar',
                    info: parametros,
                },
                success: function (data) {
                    /*RESULTADO*/
                    $('#modal_contenido').html(data);
                },
                error: function (request, status, error) {
                    alert(jQuery.parseJSON(request.responseText).Message);
                }
            });
            //CAMBIAR TITULO
            $("#modal_dinamicoLabel").html("Editar Balanza");
            //ABRIR MODAL
            $("#modal_dinamico").modal("show");

        });


        $(this).on('submit', '#formulario_editar', function(e){
            e.preventDefault();
            /*INPUST DEL FORMULARIO*/
            var id= $('#id').val();
            var nombre= $('#nombre_balanza').val();
            var ip= $('#ip').val();
            var puerto= $('#puerto').val();
            var estado= $('#estado').val();
            /*PARAMETROS*/
            var parametros={
                id: id,
                nombre: nombre,
                ip: ip,
                puerto: puerto,
                estado: estado,
            };

            console.log(parametros);
            

            $.ajax({
                async: true, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                    /*PARAMETROS A ENVIAR*/
                    accion: 'get_formulario_editar',
                    parametros: parametros
                },
                success: function (data) {
                    let dataJ=JSON.parse(data);
                    //console.log(data);
                    /*RESULTADO*/
                    if(dataJ){
                        Swal.fire(
                            'Resultado',
                            dataJ['contenido'],
                            dataJ['estado']===1?'success':'error'
                        );

                        $('#modal_dinamico').modal('hide');
                        get_tabla_balanzas();
                    }
                },
                error: function (request, status, error) {
                    alert(jQuery.parseJSON(request.responseText).Message);
                }
            });
        });


        $(this).on('click', '#btntest', function(){
            var registro_id=$(this).attr('id_balanza');
            var parametros={
                id: registro_id,
            };
            if(registro_id){
                $.ajax({
                    async: false, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                        /*PARAMETROS A ENVIAR*/
                        accion: 'get_datos_balanza',
                        parametros: parametros
                    },
                    success: function (data) {
                        let dataJ=JSON.parse(data);
                        console.log(dataJ);
                        if(dataJ){
                            if(dataJ.estado==1){
                                $('#peso_balanza_'+registro_id).val(dataJ.cantidad);
                            }else{
                                Swal.fire(
                                    'Resultado',
                                    'No se obtuvo respuesta de la balanza',
                                    'error'
                                );
                            }
                        }else{
                            Swal.fire(
                                'Resultado',
                                'No se obtuvo respuesta de la balanza',
                                'error'
                            );
                        }
                    },
                    error: function (request, status, error) {
                        alert(jQuery.parseJSON(request.responseText).Message);
                    }
                });
            }

        });




        // generar_boton_captura_peso();

        function generar_boton_captura_peso(){
            $.ajax({
                async: false, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                    /*PARAMETROS A ENVIAR*/
                    accion: 'get_balanzas_activas'
                },
                success: function (data) {
                    let dataJ=JSON.parse(data);
                    if(dataJ){

                        let balanzas_localizadas=``;
                        dataJ.forEach(element => {
                            balanzas_localizadas += `<li><a class="dropdown-item" id="btntest_ac" info='${JSON.stringify(element)}' href="#">${element.nombre}</a></li>`;
                        });
                        
                        let html=`
                            <div class="dropdown">
                                <button class="btn btn-info dropdown-toggle"  type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                   <i class="material-icons">fitness_center</i> Seleccionar Balanza 
                                </button>
                                <ul class="dropdown-menu">
                                    ${balanzas_localizadas}
                                </ul>
                            </div>
                            <div>
                                <input type="text" class="form-control form-control-sm" id="peso_balanza_captura" placeholder="Peso Capturado" readonly>
                            </div>
                        `;
                        $('#boton_captura').html(html);
                    }

                },
                error: function (request, status, error) {
                    alert(jQuery.parseJSON(request.responseText).Message);
                }
            });
        }

        $(this).on('click', '#btntest_ac', function(){
            var info = JSON.parse($(event.target).closest('li').find('a').attr('info'));
            if(info){
                $.ajax({
                    async: false, type: 'post', url: 'md_controlador_balanza_mantenimiento.php', data: {
                        /*PARAMETROS A ENVIAR*/
                        accion: 'get_datos_balanza',
                        parametros: info
                    },
                    success: function (data) {
                        let dataJ=JSON.parse(data);
                        console.log(dataJ);
                        if(dataJ){
                            if(dataJ.estado==1){
                                $('#peso_balanza_captura').val(dataJ.cantidad);
                            }else{
                                Swal.fire(
                                    'Resultado',
                                    'No se obtuvo respuesta de la balanza',
                                    'error'
                                );
                            }
                        }else{
                            Swal.fire(
                                'Resultado',
                                'No se obtuvo respuesta de la balanza',
                                'error'
                            );
                            $('#peso_balanza_captura').val('');
                        }
                    },
                    error: function (request, status, error) {
                        alert(jQuery.parseJSON(request.responseText).Message);
                    }
                });
            }

        });



        







});
</script>