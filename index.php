<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
// In PHP earlier then 4.1.0, $HTTP_POST_FILES  should be used instead of $_FILES.
header('Content-Type: text/html; charset=iso-8859-1');

$id = $_REQUEST["id"];
$nombre = $_REQUEST["nombre"];
$fecha_nac = $_REQUEST["fecha_nac"];
$fecha_nac = substr($fecha_nac,4,2).'-'.substr($fecha_nac,2,2).'-'.substr($fecha_nac,0,2);
$ID_no = $_REQUEST["ID_no"];
$calle = $_REQUEST["calle"];
$noExt = $_REQUEST["no_calle"];
$noInt = $_REQUEST["noInt"];
$colonia = $_REQUEST["colonia"];
$ciudad = $_REQUEST["ciudad"];
$estado = $_REQUEST["estado"];
$cp = $_REQUEST["cp"];
$pais = $_REQUEST["pais"];
$whatsapp = $_REQUEST["whatsapp"];
$tlf = $_REQUEST["tlf"];
$movil = $_REQUEST["movil"];
$correoElectronico = $_REQUEST["correoElectronico"];
$responsable = $_REQUEST["responsable"];
$tipo = $_REQUEST["tipo"];
$personaContacto = $_REQUEST["personaContacto"];
$tlfContacto = $_REQUEST["tlfContacto"];
$agregar = $_REQUEST["agregar"];
$edit = $_REQUEST["edit"];
$delete = $_REQUEST["delete"];

$AC_palabraBusca = $_REQUEST["AC_palabraBusca"];
if ($AC_palabraBusca!='')
   $palabraBusca=$AC_palabraBusca;
else	 
   $palabraBusca = $_REQUEST["palabraBusca"];

$buscar = $_REQUEST["buscar"];
if (isset($palabraBusca)==0) {
   $palabraBusca=$nombre;
}

if(isset($edit)) {
   include "incl/connect.incl";
   if ($nombre!='') {
      $insertSQL = "update Contactos set nombre='$nombre' where id=$id";
      mysql_query($insertSQL);
   }
   if ($fecha_nac!='') {
      $insertSQL = "update Contactos set fecha_nac='$fecha_nac' where id=$id";
      mysql_query($insertSQL);
   }
   if ($ID_no!='') {
      $insertSQL = "update Contactos set ID_no='$ID_no' where id=$id";
      mysql_query($insertSQL);
   }
   if ($personaContacto!='') {
      $insertSQL = "update Contactos set personaContacto='$personaContacto' where id=$id";
      mysql_query($insertSQL);
   }
   if ($tlfContacto!='') {
      $insertSQL = "update Contactos set tlfContacto='$tlfContacto' where id=$id";
      mysql_query($insertSQL);
   }
   if ($calle!='') {
      $insertSQL = "update Contactos set calle='$calle' where id=$id";
			mysql_query($insertSQL);
   }
   if ($noExt!='') {
      $insertSQL = "update Contactos set noExt='$noExt' where id=$id";
			mysql_query($insertSQL);
   }
   if ($noInt!='') {
      $insertSQL = "update Contactos set noInt='$noInt' where id=$id";
			mysql_query($insertSQL);
   }
   if ($colonia!='') {
      $insertSQL = "update Contactos set colonia='$colonia' where id=$id";
			mysql_query($insertSQL);
   }
   if ($ciudad!='') {
      $insertSQL = "update Contactos set ciudad='$ciudad' where id=$id";
      mysql_query($insertSQL);
   }
   if ($estado!='') {
      $insertSQL = "update Contactos set estado='$estado' where id=$id";
      mysql_query($insertSQL);
   }
   if ($cp!='') {
      $insertSQL = "update Contactos set cp='$cp' where id=$id";
      mysql_query($insertSQL);
   }
   if ($pais!='') {
      $insertSQL = "update Contactos set pais='$pais' where id=$id";
      mysql_query($insertSQL);
   }
   if ($whatsapp!='') {
      $insertSQL = "update Contactos set whatsapp='$whatsapp' where id=$id";
      mysql_query($insertSQL);
   }
   if ($tlf!='') {
      $insertSQL = "update Contactos set tlf='$tlf' where id=$id";
      mysql_query($insertSQL);
   }
   if ($movil!='') {
      $insertSQL = "update Contactos set movil='$movil' where id=$id";
      mysql_query($insertSQL);
   }
   if ($correoElectronico!='') {
      $insertSQL = "update Contactos set correoElectronico='$correoElectronico' where id=$id";
      mysql_query($insertSQL);
   }
   if ($responsable!='') {
      $insertSQL = "update Contactos set responsable='$responsable' where id=$id";
      mysql_query($insertSQL);
   }
   if ($tipo!='') {
      $insertSQL = "update Contactos set tipo='$tipo' where id=$id";
      mysql_query($insertSQL);
   }
   mysql_close($conn);
}
if (isset($delete))
{
   include "incl/connect.incl";
   $insertSQL = "delete from Contactos where id=$id";
   mysql_query($insertSQL);
   mysql_close($conn);
}  
if (isset($agregar)) {
   include "incl/connect.incl";
	 if ($cp=='')
	 		$cp=0;
	 $codigo;
   $insertSQL = "INSERT INTO `contactos` ( `responsable` , `nombre` , `fecha_nac` , `ID_no`, `personaContacto` ,`tlfContacto` , `calle` , `noExt` , `noInt` , `colonia` , `ciudad` , `estado` , `cp` , `pais` , `whatsapp` ,`tlf` , `movil` , `correoElectronico` ) VALUES ( '$responsable', '$nombre', '$fecha_nac', '$ID_no', '$personaContacto', '$tlfContacto', '$calle', '$noExt', '$noInt', '$colonia', '$ciudad', '$estado', '$cp', '$pais', '$whatsapp', '$tlf', '$movil', '$correoElectronico')";
   mysql_query($insertSQL);
   mysql_close($conn);
}


