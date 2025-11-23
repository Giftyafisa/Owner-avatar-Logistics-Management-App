<?php

 

 
$sqlu= "SELECT * FROM track  ";
			  $resultu = db_query($sqlu);
			  if(db_num_rows($resultu) > 0){
				  
              $totalu= db_num_rows($resultu);			  }else{
				$totalu = 0  ;
			  }
			  

	
?>


