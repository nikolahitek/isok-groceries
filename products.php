<?php
    require_once 'pdo_setup.php';
    $lid = null;
    $products = [];
    $urgent = [];
    $bought = [];
    $notBought = [];

    if ($_GET) {
        if (isset($_GET['list'])) {
            $lid = $_GET['list'];

            if (!(isset($_COOKIE['acc']) and in_array($lid, explode(' ', $_COOKIE['acc'])))) {
                header("location: access_products.php?list=$lid");
                exit();
            }

            if ($s = $pdo->prepare('select * from products where list_id = ?')) {
                if ($s->execute([$lid])) {
                    $products = $s->fetchAll();
                }
            }

            if (sizeof($products) > 0) {
                foreach ($products as $p) {
                    if ($p['is_bought'] == 1) {
                        array_push($bought, $p);
                    } else if ($p['is_urgent'] == 1) {
                        array_push($urgent, $p);
                    } else {
                        array_push($notBought, $p);
                    }
                }
            }
        }

        if (isset($_GET['move'])) {
            $pid = $_GET['move'];
            $product = getProduct($pid);
            if ($product != null) {
                if ($product['is_bought'] == 1) {
                    if ($s = $pdo->prepare('update products set is_bought = 0, quantity = 1 where id = ?')) {
                        $s->execute([$pid]);
                    }
                } else {
                    if ($s = $pdo->prepare('update products set is_bought = 1 where id = ?')) {
                        $s->execute([$pid]);
                    }
                }
                header('location: products.php?list=' . $lid);
            }
        }
        if (isset($_GET['inc'])) {
            $pid = $_GET['inc'];
            $product = getProduct($pid);
            if ($product != null) {
                if ($s = $pdo->prepare('update products set quantity = ? where id = ?')) {
                    $s->execute([$product['quantity'] + 1, $pid]);
                    header('location: products.php?list=' . $lid);
                }
            }
        }
        if (isset($_GET['dec'])) {
            $pid = $_GET['dec'];
            $product = getProduct($pid);
            if ($product != null and $product['quantity'] > 0) {
                if ($s = $pdo->prepare('update products set quantity = ? where id = ?')) {
                    $s->execute([$product['quantity'] - 1, $pid]);
                    header('location: products.php?list=' . $lid);
                }
            }
        }
    }

    function getProduct($pid) {
        global $products;
        foreach ($products as $p) {
            if ($p['id'] == $pid) {
                return $p;
            }
        }
        return null;
    }

    if ($_POST) {
        if (isset($_POST['name']) and isset($_POST['quantity'])) {
            $isUrgent = isset($_POST['urgent']) ? 1 : 0;
            $name = $_POST['name'];
            $quantity = $_POST['quantity'];

            if ($q = $pdo->prepare('INSERT INTO products (name, list_id, quantity, is_bought, is_urgent, created_at) VALUES (?, ?, ?, ?, ?, ?)')) {
                $q->execute([$name, $lid, $quantity, 0, $isUrgent, '2019-11-14 00:00:00']);
                header('location: products.php?list=' . $lid);
            }
        }
    }

    ?>
<html>
<head>
    <title>Products</title>
    <style>
        th, td {
            padding: 5px 10px;
        }
        a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>name</th>
            <th>quantity</th>
            <th>plus</th>
            <th>minus</th>
        </tr>
        <?php if (sizeof($urgent) > 0): ?>
            <?php foreach ($urgent as $p):?>
                <tr>
                    <td><a href="products.php?list=<?= $lid?>&move=<?= $p['id']?>"><b><?= $p['name'] ?></b></a></td>
                    <td><?= $p['quantity'] ?></td>
                    <td><a href="products.php?list=<?= $lid?>&inc=<?= $p['id']?>">+1</a></td>
                    <td><a href="products.php?list=<?= $lid?>&dec=<?= $p['id']?>">-1</a></td>
                </tr>
            <?php endforeach;?>
        <?php endif;?>

        <?php if (sizeof($notBought) > 0): ?>
            <?php foreach ($notBought as $p):?>
                <tr>
                    <td><a href="products.php?list=<?= $lid?>&move=<?= $p['id']?>"><?= $p['name'] ?></a></td>
                    <td><?= $p['quantity'] ?></td>
                    <td><a href="products.php?list=<?= $lid?>&inc=<?= $p['id']?>">+1</a></td>
                    <td><a href="products.php?list=<?= $lid?>&dec=<?= $p['id']?>">-1</a></td>
                </tr>
            <?php endforeach;?>
        <?php endif;?>

        <?php if (sizeof($bought) > 0): ?>
            <?php foreach ($bought as $p):?>
                <tr>
                    <td><a href="products.php?list=<?= $lid?>&move=<?= $p['id']?>"><strike><?= $p['name'] ?></strike></a></td>
                </tr>
            <?php endforeach;?>
        <?php endif;?>
    </table>

    <form action="" method="post">
        <label>
            Name:
            <input type="text" name="name">
        </label>
        <label>
            Quantity:
            <input type="number" name="quantity">
        </label>
        <label>
            Urgent:
            <input type="checkbox" name="urgent">
        </label>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
