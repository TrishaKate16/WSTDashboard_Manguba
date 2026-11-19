<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HumanResourceController extends Controller
{
    public function calculate(Request $request)
    {
        $employmentType = $request->employmentType;
        $position = $request->position;
        $daysAbsent = $request->daysAbsent;
        $overtimeHours = $request->overtimeHours;

        if ($employmentType == "Part-Time") {

            $monthlySalary = 15000;

        } elseif ($employmentType == "Manager" && $position == "Staff") {

            $monthlySalary = 28000;

        } elseif ($employmentType == "Regular" && $position == "Manager") {

            $monthlySalary = 45000;

        } else {

            return response()->json([
                "message" => "Invalid Employment Type or Position"
            ], 400);
        }

        $workingDays = 22;
        $daysWorked = $workingDays - $daysAbsent;

        $dailyRate = $monthlySalary / $workingDays;
        $hourlyRate = $dailyRate / 8;

        $absenceDeduction = $dailyRate * $daysAbsent;

        $overtimePay = $overtimeHours * ($hourlyRate * 1.25);

        $grossPay = $monthlySalary
                  - $absenceDeduction
                  + $overtimePay;

        $netPay = $grossPay;

        return response()->json([
            "employmentType" => $employmentType,
            "position" => $position,
            "monthlySalary" => $monthlySalary,
            "workingDays" => $workingDays,
            "daysWorked" => $daysWorked,
            "daysAbsent" => $daysAbsent,
            "dailyRate" => round($dailyRate, 2),
            "hourlyRate" => round($hourlyRate, 2),
            "absenceDeduction" => round($absenceDeduction, 2),
            "overtimeHours" => $overtimeHours,
            "overtimePay" => round($overtimePay, 2),
            "grossPay" => round($grossPay, 2),
            "netPay" => round($netPay, 2)
        ]);
    }
}

 