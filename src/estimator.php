<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"));
    echo covid19ImpactEstimator($inputData);
}


function covid19ImpactEstimator($data)
{

    $estimate = [
        'impact' => impact($data),
        'severeImpact' => severeImpact($data)
    ];

    $data = json_encode([
        'data' => $data,
        'estimate' => $estimate
    ]);

    return $data;
}


function impact($data)
{
    $currentlyInfected =  $data->reportedCases * 10;
    $infectionsByRequestedTime = infectionsByRequestedTime($data->periodType, $data->timeToElapse, $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $totalHospitalBedsByRequestedTime = totalHospitalBedsByRequestedTime($data->totalHospitalBeds, $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = $infectionsByRequestedTime * 0.02;
    $dollarsInFlight = $infectionsByRequestedTime * ($data->region->avgDailyIncomePopulation) * ($data->region->avgDailyIncomeInUSD) * ($data->timeToElapse);

    return [
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime,
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'totalHospitalBedsByRequestedTime' => $totalHospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => (int) $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => (int) $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => (int) $dollarsInFlight
    ];
}

function severeImpact($data)
{
    $currentlyInfected = $data->reportedCases * 50;
    $infectionsByRequestedTime = infectionsByRequestedTime($data->periodType, $data->timeToElapse, $currentlyInfected);
    $severeCasesByRequestedTime = (int) $infectionsByRequestedTime * 0.15;
    $totalHospitalBedsByRequestedTime = totalHospitalBedsByRequestedTime($data->totalHospitalBeds, $severeCasesByRequestedTime);
    $casesForICUByRequestedTime = $infectionsByRequestedTime * 0.05;
    $casesForVentilatorsByRequestedTime = $infectionsByRequestedTime * 0.02;
    $dollarsInFlight = $infectionsByRequestedTime * $data->region->avgDailyIncomePopulation * $data->region->avgDailyIncomeInUSD * $data->timeToElapse;

    return [
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime,
        'severeCasesByRequestedTime' => $severeCasesByRequestedTime,
        'totalHospitalBedsByRequestedTime' => $totalHospitalBedsByRequestedTime,
        'casesForICUByRequestedTime' => (int) $casesForICUByRequestedTime,
        'casesForVentilatorsByRequestedTime' => (int) $casesForVentilatorsByRequestedTime,
        'dollarsInFlight' => (int) $dollarsInFlight
    ];
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