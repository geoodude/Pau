<?php
$i = 0;
$json = file_get_contents('agenda.json');
$tascas = json_decode($json,true ) ;

$ruta_archivo = 'tascas.txt';
echo "===========================\n
a: Afegir tasca nova:\n
e: Eliminar una tasca existen:\n
l: Veure totes les tascas exitens:\n
===========================\n
Quina opcio vols triar?  \n";
$opcio = strtolower(readline());

if ($opcio == 'a' ) {
    echo "has decicidit afegir una tasca \n";

    echo "quin es el titul de la tasca\n ";
    $titul = strtolower(readline());

    echo "quina descripcio vols posar en la tasca\n";
    $descripcio = strtolower(readline());

    echo "quina es la data maxim que pots fer la tasca\n ";
    $data = strtolower(readline());
    $id= rand(1, 99999);
$nova_tasca = array(
    "ID" => $id,
    "titul" => $titul,
    "descripcio" => $descripcio,
    "data" => $data
);
$tascas[] = $nova_tasca;
$json_nou = json_encode($tascas);
file_put_contents('agenda.json', $json_nou);


} else if ($opcio =='e') {
    echo "Has decidido eliminar una tarea existente.\n\n";
    echo "Lista de tareas:\n\n";
    foreach ($tascas as $index => $tasca) {
        echo "ID:{$index}: Titulo: {$tasca['titul']}, Descripción: {$tasca['descripcio']}, Data: {$tasca['data']}\n";
    }
    echo "Ingresa el número de la tarea que deseas eliminar:\n\n ";
    $indice_eliminar = intval(readline());
    if (isset($tascas[$indice_eliminar])) {
        unset($tascas[$indice_eliminar]);
        $json_nou = json_encode($tascas);
        file_put_contents('agenda.json', $json_nou);
        echo "Tarea eliminada correctamente.\n";
    } else {
        echo "El número de tarea ingresado no es válido.\n";
    }
} else if ($opcio == 'l') {
    echo "\nLlista de tasques\n";
    
    foreach ($tascas as $index => $tascas) {
        echo "Titul: {$tascas['titul']}\n";
        echo "Descripcio: {$tascas['descripcio']}\n";
        echo "Data: {$tascas['data']}\n\n";
        
    }
} else{
    echo"lletra incorecta intentau de nou \n";
};


?>
