<?php
include './ClaveAcceso/claveAcceso.php';

date_default_timezone_set('America/Guayaquil');
$certificado_p12 = './Firma/Davicio.p12';
$contrasena_p12 = 'Pablito0508';
$signingTime = (new DateTime())->format('Y-m-d\TH:i:s.uP');
// Leer el contenido del archivo P12
$contenido_p12 = file_get_contents($certificado_p12);

if ($contenido_p12 === false) {
    die('No se pudo leer el contenido del archivo P12.');
}

// Intentar leer el certificado P12
if (!openssl_pkcs12_read($contenido_p12, $certificados, $contrasena_p12)) {
    die('Error al leer el contenido del archivo P12.');
}
$uniqueId = 'xmldsig-' . uniqid();
 $uniqueId;
// Obtener la clave privada y el certificado
$private_key = $certificados['pkey'];
$certificate = $certificados['cert'];


$certInfo = openssl_x509_parse($certificate);

if ($certInfo) {
    $serialNumber = $certInfo['serialNumber'];

    // Obtener información del emisor
    $emisor = $certInfo['issuer'];
    $nombreComun = $emisor['CN'];
    $unidadOrganizativa = $emisor['OU'];
    $organizacion = $emisor['O'];
    $Pais = $emisor['C'];
}
    echo  $issuerName ='CN=' . $nombreComun.',OU='. $unidadOrganizativa.',O=' . $organizacion.',C=' . $Pais;


$uniqueId = 'xmldsig-' . uniqid();
$fechaEmision = "19102023";
$dia = substr($fechaEmision, 0, 2);
$mes = substr($fechaEmision, 2, 2);
$anio = substr($fechaEmision, 4, 4);
$fechaFormateada =$dia.'/'.$mes.'/'.$anio;
$tipoComprobante = "01";
$ruc = "1722050935001";
$usuario='VEGA VARELA CARLOS DAVID';
$ambiente = "1";
$establecimiento="001";
$puntoemision="001";
$DirMatriz = "RAMIREZ DAVALOS ESQ Y ULPIANO PAEZ";
$serie = $establecimiento . $puntoemision;
$numeroComprobante = "000000020";
$codigonumerico = '87654321';
$tipodeEmision = '1';

$claveAcceso =  clave($fechaEmision , $tipoComprobante, $ruc,  $ambiente,  $serie,  $numeroComprobante, $codigonumerico,  $tipodeEmision);

$xml = new DOMDocument('1.0', 'utf-8');
$xml->formatOutput = true;

// Crear el elemento factura y añadir atributos
$xml_fac = $xml->createElement('factura');
$cabecera = $xml->createAttribute('id');
$cabecera->value = 'comprobante';
$cabecerav = $xml->createAttribute('version');
$cabecerav->value = '2.1.0';
$xml_inf = $xml->createElement('infoTributaria');
$xml_amb = $xml->createElement('ambiente', $ambiente);
$xml_tip =$xml->createElement('tipoEmision',$tipodeEmision);
$xml_raz =$xml->createElement('razonSocial',$usuario);
//$xml_nom =$xml->createElement('nombreComercial','CONEXION ECUADOR');
$xml_ruc =$xml->createElement('ruc',$ruc);
$xml_cla= $xml->createElement('claveAcceso',$claveAcceso);
$xml_cod= $xml->createElement('codDoc','01');
$xml_est= $xml->createElement('estab',$establecimiento);
$xml_pto= $xml->createElement('ptoEmi',$puntoemision);
$xml_sec= $xml->createElement('secuencial',$numeroComprobante);
$xml_mat= $xml->createElement('dirMatriz',$DirMatriz);
$xml_rimp= $xml->createElement('contribuyenteRimpe','CONTRIBUYENTE RÉGIMEN RIMPE');

$xml_inf->appendChild($xml_amb);
$xml_inf->appendChild($xml_tip);
$xml_inf->appendChild($xml_raz);
$xml_inf->appendChild($xml_ruc);
$xml_inf->appendChild($xml_cla);
$xml_inf->appendChild($xml_cod);
$xml_inf->appendChild($xml_est);
$xml_inf->appendChild($xml_pto);
$xml_inf->appendChild($xml_sec);
$xml_inf->appendChild($xml_mat);
$xml_inf->appendChild($xml_rimp);