?>
<html>
<head>








		<style>
			@import url( ../incl/css/page.css );
			@import url( ../incl/css/tabsexamples.css );
			@import url( ../incl/css/SyntaxHighlighter.css );
			@import url( ../incl/css/dropdown.css );
		</style>
<script src="../incl/js/modomevent3.js"></script>
<script src="../incl/js/modomt.js"></script>
<script src="../incl/js/modomext.js"></script>
<script src="../incl/js/tabs2.js"></script>
<script src="../incl/js/getobject2.js"></script>
<script src="../incl/js/xmlextras.js"></script>
<script src="../incl/js/acdropdown.js"></script>
		<!-- syntax highlight -->
<script language="javascript" src="../incl/js/shCore.js" ></script >
<script language="javascript" src="../incl/js/shBrushXML.js" ></script >
<!-- syntax highlight -->
<?php 

   include "incl/connect.incl";
	 $insertSQL="SELECT nombre FROM Contactos ORDER BY nombre";
   $result = mysql_query($insertSQL);
	 while ($row = mysql_fetch_array($result)) {

	    $clients_string.="'".$row['nombreEmpresa']."',";
	 
	 }
	 $clients_string=substr($clients_string,0,strlen($clients_string)-1);
 ?>



<title>Contactos</title>
<link rel="stylesheet" type="text/css" href="fonts.css" />
</head>
<body onLoad="self.focus();document.busca.palabraBusca.focus()">
<center>
<h1><b>SQUASH DK</b></h1><br>

</center>
<center>

<form action="index.php" name="busca">
<?php 
//<input name="palabraBusca" class="dropdown" autocomplete="off" id="inputer16" style="width: 250px;" acdropdown="true" autocomplete_list="array:clients" autocomplete_format="formatCountries" autocomplete_onselect="alertSelected" autocomplete_matchbegin="false">
 ?>




