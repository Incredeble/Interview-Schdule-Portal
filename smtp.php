<?php
include('smtp/PHPMailerAutoload.php');

function smtp_mailer($to,$subject,$interviewerName,$intervieweeName,$startTime,$endTime){
	$msg= "<p>You have new interview scheduled.</p>
			<p>Details:</p>
			<p>interviewer name : ${interviewerName}</p>
			<p>interviewee name : ${intervieweeName}  </p>
			<p>start time: $startTime </p>
			<p>end time: $endTime </p>";
	$mail = new PHPMailer(); 
	$mail->SMTPDebug  = 3;
	$mail->IsSMTP(); 
	$mail->SMTPAuth = true; 
	$mail->SMTPSecure = 'tls'; 
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587; 
	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	$mail->Username = "xyz@gmail.com";
	$mail->Password = 'password';
	$mail->SetFrom("xyz@gmail.com");
	$mail->Subject = $subject;
	$mail->Body =$msg;
	$mail->AddAddress($to);
	$mail->SMTPOptions=array('ssl'=>array(
		'verify_peer'=>false,
		'verify_peer_name'=>false,
		'allow_self_signed'=>false
	));
	if(!$mail->Send()){
		echo $mail->ErrorInfo;
	}else{
		return 'Sent';
	}

}

function smtp($command,$email1,$email2,$startTime,$endTime) {
	$interviewerName = explode("(",$email1)[0]; 
	$interviewee = explode("(",$email2); 
	$interviewerName = $interviewee[0];
	$intervieweeEmail = $interviewee[1]; 
	$email = explode(")",$intervieweeEmail)[0]; 
	if($command=="new") {
		$subject = "Interview Schedule";
		echo smtp_mailer($email,$subject,$interviewerName,$intervieweeName,$startTime,$endTime);
	}
	else if($command=="update") {
		$subject = "Update Interview Schedule";
		echo smtp_mailer($enail,$subject,$interviewerName,$intervieweeName,$startTime,$endTime);
	}
	else {
		$subject = "Cancel Interview Schedule";
		echo smtp_mailer($email,$subject,$interviewerName,$intervieweeName,$startTime,$endTime);
	}	
}

	// smtp("new","e(e@gmail.com)","m(m@gmail.com)","startTime","endTime");

?>