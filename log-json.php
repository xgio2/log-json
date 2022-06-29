<!DOCTYPE html>
<html>
<head>
    <title>LOG-JSON.COM</title>
    <meta charset="utf-8" />
</head>
<body>
<?php
         //      принимаем файл
if ($_FILES && $_FILES["filename"]["error"]== UPLOAD_ERR_OK)
{
    $name = $_FILES["filename"]["name"];
    $pathInfo = pathinfo($name);
    $onlyname = $pathInfo["filename"];
    move_uploaded_file($_FILES["filename"]["tmp_name"], $name);
    echo "Файл загружен";
}
?>
<h2>Загрузка файла</h2>
<form method="post" enctype="multipart/form-data">
    Выберите файл: <input type="file" name="filename" size="10" /><br /><br />
    <input type="submit" value="Загрузить" />
</form>
<?php
//            ===Создаем необходимые массивы===
$json_array = []; // создаем пустой массив for foreach
$arrayurls = []; // создаем пустой массив для уникальних юрл
$arraysatuscode = []; // for satus code count
$arraycrawlers = [
    "Googlebot" => 0,
    "Bingbot" => 0,
    "Business Search" => 0,
    "Yandex" => 0,
];

$tmpmas = file($name);// получаем файл
 // Начинаем работу с линиями кода
foreach ($tmpmas as $key => $line){

    $json_array = explode(" ", $line); // разбиваем линию в массив

    $countviews ++; // считаем кол во виевс
    //       === Считаем трафик ===
    if ($json_array[5] ==  "\"POST"){
        $counttraffic += $json_array[9];
    }
//          === Счетчик url ===
    if (!in_array($json_array[6], $arrayurls)){  // счет уникальных url
     //   echo $json_array[6] . "<br>"; // check name url
        $arrayurls[$counturls] = $json_array[6];
        $counturls ++;
    }
//       === Счетчик статускода ===
	if(array_key_exists($json_array[8], $arraysatuscode)) {
        $arraysatuscode[$json_array[8]]++; // счет статус кода в массиве
	} else $arraysatuscode[$json_array[8]] = 1; // если нет, создаем его
//      === Счетчик crawlers ===
    $crawlersname = explode("/", $json_array[16]);
    if(array_key_exists($crawlersname[0], $arraycrawlers)) {
        $arraycrawlers[$crawlersname[0]]++; // счет статус кода в массиве
    }


}

//      === Создание массива ===
$result = [];
$result["views"]=$countviews;
$result["urls"]=$counturls;
$result["traffic"]=$counttraffic;
$result["crawlers"]["Google"]=$arraycrawlers["Googlebot"];
$result["crawlers"]["Bing"]=$arraycrawlers["Bingbot"];
$result["crawlers"]["Baidu"]=$arraycrawlers["Business Search"];
$result["crawlers"]["Yandex"]=$arraycrawlers["Yandex"];
$result["statusCodes"]=$arraysatuscode;
//       === Если надо смотреть. Что мы получим в загрузке ===
//echo '<pre>' , var_dump($result) , '</pre>';
//echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
//    === Кодикуем код и отправляем на загрузку ===
$jsonfilename = $onlyname.".json";
file_put_contents($jsonfilename,json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
if ($tmpmas){
?>
<a href="<?=$jsonfilename?>">download <?=$jsonfilename?></a>
<?php } ?>

</body>
</html>