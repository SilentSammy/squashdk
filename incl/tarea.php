<?php 
      	   echo "<table >";
      echo '<tr>';
        echo '<td align="center" colspan="3"><br>';
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
        					 $descripcion = $_REQUEST["descripcion"];
        					 $fechaEmpieza = $_REQUEST["fechaEmpieza"];
      						 $fechaEmpieza1 = substr($fechaEmpieza,4,2).'-'.substr($fechaEmpieza,2,2).'-'.substr($fechaEmpieza,0,2);
        					 $hora = $_REQUEST["hora"];
      						 if (strlen($hora)==3) {
      						 		$hora="0".substr($hora,0,1).':'.substr($hora,1,2);
      						 } else {
									 		$hora=substr($hora,0,2).':'.substr($hora,2,2);
									 }
      						 $fechaEmpieza1 = $fechaEmpieza1." ".$hora;
        					 $importancia = $_REQUEST["importancia"];
      						 if (isset($descripcion) and $descripcion!='') {
      						    if (isset($fechaEmpieza) and $fechaEmpieza!='')
      									$insertSQL="INSERT INTO `tareas` (`empleo` , `descripcion` , `fechaEmpieza` , `importancia` , `fechaTerminado` ) VALUES ('$empleo', '$descripcion', '$fechaEmpieza1', '$importancia', '0000-00-00 00:00:00');";
      					      else
      									$insertSQL="INSERT INTO `tareas` (`empleo` , `descripcion` , `fechaEmpieza` , `importancia` , `fechaTerminado` ) VALUES ('$empleo', '$descripcion', now(), '$importancia', '0000-00-00 00:00:00');";
      						    mysql_query($insertSQL);
      						 }			 
        					 $tareaID = $_REQUEST["tareaID"];
      						 if (isset($tareaID) and $tareaID!='') {
      					   		$insertSQL="UPDATE `tareas` SET fechaTerminado=now() WHERE id=".$tareaID;
      						    mysql_query($insertSQL);
      						 }	
        					 
									 $TareaChange = $_REQUEST["TareaChange"];
									 if (isset($TareaChange) and $TareaChange!='') {
									     $date = $_REQUEST["date"];							 
          						 $fechaEmpieza1 = substr($date,4,2).'-'.substr($date,2,2).'-'.substr($date,0,2);
            					 $hora = $_REQUEST["time"];
          						 if (strlen($hora)==3) {
          						 		$hora="0".substr($hora,0,1).':'.substr($hora,1,2);
          						 } else {
    									 		$hora=substr($hora,0,2).':'.substr($hora,2,2);
    									 }
          						 $fechaEmpieza1 = $fechaEmpieza1." ".$hora;

      					   		 $insertSQL="UPDATE `tareas` SET fechaEmpieza='".$fechaEmpieza1."', alert='0000-00-00 00:00:00' WHERE id=".$TareaChange;
      						     mysql_query($insertSQL);
									 
									 }							 
      						 
      						 //echo "<big><b>Bienvenido, ".$rowEmpleo['nombre']."</b></big>";
      						 echo "<br>";
      						 echo "<br>";
      						 echo "<b>Nuevo Tarea</b>";
      						 ?>
      						 
      						 <table border="0" cellpadding="0" cellspacing="0" summary="">
      						 				<tr>
      						 						<td>Fecha:</td>
      						 						<td>Hora:</td>
      						 						<td>Descripcion:</td>
      						    				<td>Importancia:</td>
      						        </tr>
      						 				<form action="<?php $_SERVER[PHP_SELF] ?>" name="nuevaTarea" method="post">
      						 				<tr>
      						 						<td valign="top"><input type="text" name="fechaEmpieza" size="5"/></td>
      						 						<td valign="top"><input type="text" name="hora" size="4"/></td>
      						 						<td valign="top"><input type="text" name="descripcion" size="25" /></td>
      												<td valign="top">
      												<select name="importancia" onChange="document.nuevaTarea.submit()">
      												<option value="">--- Eligir ---</option>												
      												<option value="alta">Alta</option>
      												<option value="media">Media</option>
      												<option value="baja">Baja</option>
      												</select>
      												</td>
      						        </tr>
      										<input type="hidden" name="psw" value="<?php echo $psw ?>" />
      										</form>
      						 </table>
      						 <br>
      						 <table border='1' cellpadding='2' cellspacing='0' summary='' frame='border'>
      						 				<tr>
      												<td><b>En</b></td>
      												<td><b>Fecha</b></td>
      												<td><b>Descripcion</b></td>
      												<td><b>Importancia</b></td>
      												<td></td>
      										</tr>
      										<?php 
        					 				  $idTareaChange = $_REQUEST["idTareaChange"];

      										  $insertSQL="SELECT * FROM tareas WHERE fechaTerminado='0000-00-00 00:00:00' AND importancia='alta' AND empleo='".$rowEmpleo['id']."'ORDER BY fechaEmpieza";
														include "incl/showTask.php";
      										  $insertSQL="SELECT * FROM tareas WHERE fechaTerminado='0000-00-00 00:00:00' AND importancia='media' AND empleo='".$rowEmpleo['id']."'ORDER BY fechaEmpieza";
														include "incl/showTask.php";
														$insertSQL="SELECT * FROM tareas WHERE fechaTerminado='0000-00-00 00:00:00' AND importancia='baja' AND empleo='".$rowEmpleo['id']."'ORDER BY fechaEmpieza";
														include "incl/showTask.php";

      										?>
      						 </table>
      						 
      						 <?php  						 
      					} 
      					?>
      			</center>
      	</td>
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
      
