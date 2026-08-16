<?php 
$ALL = $_REQUEST["ALL"];
$client = $_REQUEST["id"];

 ?>
<html>
<head>
<title>Estado de Cuenta</title>
<link rel="stylesheet" type="text/css" href="../fonts.css" />
</head>
<body>
<?php 
	  include "../incl/connect.incl";
    $html_for_email.= "<br>";
		
$today = getdate(); 
$resultCambio = mysql_query("SELECT * FROM tipoDeCambio WHERE fecha='$today[year]-$today[mon]-$today[mday]'");
$rowCambio = mysql_fetch_array($resultCambio);
$usd=$rowCambio['cambio'];
		
		
		
     $today = getdate();
 		 $year = $today[year];
 		 $month = $today[mon];
 		 $day = $today[mday];

		
	 	$html_for_email.= "<br>";
	 	$html_for_email.= "<b><big><center>ESTADO DE CUENTA:</center></big></b><br>";
		$insertSQL = "SELECT nombreEmpresa FROM  `contactos` WHERE id =".$client;
		$resultContacto = mysql_query($insertSQL);
		$rowContacto = mysql_fetch_array($resultContacto);
		$html_for_email.= "<center><big><b>".strtoupper($rowContacto['nombreEmpresa'])."</b></big></center>";
		$today = getdate(); 
		$html_for_email.= "<center><b>".$today[mday]."/".$today[mon]."/".$today[year]."</b></center>";



		 $fecha0_timestamp = ((int)((mktime (0,0,0,$month,$day,$year) + 0*86400)));
		 $fecha0_array  = getdate( $fecha0_timestamp  );
		 $fecha0 = $fecha0_array[year].'-'.$fecha0_array[mon].'-'.$fecha0_array[mday];
		 $fecha30m_timestamp = ((int)((mktime (0,0,0,$month,$day,$year) -30*86400)));
		 $fecha30m_array  = getdate( $fecha30m_timestamp  );
		 $fecha30m = $fecha30m_array[year].'-'.$fecha30m_array[mon].'-'.$fecha30m_array[mday];
		 $fecha45m_timestamp = ((int)((mktime (0,0,0,$month,$day,$year) -45*86400)));
		 $fecha45m_array  = getdate( $fecha45m_timestamp  );
		 $fecha45m = $fecha45m_array[year].'-'.$fecha45m_array[mon].'-'.$fecha45m_array[mday];
		 $fecha60m_timestamp = ((int)((mktime (0,0,0,$month,$day,$year) -60*86400)));
		 $fecha60m_array  = getdate( $fecha60m_timestamp  );
		 $fecha60m = $fecha60m_array[year].'-'.$fecha60m_array[mon].'-'.$fecha60m_array[mday];
		 $fecha90m_timestamp = ((int)((mktime (0,0,0,$month,$day,$year) -90*86400)));
		 $fecha90m_array  = getdate( $fecha90m_timestamp  );
		 $fecha90m = $fecha90m_array[year].'-'.$fecha90m_array[mon].'-'.$fecha90m_array[mday];
		
		$result_iva = mysql_query("SELECT IVA FROM cifrasimportantes");
		$row_iva = mysql_fetch_array($result_iva);
		$IVA=$row_iva["IVA"];

    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha30m."' AND moneda='usd' AND Pagado<precioTotal*(1+".$IVA.")*.98";
		$result = mysql_query($insertSQL);
		$rowActual = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha45m."' AND facturaFecha<='".$fecha30m."' AND moneda='usd' AND Pagado<precioTotal*(1+".$IVA.")*.98";
		$result = mysql_query($insertSQL);
		$row30 = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha60m."' AND facturaFecha<='".$fecha45m."' AND moneda='usd' AND Pagado<precioTotal*(1+".$IVA.")*.98";
		$result = mysql_query($insertSQL);
		$row45 = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha<='".$fecha60m."' AND moneda='usd' AND Pagado<precioTotal*(1+".$IVA.")*.98";
		$result = mysql_query($insertSQL);
		$row60 = mysql_fetch_array($result);
		
		$usdActual=(1+$IVA)*$rowActual["sum(precioTotal)"]-$rowActual['sum(Pagado)'];
		$usd30=(1+$IVA)*$row30['sum(precioTotal)']-$row30['sum(Pagado)'];
		$usd45=(1+$IVA)*$row45['sum(precioTotal)']-$row45['sum(Pagado)'];
		$usd60=(1+$IVA)*$row60['sum(precioTotal)']-$row60['sum(Pagado)'];
		$usdTotal=$usdActual+$usd30+$usd45+$usd60;

    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha30m."' AND moneda='mxn' AND Pagado<precioTotal*";
		$result = mysql_query($insertSQL);
		$rowActual = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha45m."' AND facturaFecha<='".$fecha30m."' AND moneda='mxn'";
		$result = mysql_query($insertSQL);
		$row30 = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha>'".$fecha60m."' AND facturaFecha<='".$fecha45m."' AND moneda='mxn'";
		$result = mysql_query($insertSQL);
		$row45 = mysql_fetch_array($result);
    $insertSQL = "SELECT sum(precioTotal),sum(Pagado) FROM Cotizacion WHERE factura != 0 AND cliente=".$client." AND facturaFecha<='".$fecha60m."' AND moneda='mxn' AND Pagado<precioTotal*(1+".$IVA.")*.98";
		$result = mysql_query($insertSQL);
		$row60 = mysql_fetch_array($result);
		
		$mxnActual=(1+$IVA)*$rowActual["sum(precioTotal)"]-$rowActual['sum(Pagado)'];
		$mxn30=(1+$IVA)*$row30['sum(precioTotal)']-$row30['sum(Pagado)'];
		$mxn45=(1+$IVA)*$row45['sum(precioTotal)']-$row45['sum(Pagado)'];
		$mxn60=(1+$IVA)*$row60['sum(precioTotal)']-$row60['sum(Pagado)'];
		$mxnTotal=$mxnActual+$mxn30+$mxn45+$mxn60;
		
    $html_for_email.= '<center><table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
		$html_for_email.= '<tr><td width=60><b>Moneda</b></td><td width=60><b>Actual</b></td><td width=60><b>30-45</b></td><td width=60><b>45-60</b></td><td width=60><b>60-</b></td><td width=60><b>Total</b></td></tr>';
		$html_for_email.= '<tr><td><b>USD</b></td><td align=right>'.round($usdActual,2).'</td><td align=right>'.round($usd30,2).'</td><td align=right>'.round($usd45,2).'</td><td align=right>'.round($usd60,2).'</td><td align=right>'.round($usdTotal,2).'</td></tr>';
		$html_for_email.= '<tr><td><b>MXN</b></td><td align=right>'.round($mxnActual,2).'</td><td align=right>'.round($mxn30,2).'</td><td align=right>'.round($mxn45,2).'</td><td align=right>'.round($mxn60,2).'</td><td align=right>'.round($mxnTotal,2).'</td></tr>';
		$html_for_email.= '</table></center><br></br>';

		$minpagomxn=round($mxnTotal/6,2);
		$minpagousd=round($usdTotal/6,2);
		
	 	$html_for_email.= '<center><table  border="1" cellpadding="1" cellspacing="1" summary="" frame="border" >';
	 	$html_for_email.= '<tr><td><b>Factura</b></td><td><b>O.C.</b></td><td width="5"><b>Fecha</b></td><td width="70"><b>Total</b></td><td width="70"><b>Pago</b></td><td width="70"><b>Saldo</b></td><td><b>Moneda</b></td></tr>';
    $insertSQL = "SELECT * FROM Cotizacion WHERE factura != 0 AND cliente=".$client." ORDER BY factura desc";
		$result = mysql_query($insertSQL);
	 	$i=1;
	 	$total=0;
	 	$pagado=0;
		$plazo1=0;
	 while ($row = mysql_fetch_array($result)) {

     if (round($row["precioTotal"]*(1+$row["IVA"]),2)>1.01*$row["Pagado"] OR $ALL==1) {
	      $html_for_email.= '<tr>';

				$day = substr($row["facturaFecha"],8,2);
				$month = substr($row["facturaFecha"],5,2);
				$year = substr($row["facturaFecha"],2,2);
				$plazo2=$plazo1;
        $plazo1 = -((int)((mktime (0,0,0,$month,$day,$year) - mktime (0,0,0,$today[mon],$today[mday],$today[year]))/86400)).' days';
				if ($plazo1>=30 and $plazo2<30) {
				   $html_for_email.= '<tr><td colspan=7><center><b>30 dias</b></center></td></tr>';
				}	elseif ($plazo1>=60 and $plazo2<60) {
				   $html_for_email.= '<tr><td colspan=7><center><b>60 dias</b></center></td></tr>';
				}	elseif ($plazo1>=90 and $plazo2<90) {
				   $html_for_email.= '<tr><td colspan=7><center><b>90 dias</b></center></td></tr>';
        }
				$html_for_email.= '<td height="30" valign="bottom" >'.$row["factura"].'</td><td height="30" valign="bottom" >'.$row["NoPedClient"].'</td><td valign="bottom" align="right">'.substr($row["facturaFecha"],8,2).'-'.substr($row["facturaFecha"],5,2).'-'.substr($row["facturaFecha"],2,2).'</td><td valign="bottom" align="right">';
				$html_for_email.= round($row["precioTotal"]*(1+$row["IVA"]),2);
 			  $TAL=round($row["precioTotal"]*(1+$row["IVA"]),2);
		 		$tal = ROUND($TAL,2);
		 		if (strstr($tal,".")) {
				 	 $len = strlen($tal);
					 if (substr($tal,strlen($tal)-2,1)==".") {
         	 	  $html_for_email.= "0";
           }
       	} else {
			 		$html_for_email.= ".00";
        } 
				$html_for_email.= '</td><td valign="bottom"  align="right">-';
				$html_for_email.= '</td><td valign="bottom"  align="right">';
			  $deuda=round(round($row["precioTotal"]*(1+$row["IVA"]),2)-$row["Pagado"],2);
				if ($deuda<0) {
				   $html_for_email.= '0.00';
				} else {
				   $html_for_email.= $deuda;
 			     $TAL=$deuda;
		 		   $tal = ROUND($TAL,2);
		 		   if (strstr($tal,".")) {
				 	    $len = strlen($tal);
					    if (substr($tal,strlen($tal)-2,1)==".") {
         	 	     $html_for_email.= "0";
              }
       	   } else {
			 		    $html_for_email.= ".00";
           } 
				}	 
				$html_for_email.= '</td><td valign="bottom" >'.$row["moneda"].'</td></tr>';
				$insertSQL='select * from payments where factura = '.$row["factura"].' order by date desc';
		    $resultPayment = mysql_query($insertSQL);
			  while ($rowPayment = mysql_fetch_array($resultPayment)) {
					 $deuda+=$rowPayment["amount"];
				   $html_for_email.= '<tr><td colspan=2><center>-</center></td><td>'.substr($rowPayment["date"],8,2).'-'.substr($rowPayment["date"],5,2).'-'.substr($rowPayment["date"],2,2).'</td><td></td><td align=right>'.$rowPayment["amount"].'</td><td align=right>-</td><td></td></tr>';
			  
				}
				$insertSQL='select * from abonos where deFactura = '.$row["factura"].' and tipo="notacredito" order by fecha desc';
		    $resultAbonos = mysql_query($insertSQL);
			  while ($rowAbonos = mysql_fetch_array($resultAbonos)) {
					 $deuda+=$rowAbonos["valor"];
				   $html_for_email.= '<tr><td><center>-</center></td><td>'.substr($rowAbonos["fecha"],8,2).'-'.substr($rowAbonos["fecha"],5,2).'-'.substr($rowAbonos["fecha"],2,2).'</td><td>NC'.$rowAbonos["numero"].'</td><td align=right>'.$rowAbonos["valor"].'</td><td align=right>-</td><td></td></tr>';
			  
				}
				if ($row["moneda"]=='mxn') {
				  $total+=$row["precioTotal"]*(1+$row["IVA"]);
			    $pagado+=$row["Pagado"];
			  } else {
				  $total+=$row["precioTotal"]*(1+$row["IVA"])*$usd;
			    $pagado+=$row["Pagado"]*$usd;
				}
				
				
				$i++;
			}
   }
	  $html_for_email.= '</table></center>';
		$deudatotal=round($total,2)-round($pagado,2);
    $html_for_email.= "<br><center>Saldo: <b>".$deudatotal."mxn</b></big></center><br>";

		$html_for_email.= '<center><b><i>1usd = '.$usd.'mxn</i></b><br></br></center>';

		




    //$html_for_email.= '<center><a href="estadoDeCuenta.php?ALL=1&client='.$client.'">ALL</a></center>';
 ?>

