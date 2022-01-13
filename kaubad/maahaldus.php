<?php
require("abifunktsioonid.php");
require("login.php");
session_start();
$sorttulp="temperatuur";
$otsisona="";
$yhendus=new mysqli("localhost", "jaan", "123456", "jaan");//"d105617.mysql.zonevs.eu", "d105617_krohhin", "AKEYfass123", "d105617_jaan";  "localhost", "jaan", "123456", "jaan"
if (!isset($_SESSION["admin"])){
    $_SESSION["admin"] = false;
}
//-----------------------------------------------------------------------
//Register
if (isset($_REQUEST['rnimi']) && isset($_REQUEST['rpsw']) && isset($_REQUEST['adm'])){
    register($_REQUEST['knimi'],$_REQUEST['psw'],$_REQUEST['adm']);
}//Login
elseif (isset($_REQUEST['knimi']) && isset($_REQUEST['psw'])){
    login($_POST['knimi'],$_POST['psw']);
}
//-----------------------------------------------------------------------
//Maahaldus
if(isSet($_REQUEST["maalisamine"])){
    if (!empty(trim($_REQUEST["uuemaanimi"])) && !empty(trim($_REQUEST["uuskeskus"]))){
        lisaMaa($_REQUEST["uuemaanimi"],$_REQUEST["uuskeskus"]);
    }
    header("Location: maahaldus.php");
    exit();
}
if(isSet($_REQUEST["ilmalisamine"])){
    if (!empty($_REQUEST["temp"]) && !empty(trim($_REQUEST["kuupaev"]))){
        lisaIlm($_REQUEST["temp"], $_REQUEST["maakonna_id"], $_REQUEST["kuupaev"]);
    }
    header("Location: maahaldus.php");
    exit();
}
if(isSet($_REQUEST["kustutusid"])){
    kustutaIlm($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaIlm($_REQUEST["muudetudid"], $_REQUEST["temperatuur"],
        $_REQUEST["maakonna_id"], $_REQUEST["kuupaev"]);
}
if(isSet($_REQUEST["sort"])){
    $sorttulp=$_REQUEST["sort"];
}
if(isSet($_REQUEST["otsisona"])){
    $otsisona=$_REQUEST["otsisona"];
}
$ilmad=kysiIlmadeAndmed($sorttulp, $otsisona);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <title>Ilma Halduse leht</title>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div id="menuArea">
    <a href="register.php">Loo uus kasutaja</a>
    <?php
    if (isset($_SESSION["unimi"])){
    ?>
    <h1>Tere, <?="$_SESSION[unimi]"?></h1>
    <a href="logout.php">Logi välja</a>
    <?php
    }else{
    ?>
    <a href="login.php">Logi sisse</a>
    <?php
    }
    ?>
</div>
<button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Login</button>
<div id="id01" class="modal">

    <form class="modal-content animate" action="" method="post">
        <div class="imgcontainer">
            <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
            <img src="avatar.png" alt="Avatar" class="avatar">
        </div>

        <div class="container">
            <label for="knimi"><b>Kasutajanimi</b></label>
            <input type="text" placeholder="Kasutajanimi" name="knimi" id="knimi" required>
            <label for="psw"><b>Parool</b></label>
            <input type="password" placeholder="Parool" name="psw" id="psw" required>
            <input type="submit" value="Loo">
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw">Forgot <a href="#">password?</a></span>
        </div>
    </form>
</div>
<button onclick="document.getElementById('id02').style.display='block'" style="width:auto;">Register</button>
<div id="id02" class="modal">

    <form class="modal-content animate" action="" method="post">
        <div class="imgcontainer">
            <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">&times;</span>
            <img src="avatar.png" alt="Avatar" class="avatar">
        </div>

        <div class="container">
            <label for="knimi"><b>Kasutajanimi</b></label>
            <input type="text" placeholder="Kasutajanimi" name="rnimi" id="rnimi" required>
            <label for="psw"><b>Parool</b></label>
            <input type="password" placeholder="Parool" name="rpsw" id="rpsw" required>
            <label for=""><input type="checkbox" id="adm" name="adm" value="1">Kas on admin?</label><br>
            <input type="submit" value="Loo uus konto">
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw">Forgot <a href="#">password?</a></span>
        </div>
    </form>
</div>
<script src="modal.js"></script>
<div class="row">
    <form action="maahaldus.php">
        <div class="column" id="left">
            <h2>Ilma andmete lisamine</h2>
            <dl>
                <dt>Temperatuur:</dt>
                <dd><input type="number" name="temp" step="0.1" min="-40" max="50"/></dd>
                <dt>Maakond:</dt>
                <dd><?php
                    echo looRippMenyy("SELECT id, maakonnanimi FROM maakonnad",
                        "maakonna_id");
                    ?>
                </dd>
                <dt>Kuupäev:</dt>
                <dd><input type="datetime-local" name="kuupaev" /></dd>
            </dl>
            <input type="submit" name="ilmalisamine" value="Lisa andmed" /></div>
        <div class="column" id="mid"><h2>Maakonna lisamine</h2><div id="fre">
                <input type="text" name="uuemaanimi" placeholder="Maakonna nimi"/><br>
                <input type="text" name="uuskeskus" placeholder="Maakonna Keskus"/><br>
                <input type="submit" name="maalisamine" value="Lisa Maakond" /></div>
        </div></form>
    <div class="column" id="rig">
        <form action="maahaldus.php">
            <h2>Ilma loetelu</h2>
            <table>
                <tr>
                    <?php
                    if ($_SESSION["admin"]==true){
                    ?><th>Haldus</th><?php }?>
                    <th><a href="maahaldus.php?sort=temperatuur">Temperatuur</a></th>
                    <th><a href="maahaldus.php?sort=maakonnanimi">Maakond</a></th>
                    <th><a href="maahaldus.php?sort=kuupaev">Kuupäev</a></th>
                </tr>
                <?php foreach($ilmad as $ilm): ?>
                    <tr>
                        <?php if(isSet($_REQUEST["muutmisid"]) &&
                            intval($_REQUEST["muutmisid"])==$ilm->id): ?>
                            <td>
                                <input type="submit" name="muutmine" value="Muuda" />
                                <input type="submit" name="katkestus" value="Katkesta" />
                                <input type="hidden" name="muudetudid" value="<?=$ilm->id ?>" />
                            </td>
                            <td><input type="number" name="temperatuur" value="<?=$ilm->temperatuur ?>" step="0.1" min="-40" max="50"/></td>
                            <td><?php
                                echo looRippMenyy("SELECT id, maakonnanimi FROM maakonnad",
                                    "maakonna_id", $ilm->id);
                                ?></td>
                            <td><input type="datetime-local" name="kuupaev" value="<?=$ilm->kuupaev ?>" /></td>
                        <?php else: ?>
                        <?php
                        if ($_SESSION["admin"]==true){
                        ?><td><a href="maahaldus.php?kustutusid=<?=$ilm->id ?>"
                                   onclick="return confirm('Kas ikka soovid kustutada?')">X</a>
                                <a href="maahaldus.php?muutmisid=<?=$ilm->id ?>">M</a>
                            </td><?php }?>
                            <td><?=$ilm->temperatuur ?></td>
                            <td><?=$ilm->maakonnanimi ?></td>
                            <td><?=$ilm->kuupaev ?></td>
                        <?php endif ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>Otsi: <input type="text" name="otsisona" />
    </div>
    </form></div>

</body>
</html>
