<?php 
require_once 'runde.php'; 
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>WBH Infoscreen</title>
    <link rel="stylesheet" href="foyer.css">
</head>
<body>

    <div class="header-bar">
        <h1>Schachbundesliga<br>Zentrale Endrunde</h1>
        <h2>Berlin 2026</h2> 
    </div>

    <div id="viewport">
        <div class="slide active" id="slide-1" data-duration="20000">
            <?php include 'foyer1.php'; ?>
        </div>
        <div class="slide" id="slide-2" data-duration="60000">
            <?php include 'foyer2.php'; ?>
        </div>
    </div>

    <div class="footer-bar">
        <img src="img/UKA_Logo.png" class="logo-uka-footer">
    </div>

    <script>
        const RUNDE = <?php echo $runde < 13 ? 13 : $runde; ?>;
        const DATUM_TEXT = "<?php echo $datumText; ?>";
    </script>
    <script src="foyer.js"></script>
</body>
</html>
