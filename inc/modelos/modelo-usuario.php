<?php

    /*
    $arreglo = array(
      "respuesta" => "DESDE modelo-admin.php"
    );

    // envía respuesta a xhr.responseText en ajax donde es llamado este archivo
    die(json_encode($arreglo));
    die(json_encode($_POST));
    */

    if(isset($_POST["accion"])){
        $accion = $_POST["accion"];
    }
    if(isset($_POST["usuario"])){
        $usuario = $_POST["usuario"];
    }
    if(isset($_POST["nombre"])){
        $nombre = $_POST["nombre"];
    }
    if(isset($_POST["clave"])){
        $clave = $_POST["clave"];
    }
    if(isset($_POST["id_usuario"])){
        $id_usuario = (int) $_POST["id_usuario"];
    }

    // crear un usuario
    if($accion === "crear") {
        // Código para crear los administradores

        // hashear claves
        $opciones = array(
            "cost" => 12
        );

        $hash_clave = password_hash($clave, PASSWORD_BCRYPT, $opciones);
        /*
        $respuesta = array(
          "cost" => 12
        );
        echo json_encode($respuesta);
        */

        // importar la conexión
        include "../funciones/conexion.php";

        try {
            // Realizar la consulta a la base de datos
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, nombre, clave) VALUES (?, ?, ?) ");
            $stmt->bind_param("sss", $usuario, $nombre, $hash_clave);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                $respuesta = array(
                    "respuesta" => "correcto",
                    "id_insertado" => $stmt->insert_id,
                    "tipo" => $accion
                );
            }
            else {
                $respuesta = array(
                    "respuesta" => "error",
                    "error" => error_get_last()
                );
            }

            $stmt->close();
            $conn->close();

        }
        catch(Exception $e) {
            // En caso de un error, tomar la exepcion
            $respuesta = array(
                "error" => $e->getMessage(),
                "error" => error_get_last()
            );
        }

        echo json_encode($respuesta);
    }

    // iniciar sesión
    if($accion === "login") {
        // escribir codigo que loguee a los administradores

        include "../funciones/conexion.php";

        try {
            // Seleccionar el administrador de la base de datos
            $stmt = $conn->prepare("SELECT usuario, id, clave FROM usuarios WHERE usuario = ?");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();

            // Loguear el usuario
            // bind_result trae el resultado de la consulta y lo asigna a las variables pasadas como parámetro
            $stmt->bind_result($nombre_usuario, $id_usuario, $clave_usuario);
            $stmt->fetch();

            if($nombre_usuario){
                // El usuario existe, verificar la clave
                if(password_verify($clave, $clave_usuario )){
                    // Iniciar la sesion
                    session_start();
                    $_SESSION["nombre"] = $usuario;
                    $_SESSION["id"] = $id_usuario;
                    $_SESSION["login"] = true;
                    // Login correcto
                    $respuesta = array(
                        "respuesta" => "correcto",
                        "nombre" => $nombre_usuario,
                        "tipo" => $accion
                    );
                }
                else {
                    // Login incorrecto, enviar error
                    $respuesta = array(
                        "resultado" => "clave Incorrecta",
                        "error" => error_get_last()
                    );
                }
            }
            else {
                $respuesta = array(
                    "error" => "Usuario no existe",
                    "error" => error_get_last()
                );
            }

            $stmt->close();
            $conn->close();

        }
        catch(Exception $e) {
            // En caso de un error, tomar la exepcion
            $respuesta = array(
                "clave" => $e->getMessage(),
                "error" => error_get_last()
            );
        }

        echo json_encode($respuesta);
    }

    // eliminar un usuario
    if($accion === "eliminar") {

        // importar la conexion
        include "../funciones/conexion.php";

        try {

            // Realizar la consulta a la base de datos
            $stmt = $conn->prepare("DELETE from usuarios WHERE id = ? ");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                $respuesta = array(
                    "respuesta" => "correcto",
                    "id_eliminado" => $id_usuario,
                    "tipo" => $accion
                );
            }
            else {
                $respuesta = array(
                    "respuesta" => "error",
                    "id_eliminado" => $id_usuario,
                    "tipo" => $accion,
                    "error" => error_get_last()
                );
            }

            $stmt->close();
            $conn->close();

        }
        catch(Exception $e) {
            // En caso de un error, tomar la exepcion
            $respuesta = array(
                "error" => $e->getMessage(),
                "error" => error_get_last()
            );
        }

        echo json_encode($respuesta);
    }

?>
