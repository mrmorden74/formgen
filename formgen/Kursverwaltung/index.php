<?php
include ('inc/utilities.inc.php');
$titel = getTitel();
$links = getLinks();
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
    <ol>
        <?php
        foreach ($links As $name => $link) {
            $name = ucfirst($name);
        ?>
        <li><a href="<?= '/formgen/'.$titel.$link ?>"><?= $name ?></a></li>
        <?php
        }
        ?>
    </ol>
</body>
</html>