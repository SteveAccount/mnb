<?php

namespace Mnb;

/**
 * Class Mnb
 * @package Mnb
 */
class Mnb{
    /**
     * Returns rate of the given currency.
     * @param string $currency
     * @return float|null
     */
    public static function getExchangeRate(string $currency) : ?float{
        $client = new \SoapClient("http://www.mnb.hu/arfolyamok.asmx?wsdl");
        $rates = $client->GetCurrentExchangeRates()->GetCurrentExchangeRatesResult;
        $xml = simplexml_load_string($rates);
        foreach ($xml->Day->children() as $rate){
            if ($rate["curr"] === $currency){
                $unit = $rate["unit"];
                $value = str_replace(",", ".", $rate);
                return $value/$unit;
            }
        }
        http_response_code(503);
        return null;
    }

    /**
     * Returns rate of all currencies.
     * @return string|null
     */
    public static function getExchangeRates() : ?string{
        $result = [];
        $client = new \SoapClient("http://www.mnb.hu/arfolyamok.asmx?wsdl");
        if ($rates = $client->GetCurrentExchangeRates()->GetCurrentExchangeRatesResult){
            $xml = simplexml_load_string($rates);
            foreach($xml->Day->children() as $rate){
                $unit = $rate["unit"];
                $value = str_replace(",", ".", $rate);
                $result += [(string)$rate["curr"] => (float)$value/$unit];
            }
            return json_encode($result);
        }
        http_response_code(503);
        return null;
    }
}