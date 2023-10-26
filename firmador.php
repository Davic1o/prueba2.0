<?php

// Ruta al archivo P12
$certificado_p12 = './Firma/Davicio.p12';
$contrasena_p12 = 'Pablito0508';

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

// Ruta al documento XML a firmar
$documento_a_firmar = 'ejemplorimpe.xml';


$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$dom->load($documento_a_firmar);
// Crear el nodo de firma
$signatureNode = $dom->createElement('ds:Signature');
$signatureNode->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
$signatureNode->setAttribute('Id', $uniqueId ); 

// Crear el nodo SignedInfo
$signedInfoNode = $dom->createElement('ds:SignedInfo');

// Crear los nodos CanonicalizationMethod y SignatureMethod
$canonicalizationMethodNode = $dom->createElement('ds:CanonicalizationMethod');
$canonicalizationMethodNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

$signatureMethodNode = $dom->createElement('ds:SignatureMethod');
$signatureMethodNode->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');

$signedInfoNode->appendChild($canonicalizationMethodNode);
$signedInfoNode->appendChild($signatureMethodNode);

// Crear el nodo Reference para comprobante
$referenceNode1 = $dom->createElement('ds:Reference');
$referenceNode1->setAttribute('Id', $uniqueId.'-ref0');
$referenceNode1->setAttribute('URI', '#comprobante');


// Crear el nodo Transforms y DigestMethod para la referencia
$transformsNode1 = $dom->createElement('ds:Transforms');
$transformNode1 = $dom->createElement('ds:Transform');
$transformNode1->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
    $transformsNode1->appendChild($transformNode1);
    
    $digestMethodNode1 = $dom->createElement('ds:DigestMethod');
    $digestMethodNode1->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
    
    $signedInfoNode->appendChild($referenceNode1);
    $referenceContent1 = 'comprobante';   
    
    $xpath = new DOMXPath($dom);
    // Crear el nodo DigestValue
// Obtén el contenido específico de la referencia
$digestValueNode1 = $dom->createElement('ds:DigestValue');
$digestValue1 = base64_encode(hash('sha256', $referenceContent1, true));
$digestValueNode1->nodeValue = $digestValue1;


$referenceNode1->appendChild($transformsNode1);
$referenceNode1->appendChild($digestMethodNode1);
$referenceNode1->appendChild($digestValueNode1);

// Crear el nodo Reference para SignedProperties

$referenceNode2 = $dom->createElement('ds:Reference');
$referenceNode2->setAttribute('Type', 'http://uri.etsi.org/01903#SignedProperties');
$referenceNode2->setAttribute('URI', '#' . $uniqueId . '-signedprops');

// Crear el nodo DigestMethod y DigestValue para la segunda referencia
$digestMethodNode2 = $dom->createElement('ds:DigestMethod');
$digestMethodNode2->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');

// Crear el nodo DigestValue (inicializarlo vacío por ahora)
$digestValueNode2 = $dom->createElement('ds:DigestValue');
$referenceNode2->appendChild($digestMethodNode2);
$referenceNode2->appendChild($digestValueNode2);

$referenceNode2->appendChild($digestMethodNode2);
// Agregar el nodo Reference al documento
$referenceNode2->appendChild($digestValueNode2);
$signatureNode->appendChild($referenceNode2);

// ... (continuación de tu código)

// Ahora, busca y manipula el nodo Reference según sea necesario
$xpath = new DOMXPath($dom);
$referenceURI = '#' . $uniqueId . '-signedprops';
$signedPropsElement = $xpath->query("//*[@URI='{$referenceURI}']")->item(0);

if ($signedPropsElement) {
    // El elemento existe, puedes proceder con la firma digital
    $referenceContent2 = $dom->saveXML($signedPropsElement);
    $digestValue2 = base64_encode(hash('sha256', $referenceContent2, true));
    $digestValueNode2->nodeValue = $digestValue2;
} else {
    die('No se encontró el elemento con URI "' . $referenceURI . '" en el documento.');
}




// Agregar las referencias al nodo SignedInfo


// Crear el nodo SignatureValue
$signatureValueNode = $dom->createElement('ds:SignatureValue');
$signatureValueNode->setAttribute('Id',  $uniqueId .'-sigvalue');
$signatureValueNode->nodeValue = '';  // Agrega tu valor de firma aquí

// Crear el nodo KeyInfo
$keyInfoNode = $dom->createElement('ds:KeyInfo');

// Crear el nodo X509Data
$x509DataNode = $dom->createElement('ds:X509Data');

