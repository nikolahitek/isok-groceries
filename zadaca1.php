<?php
    if ($_POST) {
        if (isset($_POST['text'])) {
            $text = $_POST['text'];

            $prices = [];
            preg_match_all('/\$[0-9]+\.?[0-9]*/', $text, $prices);

            $sum = 0;
            foreach ($prices[0] as $p) {
                $sum = $sum + substr($p, 1);
            }
            echo $sum;
        }
    }
?>
<html>
<head>
    <title>
        Zadaca 1
    </title>
</head>
<body>
    <form action="" method="post">
        <label>
            Text:
            <textarea name="text" rows="20" cols="20"></textarea>
        </label>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
