<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Большое домашнее задание</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.8.0.release.js" type="text/javascript"></script>
    <script src="js/highcharts.js"></script>
    <script src="js/script.js" type="text/javascript"></script>
</head>
<body>
<?include_once ($_SERVER['DOCUMENT_ROOT']."/include/patent.php")?>
<?
$time_start = microtime(true);//короче мерием время по всей проге
$patent = new BDZ\Patent();
$arIndicators = $patent->arIndicators;
$arCountries = $patent->arCountries;
$arYears = $patent->arYears;

if (!empty($_REQUEST)) {
    if (!empty($_REQUEST["indicator"]) && !empty($_REQUEST["country"]) && !empty($_REQUEST["year_from"]) && !empty($_REQUEST["year_to"])) {
        $eventStatus = $patent->createChart($_REQUEST["indicator"], $_REQUEST["country"], $_REQUEST["year_from"], $_REQUEST["year_to"]);
    } else {
        $eventStatus = array("status" => "error", "message" => "Вы ввели не все данные!");
    }
}
?>

<div class="content" id="main_content" data-oy="<?=$eventStatus["oy"]?>" data-content='<?=json_encode($eventStatus["result"])?>'>

    <div class="title">
        <h2>Патентный анализатор</h2>
    </div>

    <div class="initial_data">
        <div class="introduce">
            <div class="enter">
                <div class="entered_data_title">
                    <h3>Данные</h3>
                </div>

                <?if (!empty($eventStatus["status"])):?>
                    <?if ($eventStatus["status"] == "success"):?>
                        <p class="success">График по заданным параметрам построен!</p>
                    <?else:?>
                        <p class="danger"><?=$eventStatus["message"]?></p>
                    <?endif;?>
                <?else:?>
                    <p class="empty"></p>
                <?endif;?>

                <div class="enter_line">
                    <form name="patents" id="patent_data" method="GET" style="display: inline-flex">
                        <div class="enter_indicators">
                            <h4>Выберите значения показателей:</h4>
                            <?foreach ($arIndicators as $arIndicator):?>
                                <input type="radio" name="indicator" value="<?=$arIndicator['CODE']?>"
                                    <?=(!empty($_REQUEST['indicator']) && $_REQUEST['indicator'] == $arIndicator['CODE'])? "checked" : ""?>>
                                <?=$arIndicator['NAME']?><br>
                            <?endforeach;?>
                        </div>

                        <div class="enter_countries">
                            <h4>Выберите страны:</h4>
                            <?foreach ($arCountries as $arCountry):?>
                                <input name="country[]" type="checkbox" value="<?=$arCountry['ID']?>"
                                    <?=(!empty($_REQUEST['country']) && in_array($arCountry['ID'], $_REQUEST['country']))? "checked" : ""?>>
                                <?=$arCountry['NAME']?><br>
                            <?endforeach;?>
                        </div>

                        <div class="enter_year">
                            <h4>Выберите период:</h4>

                            <select name="year_from">
                                <option disabled <?=empty($_REQUEST["year_from"])? "selected" : ""?>>
                                    Выберите год
                                </option>
                                <?foreach ($arYears as $arYear):?>
                                    <option value="<?=$arYear["YEAR"]?>" <?=(!empty($_REQUEST["year_from"]) && $_REQUEST["year_from"] == $arYear["YEAR"])? "selected" : ""?>>
                                        <?=$arYear["YEAR"]?>
                                    </option>
                                <?endforeach;?>
                            </select>

                            <h4 class="enter_year_by">по</h4>

                            <select name="year_to">
                                <option disabled <?=empty($_REQUEST["year_to"])? "selected" : ""?>>
                                    Выберите год
                                </option>
                                <?foreach ($arYears as $arYear):?>
                                    <option value="<?=$arYear["YEAR"]?>" <?=(!empty($_REQUEST["year_to"]) && $_REQUEST["year_to"] == $arYear["YEAR"])? "selected" : ""?>>
                                        <?=$arYear["YEAR"]?>
                                    </option>
                                <?endforeach;?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="entered_data_title_left_add" id="button_submit">
                    <p>Построить график</p>
                </div>
            </div>
        </div>
    </div>

    <?if (!empty($_REQUEST["indicator"]) && !empty($_REQUEST["country"]) &&
        !empty($_REQUEST["year_from"]) && !empty($_REQUEST["year_to"]) && $eventStatus["status"] == "success") {?>
        <div class="schedule">
            <div class="schedule_title">
                <h3>График</h3>
            </div>
            <div class="schedule_svg">
                <div id="container"></div>
            </div>
        </div>
    <?}?>
    <?
    $time_end = microtime(true);
    $time_1 = $time_end - $time_start;
    file_put_contents("project_time.txt", $time_1."\n\r", FILE_APPEND | LOCK_EX);
    ?>
</div>
</body>
</html>