</body>
</html>







<?php  




require("phpmailer/class.phpmailer.php");

$mail = new PHPMailer();

$mail->IsSMTP();                                   // send via SMTP
$mail->Host     = "lvvdk.globat.com:587"; // SMTP servers
$mail->SMTPAuth = true;     // turn on SMTP authentication
$mail->Username = "hdk@hdk.com.mx";  // SMTP username
$mail->Password = "Lars6024+-"; // SMTP password

$mail->From     = "hdk@hdk.com.mx";
$mail->FromName = "Herramientas DK, S.A. de C.V.";
$mail->AddAddress("hdk@hdk.com.mx","HDK"); 
$mail->AddAddress($email);               // optional name
$mail->AddReplyTo("hdk@hdk.com.mx","HDK");

$mail->WordWrap = 50;                              // set word wrap
//$mail->AddAttachment("/var/tmp/file.tar.gz");      // attachment
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); 
$mail->IsHTML(true);                               // send as HTML

$mail->Subject  =  'HDK - Estado de Cuenta '.$nombreEmpresa;
$mail->Body     =  $html_for_email;
$mail->AltBody  =  "Te hemos enviado un estado de cuenta.";

if(!$mail->Send())
{
//   echo "Message was not sent <p>";
//   echo "Mailer Error: " . $mail->ErrorInfo;
	echo '<body onload="alert(\'Message was not sent.\');printPageAndBack()"></body>';

   exit;
}

//echo "Message has been sent";
	 echo '<body onload="alert(\'Congratulations! Message was sent.\');printPageAndBack()"></body>';

?>