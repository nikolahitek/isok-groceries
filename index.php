<?php
    require_once 'pdo_setup.php';
    $shoppingLists = [];
    $favourites = [];
    $showAll = false;

    if ($s = $pdo->prepare('select * from shopping_list')) {
        if ($s->execute()) {
            $shoppingLists = $s->fetchAll();
        }
    }

    if (isset($_COOKIE['fav'])) {
        $ids = explode(' ', $_COOKIE['fav']);
        foreach ($shoppingLists as $list) {
            if (in_array($list['id'], $ids)) {
                array_push($favourites, $list);
            }
        }
    }

    if($_GET) {
        if (isset($_GET['all']) and $_GET['all'] == 'true') {
            $showAll = true;
        }
        if (isset($_GET['fav'])) {
            $fid = $_GET['fav'];
            if (isset($_COOKIE['fav'])) {
                $fids = $_COOKIE['fav'] . ' ' . $fid;
                setcookie('fav', $fids);
            } else {
                setcookie('fav', $fid);
            }
        }
    }

    unset($pdo);
?>
<html>
<head>
    <title>Shopping Lists</title>
    <style>
        th, td {
            border: black 1px solid;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Products</th>
            <th>Favourite</th>
        </tr>
        <?php if(sizeof($favourites) > 0 and !$showAll):?>
            <?php foreach ($favourites as $list):?>
                <tr>
                    <td><?= $list['id'] ?></td>
                    <td><?= $list['name'] ?></td>
                    <td><a href="products.php?list=<?= $list['id'] ?>"">list</a></td>
                    <td>Added</td>
                </tr>
            <?php endforeach;?>
        <?php else:?>
            <?php foreach ($shoppingLists as $list):?>
            <tr>
                <td><?= $list['id'] ?></td>
                <td><?= $list['name'] ?></td>
                <td><a href="products.php?list=<?= $list['id'] ?>">list</a></td>
                <?php if (in_array($list, $favourites)):?>
                    <td>Added</td>
                <?php else:?>
                    <td><a href="index.php?fav=<?= $list['id'] ?>">add</a></td>
                <?php endif;?>
            </tr>
            <?php endforeach;?>
        <?php endif;?>
    </table>
    <?php if(sizeof($favourites) > 0 and !$showAll):?>
        <a href="index.php?all=true">Show all</a>
    <?php endif;?>
    <a href="add_shopping_list.php">Add Shopping List</a>
</body>
</html>

