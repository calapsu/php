<?php

//tercer punto

//$valores = [23, 54, 32, 67, 34, 78, 98, 56, 21, 34, 57, 92, 12, 5, 61];
//sort($valores);   //scort ordena los numero de menr a mayor
//for ($i = 0; $i < 3; $i++) {
//    echo " $valores[$i], \n";
//};
// rsort($valores); // rsort ordena los nukmeros de mayor a menor
// for ($i = 0; $i < 3; $i++) {
//     echo "$valores[$i], \n";
// }
//

//2 punto

$Paises = [
    'colombia' => ['santamarta', 'garzon', 'neiva'],
    'uuss' =>  ['florida', 'carolina', 'whashigton']
];

foreach($Paises as $ciudades => $country) {
echo "$ciudades \n";
foreach($country as  $city) {
    echo " $city \n";
}
}

?>