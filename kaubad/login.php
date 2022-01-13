<?php
function login($login, $pass){
    $yhendus=new mysqli("localhost", "jaan", "123456", "jaan");//"d105617.mysql.zonevs.eu", "d105617_krohhin", "AKEYfass123", "d105617_jaan";  "localhost", "jaan", "123456", "jaan"
    //login ja parool salvestatud andmebaasiga andmetega
    if (isset($_POST['knimi']) && isset($_POST['psw'])){
        echo "Helooooooooooooo";
        $login=htmlspecialchars($_POST['knimi']);
        $pass=htmlspecialchars($_POST['psw']);
        $sool='uus';
        $krypt=crypt($pass,$sool);
        //check the database for the user
        $kask=$yhendus->prepare("SELECT id,nimi,parool,onAdmin FROM kasutaja where nimi=?");
        $kask->bind_param("s",$login);
        $kask->bind_result($id,$kasutaja,$parool,$onAdmin);
        $kask->execute();
        if ($kask->fetch() && $krypt == $parool) {
            $_SESSION['unimi'] = $login;
            if ($onAdmin == 1) {
                $_SESSION['admin'] = true;
            }else{
                $_SESSION['admin'] = false;
            }
            header("Location: maahaldus.php");
            $yhendus->close();
            exit();
        }
        echo "kasutaja $login või parool $krypt on vale";
        $yhendus->close();
    }
}
?>
<!--<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
<h1>Login Vorm</h1>
</div><form action="login.php" method="post">
    <label for="knimi">Kasutajanimi</label>
    <input type="text" placeholder="Sisesta kasutajanimi" name="knimi" id="knimi" required><br>
    <label for="psw">Kasutajanimi</label>
    <input type="password" placeholder="Sisesta parool" name="psw" id="psw" required><br>
    <input type="submit" value="Loo">
</form>
</body>
</html>-->
