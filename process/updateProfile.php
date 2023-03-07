<?php 
    include("check.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $imagename = $username."_".rand(999, 999999).$_FILES['imgInp']['name'];
        $imagetemp = $_FILES['imgInp']['tmp_name'];
        $imagePath = "../profilePics/";

        if (is_uploaded_file($imagetemp)) {
            if (move_uploaded_file($imagetemp, $imagePath.$imagename)) {
                $stmt = $con->prepare("UPDATE User SET 'Picture' = ? WHERE Id = ?");
                $stmt->bind_param("si", $imagename, $uid);
                $stmt->execute();
                if(!$stmt) {
                    die(header("HTTP/1.0 401 Erro ao guardar imagem na base de dados "));
                }

            } else {
                die(header("HTTP/1.0 401 Erro ao guardar imagem"));  
            }

        } else {
            die(header("HTTP/1.0 401 Erro no upload da imagem"));
        }
 
    } else {
        die(header("HTTP/1.0 401 Faltam parametros"));
    }

?>
