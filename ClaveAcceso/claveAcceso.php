<?php
include 'Mod11.php';
function clave($fechaEmision , $tipoComprobante, $ruc,  $ambiente,  $serie,  $numeroComprobante, $codigonumerico,  $tipodeEmision){

    $claveAccesoSinVerificador = $fechaEmision . $tipoComprobante . $ruc . $ambiente . $serie . $numeroComprobante . $codigonumerico . $tipodeEmision;
    // Se agrega el dígito verificador a la clave de acceso
    $digitoVerificador = calcularDigitoVerificador($claveAccesoSinVerificador);
    return $claveAccesoCompleta = $claveAccesoSinVerificador . $digitoVerificador;
};
?>