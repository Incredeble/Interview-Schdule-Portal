<?php
include 'db.php';
include 'smtp.php';

function deleteInterviewById() {
    if(isset($_POST['id'])) {
        try {
            $id = $_POST['id'];
            $sql = "SELECT * FROM interview where id = $id";
            $result = query($sql);
            //confirm($result);
            while($row = fetch_array($result)) {
                $email1 = $row['email1'];
                $email2 = $row['email2'];
                $startTime = $row['startTime'];
                $endTime = $row['endTime'];
                smtp("delete",$email1,$email2,$startTime,$endTime);
            }
            $sql2 = "DELETE FROM interview WHERE id = $id";
            $result2 = query($sql2);
            confirm($result2);
            return $result2 ? true : false;
        } catch (Exception $error) {
            console.log($error);
            return false;
        }
    }
}
deleteInterviewById();

?>