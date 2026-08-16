<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php 

header('Content-Type: text/html; charset=latin1');
// date_default_timezone_set('Etc/GMT+6');  // Fixed UTC-06:00, no daylight saving
$now = (new \DateTimeImmutable())->format( 'Y-m-d H:i:s' );

set_time_limit(1000);


$nombre = $_REQUEST["nombre"];
$puesto = $_REQUEST["puesto"];
$RFC = $_REQUEST["RFC"];
$personaContacto = $_REQUEST["personaContacto"];
$calle = $_REQUEST["calle"];
$noExt = $_REQUEST["noExt"];
$noInt = $_REQUEST["noInt"];
$colonia = $_REQUEST["colonia"];
$ciudad = $_REQUEST["ciudad"];
$estado = $_REQUEST["estado"];
$cp = $_REQUEST["cp"];
$pais = $_REQUEST["pais"];
$tlf1 = $_REQUEST["tlf1"];
$fax = $_REQUEST["fax"];
$movil = $_REQUEST["movil"];
$codigo = $_REQUEST["codigo"];
$correoElectronico = $_REQUEST["correoElectronico"];
$agregarPersona = $_REQUEST["agregarPersona"];
$edit = $_REQUEST["edit"];
$editpersona = $_REQUEST["editpersona"];
$deletepersona = $_REQUEST["deletepersona"];
$id=$_REQUEST["id"];
$clienteContacto=$_REQUEST["clienteContacto"];



$delete_activity=$_REQUEST["delete_activity"];
if ($delete_activity!='') {
	 include "incl/connect.incl";
	 $insertSQL = "DELETE FROM actividades WHERE id = ".$delete_activity;
   mysql_query($insertSQL);
}

$buscar=$_REQUEST["buscar"];

//Call lights API
$llamarLuces = $_REQUEST["llamarLuces"];
if ($llamarLuces == 'on') {
	$url = 'http://192.168.4.39/1/lights?state=1';
	$context = stream_context_create(['http' => ['timeout' => 5]]);
	@file_get_contents($url, false, $context);
}
if ($llamarLuces == 'off') {
	$url = 'http://192.168.4.39/1/lights?state=0';
	$context = stream_context_create(['http' => ['timeout' => 5]]);
	@file_get_contents($url, false, $context);
}

//Get lights status
$lightsStatus = array();
$statusUrl = 'http://192.168.4.39/1/lights';
$statusContext = stream_context_create(['http' => ['timeout' => 5]]);
$statusResponse = @file_get_contents($statusUrl, false, $statusContext);
if ($statusResponse !== false) {
	$lightsStatus = json_decode($statusResponse, true);
}

//update active
	 include "incl/connect.incl";
   $insertSQL = "UPDATE `contactos` SET `activo` = now() WHERE `id` = ".$id;
	 mysql_query($insertSQL);




$fecha = $_REQUEST["fecha"];
$hora = $_REQUEST["hora"];
$tipo = $_REQUEST["tipo_actividad"];

if ($hora=='' and $tipo == 'Pago')
	 $fecha=substr($fecha,4,2)."-".substr($fecha,2,2)."-".substr($fecha,0,2)." 00:00:00";
elseif ($hora=='')
	 $fecha=substr($fecha,4,2)."-".substr($fecha,2,2)."-".substr($fecha,0,2)." ".substr($now,11,5).":00";
else
	 $fecha=substr($fecha,4,2)."-".substr($fecha,2,2)."-".substr($fecha,0,2)." ".substr($hora,0,2).":".substr($hora,2,2).":00";

$duracion = $_REQUEST["duracion"];
	 

if ($duracion=='') {
	 $duracion=0;
	 $fin_fecha = $fecha;
	 
} else {

$date = date($fecha);
$time = strtotime($date);
$time = $time + (30 * 60 * $duracion);
$fin_fecha = date("Y-m-d H:i:s", $time);

}	 
	 
	 
	 
$valor = $_REQUEST["valor"];
if ($valor=='')
	 $valor=$duracion*150; //costo $150 por 30 min
$cancha = $_REQUEST["cancha"];
if ($cancha=='')
	 $cancha=0;
$descripcion = $_REQUEST["descripcion"];
$agregar_nueva_actividad = $_REQUEST["agregar_nueva_actividad"];
		



//test if court is busy
if ($tipo == 'Rent' and isset($agregar_nueva_actividad)) {

	 $insertSQL = "select * from actividades where cancha = ".$cancha." and ((fecha >= '".$fecha."' and fecha < '".$fin_fecha."') or (final > '".$fecha."' and final <= '".$fin_fecha."') or (fecha > '".$fecha."' and final < '".$fin_fecha."')) order by fecha limit 1";
   $result_activity_test = mysql_query($insertSQL);
	 $ocupado=mysql_num_rows($result_activity_test);
	 if ($ocupado=="1") {
	    $row_activity_test = mysql_fetch_array($result_activity_test);
	 		$insertSQL = "select * from contactos where id=".$row_activity_test['contacto'];
			$result_contacto = mysql_query($insertSQL);
			$row_contacto = mysql_fetch_array($result_contacto);
	    echo "<center><b>Problema: Cancha ".$cancha." ya esta ocupada en este horario!!!</b></center>";
			echo "<br>";
			echo "<center>Reservado por <span title=".$row_contacto['whatsapp']."><b>".$row_contacto['nombre']."</b></span> ".substr($row_activity_test["fecha"],0,16).'-'.substr($row_activity_test["final"],11,5)."</center>";
			echo "<br><br>";

			
	 }
}
	 
	 
	 echo $agregar_nueva_actividad;
if (isset($agregar_nueva_actividad)) {
	 include "incl/connect.incl";

	 if ($fecha=='aa-mm-dd hh:mm:00')
      $insertSQL = "INSERT INTO `actividades` ( `contacto` , `fecha` , `final` , `tipo` , `valor` , `duracion` , `cancha` , `descripcion` ) VALUES ($id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$tipo', $valor, '$duracion', '$cancha', '$descripcion')";	 
	 elseif ($ocupado == '0'or $tipo=='Pago')
      $insertSQL = "INSERT INTO `actividades` ( `contacto` , `fecha` , `final` , `tipo` , `valor` , `duracion` , `cancha` , `descripcion` ) VALUES ($id, '$fecha', '$fin_fecha', '$tipo', $valor, '$duracion', '$cancha', '$descripcion')";
	 mysql_query($insertSQL);
   mysql_close($conn);
}
if (isset($editPago)) {
	 if ($regimenFiscal=='')
	 		$regimenFiscal=0;
	 
	 include "../incl/connect.incl";
   $insertSQL = "UPDATE `contactospersonas` SET `regimenCapital` = '".$regimenCapital."', `regimenFiscal` = '".$regimenFiscal."',`nombre` = '".$nombre."',`RFC` = '".$RFC."',`personaContacto` = '".$personaContacto."',`puesto` = '".$puesto."',`calle` = '".$calle."',`noExt` = '".$noExt."',`noInt` = '".$noInt."',`colonia` = '".$colonia."',`ciudad` = '".$ciudad."',`estado` = '".$estado."',`cp` = '".$cp."',`tlf1` = '".$tlf1."',`fax` = '".$fax."',`movil` = '".$movil."',`correoElectronico` = '".$correoElectronico."' WHERE `id` = ".$clienteContacto;
	 mysql_query($insertSQL);
   mysql_close($conn);
}
  ?> 

<html>

<head>
<title>S-DK Ensenar Contacto</title>
<link rel="stylesheet" type="text/css" href="fonts.css" />
</head>

<?php 
$recibir1 = $_REQUEST["recibir1"];
if (isset($recibir1)) 
   echo '<body onLoad="self.focus();document.recibir.precio.focus();">';
else
   echo '<body onLoad="self.focus();document.search.buscar.focus();">';

 ?>

<center><h1>Squash DK - Contacto</h1></center>


  <table border="0" cellpadding="0" cellspacing="0" summary="" align="center">
    <tr><td><center>

<?php 
    if (isset($cotizacion)) {
       echo '<form action="../cotizaciones/iniciarCotizacion.php" name="Contacto" method=post>';
    } else {
       echo '<form action="ensenarContacto.php?id='.$id.'" name="Contacto" method=post>';
    } 
	 include "incl/connect.incl";

		$insertSQL = "select * from Contactos where id = ".$id;
    $result = mysql_query($insertSQL);
    $row = mysql_fetch_array($result);
       $id=$row["id"];
			 
       echo '<table border="0" cellpadding="0" cellspacing="20" summary="" align="center" bgcolor="#ffffff">';
			 echo '<tr><td valign="top">';
       echo '<table border="0" cellpadding="0" cellspacing="0" summary="" align="center" bgcolor="#ffffff">';
       echo '<tr><td align="right" valign="bottom"><a href="index.php" style="text-decoration: none;" >Nombre</a></td><td></td><td valign="bottom"><b><big><a href="editarContacto.php?id='.$row["id"].'">'.$row["nombre"].'</a></b> '.$row["regimenCapital"].'</big></td></tr>';
			 $nombreEmpresa=$row["nombreEmpresa"];
			 //getting Factura info
	     echo '<tr><td align="right">ID no.</td><td width="2"></td><td>'.$row["ID_no"].'</td></tr>';
	     echo '<tr><td align="right">Fecha Nac.</td><td width="2"></td><td>'.$row["fecha_nac"].'</td></tr>';
	     echo '<tr><td align="right">WhatsApp</td><td></td><td>'.$row["whatsapp"].'</td></tr>';
			 echo '<tr><td align="right">Calle</td><td></td><td>';
		   echo $row["calle"].' '.$row["noExt"].' '.$row["noInt"];
			 echo '';
			 if ($rowPersona["colonia"]!='') {
			   echo ', '.$rowPersona["colonia"];
			 } else { 
			   echo ', '.$row["colonia"];
			 }
			 echo '</td></tr>';
       echo '<tr><td align="right">Ciudad</td><td></td><td>';
			 if ($rowPersona["ciudad"]!='') {
			   echo $rowPersona["ciudad"];
			 } else { 
			   echo $row["ciudad"];
			 }
			 echo '';
			 if ($rowPersona["estado"]!='') {
			   echo ', '.$rowPersona["estado"];
			 } else { 
			   echo ', '.$row["estado"];
			 }
			 echo '';
			 if ($rowPersona["cp"]!='') {
			   echo ', '.$rowPersona["cp"];
			 } else { 
			   echo ', '.$row["cp"];
			 }
			 echo '</td></tr>';
			 if ($row["tlf"]!='') {
         echo '<tr><td align="right">Tel</td><td></td><td>';
			   echo $row["tlf"];
 			   echo '</td></tr>';
			 }
       echo '<tr><td align="right">Contacto</td><td></td><td>';
			   echo $row["personaContacto"].' - '.$row["tlfContacto"];
			 echo '</td></tr>';
       echo '<tr><td align="right">Movil</td><td></td><td>';
			 if ($rowPersona["movil"]!='') {
			   echo $rowPersona["movil"];
			 } else { 
			   echo $row["movil"];
			 }
			 echo '</td></tr>';
       echo '<tr><td align="right">E-mail</td><td></td><td>';
			 if ($rowPersona["correoElectronico"]!='') {
			   echo "<a href='mailto:".$rowPersona['correoElectronico']."'>".$rowPersona['correoElectronico']."</a>";
			 } else { 
			   echo "<a href='mailto:".$row['correoElectronico']."'>".$row['correoElectronico']."</a>";
			 }
			 $email=$row['correoElectronico'];
			 echo '</td></tr>';
       echo '<tr><td align="right">Responsable</td><td></td><td>';
			 
			 if ($changeResponsable!='') {
			 		
					$insertSQL = "select * from Empleos where (puesto='Camioneta' or puesto='CamionetaGerente') order by nombre";
					$tableVendedor = mysql_query($insertSQL);
					while ($rowVendedor = mysql_fetch_array($tableVendedor)) {
						if ($idVendedor==$rowVendedor['id'])
							 echo "<option value=".$rowVendedor['id']." selected='selected'>".$rowVendedor['nombre']."</option>";
						else	 
							 echo "<option value=".$rowVendedor['id'].">".$rowVendedor['nombre']."</option>";
			    }

					
					
			 } else {
			 
			 			echo $row["responsable"];
			 }
			 echo '</td></tr>';
			 
//////////////////////////////