$xml_fac->appendChild($xml_inf);



$xml_def = $xml->createElement('infoFactura');
$xml_fec= $xml->createElement('fechaEmision',$fechaFormateada);
$xml_des= $xml->createElement('dirEstablecimiento', $DirMatriz);
$xml_obl= $xml->createElement('obligadoContabilidad','NO');
$xml_comp= $xml->createElement('tipoIdentificacionComprador','05');
$xml_raz= $xml->createElement('razonSocialComprador','CARLOS DAVID VEGA ');
$xml_idc= $xml->createElement('identificacionComprador','1722050935');
$xml_tsd= $xml->createElement('totalSinImpuestos','1.00');
$xml_tod= $xml->createElement('totalDescuento','0.00');

$xml_def->appendChild($xml_fec);
$xml_def->appendChild($xml_des);
$xml_def->appendChild($xml_obl);
$xml_def->appendChild($xml_comp);
$xml_def->appendChild($xml_raz);
$xml_def->appendChild($xml_idc);
$xml_def->appendChild($xml_tsd);
$xml_def->appendChild($xml_tod);


$xml_tci = $xml->createElement('totalConImpuestos');
$xml_tim = $xml->createElement('totalImpuesto');
$xml_cim= $xml->createElement('codigo','2');
$xml_copo= $xml->createElement('codigoPorcentaje','2');
$xml_bim= $xml->createElement('baseImponible','1.00');
$xml_val= $xml->createElement('valor','0.12');


$xml_tci->appendChild($xml_tim);
$xml_tim->appendChild($xml_cim);
$xml_tim->appendChild($xml_copo);
$xml_tim->appendChild($xml_bim);
$xml_tim->appendChild($xml_val);
$xml_def->appendChild($xml_tci);


//ojo
$xml_prop= $xml->createElement('propina','0');
$xml_impt= $xml->createElement('importeTotal','1.12');
$xml_mone= $xml->createElement('moneda','DOLAR');

$xml_def->appendChild($xml_prop);
$xml_def->appendChild($xml_impt);
$xml_def->appendChild($xml_mone);


$xml_pgs = $xml->createElement('pagos');
$xml_pgo = $xml->createElement('pago');
$xml_pgf = $xml->createElement('formaPago','19');
$xml_tot= $xml->createElement('total','1.12');


$xml_pgs->appendChild($xml_pgo);
$xml_pgo->appendChild($xml_pgf);
$xml_pgo->appendChild($xml_tot);
$xml_def->appendChild($xml_pgs);

$xml_fac->appendChild($xml_def);


$xml_dtlls = $xml->createElement('detalles');
$xml_dtll = $xml->createElement('detalle');
$xml_copr = $xml->createElement('codigoPrincipal','001');
$xml_desc = $xml->createElement('descripcion','PRODUCTOS NAVIDENIOS');
$xml_cant = $xml->createElement('cantidad','1.0');
$xml_presu = $xml->createElement('precioUnitario','1.00');
$xml_desd= $xml->createElement('descuento','0.00');
$xml_presi= $xml->createElement('precioTotalSinImpuesto','1.00');
$xml_dtadic = $xml->createElement('detallesAdicionales');
$xml_adic = $xml->createElement('detAdicional');
$cabe = $xml->createAttribute('nombre');
$cabe->value = 'informacionAdicional';
$cabez = $xml->createAttribute('valor');
$cabez->value = 'Productos';


$xml_dtlls->appendChild($xml_dtll);
$xml_dtll->appendChild($xml_copr);
$xml_dtll->appendChild($xml_desc);
$xml_dtll->appendChild($xml_cant);
$xml_dtll->appendChild($xml_presu);
$xml_dtll->appendChild($xml_desd);
$xml_dtll->appendChild($xml_presi);
$xml_dtll->appendChild($xml_dtadic);
$xml_dtadic->appendChild($xml_adic);
$xml_adic->appendChild($cabe);
$xml_adic->appendChild($cabez);


$xml_ipsd = $xml->createElement('impuestos');
$xml_ipud = $xml->createElement('impuesto');
$xml_cimp= $xml->createElement('codigo','2');
$xml_copd= $xml->createElement('codigoPorcentaje','2');
$xml_tar = $xml->createElement('tarifa','12.0');
$xml_base = $xml->createElement('baseImponible', '1.00');
$xml_basv = $xml->createElement('valor', '0.12');

