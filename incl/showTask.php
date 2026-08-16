<?php 
      								 			$resultTarea = mysql_query($insertSQL);
      								 			while ($rowTarea = mysql_fetch_array($resultTarea)) {
      									 					$year = substr($rowTarea['fechaEmpieza'],0,4);
      									 					$month = substr($rowTarea['fechaEmpieza'],5,2);									 
      									 					$day = substr($rowTarea['fechaEmpieza'],8,2);									 
      									 					$hour = substr($rowTarea['fechaEmpieza'],11,2);									 
      									 					$minute = substr($rowTarea['fechaEmpieza'],14,2);									 
      									 					$started = ((int)((mktime ($hour,$minute,0,$month,$day,$year) - time(void))));
      									 					if ($started<0) {
      															 echo "<tr>";
      															 echo "<td style='color: rgb(255, 0, 0)'>";
      															 echo (floor($started/86400)+1)."d".(floor($started%86400/3600)+1)."h".floor($started%86400%3600/60)."m";
      															 echo "</td>";
      															 echo "<td style='color: rgb(255, 0, 0)'>";
																		 if ($idTareaChange==$rowTarea["id"]) {
																		    echo '<form action="hdk.php" name="changeTime" method="post">';
																				echo '<input type="text" size="4" name="date" value="'.$day.$month.substr($year,2,2).'" />';
																				echo '<input type="text" size="4" name="time" value="'.$hour.$minute.'" />';
																				echo '<input type="submit" value="Go" />';
																				echo '<input type="hidden" name="TareaChange" value="'.$idTareaChange.'" />';
																				echo '</form>';
																		 } else {
      															    echo '<a style="text-decoration: none; color: rgb(255, 0, 0)" href="hdk.php?idTareaChange='.$rowTarea["id"].'">'.$day."/".$month."/".substr($year,2,2)." ".$hour.":".$minute.'</a>';
																		 }
																		 
      															 
																		 echo "</td>";
      															 echo "<td style='color: rgb(255, 0, 0)'>";
      															 echo $rowTarea["descripcion"];
      															 echo "</td>";
      															 echo "<td style='color: rgb(255, 0, 0)'>";
      															 echo $rowTarea["importancia"];
      															 echo "</td>";
      															 echo '<td valign="top" style="color: rgb(255, 0, 0)">';
      												
      															 echo '<form action="'.$_SERVER[PHP_SELF].'" method="post" name="tareaTerminado'.$rowTarea["id"].'">';
        														 echo '<input type="hidden" name="psw" value="'.$psw.'" />';
      															 echo '<input type="hidden" name="tareaID" value="'.$rowTarea["id"].'" />';
      															 echo '</form>';
      															 echo '<a href="#" onclick="document.tareaTerminado'.$rowTarea["id"].'.submit()"  style="color: rgb(255, 0, 0)">hecho</a>';										
      															 echo '</td>';
      						 									 echo "</tr>";
      												    } else {
      						 						       echo "<tr>";
      														 	 echo "<td>";
      															 echo floor($started/86400)."d".floor($started%86400/3600)."h".floor($started%86400%3600/60)."m";
      															 echo "</td>";
      															 echo "<td>";
																		 if ($idTareaChange==$rowTarea["id"]) {
																		    echo '<form action="hdk.php" name="changeTime" method="post">';
																				echo '<input type="text" size="4" name="date" value="'.$day.$month.substr($year,2,2).'" />';
																				echo '<input type="text" size="4" name="time" value="'.$hour.$minute.'" />';
																				echo '<input type="submit" value="Go" />';
																				echo '<input type="hidden" name="TareaChange" value="'.$idTareaChange.'" />';
																				echo '</form>';
																		 } else {
      															    echo '<a style="text-decoration: none; " href="hdk.php?idTareaChange='.$rowTarea["id"].'">'.$day."/".$month."/".substr($year,2,2)." ".$hour.":".$minute.'</a>';
																		 }
      															 echo "</td>";
      															 echo "<td>";
      															 echo $rowTarea["descripcion"];
      															 echo "</td>";
      															 echo "<td>";
      															 echo $rowTarea["importancia"];
      															 echo "</td>";
      															 echo '<td valign="top">';
      												
      															 echo '<form action="'.$_SERVER[PHP_SELF].'" method="post" name="tareaTerminado'.$rowTarea["id"].'">';
        														 echo '<input type="hidden" name="psw" value="'.$psw.'" />';
      															 echo '<input type="hidden" name="tareaID" value="'.$rowTarea["id"].'" />';
      															 echo '</form>';
      															 echo '<a href="#" onclick="document.tareaTerminado'.$rowTarea["id"].'.submit()" >hecho</a>';										
      															 echo '</td>';
      						 									 echo "</tr>";
      												    }
      											}
 ?>