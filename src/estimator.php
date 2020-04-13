<?php

$inputData = array(
    "region" => array (
        "name" => "Africa",
        "avgAge" => 19.7,
        "avgDailyIncomeInUSD" => 4,
        "avgDailyIncomePopulation" => 0.73
    ),
    "periodType" => "days",
    "timeToElapse" => 38,
    "reportedCases" => 2747,
    "population" => 92931687,
    "totalHospitalBeds" => 678874
);

echo covid19ImpactEstimator($inputData);

function covid19ImpactEstimator($data)
{
    $data = array(
        'data' => $data,
        'impact' => impact($data),
        'severeImpact' => severeImpact($data)
    );

    return $data;
}


function impact($data)
{
    $currentlyInfected =  $data['reportedCases'] * 10;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $hospitalBedsByRequestedTime = hospitalBedsByRequestedTime($data['totalHospitalBeds'], $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = (int) $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = (int) ($infectionsByRequestedTime * 0.02);

    return array(
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => floor($infectionsByRequestedTime),
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'hospitalBedsByRequestedTime' => $hospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => dollarsInFlight($infectionsByRequestedTime, $data['region']['avgDailyIncomeInUSD'], $data['region']['avgDailyIncomePopulation'], $data['timeToElapse'], $data['periodType'])
    );
}

function severeImpact($data)
{
    $currentlyInfected =  $data['reportedCases'] * 50;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $hospitalBedsByRequestedTime = hospitalBedsByRequestedTime($data['totalHospitalBeds'], $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = (int) $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = (int) ($infectionsByRequestedTime * 0.02);

    return array(
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => floor($infectionsByRequestedTime),
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'hospitalBedsByRequestedTime' => $hospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => dollarsInFlight($infectionsByRequestedTime, $data['region']['avgDailyIncomeInUSD'], $data['region']['avgDailyIncomePopulation'], $data['timeToElapse'], $data['periodType'])
    );
}

function dollarsInFlight($infectionsByRequestedTime, $avgDailyIncome, $avgPopulation, $timeToElapse, $periodType)
{
    if ($periodType === 'weeks') {
        $timeToElapseInDays = $timeToElapse * 7;
        return intdiv(($infectionsByRequestedTime * $avgDailyIncome * $avgPopulation), $timeToElapseInDays);
    }
    elseif ($periodType === 'months') {
        $timeToElapseInDays = $timeToElapse * 30;
        return intdiv(($infectionsByRequestedTime * $avgDailyIncome * $avgPopulation), $timeToElapseInDays);
    }
    else {
        return intdiv(($infectionsByRequestedTime * $avgDailyIncome * $avgPopulation), $timeToElapse);
    }
}

function infectionsByRequestedTime($periodType, $timeToElapse, $currentlyInfected)
{
    if ($periodType === 'weeks') {
        $timeToElapseInDays = $timeToElapse * 7;
        $factor = intdiv($timeToElapseInDays, 3);
        return $currentlyInfected * ( pow(2, $factor) );
    }
    elseif ($periodType === 'months') {
        $timeToElapseInDays = $timeToElapse * 30;
        $factor = intdiv($timeToElapseInDays, 3);
        return $currentlyInfected * ( pow(2, $factor) );
    }
    else {
        $factor = intdiv($timeToElapse, 3);
        return $currentlyInfected * ( pow(2, $factor) );
    }
}

function hospitalBedsByRequestedTime($totalHospitalBeds, $severeCasesByRequestedTime)
{
  return (int) (0.35 * $totalHospitalBeds - $severeCasesByRequestedTime);
}