$xml_ipsd->appendChild($xml_ipud);
$xml_ipud->appendChild($xml_cimp);
$xml_ipud->appendChild($xml_copd);
$xml_ipud->appendChild($xml_tar);
$xml_ipud->appendChild($xml_base);
$xml_ipud->appendChild($xml_basv);
$xml_dtll->appendChild($xml_ipsd);

$xml_fac->appendChild($xml_dtlls);

$xml_Infa =$xml->createElement('infoAdicional');
$xml_cama2 =$xml->createElement('campoAdicional','02231074');
$atributo2 = $xml->createAttribute('nombre');
$atributo2->value='Telefono';
$xml_cama =$xml->createElement('campoAdicional','shaggi6e@gmail.com');
$atributo = $xml->createAttribute('nombre');
$atributo->value='Email';

$xml_Infa->appendChild($xml_cama2);
$xml_cama2->appendChild($atributo2);
$xml_Infa->appendChild($xml_cama);
$xml_cama->appendChild($atributo);
$xml_fac->appendChild($xml_Infa);



$signatureNode = $xml->createElement('ds:Signature');
$xml_fac->appendChild($signatureNode);
$signatureNode->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
$signatureNode->setAttribute('Id', $uniqueId ); 
$signedInfoNode = $xml->createElement('ds:SignedInfo');

// Crear los nodos CanonicalizationMethod y SignatureMethod
$canonicalizationMethodNode = $xml->createElement('ds:CanonicalizationMethod');
$canonicalizationMethodNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

$signatureMethodNode = $xml->createElement('ds:SignatureMethod');
$signatureMethodNode->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');

$signedInfoNode->appendChild($canonicalizationMethodNode);
$signedInfoNode->appendChild($signatureMethodNode);
$signatureNode->appendChild($signedInfoNode);

$referenceNode1 = $xml->createElement('ds:Reference');
$referenceNode1->setAttribute('Id', $uniqueId.'-ref0');
$referenceNode1->setAttribute('URI', '#comprobante');
$signedInfoNode->appendChild($referenceNode1);

$transformsNode1 =$xml->createElement('ds:Transforms');
$transformNode1 =$xml->createElement('ds:Transform');
$transformNode1->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');
$transformsNode1->appendChild($transformNode1);
$referenceNode1->appendChild($transformsNode1);

$digestMethodNode1 = $xml->createElement('ds:DigestMethod');
$digestMethodNode1->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$signedInfoNode->appendChild($referenceNode1);
$referenceContent1 = 'comprobante';   

$xpath = new DOMXPath($xml);
$digestValueNode1 = $xml->createElement('ds:DigestValue');
$digestValue1 = base64_encode(hash('sha256', $referenceContent1, true));
$digestValueNode1->nodeValue = $digestValue1;
$referenceNode1->appendChild($digestMethodNode1);
$referenceNode1->appendChild($digestValueNode1);


$referenceNode2 = $xml->createElement('ds:Reference');
$referenceNode2->setAttribute('Type','http://uri.etsi.org/01903#SignedProperties');
$referenceNode2->setAttribute('URI','#'.$uniqueId.'-signedprops' );
$signedInfoNode->appendChild($referenceNode2);

$digestMethodNode2 = $xml->createElement('ds:DigestMethod');
$digestMethodNode2->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
$signedInfoNode->appendChild($referenceNode2);
$referenceContent1 = $uniqueId.'-signedprops';   //aqui hay que cambiar despues de que formemos el comprobante

$xpath = new DOMXPath($xml);
$digestValueNode2 = $xml->createElement('ds:DigestValue');
$digestValue2 = base64_encode(hash('sha256', $referenceContent1, true));
$digestValueNode2->nodeValue = $digestValue2;
$referenceNode2->appendChild($digestMethodNode2);
$referenceNode2->appendChild($digestValueNode2);



$signatureValueNode = $xml->createElement('ds:SignatureValue');
$signatureValueNode->setAttribute('Id',$uniqueId.'-sigvalue');

openssl_sign($xml->C14N(false, true), $firma, $private_key, OPENSSL_ALGO_SHA256);
$signatureValueNode->nodeValue = base64_encode($firma);

$signatureNode->appendChild($signatureValueNode);

