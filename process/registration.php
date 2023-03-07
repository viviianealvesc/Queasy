<?php 
    include("connection/connect.php");

    if (isset($_POST["username"]) &&  isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["repPassword"])) {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $repPassword = $_POST["repPassword"];

        if($username == "" || $email == "" || $password == "" || $repPassword == "" ) {
            die(header("HTTP/1.0 401 Preencha todos os campos do formulário"));
        }

        $checkUsername = $con->prepare("SELECT Id FROM user WHERE Username = ? LIMIT 1");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $count = $checkUsername->get_result()->num_rows;
        if($count > 0) {
            die(header("HTTP/1.0 401 Username existente"));

        }

        $checkEmail = $con->prepare("SELECT Id FROM user WHERE Email = ? LIMIT 1");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $count = $checkEmail->get_result()->num_rows;
        if($count > 0) {
            die(header("HTTP/1.0 401 ".$password." ".$repPassword));

        }

        if($password != $repPassword) {
            die(header("HTTP/1.0 401 Passwords diferentes"));
        }

        $password = password_hash($password, PASSWORD_DEFAULT);

        $token = bin2hex(openssl_random_pseudo_bytes(20));
        $secure = rand(10000000000, 9999999999);

        $stmt = $con->prepare("INSERT INTO user (Username, Email, Password, Online, Token, Secure, Creation)
                                  VALUES (?, ?, ?, now(), ?, ?, now())");

        $stmt->bind_param("ssssi", $username,$email,$password,$token,$secure);

        $stmt->execute();

        $getUser = $con->prepare("SELECT id FROM user WHERE Email = ? LIMIT 1");

        $getUser->bind_param("s", $email);
        $getUser->execute();
        $user = $getUser->get_result()->fetch_assoc();

        if($stmt && $user) {
            setcookie("ID", $user["id"], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("TOKEN", $token, time() + (10 * 365 * 24 * 60 * 60));
            setcookie("SECURE", $secure, time() + (10 * 365 * 24 * 60 * 60));
        } else {
            die(header("HTTP/1.0 401 Ocorreu um erro na base de dados"));
        }

    } else {
        die(header("HTTP/1.0 401 Formulário de autenticação inválido"));
    }
?>