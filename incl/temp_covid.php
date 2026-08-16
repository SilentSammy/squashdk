


<?php 

        echo '<td align="center" valign="top" colspan="3"><br>';
      			echo '<center>';
       				  include "incl/connect.incl";
      					$resultEmpleo = mysql_query("SELECT * FROM Empleos WHERE contrasena='$psw'");
      					$rowEmpleo = mysql_fetch_array($resultEmpleo);
      					if (isset($psw)==0 or $rowEmpleo['nombre']=='') {
      						 if (isset($psw)) {
      						 		echo 'Usuario no existe!! ';
      						 }
      						 echo '<form name="password" action="hdk.php" method="post">';
      						 echo	'<input type="password" name="psw"/><input type="submit" name="tareas" value="Entrar"/>';
      						 echo '</form>';
      					
      					} else {
        					 $empleo = $rowEmpleo["id"];
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

      										  $insertSQL="SELECT * FROM `temperaturas_covid` order by fecha_hora desc limit 0,15";
														
      								 			$resultTomaTemp = mysql_query($insertSQL);
														$today_add=0;
      								 			while ($rowTomaTemp = mysql_fetch_array($resultTomaTemp)) {
																		 if ($today_add==0) {
																		 												
																		   $today_add=1;
																			 //echo strtotime($rowTomaTemp['fecha_hora']).' '.strtotime($hoy);
  																		 if (strtotime($rowTomaTemp['fecha_hora'])<=strtotime($hoy)) {
  																		 		 echo "<tr><td colspan=4 align=center style='color: #ff0000; font-size: 24pt'>Medir Temperatura!!</td></tr>";
  																		 }
																		 }
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
      					} 
      					?>
								
								<br><a href="incl/Temp_Covid_todos.php">Todos</a>
      			</center>
      	</td>
      </tr>
           </table>
      	</td>
      </tr>
      </table></center>
      						 <?php  						 
      										  $insertSQL="SELECT * FROM tareas WHERE fechaTerminado='0000-00-00 00:00:00' AND importancia='alta' AND empleo='".$rowEmpleo['id']."'ORDER BY fechaEmpieza";
      								 			$resultTarea = mysql_query($insertSQL);
      								 			while ($rowTarea = mysql_fetch_array($resultTarea)) {
      									 					$year = substr($rowTarea['fechaEmpieza'],0,4);
      									 					$month = substr($rowTarea['fechaEmpieza'],5,2);									 
      									 					$day = substr($rowTarea['fechaEmpieza'],8,2);									 
      									 					$hour = substr($rowTarea['fechaEmpieza'],11,2);									 
      									 					$minute = substr($rowTarea['fechaEmpieza'],14,2);									 
      									 					$started = ((int)((mktime ($hour,$minute,0,$month,$day,$year) - time(void))));
      									 					if ($started<0) {
      														   if ($rowTarea["alert"]=='0000-00-00 00:00:00') {
      						 								      echo '<script language="JavaScript">';
      						 											echo "window.open('alert/alert.php?id=".$rowTarea["id"]."&descripcion=".$rowTarea["descripcion"]."', '".substr($rowTarea["descripcion"],10)."','width=500,height=100'); window.location.href = sURL;";
      						 											echo "</script>";
      										  						$insertSQL="UPDATE tareas SET alert=NOW() where id=".$rowTarea['id'];
      								 									mysql_query($insertSQL);
      															 }
      														}
      											}
      					?>
      