//////////////////////////////			 
			 
			 
       echo '</table>';
       echo '</form>';

			 echo '</td><td>';


              $file_pointer = 'id_fotos/'.$row["id"].'.jpg';
              if (file_exists($file_pointer)) {
      			      echo ' <a href="'.$file_pointer.'" target="_blank"> <img src="id_fotos/'.$row["id"].'.jpg" width="100" alt="images (4K)" /></a>';
              } else {
			 					  echo 'Drag ID here (.jpg)<br>';
    					    echo '<center>';
									include "upload.php";
    					    echo '</center>';
              }			

			 
			 
		echo '</td></tr>';
		
		echo '</table>';
		
		echo "<br />";

		include "incl/connect.incl";

 		$insertSQL = "SELECT SUM(valor) FROM `actividades` WHERE tipo='pago' and contacto = ".$id;
    $result = mysql_query($insertSQL);
  	$row_pagos = mysql_fetch_array($result);
 		$insertSQL = "SELECT SUM(valor) FROM `actividades` WHERE tipo!='pago' and contacto = ".$id;
    $result = mysql_query($insertSQL);
  	$row_rent = mysql_fetch_array($result);
    $saldo = $row_pagos['SUM(valor)']-$row_rent['SUM(valor)'];
		echo '<center><h2>Saldo: $<b>'.$saldo.'</b></h2></center>';
		
		echo '<form action="ensenarContacto.php?id='.$id.'" method="post" style="margin-bottom: 20px;">';
		echo '<button type="submit" name="llamarLuces" value="on" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px;">Encender Luces</button>';
		echo '<button type="submit" name="llamarLuces" value="off" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Apagar Luces</button>';
		echo '</form>';
		
		if (!empty($lightsStatus)) {
			$lightState = $lightsStatus['light'] === 'on' ? 'Encendidas' : 'Apagadas';
			$stateColor = $lightsStatus['light'] === 'on' ? '#4CAF50' : '#999';
			echo '<div style="text-align: center; margin-top: 15px; padding: 15px; background-color: #f5f5f5; border-radius: 4px;">';
			echo '<p style="margin: 5px 0;"><b>Estado de Luces:</b></p>';
			echo '<p style="margin: 5px 0; font-size: 18px; color: '.$stateColor.';"><b>'.$lightState.'</b></p>';
			echo '<p style="margin: 5px 0; font-size: 12px; color: #666;">Cuarto: '.$lightsStatus['room'].'</p>';
			echo '</div>';
		}
    ?> 

<center><h2>Actividades:</h2></center>
		
		
		<?php 

		include "incl/connect.incl";

$nuevo_actividad = $_REQUEST["nuevo_actividad"];		

if (isset($nuevo_actividad)) {
	
$tipo_actividad = $_REQUEST["tipo_actividad"];		
$currentDateTime = new DateTime('now'); $currentDate = $currentDateTime->format('Y-m-d'); 
$currentDate = substr($currentDate,8,2).substr($currentDate,5,2).substr($currentDate,2,2);
       echo '<table border="0" cellpadding="0" cellspacing="1" summary="" align="center" bgcolor="#ffffff">';
		if ($tipo_actividad == "Pago")
			 	echo '<tr><td valign="top"></td><td valign="top">Fecha (ddmmaa):</td><td valign="top">Pago:</td></tr>';
		elseif ($tipo_actividad == "Rent")
			 	echo '<tr><td valign="top"></td><td valign="top">Fecha (ddmmaa):</td><td valign="top">Inicio (hhmm):</td><td valign="top">Duracion:</td><td valign="top">Cancha:</td></tr>';
	  else
			 echo '';
 

		echo '<form action="ensenarContacto.php" name="form_nueva_actividad" method="post">';
		 
		echo '<tr><td valign="top"><b>Nueva actividad:</b> <select name="tipo_actividad" onChange="document.form_nueva_actividad.submit()"><option value="None">Elegir</option>';
		if ($tipo_actividad == "Pago")
			 	echo '<option value="Pago" selected="selected">Pago</option><option value="Rent">Renta</option>';
		elseif ($tipo_actividad == "Rent")
			 	echo '<option value="Pago">Pago</option><option value="Rent" selected="selected">Renta</option>';
	  else
		    echo '<option value="Pago">Pago</option><option value="Rent">Renta</option>';
		echo '<input type="hidden" name="id" value="'.$id.'" />';
		echo '<input type="hidden" name="nuevo_actividad" value="'.$nuevo_actividad.'" />';
		echo '</select>';

		if ($tipo_actividad == "Pago") {
    
    		echo '</td><td valign="top"><input type="text" name="fecha" value="'.$currentDate.'" size="4" />';
    		echo '</td><td valign="top"><input type="text" name="valor" value="" size="4" />';
		
		}
		if ($tipo_actividad == "Rent") {
    
    		echo '</td><td valign="top"><input type="text" name="fecha" value="'.$currentDate.'" size="4" />';
    		echo '</td><td valign="top"><input type="text" name="hora" value="" size="4" />';
    		echo '</td><td valign="top"><select name="duracion">';
				echo '<option value="1">0:30</option>';
				echo '<option value="2">1:00</option>';
				echo '<option value="3">1:30</option>';
				echo '<option value="4">2:00</option>';
				echo '<option value="5">2:30</option>';
				echo '<option value="6">3:00</option>';
				echo '</select>';
    		echo '</td><td valign="top"><select name="cancha"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>';
		
		
		}
		
		echo '<input type="submit" name="agregar_nueva_actividad" value="+" /></td></tr>';
    echo '</form>';

		   echo '</td></tr>';
       echo '</table><br><br>';
		
}



		//form de buscar
		echo '<form action="ensenarContacto.php" name="search" method="post">';
		echo '<input type="hidden" name="id" value="'.$id.'" />';
		echo '<input type="text" name="buscar" value="" size="9" />';
		echo '<input type="submit" name="" value="Buscar" /><input type="submit" name="nuevo_actividad" value="+" />';
    echo '</form>';
	  

  	include "incl/connect.incl";
  	mysql_query($insertSQL);

		$insertSQL = "select * from actividades where contacto = ".$id." order by fecha desc";
    $result = mysql_query($insertSQL);
	 
	 echo "<b>Ahora: ".$now = (new \DateTime())->format( 'Y-m-d H:i:s' )."</b><br><br>";
	 $now_time = strtotime($now);
		$insertSQL = "select id from actividades where contacto = ".$id." and fecha > '".$now."' order by fecha limit 1";
    $result_nextup = mysql_query($insertSQL);
		$row_nextup = mysql_fetch_array($result_nextup);

	 echo '<table  border="1" cellpadding="3" cellspacing="0" summary="" frame="border" >';
	 echo '<thead><th>Fecha</th><th>Tipo</th><th>Duracion</th><th>Cancha</th><th>Debit</th><th>Credit</th><th>-</th></thead>';
	 
   
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
		 echo $row["tipo"]; 
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
	 if ($row["tipo"]=='Pago') {
   echo '<td align="right">';
		 echo $row["valor"]; 	 
	 echo '</td><td align="right">-</td>';
	 } else {
   echo '<td align="right">-</td><td align="right">';
		 echo $row["valor"]; 	 
	 echo '</td>';
	 
	 }
	 echo '<td><a href="ensenarContacto.php?id='.$id.'&delete_activity='.$row["id"].'">-</a></td>';
	 echo '</tr>';
	 
	 
	 }
   echo '</table><br><br>';


	    mysql_close($conn);
	
		
		
