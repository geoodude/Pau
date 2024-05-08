<?php
$json_file = 'agenda.json';
$options = getopt("hae:m:t:d:f:");

function printHelp()
{
    echo "Ús: php index2.php -a -t <títol> -d <descripció> -f <data>\n";
    echo "     php index2.php -e <id>\n";
    echo "     php index2.php -m\n";
    echo "Opcions:\n";
    echo "  -h               Mostra aquest missatge d'ajuda\n";
    echo "  -a               Afegir nova tasca\n";
    echo "  -e <id>          Eliminar una tasca existent\n";
    echo "  -m               Veure totes les tasques existents\n";
    echo "  -t <títol>       Títol de la tasca\n";
    echo "  -d <descripció>  Descripció de la tasca\n";
    echo "  -f <data>        Data màxima per fer la tasca\n";
}

if (isset($options['h'])) {
    printHelp();
    exit(0);
}

$tasques = [];

if (file_exists($json_file)) {
    $json_content = file_get_contents($json_file);
    $tasques = json_decode($json_content, true);
}

if (isset($options['a'])) {
    if (!(isset($options['t']) && isset($options['d']) && isset($options['f']))) {
        echo "Error: Falten arguments per afegir una tasca.\n";
        printHelp();
        exit(1);
    }

    $nova_tasca = array(
        "ID" => rand(1, 99999),
        "títol" => $options['t'],
        "descripció" => $options['d'],
        "data" => $options['f']
    );

    $tasques[] = $nova_tasca;
    echo "Tasca afegida correctament.\n";
} elseif (isset($options['e'])) {
    $id_eliminar = intval($options['e']);
    $trobat = false;
    foreach ($tasques as $index => $tasca) {
        if ($tasca['ID'] == $id_eliminar) {
            unset($tasques[$index]);
            $trobat = true;
            break;
        }
    }
    if ($trobat) {
        echo "Tasca amb ID $id_eliminar eliminada correctament.\n";
    } else {
        echo "No s'ha trobat cap tasca amb ID $id_eliminar.\n";
    }
} elseif (isset($options['m'])) {
    if (empty($tasques)) {
        echo "No hi ha tasques disponibles.\n";
    } else {
        echo "Llista de tasques:\n";
        foreach ($tasques as $tasca) {
            echo "ID: {$tasca['ID']}, Títol: {$tasca['títol']}, Descripció: {$tasca['descripció']}, Data: {$tasca['data']}\n";
        }
    }
} else {
    echo "Opció incorrecta.\n";
    printHelp();
    exit(1);
}

file_put_contents($json_file, json_encode($tasques, JSON_PRETTY_PRINT));
?>


