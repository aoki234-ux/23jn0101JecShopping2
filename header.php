<?php
require_once './helpers/MemberDAO.php';
require_once './helpers/CartDAO.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!empty($_SESSION['member'])) {
    $member = $_SESSION['member'];

    $cartDAO = new CartDAO();
    $cart_list = $cartDAO->get_cart_by_memberid($member->memberid);
    $num=0;
    foreach($cart_list as $cart) {
        $num += $cart->num;
}
}
?>
<header>
    <link href = "css/HeaderStyle.css" rel = "stylesheet">
    <div id = "logo">
    <a href = "index.php">
        <img src = "images/JecShoppingLogo.jpg" alt = "JecShoppingロゴ">
    </a>
    </div>
    <div id = "link">
        <form action = "index.php" method = "get">
            <input type = "text" name = "keyword">
            <input type = "submit" value = "検索">
        </form>
        <?php if(isset($member)): ?>
            <?= $member->membername?>さん
            <a href = "cart.php">カート(<?= $num?>)</a>
            
            <a href = "logout.php">ログアウト</a>
            <?php else: ?>
            <a href = "login.php">ログイン</a>
        <?php endif; ?>
    </div>
    <div id = "clear">
        <hr>
    </div>
</header>
