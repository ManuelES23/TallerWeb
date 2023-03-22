<?php

require '../../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header('location: /bienesraices/admin');
}

//Validar la URL por ID valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('location: /bienesraices/admin');
}


//base de datos

require '../../includes/config/database.php';
//Conexion
$db = conectarDB();

//Obtener los datos de la propiedad
$consulta = "SELECT * FROM propiedades WHERE id = ${id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);

// echo "<pre>";
// var_dump($propiedad);
// echo "</pre>";


//Consultar y obtener vendedores

$consulta ="SELECT * FROM vendedores";
$resultado = mysqli_query($db,$consulta);

//Arreglo con msj de errores
$errores = [];

    $titulo =$propiedad['titulo'];
    $precio =$propiedad['precio'];
    $descripcion =$propiedad['descripcion'];
    $habitaciones =$propiedad['habitaciones'];
    $wc =$propiedad['wc'];
    $estacionamiento =$propiedad['estacionamiento'];
    $vendedor =$propiedad['vendedor_id'];
    $imagenPropiedad = $propiedad['imagen'];

// Ejecutar el codigo despues de que el usuario envia en formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    //  echo "<pre>";
    //  var_dump($_POST);
    //  echo "</pre>";
     
    //  echo "<pre>";
    // var_dump($_FILES);
    // echo "</pre>";

     

    $titulo = mysqli_real_escape_string($db ,$_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedor = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');

    // Asignar files hacia una variable

    $imagen = $_FILES['imagen'];
    
   

    if(!$titulo){
        $errores[] ="Debes añadir un titulo";
    }
    if(!$precio){
        $errores[] ="Debes añadir un precio";
    }
    if(!$descripcion){
        $errores[] ="Debes añadir un descripcion";
    }
    if(!$habitaciones){
        $errores[] ="Debes añadir almenos una habitaciones";
    }
    if(!$wc){
        $errores[] ="Debes añadir un wc";
    }
    if(!$estacionamiento){
        $errores[] ="Debes añadir un estacionamiento";
    }
    if(!$vendedor){
        $errores[] ="Debes añadir un vendedor";
    }
    
    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";

    // exit;

    //Revisar que el arreglo de errores este vacio
    if(empty($errores)){



        //Crear carpeta
        $carpetaImagenes = '../../imagenes/';
        if(!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';


        /** SUBIDA DE ARCHIVOS */
        
         
        if($imagen['name']){
            //Eliminar imagen previa
            unlink($carpetaImagenes . $propiedad['imagen']);
            
            $nombreImagen = md5( uniqid(rand(), true)  ) . ".jpg";

            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );

        }else {
            $nombreImagen = $propiedad['imagen'];
        }
        
        
         



         //Subir la imagen
         


        //Inserta en la base de datos
        $query= "UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedor_id = ${vendedor} WHERE id = ${id}";

        


        $resultado = mysqli_query($db, $query);

        if($resultado){
           // Redireccionar al usuario

           header('location: /bienesraices/admin?resultado=2/');
        }
    }

    

    
}




incluirTemplates('header');

?>

    <main class="contenedor seccion">
        <h1>Actualizar Propiedad</h1>

        <a href="/bienesraices/admin/" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error):   ?>

            <div class="alerta error">
            <?php echo $error; ?>
            </div>
            
        <?php endforeach; ?>

        <form class="formulario" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo ?>">
                
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio ?>">
                
                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen" >

                <img src="../../imagenes/<?php echo $imagenPropiedad; ?>" class="imagen-small" alt="">

                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" cols="30" rows="10"><?php echo $titulo ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Proiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones ?>">
                
                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc ?>">
                
                <label for="estacionamiento">Estacinamientos:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>
                <select name="vendedor" id="">
                    <option value="">--Seleccione--</option>
                    <?php while ($row = mysqli_fetch_assoc($resultado) ) :?>
                        <option  <?php echo $vendedor === $row['id'] ? 'selected' : ''; ?>  value="<?php echo $row['id']; ?>"> <?php echo $row['nombre'] . " " . $row['apellido']; ?> </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplates('footer');
?>