<table border="0" cellpadding="1" cellspacing="0" summary="" >
<tr valign="top">
<td valign="top">
<b>Contactos: </b><input type="text" name="palabraBusca" value="">
<input type="submit" name="buscar" value="Buscar"></form>
</td>
<td valign="top">
<form action="agregarContacto.php" method="post"><input type="submit" name="add" value="+"></form>
</td>
</tr>
</table>
<center></center>
<?php 
	 $now = (new \DateTime())->format( 'Y-m-d H:i:s' );
	 $now_time = strtotime($now);
		
		$insertSQL = "select * from actividades where tipo = 'Rent' and fecha > '".$now."' order by fecha limit 20";
    $result = mysql_query($insertSQL);
	 
		$insertSQL = "select id from actividades where fecha > '".$now."' order by fecha limit 1";
    $result_nextup = mysql_query($insertSQL);
		$row_nextup = mysql_fetch_array($result_nextup);

	 echo '<table  border="1" cellpadding="3" cellspacing="0" summary="" frame="border" >';
	 echo '<thead><th colspan="4"><b><center>Proximas Actividades</center></b></th></thead>';
	 echo '<thead><th>Fecha</th><th>Jugador</th><th>Duracion</th><th>Cancha</th></thead>';
	 
   
	  while ($row = mysql_fetch_array($result)) {

   echo '<tr><td>';
	   if ($row["tipo"]=="Pago")
	   		echo substr($row["fecha"],0,10);
		 else {
		 		$fecha_time=strtotime($fecha);
				if ($row_nextup["id"]==$row["id"])
	   			 echo '<b><big>'.substr($row["fecha"],0,16).'-'.substr($row["final"],11,5).'</big></b>';
				else
				   echo substr($row["fecha"],0,16);
		 }
	 echo '</td>';
   echo '<td>';
	 		$insertSQL = "select * from contactos where id=".$row['contacto'];
			$result_contacto = mysql_query($insertSQL);
			$row_contacto = mysql_fetch_array($result_contacto);
			echo $row_contacto['nombre'];
	 echo '</td>';
   echo '<td align="center">';
	 if ($row["duracion"]!=0)
		 echo $row["duracion"]; 
	 else
	 		echo '-';
	 echo '</td>';
   echo '<td align="center">';
	 if ($row["cancha"]!=0)
		 echo $row["cancha"]; 
	 else
	 		echo '-';
	 echo '</td>';
	 echo '</tr>';
	 
	 
	 }
   echo '</table><br><br>';


 ?>

<table border="1" cellpadding="2" cellspacing="1" summary="" frame="border" >
<tr>
<td><b>Nombre</b></td>
<td><b>Fecha de Nac.</b></td>
<td><b>WhatsApp</b></td>
<td><b>E-mail</b></td>
<td><b>Saldo</b></td>
<td><b>Prox. Reserv</b></td>
</tr>
<?php
//
   include "incl/connect.incl";
	 $insertSQL="SELECT * FROM Contactos ORDER BY nombre";
   $result = mysql_query($insertSQL);
	 
	 
	 
