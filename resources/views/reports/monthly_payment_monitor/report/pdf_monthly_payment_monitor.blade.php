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
        </style>
    </head>
    <body>
        <div class="container">
            <h2 class="text-center">Student Balance Summary Report</h2>
            <div>
            </div>
            <div class="text-right">Date Generated : {{ date('F d, Y') }}</div>
            <br>
            {{--  <div class="text-right">Total Received Payment <strong class="text-danger"><h3>P {{ ($payment_sum->sum ? a_number_format($payment_sum->sum) : '0.00') }}</h3></strong></div>  --}}
            <br>
            <table class="table table-bordered">
                <tr>
                    <th>Student Name</th>
                    <th>Grade / Section</th>
                    <th>Down Payment</th>
                    @if ($request['report_filter_month'] == '' || $request['report_filter_month_to'] == '')
                        @foreach($months_array as $mon)
                            <th>{{ $mon }}</th>
                        @endforeach
                    @else
                        @for($i=$request['report_filter_month']-1;$i<$request['report_filter_month_to'];$i++)
                            <th>{{ $months_array[$i] }}</th>
                        @endfor
                    @endif 
                    <th>Balance</th>
                </tr>
                <tbody>
                    <?php
                        $total_receivables = 0;
                    ?>
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
                            $net_tuition = ($tuition + $student->grade_tuition[0]->misc_fee) - $discount;
                            $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;
                            
                            $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;

                            if ($outstanding_balance <= 0)
                            {
                                $outstanding_balance = 0;
                            }
                            
                            $monthly_amount = ($net_tuition - $student->tuition[0]->down_payment) / 10;

                            if ($monthly_amount == 0)
                            {
                                $monthly_amount = $student->grade_tuition[0]->misc_fee + 2000;
                            }
                            
                            if ($monthly_amount > $net_tuition)
                            {
                                $monthly_amount = $net_tuition;
                            }

                            if ($outstanding_balance == 0)
                            {
                                $monthly_amount = 0;
                            }
                            
                            $total_receivables += $outstanding_balance;
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
                            <td> 
                                <span class="text-red"> {{ a_number_format($student->tuition[0]->down_payment) }}</span>
                            </td>
                            @if ($request['report_filter_month'] == '')
                                <td>
                                
                                        @if ($student->tuition[0]->month_1_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_1_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                                 {{ a_number_format($student->tuition[0]->month_1_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_2_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_2_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_2_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_3_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_3_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_3_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_4_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_4_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_4_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_5_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_5_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_5_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_6_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_6_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_6_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_7_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_7_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_7_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_8_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_8_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_8_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_9_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_9_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_9_payment) }}
                                            </span>
                                        @endif
                                </td>
                                <td>
                                        @if ($student->tuition[0]->month_10_payment < $monthly_amount)
                                            <span class="text-red"> {{ a_number_format($student->tuition[0]->month_10_payment) }}</span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month_10_payment) }}
                                            </span>
                                        @endif
                                </td> 
                            @else
                                
                                @for($i=$request['report_filter_month']-1;$i<$request['report_filter_month_to'];$i++)
                                    <td>
                                        @if ($student->tuition[0][$month_field[$i]] < $monthly_amount)
                                            <span class="text-red">
                                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                            </span>
                                        @else
                                            <span class="text-green">
                                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                            </span>
                                        @endif  
                                    </td>
                                @endfor
                                {{--  <td>
                                
                                        @if ($student->tuition[0]->month < $monthly_amount)
                                            <span class="text-red">
                                             {{ a_number_format($student->tuition[0]->month) }}
                                            </span>
                                        @else
                                            <span class="text-green">
                                             {{ a_number_format($student->tuition[0]->month) }}
                                            </span>
                                        @endif  
                                </td>  --}}
                            @endif
                            
                            <td>
                                <span class="text-red"> {{ a_number_format($outstanding_balance) }}</span>
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