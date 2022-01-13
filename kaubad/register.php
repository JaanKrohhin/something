<?php
session_start();
$error = $_SESSION['error'] ?? "";

function puhastaAndmed($data){
    $data=trim($data);//eemaldab tühikud
    $data=htmlspecialchars($data);// ignores code elements
    $data=stripslashes($data);//eemaldab
    return $data;
}
function register($login, $pass, $admin)
{
    $yhendus = new mysqli("localhost", "jaan", "123456", "jaan");//"d105617.mysql.zonevs.eu", "d105617_krohhin", "AKEYfass123", "d105617_jaan";  "localhost", "jaan", "123456", "jaan"
    if (isset($_REQUEST["knimi"]) && isset($_REQUEST["psw"])) {
        $login = puhastaAndmed($_REQUEST["knimi"]);
        $pass = puhastaAndmed($_REQUEST["psw"]);
        $sool = 'uus';
        $krypt = crypt($pass, $sool);

        $kask = $yhendus->prepare("SELECT id,nimi,parool FROM kasutaja where nimi=?");
        $kask->bind_param("s", $login);
        $kask->bind_result($id, $kasutaja, $parool);
        $kask->execute();
        if ($kask->fetch()) {
            $_SESSION['error'] = "Kasutaja on juba olemas";
            header("Location: $_SERVER[PHP_SELF]");
            $yhendus->close();
            exit();
        } else {
            $_SESSION['error'] = " ";
        }
        $kask = $yhendus->prepare("INSERT INTO kasutaja(nimi,parool,onAdmin,koduleht) VALUES (?,?,?,'maahaldus.php')");
        $kask->bind_param("ssi", $login, $krypt, $_REQUEST["adm"]);
        $kask->execute();
        $_SESSION['unimi'] = $login;
        if ($admin == true) {
            $_SESSION['admin'] = true;
        }
        header("Location: maahaldus.php");
        $yhendus->close();
        exit();
    }
}
/*<?=$error ?>line 57 between the strong tag*/
?>
<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Registreerimisvorm</title>
</head>
<body>
<h1>Uue kasutaja registreerimine</h1>
<strong></strong>
<form action="register.php" method="post">
    <label for="knimi">Kasutajanimi</label>
    <input type="text" placeholder="Sisesta kasutajanimi" name="knimi" id="knimi" required><br>
    <label for="psw">Kasutajanimi</label>
    <input type="password" placeholder="Sisesta parool" name="psw" id="psw" required><br>
    <label for="adm">Kas on Admin?</label>
    <input type="checkbox" id="adm" name="adm" value="1"><br>
    <input type="submit" value="Loo">
</form>
</body>
</html>-->