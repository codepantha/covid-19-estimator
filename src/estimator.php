<?php

$inputData = [
    "region" => [
        "name" => "Africa",
        "avgAge" => 19.7,
        "avgDailyIncomeInUSD" => 4,
        "avgDailyIncomePopulation" => 0.73
    ],
    "periodType" => "days",
    "timeToElapse" => 38,
    "reportedCases" => 2747,
    "population" => 92931687,
    "totalHospitalBeds" => 678874
];

echo covid19ImpactEstimator($inputData);

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
    $currentlyInfected =  $data['reportedCases'] * 10;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);

    return [
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime
    ];
}

function severeImpact($data)
{
    $currentlyInfected = $data['reportedCases'] * 50;
    $infectionsByRequestedTime = infectionsByRequestedTime($data['periodType'], $data['timeToElapse'], $currentlyInfected);

    return [
        'currentlyInfected' => $currentlyInfected,
        'infectionsByRequestedTime' => $infectionsByRequestedTime
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