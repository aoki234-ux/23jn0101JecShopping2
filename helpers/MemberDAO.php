<?php
require_once 'DAO.php';

class Member {
    public int $memberid;
    public string $email;
    public string $membername;
    public string $zipcode;
    public string $address;
    public string $tel;
    public string $password;
}

class MemberDAO {
    public  function get_member(string $email,string $password) {

        $dbh = DAO::get_db_connect();
		
		$sql = "SELECT * from member where email = :email";
		
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		
		$member = $stmt->fetchObject('Member');

        if($member !== false) {
            if(password_verify($password,$member->password)) {
                return $member;
            }
        }
        return false;
    }

    public function insert(Member $member) {
        $dbh = DAO::get_db_connect();
		
		$sql = "INSERT INTO member(email,membername,zipcode,address,tel,password)
        values(:email,:membername,:zipcode,:address,:tel,:password)";
		
        $password = password_hash($member->password,PASSWORD_DEFAULT);

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(':email', $member->email, PDO::PARAM_STR);
        $stmt->bindValue(':membername', $member->membername, PDO::PARAM_STR);
        $stmt->bindValue(':zipcode', $member->zipcode, PDO::PARAM_STR);
        $stmt->bindValue(':address', $member->address, PDO::PARAM_STR);
        $stmt->bindValue(':tel', $member->tel, PDO::PARAM_INT);
        $stmt->bindValue(':password', $password, PDO::PARAM_INT);
		$stmt->execute();
		

    }
    public function email_exists(String $email) {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * from member 
        where email = :email";


        $stmt = $dbh->prepare($sql);
        $stmt->bindvalue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->fetch() !== false) {
            return true;
        }
        else {
            return false;
        }
    }
}
?>