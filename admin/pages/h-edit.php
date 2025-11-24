
<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;


 
 $ids = isset($_GET['id']) ? db_escape_string($_GET['id']) : '';
 

if(isset($_SESSION['uid'])){
 

 
  include '../../config/database.php';
  include '../../config/config.php';
  include 'header.php';


  $msg = "";

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
 $pid = isset($_POST['pid']) ? db_escape_string($_POST['pid']) : '';
  $ids = isset($_GET['id']) ? db_escape_string($_GET['id']) : '';
  
    



   $sql = "UPDATE history SET  pname = '$pname',shipdate = '$shipdate',saddress = '$saddress',sname = '$sname' ,raddress = '$raddress',rname = '$rname',email = '$email', status = '$status',location = '$location', pdate = '$pdate' ,pid = '$pid' WHERE id = '$ids'";
   
   if(db_query($sql)){
      

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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>


<div style="background: #f5f7f8;width: 100%;height: 100%; font-family: sans-serif; font-weight: 100;" class="be_container"> 

<div style="background:#fff;max-width: 600px;margin: 0px auto;padding: 30px;"class="be_inner_containr"> <div class="be_header">

<div class="be_logo" style="float: left;"> <img src="https://scriptsdemo.website/sms-bank-script/3d_bank/admin/pages/logo/logo.png"> </div>

<div class="be_user" style="float: right"> <p>Dear: '.$sname.' - Tracking ID: ' .$pid.'</p> </div> 

<div style="clear: both;"></div> 

<div class="be_bluebar" style="background: #1976d2; padding: 20px; color: #fff;margin-top: 10px;">

<h1>Shipment Receipt</h1>

</div> </div> 

<div class="be_body" style="padding: 20px;"> 
<p style="line-height: 25px;"> 




<div class="container">
  
             
  <table class="table table-condensed" >
  
   
        <th>Package Name</th>
        <th>Shipment Date</th>
        <th>Email</th>
      </tr>

      <tr>
        <td>'.$pname.'</td>
        <td>'.$shipdate .'</td>
        <td>'.$email.'</td>
      </tr>
      <tr>
      
      
       <tr>
        <th>Sender Address</th>
        <th>Sender Name</th>
        <th>Date</th>
      </tr>
      
        <td>'. $saddress.'</td>
        <td>'.$sname.'</td>
        <td>'.$pdate.'</td>
      </tr>
      <tr>
      
      
      
      <tr>
             <th>Receiver Address</th>
        <th>Receiver Name</th>
        <th>Package Status</th>
      </tr>
      
        <td>'. $raddress.'</td>
        <td>'.$rname.'</td>
        <td>'.$status.'</td>
      </tr>
      
      
      
      <tr>
             <th>Location</th>
        <th>Tracking ID</th>
        
      </tr>
      
        <td>'. $location.'</td>
        <td>'.$pid.'</td>
        
      </tr>
      
      
    </tbody>
  </table>
</div>

</p> <div style="margin-top: 25px;">

 </div> </div> 
 
 <div class="be_footer">
<div style="border-bottom: 1px solid #ccc;"></div> <p> Please do not reply to this email. Emails sent to this address will not be answered. 
Copyright ©2019 '.$name.'. </p> </div> </div> </div>';

if($mail->send()) {
   
   $msg = "Package history updated successfuly!";
}
               
           else{
               $msg = "Email send error!";
            }
        

   
}


}
 
 








    ?>




  <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1><i class="fa fa-home " style="font-size:30px"></i> EDIT HISTORY</h1>
           
          
          </div>


        
   
 
          <hr></hr>
          
        
          
            <div class="box-header with-border">
            
            <?php if($msg != "") echo "<div style='padding:20px;background-color:#dce8f7;color:black'> $msg</div class='btn btn-success'>" ."</br></br>";  ?>
          </br>


 <?php 
 
 $sql= "SELECT * FROM history WHERE id='$ids'";
			  $result = db_query($sql);
			  if(db_num_rows($result) > 0){
				 $row = db_fetch_assoc($result); 
				  if(isset($row['id'])){
$id = $row['id'];				      
$pid = $row['pid'];					
 $pname = $row['pname'];
   $shipdate = $row['shipdate'];
   $saddress = $row['saddress'];
   $sname = $row['sname'];
   $raddress = $row['raddress'];
   $rname = $row['rname'];
   $email = $row['email'];
   $status = $row['status'];
   $location = $row['location'];
   $pdate = $row['pdate'];
   
					  
				  }else{
					}
?>

     <form class="form-horizontal" action="h-edit.php?id=<?php echo $id;?>" method="POST" enctype="multipart/form-data" >

           <legend>History Update</legend>
		   
		
        </div>
 <div class="form-group">
        <input type="text" name="pname"  value="<?php echo $pname;?>" readonly class="form-control">
        
        <input type="hidden" name="id"  value="<?php echo $id;?>" readonly class="form-control">
        </div>

     <div class="form-group">
        <input type="text" name="shipdate " readonly  value="<?php echo $shipdate;?>"  class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="saddress" readonly  value="<?php echo $saddress;?>" class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="sname"  readonly value="<?php echo $sname;?>"   class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="raddress " readonly value="<?php echo $raddress;?>"    class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="rname"  readonly value="<?php echo $rname;?>"  class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="email" readonly value="<?php echo $email;?>"   class="form-control">
        </div>

        <div class="form-group">
        <input type="text" name="status"   placeholder="status" class="form-control">
        </div>
        
     <div class="form-group">
        <input type="text" name="location" placeholder="location"  class="form-control">
        </div>

      <div class="form-group">
        <input type="text" name="pid" value="<?php echo $pid;?>" readonly class="form-control">
        </div>
        
     <div class="form-group">
        <input type="date" name="pdate" placeholder="history date"   class="form-control">
        </div>

       
     

      


    <button style="" type="submit" class="btn btn-success" name="set" > <i class="fa fa-send"></i>&nbsp; Edit history </button>

    </form>

<?php    
          
          }
?>


    </div>
   </div>

   </div>
  </div>
  </section>
</div>

