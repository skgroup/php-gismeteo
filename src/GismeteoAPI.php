<?php
/**
 * GismteoAPI.php
 * ----------------------------------------------
 *
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2014, CKGroup.ru
 *
 * ----------------------------------------------
 * All Rights Reserved.
 * ----------------------------------------------
 */
namespace SKGroup\Gismeteo;

use SKGroup\Gismeteo\Result\Day;
use SKGroup\Gismeteo\Result\Forecast;
use SKGroup\Gismeteo\Result\Location;
use SKGroup\Gismeteo\Result\Result;

/**
 * Class GismeteoAPI
 * @package SKGroup\Gismeteo
 */
class GismeteoAPI
{
    const API_URL = 'http://services.gismeteo.ru/inform-service';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param $key
     */
    public function setKeyAPI($key)
    {
        $this->apiKey = $key;
    }

    /**
     * @param $cityId
     * @return Result
     */
    public function getWeather($cityId)
    {
        return $this->request($cityId);
    }

    /**
     * @param $cityId
     * @return Result
     */
    protected function request($cityId)
    {
        $url = static::API_URL . '/' . $this->apiKey . '/forecast/?city=' . (int)$cityId;
        $response = file_get_contents($url);

        return $this->parse($response);
    }


    /**
     * @param $response
     * @return Result
     */
    protected function parse($response)
    {
        $Result = new Result();

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        if (!isset($array['location'])) {
            return $Result;
        }

        // Данные по городу...
        $Location = new Location();
        $Location->id = $array['location']['@attributes']['id'];
        $Location->name = $array['location']['@attributes']['name'];
        $Location->name_r = $array['location']['@attributes']['name_r'];
        $Location->tzone = $array['location']['@attributes']['tzone'];
        $Location->cur_time = $array['location']['@attributes']['cur_time'];

        // Информации по дням.
        $days = [];

        if (isset($array['location']['day']['@attributes'])) {
            $array['location']['day'] = array($array['location']['day']);
        }

        foreach ($array['location']['day'] as $day) {
            $d = new Day();
            $d->date = $day['@attributes']['date'];
            $d->risem = $day['@attributes']['risem'];
            $d->setm = $day['@attributes']['setm'];
            $d->durm = $day['@attributes']['durm'];
            $d->tmin = $day['@attributes']['tmin'];
            $d->tmax = $day['@attributes']['tmax'];
            $d->tavg = round(($d->tmin + $d->tmax) / 2, 0);
            $d->pmin = $day['@attributes']['pmin'];
            $d->pmax = $day['@attributes']['pmax'];
            $d->pavg = round(($d->pmin + $d->pmax) / 2, 0);
            $d->wsmin = $day['@attributes']['wsmin'];
            $d->wsmax = $day['@attributes']['wsmax'];
            $d->hummin = $day['@attributes']['hummin'];
            $d->hummax = $day['@attributes']['hummax'];
            $d->cl = $day['@attributes']['cl'];
            $d->pt = $day['@attributes']['pt'];
            $d->pr = $day['@attributes']['pr'];
            $d->ts = $day['@attributes']['ts'];
            $d->icon = $day['@attributes']['icon'];
            $d->descr = $day['@attributes']['descr'];
            $d->p = $day['@attributes']['p'];
            $d->ws = $day['@attributes']['ws'];
            $d->wd = $day['@attributes']['wd'];
            $d->hum = $day['@attributes']['hum'];
            $d->grademax = $day['@attributes']['grademax'];

            if (isset($day['forecast'])) {
                $forecasts = [];

                if (isset($day['forecast']['@attributes'])) {
                    $day['forecast'] = array($day['forecast']);
                }

                foreach ($day['forecast'] as $forecast) {
                    $f = new Forecast();
                    $f->valid = $forecast['@attributes']['valid'];
                    $f->tod = $forecast['@attributes']['tod'];
                    $f->t = $forecast[0]['@attributes']['t'];
                    $f->p = $forecast[0]['@attributes']['p'];
                    $f->ws = $forecast[0]['@attributes']['ws'];
                    $f->wd = $forecast[0]['@attributes']['wd'];
                    $f->hum = $forecast[0]['@attributes']['hum'];
                    $f->hi = $forecast[0]['@attributes']['hi'];
                    $f->cl = $forecast[0]['@attributes']['cl'];
                    $f->pt = $forecast[0]['@attributes']['pt'];
                    $f->pr = $forecast[0]['@attributes']['pr'];
                    $f->ts = $forecast[0]['@attributes']['ts'];
                    $f->icon = $forecast[0]['@attributes']['icon'];
                    $f->descr = $forecast[0]['@attributes']['descr'];
                    $f->grade = $forecast[0]['@attributes']['grade'];

                    $forecasts[] = $f;
                }

                $d->forecast = $forecasts;
            }

            $days[] = $d;
        }

        $Result->exp_time = $array['@attributes']['exp_time'];
        $Result->location = $Location;
        $Result->day = $days;

        return $Result;
    }
}
