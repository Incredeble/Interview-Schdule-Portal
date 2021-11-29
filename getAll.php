<?php
include 'db.php';

$sql = "SELECT * FROM users";
$result = query($sql);
$count  = row_count($result);
if($count>0) {
    $data = array();
    while($row = fetch_array($result)) {
        $data[]= $row;
    }
}
echo json_encode($data);

?>