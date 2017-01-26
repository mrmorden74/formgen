<?php
include ('inc/utilities.inc.php');
include ('inc/db-connect.inc.php');
$titel = getTitel();
$links = getLinks();
$SrvId = getSrvId();
$DbId = getDbId();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= $titel ?></title>
</head>
<body>
    <h1><?= $titel ?></h1>
    <h2>Formularliste</h2>
    <ul>
        <?php
        foreach ($links As $name => $link) {
            $name = ucfirst($name);
        ?>
        <li><a href="<?= '/formgen/'.$titel.$link ?>"><?= $name ?></a></li>
        <?php
        }
        ?>
    </ul>
        <br>
        <br>
    <a href="<?= '/showFrms/'.$SrvId.'/'.$DbId ?>">Formulargenerator</a>
</body>
</html>