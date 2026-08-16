<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php 
header('Content-Type: text/html; charset=iso-8859-1');
$cotizacion = $_REQUEST["cotizacion"];

  include "incl/connect.incl";
	$insertSQL="SELECT * FROM Empleados WHERE username='".$_SERVER['REMOTE_USER']."'";
	$resultEmpleado = mysqli_query($conn, $insertSQL);
	$rowEmpleado = mysqli_fetch_array($resultEmpleado);	

   $rowEmpleo['nombre'];
	 $puestoEmpleado=$rowEmpleo['puesto'];

 ?> 
<html>
<head>
<title>S-DK - Add contact</title>
</head>
<bodylink="#0000ff" vlink="#0000ff" alink="#0000ff">
<link rel="stylesheet" type="text/css" href="fonts.css" />
<table border="0" cellpadding="0" cellspacing="0" summary="" align="center">
<tr><td>
<center>
<h2><b>SQUASH DK</b></h2><br>
<br>
<b>Add Contact</b><br>

</center>
<form action="index.php" method=post>

<table border="1" cellpadding="3" cellspacing="0" summary="" align="center" bgcolor="#ffffff">
<tr><td>Nombre</td><td><input type="text" name="nombre" size="40" value=""></td></tr>
<tr><td>Fecha Nac. (DDMMAA)</td><td><input type="text" name="fecha_nac" size="4" value=""></td></tr>
<tr><td>Tipo / no. de ID</td><td><input type="text" name="ID_no" size="40" value="">
<tr><td>Calle</td><td><input type="text" name="calle" size="26" value="">
 No. <input type="text" name="no_calle" size="4" value=""></td></tr>
<tr><td>Colonia</td><td><input type="text" name="colonia" size="40" value=""></td></tr>
<tr><td>Ciudad</td><td><input type="text" name="ciudad" size="40" value=""></td></tr>
<tr><td>Estado</td><td><input type="text" name="estado" size="40" value=""></td></tr>
<tr><td>CP</td><td><input type="text" name="cp" size="40" value=""></td></tr>
<tr><td>Pais</td><td><input type="text" name="pais" size="40" value=""></td></tr>
<tr><td>WhatsApp</td><td><input type="text" name="whatsapp" size="40" value=""></td></tr>
<tr><td>Tel.</td><td><input type="text" name="tlf" size="40" value=""></td></tr>
<tr><td>Movil</td><td><input type="text" name="movil" size="40" value=""></td></tr>
<tr><td>E-mail</td><td><input type="text" name="correoElectronico" size="40" value=""></td></tr>
<tr><td>Contacto familiar</td><td><input type="text" name="personaContacto" size="40" value=""></td></tr>
<tr><td>Tel. familiar</td><td><input type="text" name="tlfContacto" size="40" value=""></td></tr>
<tr><td>Squash DK</td><td>
<?php
if ($puestoEmpleado!='ayudante') {
	 echo '<select name="responsable">';
   include "incl/connect.incl";
   $result = mysql_query("SELECT * FROM Empleados ORDER BY nombre");
   mysqli_close($conn);
	 while ($row1 = mysqli_fetch_array($result)) {
	    if ($row1['nombre']=='Manuel Ramirez A.')
			   echo '<option value="'.$row1['nombre'].'" selected="selected">'.$row1['nombre'].'</option>';
			else
	       echo '<option value="'.$row1['nombre'].'">'.$row1['nombre'].'</option>';
	 }
} else {
   echo '<input type="hidden" name="responsable" value="'.$puestoEmpleado.'" />'.$rowEmpleado['nombre'];
}

echo '</select></td></tr>';



	
 ?> 




</table>

<p>

<?php 

if (isset($cotizacion))
	 echo '<input type="submit" name="agregar_contacto" value="Agregar & Regresar">';
else
	 echo '<input type="submit" name="agregar" value="Agregar">';

 ?>
</form>


</td></tr>
</table>


</body>