if ($tipo=='Proveedor') {

	 	 echo $buscar;

		 
     $idHerramienta = $_REQUEST["idHerramienta"];
     $recibir = $_REQUEST["recibir"];
     $ref_recibir = $_REQUEST["ref_recibir"];
		 if (isset($recibir)) {
		 		if (isset($idHerramienta)) {
				    $insertSQL = 'select * from cotizacionHerramientas where id='.$idHerramienta;
				    $resultEnt=mysql_query($insertSQL);
				 		$rowEnt = mysql_fetch_array($resultEnt);
						if ($rowEnt['recibidoFecha']=='0000-00-00')
				       $insertSQL = "update Precio".$rowEnt[marca]." set enReserva=enReserva+".$rowEnt[cantidad]." where ref='".$rowEnt[modelo]."'";				 
						else
				       $insertSQL = "update Precio".$rowEnt[marca]." set enReserva=enReserva-".$rowEnt[cantidad]." where ref='".$rowEnt[modelo]."'";				 
						mysql_query($insertSQL);

						if ($rowEnt['recibidoFecha']=='0000-00-00') {
						   $precio = $_REQUEST["precio"];
						   $descuento = $_REQUEST["descuento"];
						   $moneda_costo = $_REQUEST["moneda_costo"];
							 $costo = $precio*$descuento;

						   if ($rowEnt['proveedorFecha']=='0000-00-00')
				 		      $insertSQL="UPDATE cotizacionHerramientas SET moneda_costo='".$moneda_costo."', costo=".$costo.", recibidoFecha=now(), proveedorFecha=now(), noDePedido='None', ref_recibir='".$ref_recibir."' WHERE id=".$idHerramienta;							 
							 else
				 		      $insertSQL="UPDATE cotizacionHerramientas SET moneda_costo='".$moneda_costo."', costo=".$costo.", recibidoFecha=now(), ref_recibir='".$ref_recibir."' WHERE id=".$idHerramienta;
						} else {
						   if ($rowEnt['noDePedido']=='None')
				 		      $insertSQL="UPDATE cotizacionHerramientas SET moneda_costo='', costo=0, recibidoFecha='0000-00-00', proveedorFecha='0000-00-00', noDePedido='',ref_recibir='' WHERE id=".$idHerramienta;
							 else
				 		      $insertSQL="UPDATE cotizacionHerramientas SET moneda_costo='', costo=0, recibidoFecha='0000-00-00', ref_recibir='' WHERE id=".$idHerramienta;
		 				}
						$result = mysql_query($insertSQL);
				} 
	 	 }
		 
		 
		 
     $quitar = $_REQUEST["quitar"];
		 if (isset($quitar)) {
		 		if (isset($idHerramienta)) {
				 		$insertSQL="UPDATE cotizacionHerramientas SET Proveedor='None' WHERE id=".$idHerramienta;
		 				$result = mysql_query($insertSQL);
			  }				
	 	 }
	   echo '<br><br><center><b>Herramientas sin pedido</b></center>';
	 	 echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	   echo '<tr><td><center><b>#</b></center></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Cliente</b></td><td><b>PrecioProv.</b></td><td><b>Fecha</b></td><td><b>Quitar</b></td></tr>';
	   $insertSQL = "SELECT * FROM cotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND proveedorFecha='0000-00-00' ORDER BY modelo";
		 $result = mysql_query($insertSQL);
		 $i=1;
		 $total=0;
		 while ($row = mysql_fetch_array($result)) {
		 			 		$insertSQL="SELECT * FROM marcadeherramientas where marca='".$row["marca"]."'";
	 					  $resultPrecioBase = mysql_query($insertSQL);
							$no_rows=mysql_num_rows($resultPrecioBase);
							if ($no_rows!=0) {
		 			 			 $insertSQL="SELECT precioBase FROM precio".$row["marca"]." where ref='".$row["modelo"]."'";
	 					  	 $resultPrecioBase = mysql_query($insertSQL);
							   $rowPrecioBase = mysql_fetch_array($resultPrecioBase);
							}
							if (isset($buscar) and $buscar!='')	{
								 $insertSQL = "SELECT `nombreEmpresa` FROM `contactos` WHERE id =".$row["cliente"];
	 					  	 $resultCliente = mysql_query($insertSQL);
								 $rowCliente = mysql_fetch_array($resultCliente);
		 	 		    	 $info=strtoupper($row["modelo"]."--".$row["descripcion"]."--".$row["cantidad"]."--".$rowCliente["nombreEmpresa"]."--".$rowPrecioBase['precioBase']."--".$row["NoPedClient"]."--".$row["ref"]);
		 						 if (strstr($info,strtoupper($buscar))) {
    			 			 		echo '<tr><td valign="top"><center>'.$i.'</center></td><td valign="top">'.$row["marca"].'</td><td valign="top">'.$row["modelo"].'</td><td valign="top">'.$row["descripcion"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top" align="right">';
  							 		echo $rowCliente["nombreEmpresa"].'</td>';
			  				 		echo '<td valign="top" align=right><a >'.($rowPrecioBase['precioBase']).'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&quitar=set&idHerramienta='.$row['id'].'">X</a></td>';
								 		$total+=$rowPrecioBase['precioBase']*$row["cantidad"];							
								 		echo '</tr>';
								 		$i++;
							   
								 }
							} else {
    			 			 echo '<tr><td valign="top"><center>'.$i.'</center></td><td valign="top">'.$row["marca"].'</td><td valign="top">'.$row["modelo"].'</td><td valign="top">'.$row["descripcion"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top" align="right">';
  							 $insertSQL = "SELECT `nombreEmpresa` FROM `contactos` WHERE id =".$row["cliente"];
	 					  	 $resultCliente = mysql_query($insertSQL);
								 $rowCliente = mysql_fetch_array($resultCliente);
								 echo $rowCliente["nombreEmpresa"].'</td>';
			  				 echo '<td valign="top" align=right><a >'.($rowPrecioBase['precioBase']).'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&quitar=set&idHerramienta='.$row['id'].'">X</a></td>';
								 $total+=$rowPrecioBase['precioBase']*$row["cantidad"];
							
								 echo '</tr>';
								 $i++;
							}
			}	
			echo '</table>';
			echo '<b>'.$total.'</b>';
			echo '<form action="../proveedores/creandoOrdenDeCompra.php?idProveedor='.$id.'" method="post">';
			echo '<button type="submit">Pedir</button>';			
			echo '</form>';
			
     $idHerramienta = $_REQUEST["idHerramienta"];
     $enviado = $_REQUEST["enviado"];
		 if (isset($enviado)) {
		 		if (isset($idHerramienta)) {
				 		$insertSQL="select * from cotizacionHerramientas WHERE id=".$idHerramienta;
		 				$result = mysql_query($insertSQL);
						$row = mysql_fetch_array($result);
						if ($row['enviadoFecha']=='0000-00-00')
				 		   $insertSQL="UPDATE cotizacionHerramientas SET enviadoFecha=now() WHERE id=".$idHerramienta;
						else
							 $insertSQL="UPDATE cotizacionHerramientas SET enviadoFecha='0000-00-00' WHERE id=".$idHerramienta;
		 				$result = mysql_query($insertSQL);
				} 		
	 	 }

	   echo '<br><br><center><b>Herramientas sin recibido</b></center>';

		 echo '<form name="form_nueva_actividad" action="ensenarContacto.php" method="post">';
		 
							echo '<select name="Proveedor" onChange="document.form_nueva_actividad.submit()"><option value="None">Elegir</option><option value="pago">Pago</option><option value="rent">Renta</option>';
							echo '</select></form>';
		 
		 
		 
		 
		 
		 echo '<input type="hidden" name="buscar" value="'.$buscar.'" />';
		 echo '<input type="hidden" name="id" value="'.$id.'" />';
		 echo '<input type="hidden" name="descuento" value="'.$descuento.'" />';
		 echo '<input type="hidden" name="moneda_costo" value="'.$moneda_costo.'" />';
		 echo '<input type="text" name="ref_recibir" value="'.$ref_recibir.'" size="9" />';
		 echo '<input type="submit" name="" value="Ref." />';
		 echo '</form>';
		 
	 	 echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	   echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibir</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b>Cliente</b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
	   $insertSQL = "SELECT * FROM cotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha='0000-00-00' ORDER BY modelo";//AND proveedorFecha!='0000-00-00' 
		 $result = mysql_query($insertSQL);
		 $i=1;
		 while ($row = mysql_fetch_array($result)) {

				if (isset($buscar) and $buscar!='')	{
					 $insertSQL = "SELECT `nombreEmpresa` FROM `Contactos` WHERE id =".$row["cliente"];
	 			 	 $resultCliente = mysql_query($insertSQL);
					 $rowCliente = mysql_fetch_array($resultCliente);
		 	 	 	 $info=strtoupper($row["modelo"]."--".$row["descripcion"]."--".$row["cantidad"]."--".$rowCliente["nombreEmpresa"]."--".$rowPrecioBase['precioBase']."--".$row["NoPedClient"]."--".$row["ref"]."--".$row["noDePedido"]."--".$row["pedidoFecha"]."--".$row["enviadoFecha"]."--".$row["ref_recibir"]);
		 		   if (strstr($info,strtoupper($buscar))) {

							if ($row['factura']!=0)
     			 		   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['factura'].'&fecha='.$fecha.'">'.$i.'</a></center></td>';
							else
     			 		   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['cotizacionNo'].'&fecha='.$fecha.'">'.$i.'</a></center></td>';
					 
					 
//     			 		echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row[cotizacionNo].'&fecha='.$fecha.'">'.$i.'</a></center></td>';
							echo '<td><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&enviado=set&idHerramienta='.$row['id'].'">';
							if ($row['enviadoFecha']=='0000-00-00')
							   echo 'No';
							else 	 
								 echo $row['enviadoFecha'];
							echo '</a></td>';
							
							if ($ref_recibir!='') {
							    $recibir1 = $_REQUEST["recibir1"];
									if ($recibir1=='set' and $idHerramienta==$row['id']) {
									   echo '<td valign="top">';
										 echo '<form action="ensenarContacto.php" method="post" name="recibir">';
										 echo '<input type="hidden" name="buscar" value="'.$buscar.'" />';
										 echo '<input type="hidden" name="id" value="'.$id.'" />';
										 echo '<input type="hidden" name="ref_recibir" value="'.$ref_recibir.'" />';
										 echo '<input type="hidden" name="idHerramienta" value="'.$row['id'].'" />';
										 echo '<input type="hidden" name="recibir" value="set" />';
										 
										 $insertSQL = "SELECT costo FROM `cotizacionherramientas` WHERE marca = '".$row["marca"]."' AND `modelo` = '".$row["modelo"]."' AND costo > 0 ORDER BY recibidoFecha DESC limit 0,1";
										 $resultPrecioAntes = mysql_query($insertSQL);
										 $rowPrecioAntes = mysql_fetch_array($resultPrecioAntes);
										 
										 echo '<input type="text" name="precio" size="5" value="'.$rowPrecioAntes["costo"].'" />';
										 echo '<input type="text" name="descuento" value="'.$descuento.'" size="2" />';
                    
										echo '<select name="moneda_costo" class="small">';
                    $moneda_costo = $_REQUEST["moneda_costo"];
                    if ($moneda_costo=='usd')
                      echo '<option value="mxn">MXN</option><option value="usd" selected="selected">USD</option>';
                    else
                      echo '<option value="mxn" selected="selected">MXN</option><option value="usd">USD</option>';
                    echo '</select>';

										 echo '<input type="submit" name="recibir2" value="recibir" />';
										 echo '</form>';
//<a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&recibir1=set&idHerramienta='.$row['id'].'&ref_recibir='.$ref_recibir.'">recibir1</a></td>';
									} else {
									   echo '<td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&descuento='.$descuento.'&id='.$id.'&recibir1=set&idHerramienta='.$row['id'].'&ref_recibir='.$ref_recibir.'&moneda_costo='.$moneda_costo.'">recibir</a></td>';
									}
							} else {
									echo '<td>recibir</td>';
							}
		          if ($row["Entregado"]!='0000-00-00' or strtoupper($row["Proveedor"])=='ALMACEN')
			           echo '<td style="color: green" valign="top">';
	            else
			           echo '<td style="color: red" valign="top">';			 


							echo '<a href="http://buy1.snapon.com/catalog/search.asp?partno='.$row["modelo"].'&searchTrnsfr=true&search_type=Part&store=snapon-store" target="snapon" style="text-decoration: none;">'.$row["marca"].'</a></td><td valign="top">'.$row["modelo"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top">'.SUBSTR($row["descripcion"],0,25).'</td><td valign="top" align="right">';
							
							if ($row["Entregado"]!='0000-00-00') {
							   echo 'ALMACEN</td>';
							} else {
							   if ($rowCliente["nombreEmpresa"]=='ALMACEN') {
								    $insertSQL = 'select NoPedClient from cotizacion where id = '.$row["cotizacionNo"];
          	 			 	$resultVendedor = mysql_query($insertSQL);
          					$rowVendedor = mysql_fetch_array($resultVendedor);
							   	  echo $rowVendedor["NoPedClient"].'</td>';
										
							   		
								 } else {
							   	  echo $rowCliente["nombreEmpresa"].'</td>';
								 }
							}	 

			  			echo '<td valign="top"><a name="marca">'.$row["noDePedido"].'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td align=right>';
/*							if (strtoupper($row["marca"])=="SNAPON") {
							   $insertSQL = "SELECT `precioBase` FROM PrecioSnapon WHERE ref ='".$row["modelo"]."'";
								 $resultPrecio = mysql_query($insertSQL);
								 $rowPrecio = mysql_fetch_array($resultPrecio);
								 echo .6*$rowPrecio["precioBase"];
							}	elseif (strtoupper($row["marca"])=="APEX") {
							   $insertSQL = "SELECT `precioBase` FROM PrecioApex WHERE ref ='".$row["modelo"]."'";
								 $resultPrecio = mysql_query($insertSQL);
								 $rowPrecio = mysql_fetch_array($resultPrecio);
								 echo .5*$rowPrecio["precioBase"];
							}
	*/
							$row["ref_recibir"];
              $moneda_costo = $_REQUEST["moneda_costo"];
							echo '</td>';
							
							if ($idSplit==$row["id"]) {
							   echo '<td>';
								 echo '<form action="ensenarContacto.php" method="post">';
								 echo '<input type="hidden" name="CotizacionNo" value="'.$CotizacionNo.'" />';
								 echo '<input type="hidden" name="idSplit" value="'.$idSplit.'" />';
								 echo '<input type="hidden" name="moneda_costo" value="'.$moneda_costo.'" />';
								 echo '<input type="hidden" name="ref_recibir" value="'.$ref_recibir.'" />';
								 echo '<input type="hidden" name="descuento" value="'.$descuento.'" />';
								 echo '<input type="hidden" name="buscar" value="'.$buscar.'" />';
								 echo '<input type="hidden" name="id" value="'.$id.'" />';
								 echo '<input type="text" name="cantidadSplit" />';
								 echo '<input type="submit" name="split" value="Split" />';
								 echo '</form>';
								 echo '</td>';
			        } elseif ($row["cantidad"]>1) {
							 	 echo '<td><a href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&idSplit='.$row["id"].'&ref_recibir='.$ref_recibir.'&moneda_costo='.$moneda_costo.'&descuento='.$descuento.'">Split</a></td>';
			        } else {
							   echo '<td>No Split</td>';
			        }
							
							echo '</tr>';
							$i++;					 
					 
					 }

				} else {
							if ($row['factura']!=0)
     			 		   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['factura'].'&fecha='.$fecha.'">'.$i.'</a></center></td>';
							else
     			 		   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['cotizacionNo'].'&fecha='.$fecha.'">'.$i.'</a></center></td>';
							
							echo '<td><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&enviado=set&idHerramienta='.$row['id'].'">';
							if ($row['enviadoFecha']=='0000-00-00')
							   echo 'No';
							else 	 
								 echo $row['enviadoFecha'];
							echo '</a></td>';
							echo '<td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&recibir=set&idHerramienta='.$row['id'].'">recibir</a></td>';

		   if ($row["Entregado"]!='0000-00-00' or strtoupper($row["Proveedor"])=='ALMACEN')
			    echo '<td style="color: green" valign="top">';
	     else
			    echo '<td style="color: red" valign="top">';			 
							
							
							echo $row["marca"].'</td><td valign="top"><a href="http://buy1.snapon.com/catalog/search.asp?partno='.$row["modelo"].'&searchTrnsfr=true&search_type=Part&store=snapon-store" target="snapon" style="text-decoration: none;">'.$row["modelo"].'</a></td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top">'.SUBSTR($row["descripcion"],0,25).'</td><td valign="top" align="right">';
							$insertSQL = "SELECT `nombreEmpresa` FROM `Contactos` WHERE id =".$row["cliente"];
  	 					$resultCliente = mysql_query($insertSQL);
							$rowCliente = mysql_fetch_array($resultCliente);
							if ($row["Entregado"]!='0000-00-00') {
							   echo 'ALMACEN</td>';
							} ELSE {
							   if ($rowCliente["nombreEmpresa"]=='ALMACEN') {
								    $insertSQL = 'select NoPedClient from cotizacion where id = '.$row["cotizacionNo"];
          	 			 	$resultVendedor = mysql_query($insertSQL);
          					$rowVendedor = mysql_fetch_array($resultVendedor);
							   	  echo $rowVendedor["NoPedClient"].'</td>';
										
							   		
								 } else {
							   	  echo $rowCliente["nombreEmpresa"].'</td>';
								 }
							}	 

			  			echo '<td valign="top"><a name="marca">'.$row["noDePedido"].'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td align=right>';
							if (strtoupper($row["marca"])=="SNAPON") {
							   $insertSQL = "SELECT `precioBase` FROM PrecioSnapon WHERE ref ='".$row["modelo"]."'";
								 $resultPrecio = mysql_query($insertSQL);
								 $rowPrecio = mysql_fetch_array($resultPrecio);
								 echo .6*$rowPrecio["precioBase"];
							}
							echo '</td></tr>';
							$i++;
				 }

			}	
			echo '</table>';

			
			
			
	   echo '<br><br><center><b>Herramientas sin entregar</b></center>';
	 	 echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	   $cliente = $_REQUEST["cliente"];
	   $modelo = $_REQUEST["modelo"];
     if (isset($cliente)) {
	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibido</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b><a href="ensenarContacto.php?id='.$id.'">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado='0000-00-00' ORDER BY cliente";
		 } elseif (isset($modelo)) {
	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibido</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b><a href="ensenarContacto.php?id='.$id.'">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado='0000-00-00' ORDER BY modelo";
		 } else {
	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibir</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&cliente=1">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td><td><b>C./V.</b></td></tr>';
  		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado='0000-00-00' ORDER BY recibidoFecha";
  		 }
  		 $result = mysql_query($insertSQL);
		 $i=1;
		 while ($row = mysql_fetch_array($result)) {

				if (isset($buscar) and $buscar!='')	{
					 $insertSQL = "SELECT `nombreEmpresa`, tlf1 FROM `Contactos` WHERE id =".$row["cliente"];
	 			 	 $resultCliente = mysql_query($insertSQL);
					 $rowCliente = mysql_fetch_array($resultCliente);
		 	 	 	 $info=strtoupper($row["modelo"]."--".$row["descripcion"]."--".$row["cantidad"]."--".$rowCliente["nombreEmpresa"]."--".$rowPrecioBase['precioBase']."--".$row["NoPedClient"]."--".$row["ref"]."--".$row["noDePedido"]."--".$row["pedidoFecha"]."--".$row["enviadoFecha"]."--".$row["recibidoFecha"]."--".$row["ref_recibir"]);
		 		   if (strstr($info,strtoupper($buscar))) {

     			 		if ($row['factura']!=0)
							   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['factura'].'">'.$i.'</a></center></td>';
							else	 
							   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['cotizacionNo'].'">'.$i.'</a></center></td>';
							echo '<td><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&enviado=set&idHerramienta='.$row['id'].'">';
							if ($row['enviadoFecha']=='0000-00-00')
							   echo 'No';
							else 	 
								 echo $row['enviadoFecha'];
							echo '</a></td>';
							echo '<td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&descuento='.$descuento.'&id='.$id.'&recibir=set&idHerramienta='.$row['id'].'&ref_recibir='.$row['ref_recibir'].'">'.$row['recibidoFecha'].'</a></td>';

		   if ($row["Entregado"]!='0000-00-00' or strtoupper($row["Proveedor"])=='ALMACEN')
			    echo '<td style="color: green" valign="top">';
	     else
			    echo '<td style="color: red" valign="top">';			 


							echo $row["marca"].'</td><td valign="top">'.$row["modelo"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top">'.SUBSTR($row["descripcion"],0,25).'</td><td valign="top" align="right">';

							   if ($rowCliente["nombreEmpresa"]=='ALMACEN') {
								    $insertSQL = 'select NoPedClient from cotizacion where id = '.$row["cotizacionNo"];
          	 			 	$resultVendedor = mysql_query($insertSQL);
          					$rowVendedor = mysql_fetch_array($resultVendedor);
							   	  echo $rowVendedor["NoPedClient"].'</td>';
								 } else {
							      echo substr($rowCliente["nombreEmpresa"],0,30).' - '.$rowCliente["tlf1"].'</td>';
								 }


			  			echo '<td valign="top"><a name="marca">'.$row["noDePedido"].'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td align=right>';
							echo $row["ref_recibir"];
							echo '</td>';
							echo '<td>';
							if ($row["precioLista"]!=0) {
							   echo number_format($row["costo"],2).' ('.number_format($row["costo"]/$row["precioLista"],2).')';
							} else {
							   echo number_format($row["costo"],2);
							}
							echo '</td>';

							echo '</tr>';
							$i++;					 
					 
					 }

				} else {

     			 		if ($row['factura']!=0)
							   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row[factura].'">'.$i.'</a></center></td>';
							else	 
     			 		   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row[cotizacionNo].'">'.$i.'</a></center></td>';
							echo '<td><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&enviado=set&idHerramienta='.$row['id'].'">';
							if ($row['enviadoFecha']=='0000-00-00')
							   echo 'No';
							else 	 
								 echo $row['enviadoFecha'];
							echo '</a></td>';
							echo '<td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?id='.$id.'&recibir=set&idHerramienta='.$row['id'].'">'.$row['recibidoFecha'].'</a></td>';

      		   if ($row["Entregado"]!='0000-00-00' or strtoupper($row["Proveedor"])=='ALMACEN')
      			    echo '<td style="color: green" valign="top">';
      	     else
      			    echo '<td style="color: red" valign="top">';			 
							
							
							echo $row["marca"].'</td><td valign="top">'.$row["modelo"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top">'.SUBSTR($row["descripcion"],0,25).'</td><td valign="top" align="right">';
							$insertSQL = "SELECT `nombreEmpresa`, tlf1 FROM `Contactos` WHERE id =".$row["cliente"];
  	 					$resultCliente = mysql_query($insertSQL);
							$rowCliente = mysql_fetch_array($resultCliente);

							   if ($rowCliente["nombreEmpresa"]=='ALMACEN') {
								    $insertSQL = 'select NoPedClient from cotizacion where id = '.$row["cotizacionNo"];
          	 			 	$resultVendedor = mysql_query($insertSQL);
          					$rowVendedor = mysql_fetch_array($resultVendedor);
							   	  echo $rowVendedor["NoPedClient"].'</td>';
								 } else {
							      echo substr($rowCliente["nombreEmpresa"],0,30).' - '.$rowCliente["tlf1"].'</td>';
								 }


			  			echo '<td valign="top"><a name="marca">'.$row["noDePedido"].'</a></td><td valign="top">'.$row["pedidoFecha"].'</td><td align=right>';
							echo $row['ref_recibir'];
							echo '</td></tr>';
							$i++;
				 }

			}	
			echo '</table>';



////////////////////////////////
//********************			
			
			
			
    if (isset($buscar) and $buscar!='')	{
        $herr_rec_ent = $_REQUEST["herr_rec_ent"];
    		if ($herr_rec_ent==1) {	 
        	   echo '<br><br><center><b>Herramientas recibido y entregado</b></center>';
        	 	 echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
        	   $cliente = $_REQUEST["cliente"];
        	   $modelo = $_REQUEST["modelo"];
             if (isset($cliente)) {
        	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibido</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b><a href="ensenarContacto.php?id='.$id.'">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
        		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado!='0000-00-00' AND recibidoFecha!='0000-00-00' ORDER BY cliente";
        		 } elseif (isset($modelo)) {
        	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibido</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b><a href="ensenarContacto.php?id='.$id.'">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
        		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado!='0000-00-00' AND recibidoFecha!='0000-00-00' ORDER BY modelo";
        		 } else {
        	      echo '<tr><td><center><b>#</b></center></td><td><b>Enviado</b></td><td><b>Recibir</b></td><td><b>Marca</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&modelo=1">Modelo</a></b></td><td><b>Cant.</b></td><td><b>Descripci�n</b></td><td><b>Precio</b></td><td><b><a href="ensenarContacto.php?id='.$id.'&cliente=1">Cliente</a></b></td><td><b>Pedido</b></td><td><b>Fecha</b></td><td><b>Ref.</b></td></tr>';
        		    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE Proveedor='".strtoupper($nombreEmpresa)."' AND recibidoFecha!='0000-00-00' AND proveedorFecha!='0000-00-00' AND Entregado!='0000-00-00' AND recibidoFecha!='0000-00-00' ORDER BY modelo";
        		 }
        		 $result = mysql_query($insertSQL);
        		 $i=1;
        		 while ($row = mysql_fetch_array($result)) {
        
        					 $insertSQL = "SELECT `nombreEmpresa`, tlf1 FROM `Contactos` WHERE id =".$row["cliente"];
        	 			 	 $resultCliente = mysql_query($insertSQL);
        					 $rowCliente = mysql_fetch_array($resultCliente);
        		 	 	 	 $info=strtoupper($row["modelo"]."--".$row["descripcion"]."--".$row["cantidad"]."--".$rowCliente["nombreEmpresa"]."--".$rowPrecioBase['precioBase']."--".$row["NoPedClient"]."--".$row["ref"]."--".$row["noDePedido"]."--".$row["pedidoFecha"]."--".$row["enviadoFecha"]."--".$row["recibidoFecha"]."--".$row["ref_recibir"]);
        		 		   if (strstr($info,strtoupper($buscar))) {
        
             			 		if ($row['factura']!=0)
        							   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row[factura].'">'.$i.'</a></center></td>';
        							else	 
        							   echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row[cotizacionNo].'">'.$i.'</a></center></td>';
        							echo '<td><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&enviado=set&idHerramienta='.$row['id'].'">';
        							if ($row['enviadoFecha']=='0000-00-00')
        							   echo 'No';
        							else 	 
        								 echo $row['enviadoFecha'];
        							echo '</a></td>';
        							echo '<td valign="top"><a  style="text-decoration: none;" href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&recibir=set&idHerramienta='.$row['id'].'">'.$row['recibidoFecha'].'</a></td>';
        
        		   				if ($row["Entregado"]!='0000-00-00' or strtoupper($row["Proveedor"])=='ALMACEN')
        			    			 echo '<td style="color: green" valign="top">';
        	            else
        			    		   echo '<td style="color: red" valign="top">';			 
        
        
        							echo $row["marca"].'</td><td valign="top">'.$row["modelo"].'</td><td valign="top" align="right">'.$row["cantidad"].'</td><td valign="top">'.SUBSTR($row["descripcion"],0,25).'</td><td align=right title="'.$row["costo"].'">'.$row["precioLista"].$row["moneda"].'</td><td valign="top" align="right">';
        							echo substr($rowCliente["nombreEmpresa"],0,30).'</td>';
        
        			  			echo '<td valign="top"><a name="marca">'.$row["noDePedido"].'</a></td><td valign="top">'.$row["Entregado"].'</td><td align=right>'.$row["ref_recibir"];
        							echo '</td>';
        
        							echo '</tr>';
        							$i++;					 
        					 
        					 }
        
        			}	
        			echo '</table>';
				 } else {
				      
							echo '<br><a href="ensenarContacto.php?buscar='.$buscar.'&id='.$id.'&herr_rec_ent=1">Ver Herr. Rec. y Entr.</a>';
				 }
    } 


			
			
			
			
			
//*****************		
///////////////////////
			
} elseif (strtoupper($tipo)=='CLIENTE') {


   $insertSQL = "SELECT cambio FROM tipodecambio ORDER BY fecha DESC LIMIT 0,1";
   $resultCurrency = mysql_query($insertSQL);
   $rowCurrency = mysql_fetch_array($resultCurrency);
   $cambio=$rowCurrency['cambio'];
    echo '<table summary="" border="1" cellpadding="2" cellspacing="0">';
    echo '<tr><td><b>Pago. Prom.</b></td><td><b>Deuda</b></td><td><b>Ult. Pago</b></td><td><b>Prox. Pago</b></td></tr></td></tr>';

		echo '<tr><td valign="top" align=center>';

											$insertSQL = "SELECT amount,date,currency FROM payments WHERE client =".$row['id']." ORDER BY date DESC";
											$resultPago = mysql_query($insertSQL);
											$num_rows = mysql_num_rows($resultPago);
											$no_of_payment=0;
											if ($num_rows==0) {
											   echo 'N/P';
											} else {
											   $average_payment=0;
											   while ($rowPago = mysql_fetch_array($resultPago)) {
												 		if ($rowPago['currency']=='usd')
													  	 $average_payment+=$rowPago['amount']*$cambio;
														else
													  	 $average_payment+=$rowPago['amount'];
														
														if ($no_of_payment==6)
														   break;
														if ($rowPago['date']!=$date)
															 $no_of_payment++;
														$date=$rowPago['date'];
											   }
										     echo number_format($average_payment/$no_of_payment,2,'.',',');
											}
											echo "</td>";
											$id_cliente=$row['id'];
											$insertSQL = "SELECT sum( precioTotal * (1+IVA) - Pagado ) FROM Cotizacion WHERE factura!='0' AND Pedido != '0000-00-00' AND moneda='usd' AND `Pagado` < 1.14 * `precioTotal` AND cliente =".$row['id']." ORDER BY facturaFecha DESC";
											$resultCredit = mysql_query($insertSQL);
											$rowCredit = mysql_fetch_array($resultCredit);
											$credit=0;
											$credit=$rowCredit['sum( precioTotal * (1+IVA) - Pagado )']*$cambio;
											$insertSQL = "SELECT sum( precioTotal * (1+IVA) - Pagado ) FROM Cotizacion WHERE factura!='0' AND Pedido != '0000-00-00' AND moneda='mxn' AND `Pagado` < 1.14 * `precioTotal` AND cliente =".$row['id']." ORDER BY facturaFecha DESC";
											$resultCredit = mysql_query($insertSQL);
											$rowCredit = mysql_fetch_array($resultCredit);
											$credit+=$rowCredit['sum( precioTotal * (1+IVA) - Pagado )'];
											
											echo "<td valign='top' align=center><big><b><font color='red'>".number_format($credit,2,'.',',')."</font></b></big></td>";
											$insertSQL = "SELECT date FROM payments WHERE client =".$row['id']." ORDER BY date DESC LIMIT 0,5";
											$resultPago = mysql_query($insertSQL);
											$rowPago = mysql_fetch_array($resultPago);
											$day_since_last_payment = round((strtotime('today')-strtotime($rowPago['date']))/(60*60*24));
											$lastMonth = date('Y-m-d', strtotime('last month'));		
											$average_payment=$rowPago['sum( amount )']/5;
											if ($rowPago['date']<$lastMonth and $rowCredit['sum( precioTotal * (1+IVA) - Pagado )']>0)
												 echo "<td style='color: red;' valign='top' align=right>".$day_since_last_payment." d</td>";
											else
												 echo "<td valign='top' align=center>".$day_since_last_payment." d</td>";

                      $insertSQL = "SELECT * FROM Cotizacion WHERE factura!=0 AND Pedido != '0000-00-00' AND `Pagado` < 1.14 * `precioTotal` AND cliente=".$id." ORDER BY factura LIMIT 0 , 1 ";
                     	$result = mysql_query($insertSQL);
                  		$num_rows = mysql_num_rows($result);
                  	  $row = mysql_fetch_array($result);
                      echo '<td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['id'].'">';
                  		$year = substr($row['facturaFecha'],0,4);
                  		$month = substr($row['facturaFecha'],5,2);									 
                  		$day = substr($row['facturaFecha'],8,2);	
                  							 								 
                      echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['CondPago']+1)*86400 - time(void))/86400));
											echo ' d</td>';
          						echo "</tr>";		
		
		
		echo '</table>';


	  $flujo = $_REQUEST["flujo"];
		if ($flujo==1) {
		
          //showing reporte	 
/*          	 		$insertSQL = "SELECT * FROM inventarioCamionetas WHERE vendedor='".$idVendedor."' AND cantidad!=0 ORDER BY modelo";
          			$result = mysql_query($insertSQL);
  */        	 		echo '<center><h2><b>Reporte</b></h2></center>';
          	 		$i=1;
          			$total=0;
          
          
          			$today = getdate(); 
          			print("<center>HOY: ".$today[mday]."/".$today[mon]."/".$today[year]."</center>");

      					echo '<center><iframe src="../precios.php?id='.$id.'" width="1001" height="300" FRAMEBORDER=0 SCROLLING=NO>';
        				echo '';
      					echo '</iframe></center>';  

          	 		echo '<table summary="" border="1" cellpadding="2" cellspacing="0">';
          			echo '<tr><td><b>Semana</b></td><td><b>Entregas</b></td><td><b>Cobranzas</b></td></tr>';
          			
          
//          		  echo '<tr><td align=center>Hoy</td>';
          
          			$firstdate = mktime(0, 0, 0, $today[mon], $today[mday], $today[year]);			
          			$firstdate = date("Y-m-d", $firstdate);
           			$insertSQL = 'select sum(precioLista) from cotizacionHerramientas where cliente='.$id.' AND Entregado="'.$firstdate.'"';
          			$resultEntrega = mysql_query($insertSQL);
          			$rowEntrega = mysql_fetch_array($resultEntrega);
//          			echo '<td align=right>'.$rowEntrega['sum(precioLista*1.15)'].'</td>';
          
          			$insertSQL = 'select sum(amount) from payments where client='.$id.' AND date="'.$firstdate.'"';
          			$resultCobranza = mysql_query($insertSQL);
          			$rowCobranza = mysql_fetch_array($resultCobranza);
//          			echo '<td align=right>'.$rowCobranza['sum(amount)'].'</td></tr>';
          
          
          
          $week=0;
          while ($week<12) {
          $firstdate = mktime(0, 0, 0, $today[mon], $today[mday], $today[year])-7*($week+1)*86400;			
          $firstdate = date("Y-m-d", $firstdate);
          $lastdate = mktime(0, 0, 0, $today[mon], $today[mday], $today[year])-7*$week*86400;			
          $lastdate = date("Y-m-d", $lastdate);
          		  echo '<tr><td align=center>'.$week.'</td>';
          
								$insertSQL = 'select cambio from tipodecambio order by fecha desc limit 0,1';  
          			$resultTC = mysql_query($insertSQL);
          			$rowTC = mysql_fetch_array($resultTC);
		
          			$insertSQL = 'select sum(cantidad*precioLista) from cotizacionHerramientas where moneda="usd" and cliente='.$id.' AND Entregado<="'.$lastdate.'" AND Entregado>"'.$firstdate.'"' ;
          			$resultEntrega = mysql_query($insertSQL);
          			$rowEntregaUSD = mysql_fetch_array($resultEntrega);
          			$insertSQL = 'select sum(cantidad*precioLista) from cotizacionHerramientas where moneda="mxn" and cliente='.$id.' AND Entregado<="'.$lastdate.'" AND Entregado>"'.$firstdate.'"' ;
          			$resultEntrega = mysql_query($insertSQL);
          			$rowEntregaMXN = mysql_fetch_array($resultEntrega);
								$EntregaWeek=(1+$IVA)*$rowEntregaMXN['sum(cantidad*precioLista)']+$rowEntregaUSD['sum(cantidad*precioLista)']*$rowTC['cambio'];								
          			echo '<td align=right>'.round($EntregaWeek,2).'</td>';
          
          			$insertSQL = 'select sum(amount) from payments where currency="usd" AND client="'.$id.'" AND date<="'.$lastdate.'" AND date>"'.$firstdate.'"' ;
          			$resultCobranza = mysql_query($insertSQL);
          			$rowCobranzaUSD = mysql_fetch_array($resultCobranza);
          			$insertSQL = 'select sum(amount) from payments where currency="mxn" AND client="'.$id.'" AND date<="'.$lastdate.'" AND date>"'.$firstdate.'"' ;
          			$resultCobranza = mysql_query($insertSQL);
          			$rowCobranzaMXN = mysql_fetch_array($resultCobranza);
								$CobranzaWeek=$rowCobranzaMXN['sum(amount)']+$rowCobranzaUSD['sum(amount)']*$rowTC['cambio'];
          			echo '<td align=right>'.round($CobranzaWeek,2).'</td></tr>';
          '<br>';
          $week+=1;
          $Entrega+=$EntregaWeek;
          $Cobranza+=$CobranzaWeek;
          }
          $Entrega=$Entrega/$week;
          $Cobranza=$Cobranza/$week;
          
          			echo '<tr><td><b>Promedio</b></td><td align=right><b>'.round($Entrega,2).'</b></td><td align=right><b>'.round($Cobranza,2).'</b></td></tr>';
          
          			echo '</table>';	 
          
          			
          			echo '<br>Total: '.$total.'</b>';
          
		
		
		
		}





    if (isset($buscar) and $buscar!='') {
		//buscar en facturas no entregados
	  $insertSQL = "SELECT * FROM Cotizacion WHERE Pedido!='0000-00-00' AND fechaEntregado='0000-00-00' and cliente=".$id." ORDER BY factura DESC";
		$result = mysql_query($insertSQL);
	
		echo "<b>No Entregado:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
		echo '<tr><td><center>En d�as</center></td><td>Ref.</td><td>Cliente</td><td>Contacto</td><td>Fecha</td><td>Cant.</td><td>Suma</td></tr>';
		$i=1;
		while ($row = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);

  			  $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
					$precioTotal=number_format($row["precioTotal"],2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($r["nombreEmpresa"].$row["contacto"].$row["fecha"].$row["precioTotal"].$precioConIva.$row["Pedido"].$row["factura"].$row["facturaFecha"].$row["NoPedClient"].$row["ref"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {

	    			 echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?deMenu=1&numero='.$row["ref"].'&fecha='.$row["fecha"].'&contacto='.$row['contacto'].'&CotizacionNo='.$row['id'].'&cliente='.$row['cliente'].'&NOPartidas='.$NOPartidas.'&id='.$row["id"].'">';
						 $year = substr($row['Pedido'],0,4);
						 $month = substr($row['Pedido'],5,2);									 
						 $day = substr($row['Pedido'],8,2);	
						 echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['TiempoEntrega']+1)*86400 - time(void))/86400));
			
						 echo '</a></center></td><td valign="top">';
						 if ($row["factura"]!=0)
			   		 		echo $row["factura"];
			       else
			   		 		echo $row["ref"].'/'.$row["NoPedClient"];
			       echo '</td><td valign="top">'.$r["nombreEmpresa"].'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["Pedido"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$precioTotal.'</td></tr>';
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
				 $i++;
    }
	  echo '</table>';




		
		//buscar en facturas no pagados
    $insertSQL = "SELECT * FROM Cotizacion WHERE factura!='0' AND Pedido != '0000-00-00' AND `Pagado` < ((1+`IVA`) * `precioTotal`)-.01 AND cliente=".$id." ORDER BY factura DESC";
   	$result = mysql_query($insertSQL);
		echo "<b>No Pagado:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
		echo '<tr><td><center>En d�as</center></td><td>Ref.---</td><td>Cliente</td><td>Contacto</td><td>Fecha</td><td>Fecha Pago</td><td>Cant.</td><td>Suma</td></tr>';
		$i=1;
		
		while ($row = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);
			    $precioConIva=ROUND($row["precioTotal"]+$row["ivaTotal"],2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($r["nombreEmpresa"].$row["contacto"].$row["fecha"].$row["precioTotal"].$precioConIva.$row["Pedido"].$row["factura"].$row["facturaFecha"].$row["NoPedClient"].$row["ref"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {

	    			 echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?deMenu=1&numero='.$row["ref"].'&fecha='.$row["fecha"].'&contacto='.$row['contacto'].'&CotizacionNo='.$row['id'].'&cliente='.$row['cliente'].'&NOPartidas='.$NOPartidas.'&id='.$row["id"].'">';
						 $year = substr($row['facturaFecha'],0,4);
						 $month = substr($row['facturaFecha'],5,2);									 
						 $day = substr($row['facturaFecha'],8,2);	
						 echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['TiempoEntrega']+1)*86400 - time(void))/86400));
			
						 echo '</a></center></td><td valign="top">';
						 if ($row["factura"]!='0')
			   		 		echo $row["factura"].' ('.$row["NoPedClient"].')';
			       else
			   		 		echo $row["NoPedClient"];
			       echo '</td><td valign="top">'.$r["nombreEmpresa"].'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["facturaFecha"].'</td>';
						 
						 if ($row["fechaPagoApprox"]=='0000-00-00') 
						 		echo '<td style="color: red;" align="center" valign="top" align="right">N/A</td>';
						 elseif (((int)((mktime (0,0,0,$month,$day,$year) + ($row["fechaPagoApprox"]+1)*86400 - time(void))/86400))<=0)
 						 		echo '<td valign="top" style="color: red;" align="right">'.$row["fechaPagoApprox"].'</td>';
						 else
 						 		echo '<td valign="top" align="right">'.$row["fechaPagoApprox"].'</td>';

						 echo '<td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$precioConIva.'</td></tr>';
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
				 $i++;
    }
	  echo '</table>';
	
	
		
		//buscar en facturas terminados
	  $insertSQL = "SELECT * FROM Cotizacion WHERE fechaEntregado!='0000-00-00' and cliente=".$id." and Pagado>(1.16*precioTotal)-.1 and Pagado>0 ORDER BY factura DESC";
    $result = mysql_query($insertSQL);
		echo "<b>Terminados:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
		echo '<tr><td><center>En d�as</center></td><td>Ref.</td><td>Cliente</td><td>Contacto</td><td>Fecha</td><td>Cant.</td><td>Suma</td></tr>';
		$i=1;
		while ($row = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);
			    $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($r["nombreEmpresa"]."--".$row["contacto"]."--".$row["fecha"]."--".$row["precioTotal"]."--".$precioConIva.$row["Pedido"]."--".$row["factura"]."--".$row["facturaFecha"]."--".$row["NoPedClient"]."--".$row["ref"]);
		 if ($row["factura"]=='F12655') {
		    echo $info;
		 }
		 
		 			if (strstr($info,strtoupper($buscar))) {

	    			 echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?deMenu=1&numero='.$row["ref"].'&fecha='.$row["fecha"].'&contacto='.$row['contacto'].'&CotizacionNo='.$row['id'].'&cliente='.$row['cliente'].'&NOPartidas='.$NOPartidas.'&id='.$row["id"].'">';
						 $year = substr($row['Pedido'],0,4);
						 $month = substr($row['Pedido'],5,2);									 
						 $day = substr($row['Pedido'],8,2);	
						 echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['TiempoEntrega']+1)*86400 - time(void))/86400));
			
						 echo '</a></center></td><td valign="top">';
						 if ($row["factura"]!='0')
			   		 		echo $row["factura"].' ('.$row["NoPedClient"].')';
			       else
			   		 		echo $row["ref"];
			       echo '</td><td valign="top">'.$r["nombreEmpresa"].'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["Pedido"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$precioConIva.'</td></tr>';
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
				 $i++;
    }
	  echo '</table>';


		
		//buscar en cotizaciones
	 	$insertSQL = "SELECT * FROM Cotizacion WHERE remision=0 and comentario='' and cliente=".$id." ORDER BY fecha DESC";
   	$result = mysql_query($insertSQL);
		echo "<b>Cotizaciones:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
		echo '<tr><td><center>En d�as</center></td><td>Ref.</td><td>Cliente</td><td>Contacto</td><td>Fecha</td><td>Cant.</td><td>Suma</td></tr>';
		$i=1;
		while ($row = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);

				  $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($r["nombreEmpresa"].$row1["modelo"].$row1["cotizacionRef"].$row1["marca"].$precioConIva.$row1["descripcion"].$row1["Proveedor"].$row1["noDePedido"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {

	    			 echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?deMenu=1&numero='.$row["ref"].'&fecha='.$row["fecha"].'&contacto='.$row['contacto'].'&CotizacionNo='.$row['id'].'&cliente='.$row['cliente'].'&NOPartidas='.$NOPartidas.'&id='.$row["id"].'">';
						 $year = substr($row['Pedido'],0,4);
						 $month = substr($row['Pedido'],5,2);									 
						 $day = substr($row['Pedido'],8,2);	
						 echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['TiempoEntrega']+1)*86400 - time(void))/86400));
			
						 echo '</a></center></td><td valign="top">';
						 if ($row["factura"]!=0)
			   		 		echo $row["factura"];
			       else
			   		 		echo $row["ref"];
			       echo '</td><td valign="top">'.$r["nombreEmpresa"].'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["Pedido"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$precioConIva.'</td></tr>';
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
				 $i++;
    }
	  echo '</table>';


		
		//buscar en pagos
	  $result = mysql_query("SELECT * FROM `payments` where client=".$id." ORDER BY factura DESC");
		
		//buscar en herramientas no entregados
	   $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE cliente=".$id." AND Pedido='si' AND Entregado='0000-00-00' ORDER BY modelo";
		 $result = mysql_query($insertSQL);

		echo "<b>Herramientas Pedido:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	     echo '<tr><td><b>#</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Precio Unidad</b></td><td><b>Pedido</b></td><td><b>Orden</b></td><td><b>Enviado</b></td><td><b>Recibido</b></td></tr>';
		$i=1;
		while ($row1 = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row1['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);

			    $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($r["nombreEmpresa"].$row1["modelo"].$row1["cotizacionRef"].$row1["marca"].$precioConIva.$row1["descripcion"].$row1["Proveedor"].$row1["noDePedido"].$row["precioTotal"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {
					       if ($row1["factura"]!='0') {
								    $cotizacionNo=$row1["factura"];
								 } else {
								    $cotizacionNo=$row1["cotizacionNo"];
								 }  
								 
	               echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$cotizacionNo.'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
			           echo '<td valign="top">'.$row1["NoPedClient"].'</td><td valign="top"><a href="../proveedores/creandoOrdenDeCompra.php?noOC='.$row1["noDePedido"].'" style="text-decoration: none;">'.$row1["noDePedido"].'</a></td>';
							   if ($row1["enviadoFecha"]=='0000-00-00') 
								    echo '<td valign="top">No</td>';
								 else
								    echo '<td valign="top">'.substr($row1["enviadoFecha"],8,2).'/'.substr($row1["enviadoFecha"],5,2).'/'.substr($row1["enviadoFecha"],0,4).'</td>';
							   if ($row1["recibidoFecha"]=='0000-00-00') 
								    echo '<td valign="top">No</td></tr>';
								 else
								    echo '<td valign="top">'.substr($row1["recibidoFecha"],8,2).'/'.substr($row1["recibidoFecha"],5,2).'/'.substr($row1["recibidoFecha"],0,4).'</td></tr>';
								 $i++;
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
    }
	  echo '</table>';



		
		//buscar en herramientas entregados
	   $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE cliente=".$id." AND Entregado!='0000-00-00' ORDER BY modelo";
  	 $result = mysql_query($insertSQL);

		echo "<b>Herramientas Entregadas:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	     echo '<tr><td><b>#</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Precio Unidad</b></td><td><b>Factura</b></td><td><b>Orden</b></td><td><b>Entregado</b></td></tr>';
		$i=1;
		while ($row1 = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row1['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);

			    $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			    
		 	 		$info=strtoupper($r["nombreEmpresa"].$row1["modelo"].$row1["cotizacionRef"].$row1["marca"].$precioConIva.$row1["descripcion"].$row1["Proveedor"].$row1["noDePedido"].$row1["precioLista"].$row1["Entregado"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {
					       if ($row1["factura"]!='0') {
								    $cotizacionNo=$row1["factura"];
	                  echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$cotizacionNo.'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
								 } elseif ($row1["remision"]!='0') {
								    $cotizacionNo=$row1["remision"];
	                  echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../remisiones/agregarHerrRemision.php?deMenu=1&remision='.$cotizacionNo.'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
								 } else {
								    $cotizacionNo=$row1["cotizacionNo"];
	                  echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$cotizacionNo.'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
								 }  
	               //echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$cotizacionNo.'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
	    		       
								 if ($row1['factura']!=0) {
								    $insertSQL = "SELECT * FROM Cotizacion WHERE id=".$row1['factura'];
								 } else {
								    $insertSQL = "SELECT * FROM Cotizacion WHERE id=".$row1['cotizacionNo'];
								 
								 }
      		          $resultNoFactura = mysql_query($insertSQL);
      		       $rowNoFactura = mysql_fetch_array($resultNoFactura);
  		           echo '<td valign="top">'.$rowNoFactura["factura"];
								 echo '</td><td valign="top"><a href="../proveedores/creandoOrdenDeCompra.php?noOC='.$row1["noDePedido"].'" style="text-decoration: none;">'.$row1["noDePedido"].'</a></td>';
							   if ($row1["Entregado"]=='0000-00-00') 
								    echo '<td valign="top">No</td>';
								 else
								    echo '<td valign="top">'.substr($row1["Entregado"],8,2).'/'.substr($row1["Entregado"],5,2).'/'.substr($row1["Entregado"],0,4).'</td>';
								 $i++;
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
    }
	  echo '</table>';



			
		//buscar en herramientas cotizados
	   $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE cliente=".$id." and Pedido='no' ORDER BY modelo";
  	 $result = mysql_query($insertSQL);

		echo "<b>Herramientas Cotizadas:</b>";
	  echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	     echo '<tr><td><b>#</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Precio Unidad</b></td><td><b>Pedido</b></td><td><b>Entregado</b></td></tr>';
		$i=1;
		while ($row1 = mysql_fetch_array($result)) {
	    		$insertSQL = "SELECT * FROM Contactos WHERE id=".$row1['cliente'];
      		$res = mysql_query($insertSQL);
      		$r = mysql_fetch_array($res);

			    $precioConIva=ROUND($row["precioTotal"],2)+ROUND($row["precioTotal"]*$row["IVA"],2);
		 			$precioConIva = ROUND($precioConIva,2);
		 			if (strstr($precioConIva,".")) {
			  		 $len = strlen($precioConIva);
						 if (substr($precioConIva,strlen($precioConIva)-2,1)==".") {
           	 		$precioConIva=$precioConIva."0";
             }
          } else {
			 			 $precioConIva=$precioConIva.".00";
					} 
			 
		 	 		$info=strtoupper($row1["modelo"].$row1["cotizacionRef"].$row1["marca"].$precioConIva.$row1["precioLista"].$row1["descripcion"].$row1["Proveedor"].$row1["noDePedido"]);
		 
		 			if (strstr($info,strtoupper($buscar))) {
	               echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../cotizaciones/agregarCotizacion.php?CotizacionNo='.$row1[cotizacionNo].'&id='.$row["id"].'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';
			           echo '<td valign="top">'.$row1["cotizacionRef"].'</td>';
							   if ($row1["Entregado"]=='0000-00-00') 
								    echo '<td valign="top">No</td>';
								 else
								    echo '<td valign="top">'.substr($row1["Entregado"],8,2).'/'.substr($row1["Entregado"],5,2).'/'.substr($row1["Entregado"],0,4).'</td>';
								 $i++;
         }

				 $total+=round($row["cantidad"]*$row["precioLista"]);
    }
	  echo '</table>';
		
		
		
		
		} else {
		
	  $insertSQL = "SELECT * FROM Cotizacion WHERE Pedido!='0000-00-00' AND fechaEntregado='0000-00-00' and cliente=".$id." ORDER BY factura DESC";
		$result = mysql_query($insertSQL);
	 	echo "<br>";
	 	echo "<b>No Entregado:</b>";
	 	echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	 	echo '<tr><td>En d�as</td><td>Ref.</td><td>Fecha</td><td>Cant.</td><td>Suma</td></tr>';
	 	$i=1;
	 	while ($row = mysql_fetch_array($result)) {
		 if ($row["fechaEntregado"]=="0000-00-00") {
	    echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['id'].'">';
			$year = substr($row['Pedido'],0,4);
			$month = substr($row['Pedido'],5,2);									 
			$day = substr($row['Pedido'],8,2);	
								 
			echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['TiempoEntrega']+1)*86400 - time(void))/86400));
			
			echo '</a></center></td><td valign="top">'.$row["ref"].'/'.$row["NoPedClient"].'</td><td valign="top" align="right">'.$row["Pedido"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$row["precioTotal"].'</td></tr>';
			$total+=round($row["cantidad"]*$row["precioLista"]);
			$i++;
		  $total_por_entregar+=$row["precioTotal"];
		 }
    }
	  echo '</table>';
		echo $total_por_entregar;
	  echo "<br><b>Herramientas que no han sido entregadas:</b>";
//	  $insertSQL = "SELECT * FROM Cotizacion WHERE Pedido!='0000-00-00' and fechaEntregado='0000-00-00' and cliente=".$id." ORDER BY fecha";
//		$result = mysql_query($insertSQL);
	  $todoMarcas=" ";
	  if (isset($marca)) {
	     echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	     echo '<tr><td><b>#</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Precio U.</b></td><td><b>Inv.</b></td><td><b>Cam.</b></td><td><b>Pedido</b></td><td width="60"><b>Orden</b></td><td><b>Entr. Est.</b></td><td><b>Enviado</b></td><td><b>Recibido</b></td></tr>';
	  }	
	  $i=1;
    if (isset($marca)==0) {
					 	 $marcas = array();
       echo "<br><a href='ensenarContacto.php?id=".$id."&marca=TODO' style='text-decoration: none;'>TODO</a>";
	  }

//	  while ($row = mysql_fetch_array($result)) {
	    $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE pedidoFecha!='0000-00-00' and cliente=".$id." and Entregado='0000-00-00' order by modelo ";//CotizacionNo='".$row['id']."' AND 
		  $result1 = mysql_query($insertSQL);
	    while ($row1 = mysql_fetch_array($result1)) {
		     if ($row1['Entregado']=="0000-00-00") {
			     if (isset($marca)) {
				      if ($row1["marca"]==$marca or $marca=='TODO') {
	               echo '<tr><td valign="top">';
								 
								 if ($row1['factura']!=0) {
								 		 echo '<a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row1['factura'].'&id='.$row["id"].'">'.$i.'</a>';
								 } else {
								 		 echo '<a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row1['cotizacionNo'].'&id='.$row["id"].'">'.$i.'</a>';								 
								 }
								 echo '</td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td>';

          			 //que hay en existencia
         			 
          			 $insertSQL1 = "SELECT * FROM `marcadeherramientas`";
           	     $resultMarcasHerr = mysql_query($insertSQL1);
          			 $marca11='none';
          	     while ($rowMarcasHerr = mysql_fetch_array($resultMarcasHerr)) {
          			    if ($row1["marca"]==$rowMarcasHerr["marca"])
          					   $marca11=$rowMarcasHerr["marca"];
          			 }
          			 echo '<td valign="top" align="center">';
          			 if ($marca11!='none') {
          	        $insertSQL = "SELECT enReserva FROM Precio".$row1["marca"]." where ref='".$row1["modelo"]."'";
           	        $resultAlm = mysql_query($insertSQL);
           	        $rowAlm = mysql_fetch_array($resultAlm);
          			    echo $rowAlm['enReserva']; 
          			 } else {
          			    echo 'N/A'; 			 
          			 }
          			 echo '</td>';


          			   //mysql_close($conn);
          	       include "../incl/connect_trucks.incl";
          				 
          				 $insertSQL = "SELECT * FROM `inventariocamionetas` where marca = '".$row1["marca"]."' and modelo = '".$row1["modelo"]."' and cantidad > 0";
          				 $result_trucks=mysql_query($insertSQL);
          				 
          				 $number=mysql_num_rows($result_trucks);
			   					 
									 if ($number!=0)
									    echo '<td align=center><a href="../embarques/camionetas/camionetas.php?idVendedor=adm&menu=admInventario&busca='.$row1["modelo"].'" style="text-decoration: none;">'.$number.'</a></td>';
									 else
									    echo '<td align=center>-</td>';
          				 
          			   mysql_query($insertSQL);
          			   //mysql_close($conn);
          	       include "../incl/connect.incl";
									 
								 
								 
								 

			           if ($row1["noDePedido"]=='' and  $row1["Proveedor"]=='ALMACEN') {
          				  $insertSQL = "SELECT NoPedClient FROM `cotizacion` where id=".$row1["cotizacionNo"];
          				  $result_NoPed=mysql_query($insertSQL);
										$row_NoPed = mysql_fetch_array($result_NoPed);
								    echo '<td valign="top">'.$row_NoPed["NoPedClient"].'</td><td valign="top">'.$row1["Proveedor"].'</td>';								 
			           } elseif ($row1["noDePedido"]=='' and  $row1["Proveedor"]!='') {
          				  $insertSQL = "SELECT NoPedClient FROM `cotizacion` where id=".$row1["cotizacionNo"];
          				  $result_NoPed=mysql_query($insertSQL);
										$row_NoPed = mysql_fetch_array($result_NoPed);
								    echo '<td valign="top">'.$row_NoPed["NoPedClient"].'</td><td valign="top" style="color: ORANGE">SIN PED.</td>';								 
				         } elseif ($row1["noDePedido"]=='') {
          				  $insertSQL = "SELECT NoPedClient FROM `cotizacion` where id=".$row1["cotizacionNo"];
          				  $result_NoPed=mysql_query($insertSQL);
										$row_NoPed = mysql_fetch_array($result_NoPed);
								    echo '<td valign="top">'.$row_NoPed["NoPedClient"].'</td><td valign="top" style="color: RED">SIN PROV.</td>';								 
								 } else {								 
          				  $insertSQL = "SELECT NoPedClient FROM `cotizacion` where id=".$row1["cotizacionNo"];
          				  $result_NoPed=mysql_query($insertSQL);
										$row_NoPed = mysql_fetch_array($result_NoPed);
								    echo '<td valign="top">'.$row_NoPed["NoPedClient"].'</td><td valign="top"><a href="../proveedores/creandoOrdenDeCompra.php?noOC='.$row1["noDePedido"].'" style="text-decoration: none;">'.$row1["noDePedido"].'</a></td>';
								 }
								 
            			$year = substr($row1['entrega_estimado'],0,4);
            			$month = substr($row1['entrega_estimado'],5,2);									 
            			$day = substr($row1['entrega_estimado'],8,2);	
            								 
            			$late = ((int)((mktime (0,0,0,$month,$day,$year)  - time(void))/86400));
								 if ($late<0)
								    echo '<td valign="top" align="center" style="color: red">'.substr($row1["entrega_estimado"],8,2).'/'.substr($row1["entrega_estimado"],5,2).'/'.substr($row1["entrega_estimado"],2,2).'</td>';
							   else
								    echo '<td valign="top" align="center">'.substr($row1["entrega_estimado"],8,2).'/'.substr($row1["entrega_estimado"],5,2).'/'.substr($row1["entrega_estimado"],2,2).'</td>';
								 
								 if ($row1["enviadoFecha"]=='0000-00-00') 
								    echo '<td valign="top">No</td>';
								 else
								    echo '<td valign="top">'.substr($row1["enviadoFecha"],8,2).'/'.substr($row1["enviadoFecha"],5,2).'/'.substr($row1["enviadoFecha"],0,4).'</td>';
							   if ($row1["recibidoFecha"]=='0000-00-00') 
								    echo '<td valign="top">No</td></tr>';
								 else
								    echo '<td valign="top">'.substr($row1["recibidoFecha"],8,2).'/'.substr($row1["recibidoFecha"],5,2).'/'.substr($row1["recibidoFecha"],0,4).'</td></tr>';
								 $i++;
						  }	
							
				   } else {


				     if (strstr($todoMarcas,$row1["marca"])) {
					   
						 } else {			
						 	 $marcas[$row1["id"]]=$row1["marca"];
						   $todoMarcas=$todoMarcas.$row1["marca"];

					   }


				   }		    
         }
	    }
	  //}	 
		if (isset($marca)) {
	    echo '</table>';
		} else {
             asort($marcas);
             foreach ($marcas as $key => $val) {
				         echo "<br><a href='ensenarContacto.php?id=".$id."&marca=".$val."' style='text-decoration: none;'>".$val."</a>";
             }
		}
    echo "<br>";



	 	echo "<b>No Pagado:</b>";
	 	echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	 	echo '<tr><td>En d�as</td><td>Factura</td><td>Cliente</td><td>Ref.</td><td>Fecha</td><td>Fecha Pago</td><td>Pagado</td><td>Suma</td><td>Mon.</td><td>Rec.</td></tr>';
	 	$i=1;
	 	$total=0;
    $insertSQL = "SELECT * FROM Cotizacion WHERE factura!='0' AND Pedido != '0000-00-00' AND `Pagado` < ((1+`IVA`) * `precioTotal`)-0.01 AND cliente=".$id." ORDER BY factura DESC";
   	$result = mysql_query($insertSQL);
		$num_rows = mysql_num_rows($result);
	 	echo "<br>";
		$facturas = array();
	  while ($row = mysql_fetch_array($result)) {
		    if (strstr(strtoupper($row['factura']),'F'))
		   	    $facturas[$row['id']]='999999'.substr($row['factura'],1);
				else
		   	    $facturas[$row['id']]=$row['factura'];
		}
asort($facturas);
foreach ($facturas as $key => $val) {
    $insertSQL = "SELECT * FROM Cotizacion WHERE id=".$key;
   	$result = mysql_query($insertSQL);
		$row = mysql_fetch_array($result);
	      echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['id'].'">';
				$year = substr($row['facturaFecha'],0,4);
				$month = substr($row['facturaFecha'],5,2);									 
				$day = substr($row['facturaFecha'],8,2);	
									 								 
        echo ((int)((mktime (0,0,0,$month,$day,$year) + ($row['CondPago']+1)*86400 - time(void))/86400));
			  echo '</a></center></td><td>'.$row["factura"].'</td><td>'.$row["clienteContacto"].'</td><td valign="top">';
				
				if ($row["NoPedClient"]!='') {
            $file_pointer = '../pedidos/test_uploads/'.$row["NoPedClient"].'.pdf';
    				$pos = strpos($row["NoPedClient"], ' ', 0);
            $file_pointer1 = '../pedidos/test_uploads/'.substr($row["NoPedClient"],0,$pos).'.pdf';
    				$pos = strpos($row["NoPedClient"], ' ', $pos+1);				
            $file_pointer2 = '../pedidos/test_uploads/'.substr($row["NoPedClient"],0,$pos).'.pdf';
    				$pos = strpos($row["NoPedClient"], ' ', $pos+1);				
            $file_pointer3 = '../pedidos/test_uploads/'.substr($row["NoPedClient"],0,$pos).'.pdf';
            if (file_exists($file_pointer) or file_exists($file_pointer1) or file_exists($file_pointer2) or file_exists($file_pointer3)) {
    			      echo ' <a href="../pedidos/test_uploads/'.$row["NoPedClient"].'.pdf" target="_blank" style="text-decoration: none;">'.$row["NoPedClient"].'</a>';
            }else {
    			      echo ' <a href="../pedidos/test_uploads/'.$row["NoPedClient"].'.pdf" target="_blank" style="text-decoration: none; color: #ff0000;">'.$row["NoPedClient"].'</a>';
            }			
				}
				
				echo '</td>';
				
				
				echo '<td valign="top" align="right">'.substr($row["facturaFecha"],8,2).'-'.substr($row["facturaFecha"],5,2).'-'.substr($row["facturaFecha"],2,2).'</td>';

						 if ($row["fechaPagoApprox"]=='0000-00-00') 
						 		echo '<td style="color: red;" align="center" valign="top" align="right">N/A</td>';
						 elseif (((int)((mktime (0,0,0,$month,$day,$year) + ($row["fechaPagoApprox"]+1)*86400 - time(void))/86400))<=0)
 						 		echo '<td valign="top" style="color: red;" align="right">'.$row["fechaPagoApprox"].'</td>';
						 else
 						 		echo '<td valign="top" align="right">'.$row["fechaPagoApprox"].'</td>';

				
				echo '<td valign="top" align="right">'.$row["Pagado"].'</td><td valign="top" align="right">';
				
		 			$precioConIva=round($row["precioTotal"]+$row["ivaTotal"],2);
				echo number_format($precioConIva,2);

				echo '</td>';
				
				
				
				echo '<td valign="top" align="right">'.$row["moneda"].'</td>';
				

          $file_pointer_rec = '../pedidos/recibos/'.$row["id"].'.pdf';
          if (file_exists($file_pointer_rec)) {
          	 echo '<td align="center"><b><a href="../pedidos/recibos/'.$row["id"].'.pdf" style="text-decoration: none;">V</a></b></td>';
          }else {
          	 echo '<td align="center" style="color: red"><b>X</b></td>';
          }			
				
				
				echo '</tr>';

    				if ($row["moneda"]=='usd')
    			     $total+=($row["precioTotal"]+$row["ivaTotal"]-$row["Pagado"])*$cambio;
    				else
    			     $total+=$row["precioTotal"]+$row["ivaTotal"]-$row["Pagado"];

			  $i++;
		
}

	




	  echo '</table>';
    echo "Credito: <b>".number_format($total,2)."</b><br><br>";
	 

						 
		
	 $insertSQL='SELECT sum( preciolista * cantidad ) FROM `CotizacionHerramientas` WHERE `factura` = 0 AND `remision` != 0 AND moneda="usd" AND cliente='.$id;
   $resultRemision = mysql_query($insertSQL);
	 $rowRemision = mysql_fetch_array($resultRemision);
   $today = getdate(); 
   $resultCambio = mysql_query("SELECT * FROM tipoDeCambio WHERE fecha='$today[year]-$today[mon]-$today[mday]'");
   $rowCambio = mysql_fetch_array($resultCambio);
	 $valueRemision=round($rowRemision['sum( preciolista * cantidad )'],2)*$rowCambio['cambio'];
	 $insertSQL='SELECT sum( preciolista * cantidad ) FROM `CotizacionHerramientas` WHERE `factura` = 0 AND `remision` != 0 AND moneda="mxn" AND cliente='.$id;
   $resultRemision = mysql_query($insertSQL);
	 $rowRemision = mysql_fetch_array($resultRemision);
	 $valueRemision+=round($rowRemision['sum( preciolista * cantidad )'],2);
	 $valueRemision=round($valueRemision,2);
	 
	 $PendingRemision=$_REQUEST["PendingRemision"];

	  if (isset($PendingRemision)==0) {
	     echo '<a href="ensenarContacto.php?id='.$id.'&PendingRemision=1">Pendiente para facturar: <b>'.$valueRemision.'</b></a>';
	     echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	     echo '<tr><td><b>#</b></td><td><b>Marca</b></td><td><b>Modelo</b></td><td><b>Descripci�n</b></td><td><b>Cant.</b></td><td><b>Precio U.</b></td><td><b>Mon.</b></td><td><b>Remision</b></td><td><b>Entr.</b></td></tr>';
	  	 $i=1;
    	 $insertSQL = "SELECT * FROM CotizacionHerramientas WHERE `factura` = 0 AND `remision` != 0 AND cliente=".$id." order by modelo";
		   $result1 = mysql_query($insertSQL);
	     while ($row1 = mysql_fetch_array($result1)) {
	               echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../remisiones/agregarHerrRemision.php?deMenu=1&&remision='.$row1["remision"].'">'.$i.'</a></td><td valign="top">'.$row1["marca"].'</td><td valign="top">'.$row1["modelo"].'</td><td valign="top">'.$row1["descripcion"].'</td><td valign="top" align="right">'.$row1["cantidad"].'</td><td valign="top" align="right">'.$row1["precioLista"].'</td><td>'.$row1["moneda"].'</td><td align="center">'.$row1["remision"].'</td><td align="center">'.$row1["Entregado"].'</td>';
								 $i++;
	     }	 
	    echo '</table>';
      echo "<br>";
   } else {
	    echo '<a href="ensenarContacto.php?id='.$id.'">Remisiones: <b>'.$valueRemision.'</b></a>';
	    $insertSQL = "SELECT * FROM Cotizacion WHERE Comentario='' and remision!=0 and remisionFactura=0 AND cliente=".$id." ORDER BY fecha DESC";
      $result = mysql_query($insertSQL);
	    echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	    echo '<tr><td><center>#</center></td><td>Remision</td><td>Cliente</td><td>Contacto</td><td>Fecha</td><td>Cant.</td><td>Suma</td><td>Mon.</td></tr>';
	    $i=1;$total=0;
	    while ($row = mysql_fetch_array($result)) {
	 					$insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
   					$res = mysql_query($insertSQL);
   					$r = mysql_fetch_array($res);
	    			echo '<tr><td valign="top"><center><a  style="text-decoration: none;" href="../remisiones/agregarHerrRemision.php?deMenu=1&remision='.$row['remision'].'">'.$i.'</a></center></td><td valign="top">'.$row["remision"].'</td><td valign="top">'.$r["nombreEmpresa"].'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["remisionFecha"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td><td valign="top" align="right">'.$row["precioTotal"].'</td><td>'.$row["moneda"].'</td></tr>';
						$total+=round($row["precioTotal"]);
						$i++;
      }
	 		echo '</table>';
   }

	 
	 $i_cotizacion = $_REQUEST["i_cotizacion"];
	 if (isset($i_cotizacion)==0) {
	    $i_cotizacion=0;
	 }
	           include "../incl/connect.incl";
	 				   $insertSQL = "SELECT * FROM Cotizacion WHERE remision=0 and comentario='' and cliente=".$id." ORDER BY fecha DESC limit ".$i_cotizacion.",30";
   				   $result = mysql_query($insertSQL);
					 	 echo '<br><br><br><b>Cotizaciones</b>';
					 	 echo '<table border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
						 echo '<tr><td>Ref.</td><td>Cliente</td><td>Contacto</td><td>Fecha</td></tr>';
	 					 $i=1;
	 					 while ($row = mysql_fetch_array($result)) {
	 					   $insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
   					   $res = mysql_query($insertSQL);
   					   $r = mysql_fetch_array($res);
	    			   echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../cotizaciones/agregarCotizacion.php?deMenu=1&numero='.$row["ref"].'&fecha='.$row["fecha"].'&contacto='.$row['contacto'].'&CotizacionNo='.$row['id'].'&cliente='.$row['cliente'].'&NOPartidas='.$NOPartidas.'&id='.$row["id"].'">'.$row["ref"].'</a></td><td valign="top">'.substr($r["nombreEmpresa"],0,30).'</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["fecha"].'</td></tr>';
						   $total+=round($row["cantidad"]*$row["precioLista"]);
						   $i_cotizacion++;
   				   }
	           echo '</table>';
$j=30+$i_cotizacion;$k=$i_cotizacion-60;$kk=$i_cotizacion-59;$l=$i_cotizacion-30;$ii=$i_cotizacion+1;
if ($i_cotizacion>30) 
   echo '<a href="ensenarContacto.php?id='.$id.'&i_cotizacion='.$k.'">'.$kk.'-'.$l.'</a> - ';
if ($i_cotizacion>29) 
echo '<a href="ensenarContacto.php?id='.$id.'&i_cotizacion='.$i_cotizacion.'">'.$ii.'-'.$j.'</a>';




						 
	 $terminados=$_REQUEST["terminados"];					 
   if ($terminados==1) {  						 
      
      	 echo "<br><br>";
      	 echo "<b>Entregado y Pagado:</b>";
      	 $insertSQL = "SELECT * FROM Cotizacion WHERE fechaEntregado!='0000-00-00' and cliente=".$id." and `Pagado` > ((1+`IVA`) * `precioTotal`)-0.1 AND factura!='0'  ORDER BY factura DESC";
         $result = mysql_query($insertSQL);
      	 echo '<table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
      	 echo '<tr><td>#</td><td>Ref.</td><td>Contacto</td><td>Fecha</td><td>Cant.</td><td>Suma</td><td>Pago</td></tr>';
      	 $i=1;
      	 while ($row = mysql_fetch_array($result)) {
      	     $insertSQL = "SELECT * FROM Contactos WHERE id=".$row['cliente'];
             $res = mysql_query($insertSQL);
             $r = mysql_fetch_array($res);
      	     echo '<tr><td valign="top"><a  style="text-decoration: none;" href="../pedidos/verPedido.php?CotizacionNo='.$row['id'].'">'.$i.'</a></td><td valign="top">';
      			 if ($row["factura"]!='0') {
      			    echo $row["factura"];
      			 } else {
      			    echo $row["ref"];
      			 }
      			 echo '</td><td valign="top">'.$row["contacto"].'</td><td valign="top" align="right">'.$row["fecha"].'</td><td valign="top" align="right">'.$row["partidaCantidad"].'</td>';

 						 echo '<td valign="top" align="right">'.NUMBER_FORMAT((1+$row["IVA"])*$row["precioTotal"],2);
						 echo '</td>';
 						 echo '<td valign="top" align="right">'.NUMBER_FORMAT($row["Pagado"],2);

      			 echo '</td></tr>';
      			 $total+=round($row["cantidad"]*$row["precioLista"]);
      			 $i++;
         }
      	 echo '</table><br><br>';
      
         echo '<b><center>Pagos</center></b>';
      	 $result = mysql_query("SELECT * FROM `payments` where client=".$id." ORDER BY date DESC");
      	 echo '<table summary="" border="1" cellpadding="2" cellspacing="0">';
         echo '<tr><td>#</td><td>Fecha</td><td>Factura</td><td>Cuenta</td><td>Cantidad</td></tr>';
      	 $i=1; $amount=0; $date='';
         while ($row = mysql_fetch_array($result)) {
      	   if ($date!=$row['date'] and $date!='') {
      	 	   	$result_payments = mysql_query("SELECT * FROM `payments` where client=".$id." and date=".$date." ORDER BY no_pago_diario ");
         		 	while ($row = mysql_fetch_array($result)) {
					 
                 echo '<tr><td>'.$i.'</td><td>'.$date.'</td><td>';
          		   if ($row['tipo']!='factura')
          		  	  echo "R";
          		   echo $factura.'</td><td>';
          			 $factura='';
          		   echo $account;
          		   echo '</td><td align=right>'.$amount.'</td>';
          			 $amount=0;
          		   $i++;
						 	}
           }
      		 $date=$row['date'];
      		 $amount+=$row['amount'];
      		 $factura=$factura.$row['factura'].', ';
      		 
           $insertSQL="SELECT * FROM `accounts` WHERE id=".$row['account'];
      	   $result1 = mysql_query($insertSQL);
      	   $row1 = mysql_fetch_array($result1);
      	   $account=$row1['nombre'];
      
         }
             echo '<tr><td>'.$i.'</td><td>'.$date.'</td><td>';
      		   echo $factura.'</td><td>';
      			 $factura='';
      		   echo $account;
      		   echo '</td><td align=right>'.$amount.'</td>';
      	 
      	 }
         echo '</table>';

	 } 
	 if ($terminados!=1) {


         echo '<b><center>Pagos</center></b>';
      	 $result = mysql_query("SELECT * FROM `payments` where client=".$id." ORDER BY date DESC LIMIT 0 , 300 ");
      	 echo '<center><table summary="" border="1" cellpadding="2" cellspacing="0">';
         echo '<tr><td>#</td><td>Fecha</td><td>Factura</td><td>Cuenta</td><td>Cantidad</td></tr>';
      	 $i=1; $amount=0;$date=''; $test_string='';
         while ($row = mysql_fetch_array($result)) {
  				   if (strstr($test_string,'+*'.$row['date'].'**'.$row['no_pago_diario'].'**'.$row['account'].'*+')=='') {
  
  
  
                 echo '<tr><td>'.$i.'</td><td>'.$row['date'].'</td><td>';
								 
								 $insertSQL="SELECT * FROM `payments` where client=".$id." and date='".$row['date']."' and no_pago_diario=".$row['no_pago_diario']." and account=".$row['account']." ORDER BY factura ";
								 $result_pago = mysql_query($insertSQL);
								 while ($row_pago = mysql_fetch_array($result_pago)) {
								 
								    $factura.=$row_pago['factura'].', ';
								    $amount+=$row_pago['amount'];
								 }
          		   if ($row['tipo']!='factura')
          		  	  echo "R";
          		   echo $factura.'</td><td>';
  
                 $insertSQL="SELECT * FROM `accounts` WHERE id=".$row['account'];
            	   $result1 = mysql_query($insertSQL);
            	   $row1 = mysql_fetch_array($result1);
            	   $account=$row1['nombre'];
  
          		   echo $account;
          		   echo '</td><td align=right>'.$amount.'</td></tr>';
          			 $amount=0;$factura='';
          			 //		   echo '<td><a href="payments.php?id='.$row['id'].'&delete=si">Eliminar</a></td></tr>';
          		   $i++;
  
  						   $test_string.='+*'.$row['date'].'**'.$row['no_pago_diario'].'**'.$row['account'].'*+';
             }
      		 
      
         }
      	 
      	 
         echo '</table></center>';
	 

				 



	 
	 
	 			 echo '<br><center><a href="ensenarContacto.php?terminados=1&id='.$id.'">Terminados</a></center>';
					
	 }
	 
   echo $send_statement=$_REQUEST["send_statement"];
	 if ($send_statement==1) {
	 		include "../incl/send_statement.php";	 
	 }
	 
	 
	 
	 
	  	 mysql_close($conn);
}

	 
 ?> 


</center></td></tr>
</table>


</body>
