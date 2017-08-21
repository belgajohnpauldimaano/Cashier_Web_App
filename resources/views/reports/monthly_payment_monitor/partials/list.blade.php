<form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor') }}" id="form_monthly_payment_monitor_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="report_filter_month" name="report_filter_month" value="{{ $request['filter_month'] }}">
    <input type="hidden" id="report_filter_month_to" name="report_filter_month_to" value="{{ $request['filter_month_to'] }}">
</form>


<div class="pull-right">
    {{ $Students->links('admin.manage_student.partials.student_data_list_pagination') }}
</div>
<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
<table class="table table-bordered">
    <tr>
        <th>Student Name</th>
        <th>Grade / Section</th>
        <th>Down Payment</th>
        @if ($request['filter_month'] != '' && $request['filter_month_to'] != '')
            @for($i=$request['filter_month']-1;$i<$request['filter_month_to'];$i++)
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
                
                $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment - $student->tuition[0]->down_payment;

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
                    <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->down_payment) }}</span>
                </td>
                @if ($request['filter_month'] == '')
                     <td>
                    
                            @if ($student->tuition[0]->month_1_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_1_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($student->tuition[0]->month_1_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_2_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_2_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_2_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_3_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_3_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_3_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_4_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_4_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_4_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_5_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_5_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_5_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_6_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_6_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_6_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_7_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_7_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_7_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_8_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_8_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_8_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_9_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_9_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_9_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($student->tuition[0]->month_10_payment < $monthly_amount)
                                <span class="text-red">&#8369; {{ a_number_format($student->tuition[0]->month_10_payment) }}</span>
                            @else
                                <span class="text-green">
                                   &#8369; {{ a_number_format($student->tuition[0]->month_10_payment) }}
                                </span>
                            @endif
                    </td> 
                    @else
                        @for($i=$request['filter_month']-1;$i<$request['filter_month_to'];$i++)
                            <td>
                                @if ($student->tuition[0][$month_field[$i]] < $monthly_amount)
                                    <span class="text-red">
                                    &#8369; {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                    </span>
                                @else
                                    <span class="text-green">
                                    &#8369; {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                    </span>
                                @endif  
                            </td>
                        @endfor
                    @endif
                
                <td>
                    <span class="text-red">&#8369; {{ a_number_format($outstanding_balance) }}</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>