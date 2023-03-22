<?php
//base de datos

require '../../includes/funciones.php';

$auth = estaAutenticado();

if(!$auth){
    header('location: /bienesraices/admin');
}


require '../../includes/config/database.php';
//Conexion
$db = conectarDB();
//Consultar y obtener vendedores

$consulta ="SELECT * FROM vendedores";
$resultado = mysqli_query($db,$consulta);

//Arreglo con msj de errores
$errores = [];

    $titulo ='';
    $precio ='';
    $descripcion ='';
    $habitaciones ='';
    $wc ='';
    $estacionamiento ='';
    $vendedor ='';

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
    if(!$imagen['name'] || $imagen['error']){
        $errores[] = "La imagen es obligatoria";
    }
    
    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";

    // exit;

    //Revisar que el arreglo de errores este vacio
    if(empty($errores)){

        /** SUBIDA DE ARCHIVOS */
        // Crear carpeta
        $carpetaImagenes = '../../imagenes/';
        if(!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = md5( uniqid(rand(), true)  ) . ".jpg";



        //Subir la imagen
        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );


        //Inserta en la base de datos
        $query= "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedor_id) VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc','$estacionamiento','$creado?', '$vendedor')";

        // echo $query;

        $resultado = mysqli_query($db, $query);

        if($resultado){
           // Redireccionar al usuario

           header('location: /bienesraices/admin?resultado=1/');
        }
    }

    

    
}



incluirTemplates('header');

?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        <a href="/bienesraices/admin/" class="boton boton-verde">Volver</a>

        <?php foreach($errores as $error):   ?>

            <div class="alerta error">
            <?php echo $error; ?>
            </div>
            
        <?php endforeach; ?>

        <form action="/bienesraices/admin/propiedades/crear.php" class="formulario" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo ?>">
                
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio ?>">
                
                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen" >

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

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplates('footer');
?>