<!doctype>
<html>
    <head>
        <title>Student Summary Balance</title>
           {{--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">     --}}

        <style>
            @page {
                margin : 2mm;
            }
           body {
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                font-size: 11px;
                line-height: 1.42857143;
                color: #333;
                background-color: #fff;
            }
            .h2, h2 {
                font-size: 30px;
                font-weight: 400;
            }
            .container {
                padding-right: 15px;
                padding-left: 15px;
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
                padding: 6px;
                line-height: 1.42857143;
                vertical-align: top;
                border-top: 1px solid #ddd;
            }
            .text-danger {
                color: #a94442;
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
            {{--  <div class="text-right">Total Tuition Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_tuition_balance) }}</strong></div>
            <div class="text-right">Total Other Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_additional_fee) }}</strong></div>
            <div class="text-right">Total Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_additional_fee + $StudentTuitionFee->total_tuition_balance)}}</strong></div>  --}}
            <div class="text-right">Date Generated : {{ date('F d, Y') }}</div>
            <br>
            <?php
                $over_all_tuition_sum = 0;
                $over_all_discount_sum = 0;
                $over_all_net_tuition = 0;
                $over_all_paid_fee = 0;
                $over_all_add_fee = 0;
                $over_all_add_paid_fee = 0;
                $over_outstanding_balace = 0;

            ?>
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Section</th>
                    <th>Tuition</th>
                    <th>Discount</th>
                    <th>Net Tuition</th>
                    <th>Tuition Paid Fees</th>
                    <th>Outstanding Balance</th>
                    <th>Additional Fees</th>
                    <th>Additional Paid Fees</th>
                </tr>
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
                        $paid_fee = $student->tuition[0]->total_payment;
                        $add_paid_fee = $student->tuition[0]->additional_fee_total;
                        $additional_fee_total = 0;
                        
                        $additiona_fee_total_payment = $student->tuition[0]->additional_fee_total;
                        
                        $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;
                        if ($outstanding_balance <= 0)
                        {
                            $outstanding_balance = 0;
                        }

                        $over_all_tuition_sum += $tuition_fee;
                        $over_all_discount_sum += $discount;
                        $over_all_net_tuition += $net_tuition;
                        $over_all_paid_fee += $paid_fee;
                        $over_all_add_paid_fee += $add_paid_fee;
                        $over_outstanding_balace += $outstanding_balance;
                    ?>
                    <tr>
                        <td>
                            {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                        </td>
                        <td>
                            @if ($student->grade)
                                {{ $student->grade->grade }}
                            @endif
                        </td>
                        <td>
                            @if ($student->section)
                                {{ $student->section->section_name }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($student->grade_tuition)
                                {{ a_number_format($tuition_fee) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($student->discount_list)
                                {{ a_number_format($discount) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($student->discount_list)
                                {{ a_number_format($net_tuition) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($student->tuition)
                                {{ a_number_format( $paid_fee) }}
                            @endif
                        </td>
                        <td class="text-danger text-right">
                                {{ a_number_format($outstanding_balance ) }}
                        </td>
                        <td class="text-right">
                            @if ($student->additional_fee)
                                @foreach($student->additional_fee as $additional)
                                    <?php  
                                        $additional_fee_total += $additional->additional_amount; 
                                    ?>
                                @endforeach
                                <?php $over_all_add_fee += $additional_fee_total; ?>
                                {{ a_number_format($additional_fee_total - $additiona_fee_total_payment) }}
                            @endif
                        </td>
                        <td class="text-right">
                                {{ a_number_format($add_paid_fee ) }}
                        </td>
                    </tr>
                @endforeach
                    <tr class="text-danger">
                        <td colspan="3">Total</td>
                        <td class="text-right">{{ a_number_format($over_all_tuition_sum) }}</td>
                        <td class="text-right">{{ a_number_format($over_all_discount_sum) }}</td>
                        <td class="text-right">{{ a_number_format($over_all_net_tuition) }}</td>
                        <td class="text-right">{{ a_number_format($over_all_paid_fee) }}</td>
                        <td class="text-right">{{ a_number_format($over_outstanding_balace) }}</td>
                        <td class="text-right">{{ a_number_format($over_all_add_fee - $over_all_add_paid_fee) }}</td>
                        <td class="text-right">{{ a_number_format($over_all_add_paid_fee) }}</td>
                    </tr>
            </table>
        </div>
    </body>
</html>