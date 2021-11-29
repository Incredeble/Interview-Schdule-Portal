<?php
include 'db.php';

$sql = "SELECT * FROM interview";
$result = query($sql);
$count  = row_count($result);
$data = array();
if($count>0) {
    while($row = fetch_array($result)) {
        $data[]= $row;
    }
}
echo json_encode($data);

?>