////////////////////////////	 
	 
   if(isset($palabraBusca))
	 {   
	 		 echo '<center>El resultado de la búsqueda: '.$palabraBusca.'</center>';
			 $b=0;
			 
	 	   echo "<b>Ahora: ".$now = (new \DateTime())->format( 'Y-m-d H:i:s' )."</b><br><br>";
  	   $now_time = strtotime($now);

			 
   		 while ($row = mysql_fetch_array($result)) {	 		
			 			 $test=strtolower($row['nombre'].$row['personaContacto'].$row['calle'].$row['ciudad'].$row['estado'].$row['cp'].$row['pais'].$row['whatsApp'].$row['tlf'].$row['movil'].$row['correoElectronico'].$row['responsable']);
						 $find=strtolower($palabraBusca);
						 $findLen = strlen($find);
						 $testLen = strlen($test);
						 $subTest=substr($test, 0, $findLen);
						 $i=0;
						 $a=0;
						 while ($i<($testLen-$findLen+1)) {
  		  		 			 $subTest=substr($test, $i, $findLen);
  		  					 if (strcmp($find,$subTest)==0 and $a!=1) {
          				 		echo "<tr>";
       						    echo "<td valign='top'><a href='ensenarContacto.php?id=".$row['id']."' style='text-decoration: none;'>";											
											
											echo $row['nombre'];
				  						echo "</a>";
											
				  						echo "</td><td>".$row['fecha_nac']."</td><td valign='top' >".$row['whatsapp']."</td><td valign='top' >".$row['correoElectronico']."</td><td valign='top' align=right>";

											$insertSQL='SELECT SUM(valor) FROM `actividades` WHERE contacto = '.$row['id'].' AND tipo = "Pago"';
   										$result_pago = mysql_query($insertSQL);
											$row_pago = mysql_fetch_array($result_pago);

											$insertSQL='SELECT SUM(valor) FROM `actividades` WHERE contacto = '.$row['id'].' AND tipo = "Rent"';
   										$result_rent = mysql_query($insertSQL);
											$row_rent = mysql_fetch_array($result_rent);
											
											echo $saldo = number_format($row_pago["SUM(valor)"]-$row_rent["SUM(valor)"],2);
											
											echo "</td>";

                  		 $insertSQL = "select * from actividades where contacto = ".$row['id']." and fecha > '".$now."' order by fecha limit 1";
                       $result_nextup = mysql_query($insertSQL);
                  		 $row_nextup = mysql_fetch_array($result_nextup);
											
											echo "<td valign='top' align=right>".substr($row_nextup["fecha"],0,16).'-'.substr($row_nextup["final"],11,5)."</td>";

          						echo "</tr>";
											$b++;
											$id_contacto=$row['id'];
											$a=1;
			  					 }
			  					 $i++;
						 }			
       }
			 if ($b==1)
			 		echo '<meta http-equiv="refresh" content="0;URL=ensenarcontacto.php?id='.$id_contacto.'">';
			 

	 } else {
	 
	 
	 
////////////////////////	 
/*   if(isset($palabraBusca))
	 {   
	 
	 		 echo '<center>El resultado de la búsqueda: '.$palabraBusca.'</center>';
			 $b=0;
   		 while ($row = mysql_fetch_array($result)) {	 		
			 			 $test=strtolower($row['nombreEmpresa'].$row['personaContacto'].$row['calle'].$row['ciudad'].$row['estado'].$row['cp'].$row['pais'].$row['tlf1'].$row['tlf2'].$row['fax'].$row['movil'].$row['correoElectronico'].$row['paginaWeb'].$row['RFC'].$row['tipo'].$row['codigo'].$row['responsable']);
						 $find=strtolower($palabraBusca);
						 $findLen = strlen($find);
						 $testLen = strlen($test);
						 $subTest=substr($test, 0, $findLen);
						 $i=0;
						 $a=0;
						 while ($i<($testLen-$findLen+1)) {
  		  		 			 $subTest=substr($test, $i, $findLen);
  		  					 if (strcmp($find,$subTest)==0 and $a!=1) {
          				 		echo "<tr>";
          						echo "<td valign='top'><a href='ensenarContacto.php?id=".$row['id']."' style='text-decoration: none;'>";
				  						echo $row['nombreEmpresa'];
				  						echo "</a>";
				  						echo "</td><td valign='top' >".$row['personaContacto']."</td><td valign='top' >".$row['tlf1']."</td><td valign='top'>".$row['fax']."</td><td valign='top'><a href='mailto:".$row['correoElectronico']."'>".$row['correoElectronico']."</a></td><td valign='top'>".$row['paginaWeb']."</td>";
          						echo "</td></tr>";
											$id=$row['id'];
											$b++;
											$a=1;
			  					 }
			  					 $i++;
						 }			
       }
			 if ($b==1)
			 		echo '<meta http-equiv="refresh" content="0;URL=ensenarcontacto.php?id='.$id.'">';
					

	 } else {*/
/*   	 while ($row = mysql_fetch_array($result)) {
	 				 if ($row['category']==$cat || $cat=='') {
         	 		echo "<tr>";
         			echo "<td valign='top'><a href='ensenarContacto.php?id=".$row['id']."' style='text-decoration: none;'>";
				 			echo $row['nombreEmpresa'];
				 			echo "</a>";
				 			echo "</td><td valign='top'>".$row['personaContacto']."</td><td valign='top' >".$row['tlf1']."</td><td valign='top'>".$row['fax']."</td><td valign='top'><a href='mailto:".$row['correoElectronico']."'>".$row['correoElectronico']."</a></td><td valign='top'>".$row['paginaWeb']."</td>";
         			echo "</td></tr>";
					 }
     }
*/	 }
   mysql_close($conn);
?>




</table>
</center>

</body>
</html>

