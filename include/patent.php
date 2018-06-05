<?php

namespace BDZ;


class Patent
{
    public static $BD;
    public static $host = "localhost";
    public static $user = "root";
    public static $pass = "root";
    public static $db_name = "PatentAnalise";
    public $arCountries;
    public $arIndicators;
    public $arYears;

    function __construct() {
        self::$BD = new \mysqli(self::$host, self::$user, self::$pass, self::$db_name);
        self::$BD->set_charset('utf8');
        $this->getCountryData();
        $this->getIndicatorData();
        $this->getYearData();
    }

    public function getCountryData() {
        $rsCountry = self::$BD -> query("SELECT * FROM Country ORDER BY `NAME`");
        if ($rsCountry) {
            $this->arCountries = $rsCountry->fetch_all(MYSQLI_ASSOC);
            return $this->arCountries;
        }
    }

    public function getIndicatorData() {
        $rsIndicator = self::$BD -> query("SELECT * FROM Indicator ORDER BY `NAME`");
        if ($rsIndicator) {
            $this->arIndicators = $rsIndicator->fetch_all(MYSQLI_ASSOC);
            return $this->arIndicators;
        }
    }

    public function getYearData() {
        $rsYear = self::$BD -> query("SELECT * FROM Year ORDER BY `YEAR`");
        if ($rsYear) {
            $this->arYears = $rsYear->fetch_all(MYSQLI_ASSOC);
            return $this->arYears;
        }
    }

    public function createChart($indicator, $country, $year_from, $year_to)
    {
        $indicator = trim($indicator);
        $year_from = intval($year_from);
        $year_to = intval($year_to);

        if (!empty($indicator) && !empty($country) && !empty($year_from) && !empty($year_to)) {

            foreach ($country as $arCounty) {
                $arQueryCountry[] = "`COUNTRY` = " . $arCounty;
            }

            foreach ($this->arIndicators as $indic) {
                if ($indic["CODE"] == $indicator) {
                    $chartIndicator = $indic["NAME"];
                }
            }

            if (!empty($arQueryCountry) && !empty($chartIndicator)) {
                if ($year_from < $year_to) {
                    $strCountry = implode(" OR ", $arQueryCountry);
                    $querySelect = "SELECT * FROM `Data` WHERE (". $strCountry .") AND `YEAR` BETWEEN ". $year_from ." AND ". $year_to ." ORDER BY `YEAR`";
                    $arQuery = self::$BD -> query($querySelect)->fetch_all(MYSQLI_ASSOC);
                    print_r(self::$BD -> error);

                    if ($arQuery) {
                        foreach ($this->arCountries as $arCounties) {
                            $arResult[$arCounties["ID"]] = array(
                                'name' => $arCounties["NAME"]
                            );
                            foreach ($arQuery as $row) {
                                if ($arCounties["ID"] == $row["COUNTRY"]) {
                                    $eventDate = new \DateTime("01.01.".$row["YEAR"]);
                                    $dateFormat = $eventDate->format("U");
                                    $date = (intval($dateFormat) + 14400) * 1000;
                                    $arResult[$arCounties["ID"]]["data"][] = [
                                        $date, floatval($row[$indicator]), $row["YEAR"]
                                    ];
                                }
                            }
                            if (empty($arResult[$arCounties["ID"]]["data"])) {
                                unset($arResult[$arCounties["ID"]]);
                            }
                        }
                    } else {
                        $status = "error";
                        $message = "Патенты по заданным параметрам не найдены";
                    }
                } else {
                    $status = "error";
                    $message = "Год начала должен быть больше года окончания";
                }
            } else {
                $status = "error";
                $message = "Страны не найдены";
            }
        } else {
            $status = "error";
            $message = "Вы ввели не все данные!";
        }

        if (!empty($arResult)) {
            $status = "success";
        }

        return array(
            "status" => !empty($status) ? $status : "",
            "result" => !empty($arResult) ? $arResult : "",
            "oy" => !empty($chartIndicator) ? $chartIndicator : "",
            "message" => !empty($message) ? $message : ""
        );
    }
}