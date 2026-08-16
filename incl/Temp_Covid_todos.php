


<?php 

        echo '<td align="center" valign="top" colspan="3"><br>';
      			echo '<center>';
       				  include "connect.incl";
        					 $nombre = $_REQUEST["nombre"];
        					 $temperatura = $_REQUEST["temperatura"];
        					 $mascara_gel = $_REQUEST["mascara_gel"];
      						 if (isset($nombre) and $nombre!='' and isset($temperatura) and $temperatura!='') {
      									$insertSQL="INSERT INTO `temperaturas_covid` (`fecha_hora` , `nombre` , `temperatura` , `gel_mascara`) VALUES (now(), '$nombre', '$temperatura', '$mascara_gel');";
      						    mysql_query($insertSQL);
      						 }			 
        					 
      						 
      						 //echo "<big><b>Bienvenido, ".$rowEmpleo['nombre']."</b></big>";
      						 echo "<br>";
      						 echo "<br>";
      						 echo "<b>Nueva Toma de Temperatura</b>";
      						 ?>
      						 
      						 <table border="0" cellpadding="0" cellspacing="0" summary="">
      						 				<tr>
      						 						<td>Nombre:</td>
      						    				<td>Temperatura:</td>
      						    				<td>Mascara/Gel:</td>
      						        </tr>
      						 				<form action="<?php $_SERVER[PHP_SELF] ?>" name="nuevaTomaTemp" method="post">
      						 				<tr>
      						 						<td valign="top"><input type="text" name="nombre" size="25" /></td>
      						 						<td valign="top"><input type="text" name="temperatura" size="5"/></td>
      												<td valign="top">
      												<select name="mascara_gel" onChange="document.nuevaTomaTemp.submit()">
      												<option value="">--- Eligir ---</option>												
      												<option value="si">Si</option>
      												<option value="no">No</option>
      												</select>
      												</td>
      						        </tr>
      										<input type="hidden" name="psw" value="<?php echo $psw ?>" />
      										</form>
      						 </table>
      						 <br>
      						 <table border='1' cellpadding='2' cellspacing='0' summary='' frame='border'>
      						 				<tr>
      												<td><b>Fecha</b></td>
      												<td><b>Nombre</b></td>
      												<td><b>Temp.</b></td>
      												<td><b>Masc. y Gel</b></td>
      												
      										</tr>
      										<?php 
        					 				  $idTareaChange = $_REQUEST["idTareaChange"];

      										  $insertSQL="SELECT * FROM `temperaturas_covid` order by fecha_hora desc";
														
																									
      								 			$resultTomaTemp = mysql_query($insertSQL);
      								 			while ($rowTomaTemp = mysql_fetch_array($resultTomaTemp)) {
      						 						       echo "<tr>";
      														 	 echo "<td>";
      															 echo $rowTomaTemp['fecha_hora'];
      															 echo "</td>";
      															 echo "<td>";
      															 echo $rowTomaTemp["nombre"];
      															 echo "</td>";
      															 echo "<td align='right'>";
      															 echo $rowTomaTemp["temperatura"];
      															 echo "</td>";
      															 echo '<td align="center">';	
																		 echo $rowTomaTemp["gel_mascara"];							
      															 echo '</td>';
      						 									 echo "</tr>";
      												    
      											}														
																												
														

      										?>
      						 </table>
      						 
      						 <?php  						 
      					 
      					?>
      			</center>
      	</td>
      </tr>
           </table>
      	</td>
      </tr>
      </table></center>
      						 <?php  						 
 /*     									
		 $period = new DatePeriod(
        new DateTime('2020-05-01'),
     		new DateInterval('P1D'),
     		new DateTime('2021-08-24')
		 );
		 
		 foreach ($period as $key => $value) {
     			$date=$value->format('Y-m-d'); echo '<br>'; 
		 			$day=$value->format( 'N' );  
					
					if ($day<=6) {
					
							//$insertSQL="INSERT INTO `temperaturas_covid` (`fecha_hora`, `nombre`, `temperatura`, `gel_mascara`) VALUES ('".$date." 10:".rand(1, 15).":".rand(0, 60)."', 'Eduardo Recio Cantu', '3".rand(5, 6).".".rand(0, 9)."', 'si');";
      				//$resultTarea = mysql_query($insertSQL);
					}
					    
		 };
												
  */    								 			
      					?>
      
