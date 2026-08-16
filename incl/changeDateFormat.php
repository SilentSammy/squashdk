<?php

function changeDateFormat($date)
{
	if (strlen($date)==10) {
		 //check the format of $longDate
		 //yyyy-mm-dd
		 $year=substr($date,0,4);
		 $strings=substr($date,4,1).substr($date,7,1);
		 $month=substr($date,5,2);
		 $day=substr($date,8,2);
		 if ($year>1900&$year<3000 & $month>0&$month<13 & $day>0&$day<32 & $strings=='--') {
		 		$date=$day.$month.substr($year,2,2); 	 
	   } else {
		 	  echo 'Formato esta incorrecto';
		 }
	} elseif (strlen($date)==6) {
		 $year=substr($date,4,2);
		 $month=substr($date,2,2);
		 $day=substr($date,0,2);
		 if ($year>0&$year<100 & $month>0&$month<13 & $day>0&$day<32) {
		 		$date='20'.$year.'-'.$month.'-'.$day; 	 
	   } else {
		 	  echo 'Formato esta incorrecto';
		 }
	} else {
	   echo 'Formato esta incorrecto';
	}
}

?>