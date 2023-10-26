<?php

// URL del servicio web
$wsdlUrl = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';

// Datos del comprobante en formato base64Binary
$comprobanteXmlBase64 = base64_encode(file_get_contents('./ejemplorimpe.xml'));

// Configuración del cliente SOAP
$options = [
    'trace' => 1,
    'exceptions' => 1,
    'cache_wsdl' => WSDL_CACHE_NONE,
];

try {
    // Crear el cliente SOAP
    $soapClient = new SoapClient($wsdlUrl, $options);

    // Parámetros para la operación 'validarComprobante'
    $params = [
        'xml' => $comprobanteXmlBase64,
    ];

    // Llamar a la operación 'validarComprobante'
    $response = $soapClient->validarComprobante($params);

    // Imprimir la respuesta
    print_r($response);

} catch (SoapFault $fault) {
    // Manejar errores SOAP
    echo "Error: " . $fault->faultcode . ": " . $fault->faultstring;
} catch (Exception $e) {
    // Manejar otras excepciones
    echo "Error: " . $e->getMessage();
}
?>
