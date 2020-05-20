<?php
    $myArray = [
        [1, 'a'],
        [15, 'b'],
        [4, 'c'],
        [8, 'd'],
        [12, 'e'],
        [22, 'f'],
    ];
    function s($array) {
       $arsize1 = sizeof($array) -1;
       $arsize = $arsize1 + 1;
       for ($i=0; $i < $arsize1; $i++) {
           for ($j = $i+1; $j < $arsize ;$j++){
                if ($array[$j][0] < $array[$i][0]) {
                    for ($k=0; $k<2; $k++) {
                         $a[$k] = $array[$i][$k];
                         $array[$i][$k] = $array[$j][$k];
                         $array[$j][$k] = $a[$k];
                     }
                 }
           }
       }
       return $array;
    }
    $newarray = s($myArray);
    foreach ($newarray as $value) {
       echo '<br> each  = '.$value[0]. ' '.$value[1];
    }
?>

