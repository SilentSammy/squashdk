<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php 
$cotizacion = $_REQUEST["cotizacion"];
$id=$_REQUEST["id"];
 ?> 
<html>
<head>
<title>S-DK Editar Contacto</title>
<link rel="stylesheet" type="text/css" href="../fonts.css" />
</head>
<body link="#0000ff" vlink="#0000ff" alink="#0000ff">
<link rel="stylesheet" type="text/css" href="../fonts.css" />

<center><h1>Squash DK - Edit Contacto</h1></center>

<table border="0" cellpadding="0" cellspacing="0" summary="" align="center">
<tr><td>
<?php 
   echo '<form action="index.php" method=post>';


include "incl/connect.incl";
// Hent først data ud
$result = mysql_query("select * from Contactos where id = $id");
// Kør så igennem for hver række

while ($row = mysqli_fetch_array($result)) {
echo '<table border="1" cellpadding="3" cellspacing="0" summary="" align="center" bgcolor="#ffffff">';
echo '<tr><td>Nombre</td><td><input type="text" name="nombre" size="20" value="'.$row["nombre"].'"></td></tr>';
$fecha_nac=substr($row["fecha_nac"],8,2).substr($row["fecha_nac"],5,2).substr($row["fecha_nac"],2,2);
echo '<tr><td>Fecha Nac.</td><td><input type="text" name="fecha_nac" size="20" value="'.$fecha_nac.'"> (AAMMDD)</td></tr>';
echo '<tr><td>ID no.</td><td><input type="text" name="ID_no" size="20" value="'.$row["ID_no"].'"></td></tr>';
echo '<tr><td>Calle</td><td><input type="text" name="calle" size="20" value="'.$row["calle"].'"> No. Ext.<input type="text" name="noExt" size="5" value="'.$row["noExt"].'"> No. Int.<input type="text" name="noInt" size="5" value="'.$row["noInt"].'"></td></tr>';
echo '<tr><td>Colonia</td><td><input type="text" name="colonia" size="20" value="'.$row["colonia"].'"></td></tr>';
echo '<tr><td>Ciudad</td><td><input type="text" name="ciudad" size="20" value="'.$row["ciudad"].'"></td></tr>';
echo '<tr><td>Estado</td><td><input type="text" name="estado" size="20" value="'.$row["estado"].'"></td></tr>';
echo '<tr><td>CP</td><td><input type="text" name="cp" size="20" value="'.$row["cp"].'"></td></tr>';
echo '<tr><td>Pais</td><td><input type="text" name="pais" size="20" value="'.$row["pais"].'"></td></tr>';
echo '<tr><td>WhatsApp</td><td><input type="text" name="whatsapp" size="20" value="'.$row["whatsapp"].'"></td></tr>';
echo '<tr><td>Tel.</td><td><input type="text" name="tlf" size="20" value="'.$row["tlf"].'"></td></tr>';
echo '<tr><td>Cel.</td><td><input type="text" name="movil" size="20" value="'.$row["movil"].'"></td></tr>';
echo '<tr><td>E-mail</td><td><input type="text" name="correoElectronico" size="20" value="'.$row["correoElectronico"].'"></td></tr>';
echo '<tr><td>Contacto</td><td><input type="text" name="personaContacto" size="20" value="'.$row["personaContacto"].'"></td></tr>';
echo '<tr><td>Tel. Contacto</td><td><input type="text" name="tlfContacto" size="20" value="'.$row["tlfContacto"].'"></td></tr>';
echo '<tr><td>ContactoHDK</td><td><select name="responsable">';

include "../incl/connect.incl";
   $result = mysql_query("SELECT * FROM Empleados ORDER BY nombre");
   mysqli_close($conn);
	 while ($row1 = mysqli_fetch_array($result)) {
	    if ($row1['nombre']==$row['responsable'])
			   echo '<option value="'.$row1['nombre'].'" selected="selected">'.$row1['nombre'].'</option>';
			else
	       echo '<option value="'.$row1['nombre'].'">'.$row1['nombre'].'</option>';
	 }
	
echo '</select></td></tr>';
echo '</table>';
echo '<input type="hidden" name="id" value="'.$id.'">';
echo '<input type="submit" name="edit" value="Cambiar">';
echo '<input type="submit" name="delete" value="Eliminar">';
echo '</form>';

}	 


?>
<p>




</td></tr>
</table>


</body>