$keyInfoNode = $xml->createElement('ds:KeyInfo');
$x509DataNode = $xml->createElement('ds:X509Data');
$x509CertificateNode = $xml->createElement('ds:X509Certificate', base64_encode($certificate));

$x509DataNode->appendChild($x509CertificateNode);
$keyInfoNode->appendChild($x509DataNode);
$signatureNode->appendChild($keyInfoNode);


$objectNode = $xml->createElement('ds:Object');
$signatureNode->appendChild($objectNode);

$qualifyingPropertiesNode = $xml->createElement('xades:QualifyingProperties');
$qualifyingPropertiesNode->setAttribute('xmlns:xades', 'http://uri.etsi.org/01903/v1.3.2#');
$qualifyingPropertiesNode->setAttribute('xmlns:xades141', 'http://uri.etsi.org/01903/v1.4.1#');
$qualifyingPropertiesNode->setAttribute('Target', '#' . $uniqueId);
$objectNode->appendChild($qualifyingPropertiesNode);

$signedPropertiesNode = $xml->createElement('xades:SignedProperties');
$signedPropertiesNode->setAttribute('Id', $uniqueId.'-signedprops');
$qualifyingPropertiesNode->appendChild($signedPropertiesNode);

$SignedSignaturePropertiesNode = $xml->createElement('xades:SignedSignatureProperties');
$signedPropertiesNode->appendChild($SignedSignaturePropertiesNode);

$signingTimeNode = $xml->createElement('xades:SigningTime', $signingTime);
$SignedSignaturePropertiesNode->appendChild($signingTimeNode);

$signingCertificateNode = $xml->createElement('xades:SigningCertificate');
$SignedSignaturePropertiesNode->appendChild($signingCertificateNode);

$certeNode = $xml->createElement('xades:Cert');
$signingCertificateNode->appendChild($certeNode);

$certDigestNode = $xml->createElement('xades:CertDigest');
$certeNode->appendChild($certDigestNode);

$digestMethodNode = $xml->createElement('ds:DigestMethod');
$certDigestNode->appendChild($digestMethodNode);
$digestMethodNode->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');



$certDigestNode->appendChild($digestMethodNode);

// Calcular el DigestValue del certificado
$certificateBinary = base64_decode($certificate); // Suponiendo que $certificate contiene el certificado en formato base64
$digestValue3 = base64_encode(hash('sha256', $certificateBinary, true));

$digestValueNode3 = $xml->createElement('ds:DigestValue', $digestValue3);
$certDigestNode->appendChild($digestValueNode3);

$issuerSerialNode = $xml->createElement('xades:IssuerSerial');
$certeNode->appendChild($issuerSerialNode);

// Añadir el contenido de IssuerName y SerialNumber
$issuerName = "CN=AUTORIDAD DE CERTIFICACION SUBCA-2 SECURITY DATA,OU=ENTIDAD DE CERTIFICACION DE INFORMACION,O=SECURITY DATA S.A. 2,C=EC";


$issuerNameNode = $xml->createElement('ds:X509IssuerName', $issuerName);
$serialNumberNode = $xml->createElement('ds:X509SerialNumber', $serialNumber);

$issuerSerialNode->appendChild($issuerNameNode);
$issuerSerialNode->appendChild($serialNumberNode);

$signedDataObjectPropertiesNode = $xml->createElement('xades:SignedDataObjectProperties');
$signedPropertiesNode->appendChild($signedDataObjectPropertiesNode);

$dataObjectFormatNode = $xml->createElement('xades:DataObjectFormat');
$dataObjectFormatNode->setAttribute('ObjectReference', $uniqueId.'-ref0');
$signedDataObjectPropertiesNode->appendChild($dataObjectFormatNode);

$descriptionNode = $xml->createElement('xades:Description','FIRMA DIGITAL SRI');
$dataObjectFormatNode->appendChild($descriptionNode);

$mimeTypeNode = $xml->createElement('xades:MimeType','text/xml');
$dataObjectFormatNode->appendChild($mimeTypeNode);

$EncodingNode = $xml->createElement('xades:Encoding','UTF-8');
$dataObjectFormatNode->appendChild($EncodingNode);




$xml_fac->appendChild($cabecera);
$xml_fac->appendChild($cabecerav);
$xml->appendChild($xml_fac);






// Guardar el XML en un archivo
$xml->save('./ejemploRimpe.xml');
?>
