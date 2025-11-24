<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;

$msg = "";

if(isset($_SESSION['uid'])){
 
 
 $id = $_GET['id'];
  include '../../config/database.php';
  include '../../config/config.php';
  include 'header.php';


}
else{

    header("location:../pages/login.php");
}




if(isset($_POST['set'])){

 $pname = isset($_POST['pname']) ? db_escape_string($_POST['pname']) : '';
   $shipdate = isset($_POST['shipdate']) ? db_escape_string($_POST['shipdate']) : '';
   $saddress = isset($_POST['saddress']) ? db_escape_string($_POST['saddress']) : '';
   $sname = isset($_POST['sname']) ? db_escape_string($_POST['sname']) : '';
   $raddress = isset($_POST['raddress']) ? db_escape_string($_POST['raddress']) : '';
   $rname = isset($_POST['rname']) ? db_escape_string($_POST['rname']) : '';
   $email = isset($_POST['email']) ? db_escape_string($_POST['email']) : '';
   $status = isset($_POST['status']) ? db_escape_string($_POST['status']) : '';
   $location = isset($_POST['location']) ? db_escape_string($_POST['location']) : '';
   $pdate = isset($_POST['pdate']) ? db_escape_string($_POST['pdate']) : '';
   
    $remark = isset($_POST['remark']) ? db_escape_string($_POST['remark']) : '';
   
   
   $edd = isset($_POST['edd']) ? db_escape_string($_POST['edd']) : '';
   $weight = isset($_POST['weight']) ? db_escape_string($_POST['weight']) : '';
   $servicetype = isset($_POST['servicetype']) ? db_escape_string($_POST['servicetype']) : '';
   $pdesc = isset($_POST['pdesc']) ? db_escape_string($_POST['pdesc']) : '';
   $qty = isset($_POST['qty']) ? db_escape_string($_POST['qty']) : '';
   
   $image = isset($_FILES['image']['name']) ? db_escape_string($_FILES['image']['name']) : '';
	$target = !empty($image) ? "pimages/".basename($image) : '';
   
   // Validate required fields
   $required_fields = array('pname', 'email', 'sname', 'rname');
   $validation_error = false;
   foreach($required_fields as $field) {
       if(empty($$field)) {
           $validation_error = true;
           break;
       }
   }
   
   if($validation_error) {
       $msg = "Error: Please fill in all required fields (Package Name, Email, Sender Name, Receiver Name)";
   } else {
   
 $pid = substr(str_shuffle("0JHGGSGJHS123456HHDHYDJH789"), 0, 10);
  
    



   $sql = "INSERT INTO track (pname,shipdate,saddress,sname,raddress,rname,email,status,location,pdate,pid,edd,weight,servicetype,pdesc,qty,image,remark) VALUES ('$pname','$shipdate','$saddress','$sname','$raddress','$rname','$email','$status','$location','$pdate','$pid','$edd','$weight','$servicetype','$pdesc','$qty','$image','$remark')";
   
   $query_result = db_query($sql);
   if($query_result){
       
       if(!empty($image) && isset($_FILES['image']['tmp_name'])){
           if(!move_uploaded_file($_FILES['image']['tmp_name'], $target)){
               $msg = "Package added but image upload failed!";
           } else {
               $msg = "Package added successfully!";
           }
       } else {
           $msg = "Package added successfully (no image)!";
       }
      

$sql1 = "INSERT INTO history (pname,shipdate,saddress,sname,raddress,rname,email,status,location,pdate,pid,edd,weight,servicetype,pdesc,qty,image,remark) VALUES ('$pname','$shipdate','$saddress','$sname','$raddress','$rname','$email','$status','$location','$pdate','$pid','$edd','$weight','$servicetype','$pdesc','$qty','$image','$remark')";

$hist_result = db_query($sql1);
if(!$hist_result) {
    error_log("History insert failed for PID: $pid");
}

$sql2 = " INSERT INTO ocontrol (pid) VALUES ('$pid') ";

$ctrl_result = db_query($sql2);
if(!$ctrl_result) {
    error_log("OControl insert failed for PID: $pid");
}

 //send email


require_once "PHPMailer/PHPMailer.php";
require_once 'PHPMailer/Exception.php';


//PHPMailer Object
$mail = new PHPMailer;

//From email address and name
$mail->From = $emaila;
$mail->FromName = $name;

//To address and name
$mail->addAddress($email);
$mail->addAddress("$email"); //Recipient name is optional

//Address to which recipient will reply

//Send HTML or Plain Text email
$mail->isHTML(true);

$mail->Subject = "Shipment Receipt";

$mail->Body = '



  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"><\/script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"><\/script>


<div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;" class="be_container"> 

<div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;"class="be_inner_containr"> <div class="be_header">

<div class="be_logo" style="float: left;"> <img src="https://'.$bankurl.'/admin/pages/logo/'.$logo.'"> <\/div>

<div class="be_user" style="float: right"> <p>Dear: '.$sname.' - Tracking ID: ' .$pid.'<\/p> <\/div> 

<div style="clear: both;"><\/div> 

<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

<h1>Shipment Receipt<\/h1>

<\/div> <\/div> 

<div class="be_body" style="padding: 20px;"> 
<p style="line-height: 25px;"> 




<div class="container">
  
             
  <table class="table table-condensed" >
  
   
        <tr>
        <th>Package Name<\/th>
        <th>Shipment Date<\/th>
        <th>Email<\/th>
      <\/tr>

      <tr>
        <td>'.$pname.'<\/td>
        <td>'.$shipdate .'<\/td>
        <td>'.$email.'<\/td>
      <\/tr>
      
       <tr>
        <th>Sender Address<\/th>
        <th>Sender Name<\/th>
        <th>Date<\/th>
      <\/tr>
      
      <tr>
        <td>'. $saddress.'<\/td>
        <td>'.$sname.'<\/td>
        <td>'.$pdate.'<\/td>
      <\/tr>
      
      <tr>
             <th>Receiver Address<\/th>
        <th>Receiver Name<\/th>
        <th>Package Status<\/th>
      <\/tr>
      
      <tr>
        <td>'. $raddress.'<\/td>
        <td>'.$rname.'<\/td>
        <td>'.$status.'<\/td>
      <\/tr>
      
      
      
      <tr>
             <th>Location<\/th>
        <th>Tracking ID<\/th>
        
      <\/tr>
      
      <tr>
        <td>'. $location.'<\/td>
        <td>'.$pid.'<\/td>
        
      <\/tr>
      
      
    <\/tbody>
  <\/table>
<\/div>

<\/p> <div style="margin-top: 25px;">

 <\/div> <\/div> 
 
 <div class="be_footer">
<div style="border-bottom: 1px solid #ccc;"><\/div> <p> Please do not reply to this email. Emails sent to this address will not be answered. 
Copyright ©2019 '.$name.'. <\/p> <\/div> <\/div> <\/div>';

        if($mail->send()) {
           $msg = "New Package has been added successfully with confirmation email!";
        } else {
           $msg = "Package added but email notification failed: " . $mail->ErrorInfo;
           error_log("Email send error for PID: $pid - " . $mail->ErrorInfo);
        }
   } else {
       $msg = "Failed to add package. Please check all fields are filled correctly.";
       error_log("Track insert failed. SQL: $sql");
   }
   } // Close validation block
        

   
}





