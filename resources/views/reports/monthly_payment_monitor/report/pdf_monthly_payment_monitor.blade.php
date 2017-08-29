<!doctype>
<html>
    <head>
        <title>Student Summary Balance</title>
            {{--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">      --}}

        <style>
           body {
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                font-size: 10px;
                line-height: 1.42857143;
                color: #333;
                background-color: #fff;
            }
            .h2, h2, .h3, h3 {
                font-size: 30px;
                font-weight: 400;
                margin-top : 0;
            }
            .container {
                margin-right: auto;
                margin-left: auto;
            }
            .table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 20px;
                            
                border-spacing: 0;
                border-collapse: collapse;
            }
            .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
                border: 1px solid #ddd;
            }
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
                padding: 5px;
                line-height: 1.42857143;
                vertical-align: top;
                border-top: 1px solid #ddd;
            }
            .text-red {
                color: #a94442;
            }
            .text-green {
                color: #00a65a !important;
            }
            .text-right {
                text-align: right;
            }
            .text-center {
                text-align: center;
            }
            .hidden {
                display: none!important;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2 class="text-center">Student Balance Summary Report</h2>
            <div>
            </div>
            <div class="text-right">Date Generated : {{ \Carbon\Carbon::now('Asia/Manila')->format('m, d, Y h:i a') }}</div>
            <div class="text-right">Generated By   : {{ Auth::user()->first_name .  ' ' . Auth::user()->last_name }}</div>
            <br>
            {{--  <div class="text-right">Total Received Payment <strong class="text-danger"><h3>P {{ ($payment_sum->sum ? a_number_format($payment_sum->sum) : '0.00') }}</h3></strong></div>  --}}
            <br>
            <table class="table table-bordered">
                <tr>
                    <th>Student Name</th>
                    <th>Grade / Section</th>
                    <th>Down Payment</th>
                    @if ($request['report_filter_month'] != '' && $request['report_filter_month_to'] != '')
                        @for($i=$request['report_filter_month']-1;$i<$request['report_filter_month_to'];$i++)
                            <th>{{ $months_array[$i] }}</th>
                        @endfor
                    @else
                        @foreach($months_array as $mon)
                            <th>{{ $mon }}</th>
                        @endforeach
                    @endif 
                    <th>Balance</th>
                </tr>
    <tbody>
        <?php  $total_receivables = 0; ?>
        @foreach ($Students as $student)
            <?php
                $discount = 0;
                $tuition = $student->grade_tuition[0]->tuition_fee; 
                
                $discount += ($student->discount_list->scholar != 0 ? $student->discount_list->scholar * $tuition : 0);
                $discount += ($student->discount_list->school_subsidy != 0 ? $student->discount_list->school_subsidy : 0);
                $discount += ($student->discount_list->employee_scholar != 0 ? $student->discount_list->employee_scholar * $tuition : 0);
                $discount += ($student->discount_list->gov_subsidy  != 0 ? $student->discount_list->gov_subsidy  : 0);
                $discount += ($student->discount_list->acad_scholar  != 0 ? $student->discount_list->acad_scholar * $tuition : 0);
                $discount += ($student->discount_list->family_member  != 0 ? $student->discount_list->family_member * $tuition : 0);
                $discount += ($student->discount_list->nbi_alumni  != 0 ? $student->discount_list->nbi_alumni * $tuition : 0);
                $discount += ($student->discount_list->cash_discount  != 0 ? $student->discount_list->cash_discount * $tuition : 0);
                $discount += ($student->discount_list->cwoir_discount  != 0 ? $student->discount_list->cwoir_discount * $tuition : 0);
                $discount += ($student->discount_list->st_joseph_discount  != 0 ? $student->discount_list->st_joseph_discount : 0);
                               
                $tuition_fee = ($tuition + $student->grade_tuition[0]->misc_fee);
                $net_tuition = ($tuition - $discount) +  $student->grade_tuition[0]->misc_fee;
                $upon_enrollment = $student->grade_tuition[0]->upon_enrollment;
                $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;
                $tmp_tuition = (($tuition - $discount) + $student->grade_tuition[0]->misc_fee) - $student->tuition[0]->down_payment;

                $left_unpaid_down = 0;
                
                $monthly_amount = ($tuition_fee - $upon_enrollment) / 10;
                $tmp_monthly_amount = $monthly_amount;

                
                $left_unpaid_down = $upon_enrollment - $student->tuition[0]->down_payment;
                
                if ($monthly_amount > $net_tuition)
                {
                    $monthly_amount = $net_tuition;
                }

                if ($outstanding_balance == 0)
                {
                    $monthly_amount = 0;
                }


                //echo $tuition_fee;

                $total_monthly_payment = 0;
                $total_monthly_amount = 0;
            ?>
            <tr>
                <td>
                    <small>{{ $student->last_name . ', ' . $student->first_name . ' ' . $student->middle_name }}</small>
                </td>
                <td>
                    @if ($student)
                        <small>{{ $student->grade->grade . ' / ' . $student->section->section_name }}</small>
                    @endif
                </td>
                <td class="text-right"> 
                    <span class="text-red"> {{ a_number_format($student->tuition[0]->down_payment) }} / {{ $upon_enrollment }}</span>
                </td>
                @if ($request['report_filter_month'] != '' && $request['report_filter_month_to'] != '')
                    @for($i=0;$i<10;$i++)
                        <td class="{{ (($i>=$request['report_filter_month'] - 1) && ($i<=$request['report_filter_month_to']-1) ? '' : 'hidden') }} text-right">
                            @if ($tmp_tuition > $tmp_monthly_amount)
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                / {{a_number_format($tmp_monthly_amount)}}
                                <?php
                                    $tmp_tuition = $tmp_tuition - $tmp_monthly_amount;
                                    if (($i>=$request['report_filter_month'] - 1) && ($i<=$request['report_filter_month_to']-1))
                                    {
                                        $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                        $total_monthly_amount += $tmp_monthly_amount;
                                    }
                                ?>
                            @else
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                / {{a_number_format($tmp_tuition)}}
                                <?php
                                    if (($i>=$request['report_filter_month'] - 1) && ($i<=$request['report_filter_month_to']-1))
                                    {
                                        $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                        $total_monthly_amount += $tmp_tuition;
                                    }
                                    $tmp_tuition = $tmp_tuition - $tmp_tuition;
                                ?>
                            @endif
                        </td>
                    @endfor
                @else
                    @for($i=0;$i<10;$i++)
                        <td class="text-right">
                            @if ($tmp_tuition > $tmp_monthly_amount)
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                / {{a_number_format($tmp_monthly_amount)}}
                                <?php
                                    $tmp_tuition = $tmp_tuition - $tmp_monthly_amount;
                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                    $total_monthly_amount += $tmp_monthly_amount;
                                ?>
                            @else
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                / {{a_number_format($tmp_tuition)}}
                                <?php
                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                    $total_monthly_amount += $tmp_tuition;
                                    $tmp_tuition = $tmp_tuition - $tmp_tuition;
                                ?>
                            @endif
                        </td>
                    @endfor
                     {{--  <td>
                    
                            @if ($student->tuition[0]->m1 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m1) }}</span>
                            @else
                                <span class="text-green">
                                     {{ a_number_format($student->tuition[0]->m1) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m2 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m2) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m2) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m3 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m3) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m3) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m4 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m4) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m4) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m5 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m5) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m5) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m6 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m6) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m6) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m7 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m7) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m7) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m8 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m8) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m8) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m9 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m9) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m9) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->m10 < $monthly_amount)
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->m10) }}</span>
                            @else
                                <span class="text-green">
                                    {{ a_number_format($student->tuition[0]->m10) }}
                                </span>
                            @endif
                    </td>   --}}
                @endif
                <td class="text-right">
                    <span class="text-red">
                        {{ a_number_format(($total_monthly_amount - $total_monthly_payment) + $left_unpaid_down) }}
                        <?php 
                            $total_receivables = $total_receivables + (($total_monthly_amount - $total_monthly_payment) + $left_unpaid_down);
                        ?>
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
            </table>

            <div>
                <strong>Total Receivables : </strong>
                <h3 class="text-red">{{ a_number_format($total_receivables) }}</h3>
            </div>
        </div>
    </body>
</html>