// Crear el nodo X509Certificate
$x509CertificateNode = $dom->createElement('ds:X509Certificate');
$x509CertificateNode->nodeValue = 'MIILzTCCCbWgAwIBAgIE...';  // Agrega tu certificado X.509 aquí

// Agregar el certificado al nodo X509Data
$x509DataNode->appendChild($x509CertificateNode);

// Agregar el nodo X509Data al nodo KeyInfo
$keyInfoNode->appendChild($x509DataNode);

// Crear el nodo Object
$objectNode = $dom->createElement('ds:Object');

// Crear el nodo QualifyingProperties
$qualifyingPropertiesNode = $dom->createElement( 'xades:QualifyingProperties');
$qualifyingPropertiesNode->setAttribute('xmlns:xades141', 'http://uri.etsi.org/01903/v1.4.1#');
$qualifyingPropertiesNode->setAttribute('Target', '#xmldsig-f90f8de5-d999-4fc1-85b2-4362e975acc1');

// Crear el nodo SignedProperties
$signedPropertiesNode = $dom->createElement('xades:SignedProperties');
$signedPropertiesNode->setAttribute('Id', 'xmldsig-f90f8de5-d999-4fc1-85b2-4362e975acc1-signedprops');

// Crear el nodo SignedSignatureProperties
$signedSignaturePropertiesNode = $dom->createElement('xades:SignedSignatureProperties');

// Crear el nodo SigningTime
$signingTimeNode = $dom->createElement('xades:SigningTime', '2023-10-03T21:43:09.542-05:00');

// Crear el nodo SigningCertificate
$signingCertificateNode = $dom->createElement('xades:SigningCertificate');

// Crear el nodo Cert
$certNode = $dom->createElement('xades:Cert');

// Crear el nodo CertDigest
$certDigestNode = $dom->createElement('xades:CertDigest');

// Crear el nodo DigestMethod y DigestValue para CertDigest
$digestMethodNode3 = $dom->createElement('ds:DigestMethod');
$digestMethodNode3->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');

$digestValueNode3 = $dom->createElement('ds:DigestValue', 'ujoQx5MkY2s67XkZYKo3onnSmW4L0Cre3b9bTUM9Ib8=');

$certDigestNode->appendChild($digestMethodNode3);
$certDigestNode->appendChild($digestValueNode3);

// Crear el nodo IssuerSerial
$issuerSerialNode = $dom->createElement('xades:IssuerSerial');

// Crear el nodo X509IssuerName y X509SerialNumber
$x509IssuerNameNode = $dom->createElement('ds:X509IssuerName', 'CN=AUTORIDAD DE CERTIFICACION SUBCA-2 SECURITY DATA,OU=ENTIDAD DE CERTIFICACION DE INFORMACION,O=SECURITY DATA S.A. 2,C=EC');
$x509SerialNumberNode = $dom->createElement('ds:X509SerialNumber', '206204009');

$issuerSerialNode->appendChild($x509IssuerNameNode);
$issuerSerialNode->appendChild($x509SerialNumberNode);

// Agregar nodos al Cert
$certNode->appendChild($certDigestNode);
$certNode->appendChild($issuerSerialNode);

// Agregar nodos al SignedSignatureProperties
$signedSignaturePropertiesNode->appendChild($signingTimeNode);
$signedSignaturePropertiesNode->appendChild($signingCertificateNode);
$signedSignaturePropertiesNode->appendChild($certNode);

// Agregar nodos al SignedProperties
$signedPropertiesNode->appendChild($signedSignaturePropertiesNode);

// Agregar nodos al QualifyingProperties
$qualifyingPropertiesNode->appendChild($signedPropertiesNode);

// Agregar nodos al Object
$objectNode->appendChild($qualifyingPropertiesNode);

// Agregar nodos al nodo de firma
$signatureNode->appendChild($signedInfoNode);
$signatureNode->appendChild($signatureValueNode);
$signatureNode->appendChild($keyInfoNode);
$signatureNode->appendChild($objectNode);

// Agregar el nodo de firma al documento
//$dom->appendChild($signatureNode);

$facturaNode = $dom->getElementsByTagName('factura')->item(0);

// Asegurarse de que se encontró el nodo <factura>
if ($facturaNode) {
    // Agregar el nodo de firma a la factura
    $facturaNode->appendChild($signatureNode);
} else {
    die('No se encontró el nodo <factura> en el documento.');
}
// Imprimir el XML resultante
 $dom->save('./firmado.xml');

?>
