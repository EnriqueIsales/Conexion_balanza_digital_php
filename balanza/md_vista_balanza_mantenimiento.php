<?php
class mp_vista_balanza_mantenimiento{
    public function __construct(){
    }


    function get_tabla_balanza_mantenimiento($data) {
            ?>
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>IP</th>
                        <th>Puerto</th>
                        <th>Estado</th>
                        <!-- <th>Fecha Ingreso</th> -->
                        <!-- <th>Fecha Modificación</th> -->
                        <!-- <th>Usuario Ingreso</th> -->
                        <!-- <th>Usuario Modif.</th> -->
                        <th style="width:160px">Acciones</th>
                        <th style="width:160px">Test</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if($data){
                    
                            foreach ($data as $s): ?>
                            <tr>
                                <td><?php echo $s['id']; ?></td>
                                <td><?php echo htmlspecialchars($s['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($s['ip']); ?></td>
                                <td><?php echo $s['puerto']; ?></td>
                                <td><?php echo $s['estado']; ?></td>
                                <!-- <td><?php //echo $s['fecha_ingreso']; ?></td> -->
                                <!-- <td><?php // echo $s['fecha_modificacion']; ?></td> -->
                                <!-- <td><?php // echo htmlspecialchars($s['usuario']); ?></td> -->
                                <!-- <td><?php //echo htmlspecialchars($s['usuario_modificacion']); ?></td> -->
                                <td>
                                    <button type="" class="btn btn-sm btn-warning" id="ba_edit" id_balanza="<?php echo $s['id']?>">Editar</button>
                                    <button type="" class="btn btn-sm btn-danger" id="eli_edit" id_balanza="<?php echo $s['id']?>">Eliminar</button>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" id="peso_balanza_<?php echo $s['id']?>" readonly>
                                    <button type="button" class="btn btn-sm btn-info" id="btntest" id_balanza="<?php echo $s['id']?>" >Test</button>
                                </td>
                            </tr>
                            <?php endforeach; 
                        }else{
                            ?>
                            <tr>
                                <td colspan="10" class="text-center">NO SE ENCONTRARON DATOS</td>
                            </tr>
                            <?php
                        }
                        ?>
                </tbody>
            </table>
            
            <?php
        
    }




    function get_formulario_balanza_mantenimiento($data) {
        $isEdit = isset($data) && is_array($data);
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        ?>
        <div class="container py-1">

            <form method="post" id="formulario_<?php echo $isEdit? 'editar': 'nuevo'; ?>"   action="">
                <?php if ($isEdit): ?>
                <input type="hidden" name="id" id="id" value="<?php echo $data['id']; ?>">
                <?php endif; ?>


                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input class="form-control" name="nombre_balanza" id="nombre_balanza" required
                        value="<?php echo $isEdit? htmlspecialchars($data['nombre']) : ''; ?>">
                </div>


                <div class="mb-3 row">
                    <div class="col-md-6">
                        <label class="form-label">IP</label>
                        <input class="form-control" name="ip" id="ip" required
                            value="<?php echo $isEdit? htmlspecialchars($data['ip']) : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Puerto</label>
                        <input class="form-control" name="puerto" id="puerto" required
                            value="<?php echo $isEdit? htmlspecialchars($data['puerto']) : '8899'; ?>">
                    </div>
                </div>

                    <?php
                    if($isEdit){
                        ?>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="1"
                                    <?php echo ($isEdit && $data['estado']=='1')? 'selected' : ''; ?>>Activo
                                </option>
                                <option value="0"
                                    <?php echo ($isEdit && $data['estado']=='0')? 'selected' : ''; ?>>Inactivo
                                </option>
                            </select>
                        </div>
                        <?php
                    }

                    ?>


                <div class="mb-3 row">
                    <div class="col-md-6">
                        <button type="submit" id="boton_grabar"  class="btn btn-success w-100">Guardar</button>
                    </div>
                    <div class="col-md-6">
                        <button type="reset" class="btn btn-secondary w-100">Limpiar</button>
                    </div>
                </div>
            </form>
        </div>
                
        <?php
    }





}