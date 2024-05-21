<?php
if (PHP_SAPI !== 'cli') {
   die("Aquest script només es pot executar des de la línia de comandes (CLI).\n");
}


$options = getopt("hae:m:t:d:f:");


function printHelp() {
   echo "Ús: php prac3.php -a -t <títol> -d <descripció> -f <data>\n";
   echo "     php prac3.php -e <id>\n";
   echo "     php prac3.php -m\n";
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


$config_dir = getenv('HOME') . '/.config';
$config_file = $config_dir . '/task-manager.cfg';
$json_file = $config_dir . '/task-manager.json';
$csv_file = $config_dir . '/task-manager.csv';


if (!file_exists($config_dir)) {
   mkdir($config_dir, 0777, true);
}


$storage_type = '';


if (!file_exists($config_file)) {
   echo "És la primera vegada que executes el programa.\n";
   echo "Selecciona el format d'emmagatzematge de dades:\n";
   echo "1. JSON\n";
   echo "2. CSV\n";
   $option = trim(fgets(STDIN));


   switch ($option) {
       case '1':
           $storage_type = 'json';
           break;
       case '2':
           $storage_type = 'csv';
           break;
       default:
           echo "Opció no vàlida.\n";
           exit(1);
   }


  
   file_put_contents($config_file, "storage_type=$storage_type\n");
} else {
   $config = parse_ini_file($config_file);
   $storage_type = $config['storage_type'] ?? 'json';
}


$tasques = [];


function loadTasksFromJson($json_file) {
   if (file_exists($json_file)) {
       $json_content = file_get_contents($json_file);
       return json_decode($json_content, true) ?? [];
   }
   return [];
}


function loadTasksFromCsv($csv_file) {
   $tasks = [];
   if (file_exists($csv_file)) {
       if (($handle = fopen($csv_file, 'r')) !== false) {
           while (($data = fgetcsv($handle, 1000, ",")) !== false) {
               $tasks[] = [
                   'ID' => $data[0],
                   'títol' => $data[1],
                   'descripció' => $data[2],
                   'data' => $data[3]
               ];
           }
           fclose($handle);
       }
   }
   return $tasks;
}


if ($storage_type == 'json') {
   $tasques = loadTasksFromJson($json_file);
} else {
   $tasques = loadTasksFromCsv($csv_file);
}


function saveTasksToJson($json_file, $tasks) {
   file_put_contents($json_file, json_encode($tasks, JSON_PRETTY_PRINT));
}


function saveTasksToCsv($csv_file, $tasks) {
   if (($handle = fopen($csv_file, 'w')) !== false) {
       foreach ($tasks as $task) {
           fputcsv($handle, $task);
       }
       fclose($handle);
   }
}


if (isset($options['a'])) {
   if (!(isset($options['t']) && isset($options['d']) && isset($options['f']))) {
       echo "Error: Falta arguments per afegir una tasca.\n";
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


   if ($storage_type == 'json') {
       saveTasksToJson($json_file, $tasques);
   } else {
       saveTasksToCsv($csv_file, $tasques);
   }
} elseif (isset($options['e'])) {
   $id_eliminar = intval($options['e']);
   $trobat = false;
   foreach ($tasques as $index => $tasca) {
       if ($tasca['ID'] == $id_eliminar) {
           unset($tasques[$index]);
           $trobat = true;
           $tasques = array_values($tasques); 
           break;
       }
   }
   if ($trobat) {
       echo "Tasca amb ID $id_eliminar eliminada correctament.\n";
   } else {
       echo "No s'ha trobat cap tasca amb ID $id_eliminar.\n";
   }


   if ($storage_type == 'json') {
       saveTasksToJson($json_file, $tasques);
   } else {
       saveTasksToCsv($csv_file, $tasques);
   }
} elseif (isset($options['m'])) {
   if (empty($tasques)) {
       echo "No hi ha tasques disponibles.\n";
   } else {
       echo "Llista de tasques:\n";
       echo str_pad("ID", 10) . str_pad("Títol", 30) . str_pad("Descripció", 50) . str_pad("Data", 15) . "\n";
       echo str_repeat("-", 105) . "\n";
       foreach ($tasques as $tasca) {
           echo str_pad($tasca['ID'], 10) . str_pad($tasca['títol'], 30) . str_pad($tasca['descripció'], 50) . str_pad($tasca['data'], 15) . "\n";
       }
   }
} else {
   echo "Opció incorrecta.\n";
   printHelp();
   exit(1);
}
?>


 
