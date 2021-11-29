<?php 
include 'db.php';

function checkAvailability($email,$startTime,$endTime,$id) {
    try {
        $check1 = 0;
        $check2 = 0;
        $sql1 = "SELECT COUNT(*) FROM interview WHERE email1 = $email and id != $id and (startTime > $endTime or endTime < $startTime)";
        $result1 = query($sql1);
        if($result1) {
            $check1 = row_count($result1);
        }

        $sql2 = "SELECT COUNT(*) FROM interview WHERE email2 = $email and id != $id and (startTime > $endTime or endTime < $startTime)";
        $result2 = query($sql2);
        if($result2) {
            $check2 = row_count($result2);
        }

        return ($check1>0 || $check2>0);
    }
    catch (Exception $error) {
        echo "fail";
    }
}

function updateInterviewById() {
    try {
        $id        = $_POST['id'];
        $email1    = $_POST['email1'];
        $email2    = $_POST['email2'];
        $startTime = $_POST['startTime'];
        $endTime   = $_POST['endTime'];
        $check1 = checkAvailability($email1, $startTime, $endTime, $id);
        $check2 = checkAvailability($email2, $startTime, $endTime, $id);

        //console.log("UpdateIn",id, email1,email2,start,end);
        if($check1) {
            $data = array("id"=>-1);
            echo json_encode($data);
        }
        else if($check2) {
            $data = array("id"=>-2);
            echo json_encode($data);
        }
        else {
            smtp("update",$email1,$email2,$startTime,$endTime);
            $sql = 'UPDATE interview SET startTime = "'.$startTime.'", endTime = "'.$endTime.'" WHERE id = "'.$id.'"';
            $result = query($sql);
            confirm($result);
            // const ms = mailService.getMailServiceInstance();
            // ms.update(email1, email2, startTime, endTime);
            $data = array("id"=>1);
            echo json_encode($data);
        }
    } catch (Exception $error) {
        echo "errors";
    }
}

updateInterviewById();

?>