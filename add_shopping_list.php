<?php
    require_once 'pdo_setup.php';

    if($_POST) {
        if (isset($_POST['name']) and isset($_POST['password']) and isset($_POST['creator'])) {
            $creatorsLists = [];
            $name = $_POST['name'];
            $pass = $_POST['password'];
            $creator = $_POST['creator'];

            if ($s = $pdo->prepare('select name from shopping_list where creator = ?')) {
                if ($s->execute([$creator])) {
                    $creatorsLists = $s->fetchAll();

                    if (checkPresence($name, $creatorsLists)) {
                        echo 'Name already exists.';
                    } else {
                        if ($q = $pdo->prepare('INSERT INTO shopping_list (name, creator, secret) VALUES (?, ?, ?)')) {
                            $q->execute([$name, $creator, md5($pass)]);
                            header('location: index.php');
                        }
                    }
                }
            }

        }
    }

    function checkPresence($listName, $lists) {
        foreach ($lists as $list) {
            if ($list[0] == $listName) {
                return true;
            }
        }
        return false;
    }
?>
<html>
<head>
    <title>Add</title>
</head>
<bod>
    <form action="" method="post">
        <label>
            Name:
            <input type="text" name="name">
        </label>
        <label>
            Password:
            <input type="password" name="password">
        </label>
        <label>
            Email:
            <input type="text" name="creator">
        </label>
        <button type="submit">Submit</button>
    </form>
</bod>
</html>
