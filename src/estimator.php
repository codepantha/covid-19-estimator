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
        'estimate' => array(
            'impact' => impact($data),
            'severeImpact' => severeImpact($data)
        )
    );

    return $data;
}


function impact($data)
{
    $currentlyInfected =  $data['reportedCases'] * 10;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $totalHospitalBedsByRequestedTime = totalHospitalBedsByRequestedTime($data['totalHospitalBeds'], $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = (int) $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = (int) $infectionsByRequestedTime * 0.02;
    $dollarsInFlight = (int) ($infectionsByRequestedTime * ($data['region']['avgDailyIncomePopulation']) * ($data['region']['avgDailyIncomeInUSD']) * ($data['timeToElapse']));

    return array(
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime,
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'totalHospitalBedsByRequestedTime' => $totalHospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => $dollarsInFlight
    );
}

function severeImpact($data)
{
    $currentlyInfected =  $data['reportedCases'] * 50;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $totalHospitalBedsByRequestedTime = totalHospitalBedsByRequestedTime($data['totalHospitalBeds'], $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = (int) $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = (int) $infectionsByRequestedTime * 0.02;
    $dollarsInFlight = (int) ($infectionsByRequestedTime * ($data['region']['avgDailyIncomePopulation']) * ($data['region']['avgDailyIncomeInUSD']) * ($data['timeToElapse']));

    return array(
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime,
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'totalHospitalBedsByRequestedTime' => $totalHospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => $dollarsInFlight
    );
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

function totalHospitalBedsByRequestedTime($totalHospitalBeds, $severeCasesByRequestedTime)
{
    $availableBeds = (int) ($totalHospitalBeds * 0.35);
    return $availableBeds - $severeCasesByRequestedTime;
}
