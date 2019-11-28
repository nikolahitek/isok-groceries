<?php
    require_once 'pdo_setup.php';
    $lid = null;
    $list = null;

    if ($_GET) {
        if (isset($_GET['list'])) {
            $lid = $_GET['list'];

            if ($s = $pdo->prepare('select * from shopping_list where id = ?')) {
                if ($s->execute([$lid])) {
                    $list = $s->fetch();
                }
            }
        }
    }

    if ($_POST) {
        if (isset($_POST['password']) and $list != null) {
            $pass = $_POST['password'];
            if (md5($pass) == $list['secret']) {
                if (isset($_COOKIE['acc'])) {
                    $aids = $_COOKIE['acc'] . ' ' . $list['id'];
                    setcookie('acc', $aids);
                } else {
                    setcookie('acc', '' . $list['id']);
                }
                header("location: products.php?list=" . $list['id']);
                exit();
            }
        }
    }

?>
<html>
<head>
    <title>Access Request</title>
</head>
<body>
    <?php if($list != null): ?>
    <form action="" method="post">
        <label>
            Enter the shopping list '<?= $list['name'] ?>' password:
            <input type="password" name="password">
        </label>
        <button type="submit">Submit</button>
    </form>
    <?php endif;?>
</body>
</html>
