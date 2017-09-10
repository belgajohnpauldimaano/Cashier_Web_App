<form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor') }}" id="form_monthly_payment_monitor_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="report_filter_month" name="report_filter_month" value="{{ $request['filter_month'] }}">
    <input type="hidden" id="report_filter_month_to" name="report_filter_month_to" value="{{ $request['filter_month_to'] }}">
</form>
<form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_summary_monitor') }}" id="form_monthly_payment_summary_monitor_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="report_filter_month" name="report_filter_month" value="{{ $request['filter_month'] }}">
    <input type="hidden" id="report_filter_month_to" name="report_filter_month_to" value="{{ $request['filter_month_to'] }}">
</form>

<form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor_teacher') }}" id="form_monthly_payment_summary_monitor_teacher_report" method="POST">
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
        <th>Tuition Fee</th>
        <th>Down Payment</th>
        <th>Discount</th>
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
                
                $discount += ($student->discount_list->scholar          != 0 ? $student->discount_list->scholar * $tuition : 0);
                $discount += ($student->discount_list->school_subsidy   != 0 ? $student->discount_list->school_subsidy : 0);
                $discount += ($student->discount_list->employee_scholar != 0 ? $student->discount_list->employee_scholar * $tuition : 0);
                $discount += ($student->discount_list->gov_subsidy      != 0 ? $student->discount_list->gov_subsidy  : 0);
                $discount += ($student->discount_list->acad_scholar     != 0 ? $student->discount_list->acad_scholar * $tuition : 0);
                $discount += ($student->discount_list->family_member    != 0 ? $student->discount_list->family_member * $tuition : 0);
                $discount += ($student->discount_list->nbi_alumni       != 0 ? $student->discount_list->nbi_alumni * $tuition : 0);
                $discount += ($student->discount_list->cash_discount    != 0 ? $student->discount_list->cash_discount * $tuition : 0);
                $discount += ($student->discount_list->cwoir_discount   != 0 ? $student->discount_list->cwoir_discount * $tuition : 0);
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
                <td>{{ a_number_format($tuition_fee) }}</td>
                <td> 
                {{--  {{ a_number_format(($left_unpaid_down > 0 ? $student->grade_tuition[0]->misc_fee + ($net_tuition >= 2000 ? 2000 : $net_tuition) : $left_unpaid_down)) }}  --}}
                    <span class="text-red"> {{ a_number_format($student->tuition[0]->down_payment) }}</span>
                </td>
                <td>{{ a_number_format($discount) }}</td>
                @if ($request['filter_month'] != '' && $request['filter_month_to'] != '')
                    @for($i=0;$i<10;$i++)
                        <td class="{{ (($i>=$request['filter_month'] - 1) && ($i<=$request['filter_month_to']-1) ? '' : 'hidden') }}">
                            @if ($tmp_tuition > $tmp_monthly_amount)
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                {{--  / {{a_number_format($tmp_monthly_amount)}}  --}}
                                <?php
                                    $tmp_tuition = $tmp_tuition - $tmp_monthly_amount;
                                    if (($i>=$request['filter_month'] - 1) && ($i<=$request['filter_month_to']-1))
                                    {
                                        $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                        $total_monthly_amount += $tmp_monthly_amount;
                                    }
                                ?>
                            @else
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                {{--  / {{a_number_format($tmp_tuition)}}  --}}
                                <?php
                                    if (($i>=$request['filter_month'] - 1) && ($i<=$request['filter_month_to']-1))
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
                        <td>
                            @if ($tmp_tuition > $tmp_monthly_amount)
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                {{--  / {{a_number_format($tmp_monthly_amount)}}  --}}
                                <?php
                                    $tmp_tuition = $tmp_tuition - $tmp_monthly_amount;
                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                    $total_monthly_amount += $tmp_monthly_amount;
                                ?>
                            @else
                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                {{--  / {{a_number_format($tmp_tuition)}}  --}}
                                <?php
                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                    $total_monthly_amount += $tmp_tuition;
                                    $tmp_tuition = $tmp_tuition - $tmp_tuition;
                                ?>
                            @endif
                        </td>
                    @endfor
                @endif
                <td>
                    <span class="text-red">
                        {{ a_number_format(($total_monthly_amount - $total_monthly_payment) + $left_unpaid_down) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>