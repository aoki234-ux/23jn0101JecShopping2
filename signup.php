<?php

require_once './helpers/MemberDAO.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = $_POST['email'];
$password = $_POST['password'];
$password2 = $_POST['password2'];
$membername = $_POST['membername'];
$zipcode = $_POST['zipcode'];
$address = $_POST['address'];
$tel1 = $_POST['tel1'];
$tel2 = $_POST['tel2'];
$tel3 = $_POST['tel3'];

$memberDAO = new MemberDAO();

if(!preg_match('/\A\d{3}-\d{4}\z/',$zipcode)) {
$errs['zipcode'] = '郵便番号は3桁-4桁で入力してください。';

}
if(isset($_POST['register'])){
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $errs['email'] = 'メールアドレスの形式が正しくありません。';
    }else if($memberDAO->email_exists($email) === true) {
        $errs['email'] = 'このメールアドレスは既に登録されています。';
    }
    
    if(!preg_match('/\A.{4,}\z/',$password)) {
        $errs['password'] = 'パスワードは4文字以上で入力してください。';
    }else if($password !== $password2) {
    $errs['password'] = 'パスワードが一致しません。';
    }
    
    if($membername === '') {
        $errs['membername'] = 'お名前を入力してください。';
    }
    
    if(!preg_match('/\A\d{3}-\d{4}\z/',$zipcode)) {
    $errs['zipcode'] = '郵便番号は3桁-4桁で入力してください。';
    }
    
    if($address === '') {
        $errs['address'] = '住所を入力してください。';
    }
    
    if(!preg_match('/\A(\d{2,5})?\z/',$tel1) 
    || !preg_match('/\A(\d{1,4})?\z/',$tel2)
    || !preg_match('/\A(\d{4})?\z/',$tel3)) {
        $errs['tel'] = '電話番号は半角数字2~5桁、1~4桁、4桁で入力してください。';
    }
    

if(empty($errs)) {
$member = new Member();
$member->email=$email;
$member->password=$password;
$member->membername=$membername;
$member->zipcode=$zipcode;
$member->address=$address;

$member->tel='';
if($tel1 !== '' && $tel2 !== '' && $tel3 !== ''){
$member->tel ="{$tel1}-{$tel2}-{$tel3}";
}

$memberDAO->insert($member);

header('Location:signupEnd.php');
exit;
    }
}else if(isset($_POST['search'])) {
    $zipcode2 = preg_replace("/[^0-9]/","",$zipcode);
    $url = "https://zipcloud.ibsnet.co.jp/api/search?zipcode=".$zipcode2;    // WebAPIのURL
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_PROXY, "http://proxy00.jec.ac.jp:8080/"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $data = curl_exec($ch);
        $json = json_decode($data);
        curl_close($ch);
        $i=0;
}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset = "utf-8">
    <title>新規会員登録</title>
</head>
<body>
<?php include 'header.php';?>

<h1>会員登録</h1>
<p> 以下の項目を入力し、登録場端をクリックしてください。（*は必須）</p>
<form action = "signup.php" method = "POST">
    <table>
    <tr>
        <td>メールアドレス*</td>
        <td><input type = "email" name = "email" value = "<?= @$email?>" >
            <span style = "color:red"><?= @$errs['email'] ?></span></td>
    </tr>
    <tr>
        <td>パスワード(4文字以上)*</td>
        <td><input type = "password" name = "password" value = "<?= @$password?>" minlength = "4" >
        <span style = "color:red"><?= @$errs['password']?></span></td>
    </tr>
    <tr>
        <td>パスワード(再入力)*</td>
        <td><input type = "password" name = "password2"></td>
    </tr>
    <tr>
        <td>お名前*</td>
        <td><input type = "text" name = "membername" value = "<?=@$membername?>" >
        <span style = "color:red"><?= @$errs['membername']?></span></td>
    </tr>
    <tr>
        <form action = "" method = "POST">
        <td>郵便番号* </td>
        <td><input type = "text" name = "zipcode" value = "<?= @$zipcode?>" pettern = "\d{3}-\d{4}" title = "郵便番号は3桁-4桁でハイフン(-)を入れて入力してください">
        <span style = "color:red"><?= @$errs['zipcode']?></span>
        <input type = "submit" name = "search" value = "住所検索"></td>
</form>
    </tr>
    <tr>
        <td>住所*</td>
        <?php if(!empty($json->results)) :?>
         <td><?php foreach($json->results as $data) {?>
            <?php $ret = $data->address1.$data->address2.$data->address3;?>
             <input type = "text"name = "address" value = "<?= @$ret?>">
           <?php }?></td>  
         <?php else :?> 
        <td><input type = "text"name = "address" value = "">
        <span style = "color:red"><?= @$errs['address']?></span></td>
        <?php endif ;?>
    </tr>
    <tr>
        <td>電話番号</td>
        <td>
            <input type = "tel" name = "tel1" value = "<?=@$tel1?>" size = "4" >-
            <input type = "tel" name = "tel2" value = "<?=@$tel2?>" size = "4" >-
            <input type = "tel" name = "tel3" value = "<?=@$tel3?>" size = "4" >
            <span style = "color:red"><?= @$errs['tel']?></span>
        </td>
    </tr>
    </table>
    <input type = "submit" name = "register" value = "登録する">
</form>
</body>
</html>