if(isset($_POST['uset'])){

   $id = isset($_POST['id']) ? db_escape_string($_POST['id']) : '';
 $pname = isset($_POST['pname']) ? db_escape_string($_POST['pname']) : '';
   $shipdate = isset($_POST['shipdate']) ? db_escape_string($_POST['shipdate']) : '';
   $saddress = isset($_POST['saddress']) ? db_escape_string($_POST['saddress']) : '';
   $sname = isset($_POST['sname']) ? db_escape_string($_POST['sname']) : '';
   $raddress = isset($_POST['raddress']) ? db_escape_string($_POST['raddress']) : '';
   $rname = isset($_POST['rname']) ? db_escape_string($_POST['rname']) : '';
   $email = isset($_POST['email']) ? db_escape_string($_POST['email']) : '';
   $status = isset($_POST['status']) ? db_escape_string($_POST['status']) : '';
   $location = isset($_POST['location']) ? db_escape_string($_POST['location']) : '';
   $pdate = isset($_POST['pdate']) ? db_escape_string($_POST['pdate']) : '';
   $pid = substr(str_shuffle("0JHGGSGJHS123456HHDHYDJH789"), 0, 10);
  
    
     
 
    $sql = "UPDATE settings SET  pname='$pname', shipdate='$shipdate', saddress='$saddress', sname='$sname', raddress='$raddress', rname='$rname', email='$email', status ='$status ', location='$location',pdate='$pdate', pid='$pid' WHERE id = '$id' ";
    
    if(db_query($sql)){
 
     move_uploaded_file($_FILES['logo']['tmp_name'], $target);
       
       $msg = "Settings Updated!";
     }else{
       
       $msg = "Settings Not Updated!";
     }
 }
 
 








    ?>




  <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1><i class="fa fa-home " style="font-size:30px"></i> ADD NEW PACKAGE</h1>
           
          
          </div>


        
   
 
          <hr></hr>
          
        
          
            <div class="box-header with-border">
            
            <?php if($msg != "") echo "<div style='padding:20px;background-color:#dce8f7;color:black'> $msg</div class='btn btn-success'>" ."</br></br>";  ?>
          </br>

     <form class="form-horizontal" action="inmessage.php" method="POST" enctype="multipart/form-data" >

           <legend>Package Adding </legend>
		   
		
 <div class="form-group">
        <input type="text" name="pname" placeholder="Package Name"   class="form-control">
        </div>

     <div class="form-group">
         <label>Shipment date </label>
        <input type="date" name="shipdate" placeholder="Shipment date "  class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="saddress" placeholder="Sender address" class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="sname" placeholder="Sender name"   class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="raddress" placeholder="Receiver address "  class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="rname" placeholder="Receiver name"   class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="email" placeholder="Receiver Email"   class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="status" placeholder="shipment status e.g in transit"   class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="location" placeholder="Location"  class="form-control">
        </div>

      
        
     <div class="form-group">
         <label>Package update date </label>
        <input type="date" name="pdate" placeholder="Package Update Date"   class="form-control">
        </div>


   <div class="form-group">
       <label>Expected delivery date </label>
        <input type="date" name="edd" placeholder="Expected Delivery Date"   class="form-control">
        </div>

   <div class="form-group">
        <input type="text" name="weight" placeholder="Package Weight"   class="form-control">
        </div>

   <div class="form-group">
        <input type="text" name="servicetype" placeholder="Package Servicetype"   class="form-control">
        </div>

   <div class="form-group">
        <input type="text" name="pdesc" placeholder="Package Description"   class="form-control">
        </div>

   <div class="form-group">
        <input type="text" name="qty" placeholder="Package quantity"   class="form-control">
        </div>


   <div class="form-group">
        <input type="file" name="image" placeholder="Package image"   class="form-control">
        </div>

       
     

   <div class="form-group">
        <input type="text" name="remark" placeholder="Remark"   class="form-control">
        </div>

      
      
	  
	  <button style="" type="submit" class="btn btn-primary" name="set" > <i class="fa fa-send"></i>&nbsp; Add Package </button>

    

    </form>


    </div>
   </div>

   </div>
  </div>
  </section>
</div>
<?php
?>
