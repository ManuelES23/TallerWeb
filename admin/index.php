<?php

require '../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header('location: /bienesraices/');
}




//Importar la coneccion
require '../includes/config/database.php';
$db = conectarDB();

//Escribir el Query
$query = "SELECT * FROM propiedades";
//Consultar la base de datos
$resultadoConsulta = mysqli_query($db,$query);




//Mensaje Condicional
$resultado = $_GET['resultado'] ?? null;

if($_SERVER['REQUEST_METHOD']=== 'POST'){
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if($id){
        //Elimina el archivo
        $query = "SELECT imagen FROM propiedades WHERE id = ${id}";

        $resultado = mysqli_query($db , $query);
        $propiedead = mysqli_fetch_assoc($resultado);

        unlink('../imagenes/' . $propiedead['imagen']);


        //Elimina la propiedad
        $query = "DELETE FROM propiedades WHERE id = ${id}";

        $resultado = mysqli_query($db , $query);

        if($resultado){
            header('location: /bienesraices/admin?resultado=3');
        }
    }


    var_dump($id);

}


//Incluye un template
incluirTemplates('header');

?>

    <main class="contenedor seccion">
        <h1>Administrador de Bienes Raices</h1>
        <?php if( intval($resultado)  === 1):  ?>
            <p class="alerta exito">Anuncio Creado Correctamente</p>
        <?php elseif( intval($resultado)  === 2 ): ?>
            <p class="alerta exito">Anuncio Actualizado Correctamente</p>
        <?php elseif( intval($resultado)  === 3 ): ?>
            <p class="alerta exito">Anuncio Eliminado Correctamente</p>
        <?php endif; ?>
        <a href="/bienesraices/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>

        <!-- Mostrar los resultados -->
        <table class="propiedades"> 
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TITULO</th>
                    <th>IMAGEN</th>
                    <th>PRECIO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>

            <tbody>
                <?php while($propiedead = mysqli_fetch_assoc($resultadoConsulta)) : ?>
                <tr>
                    <td> <?php echo $propiedead['id']; ?> </td>
                    <td><?php echo $propiedead['titulo']; ?></td>
                    <td> <img src="../imagenes/<?php echo $propiedead['imagen']; ?>" alt="Imagen" class="imagen-tabla"> </td>
                    <td>$ <?php echo $propiedead['precio']; ?></td>
                    <td>
                        
                        <form method="POST" class="w100" action="">
                            <input type="hidden" name="id" value="<?php echo $propiedead['id']; ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                       
                        <a href="propiedades/actualizar.php?id=<?php echo $propiedead['id']; ?>" class="boton-amarillo-block">Actualizar</a>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


    </main>

<?php

    // Cerrar la conexion
    mysqli_close($db);

    incluirTemplates('footer');
?>