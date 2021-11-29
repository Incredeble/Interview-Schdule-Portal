<?php
include 'db.php';
include 'smtp.php';

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
        return false;
    }
}


function insert() {
    if(isset($_POST['email1']) && isset($_POST['email2']) && isset($_POST['startTime']) && isset($_POST['endTime'])) {
        try {
            $id=-1;
            $email1     = $_POST['email1'];
            $email2     = $_POST['email2'];
            $startTime  = $_POST['startTime'];
            $endTime    = $_POST['endTime'];
            $check1 = checkAvailability($email1,$startTime,$endTime,$id);
            $check2 = checkAvailability($email2,$startTime,$endTime,$id);
            if($check1) {
                //echo "Interviewer not availableat that time";
                $data = array("id"=>-1);
                echo json_encode($data);
            }
            else if($check2) {
                echo "Interviewe2 not availableat that time";
                $data = array("id"=>-2);
                echo json_encode($data);
            }
            else {
                smtp("new",$email1,$email2,$startTime,$endTime);
                $sql='INSERT INTO interview(email1,email2,startTime,endTime)  VALUES("'.$email1.'","'.$email2.'","'.$startTime.'","'.$endTime.'")';
                $result = query($sql);
                $data = array("id"=>2);
                echo json_encode($data);
                
            }
        }
        catch(Exception $error) {
            $data = array("id"=>"error");
            echo json_encode($data);
        }
    }
}

insert();

?>