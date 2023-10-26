<?php

// URL del servicio web SOAP
$wsdl_url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';

// Parámetros de configuración del cliente SOAP
$soap_options = array(
    'trace' => 1, // Habilita el seguimiento de la solicitud y respuesta SOAP
    'exceptions' => true, // Habilita excepciones en caso de errores
);

try {
    // Crea una instancia del cliente SOAP
    $client = new SoapClient($wsdl_url, $soap_options);

    // Clave de acceso del comprobante que deseas autorizar
    $claveAcceso = '1910202301172205093500110010010000000208765432112';

    // Llama a la operación 'autorizacionComprobante'
    $params = array('claveAccesoComprobante' => $claveAcceso);
    $response = $client->autorizacionComprobante($params);

    // Procesa la respuesta
    $respuesta = $response->autorizacionComprobanteResponse->RespuestaAutorizacionComprobante;
    $estado = $respuesta->estado;
    $numeroAutorizacion = $respuesta->numeroAutorizacion;

    // Imprime la respuesta
    echo "Estado: $estado\n";
    echo "Número de Autorización: $numeroAutorizacion\n";

    // Puedes acceder a más detalles de la respuesta según tus necesidades
    // var_dump($respuesta);
} catch (SoapFault $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
}

?>

