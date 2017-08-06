<form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor') }}" id="form_monthly_payment_monitor_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="report_filter_month" name="report_filter_month" value="{{ $request['filter_month'] }}">
</form>


<div class="pull-right">
    {{ $StudentTuitionFee->links('admin.manage_student.partials.student_data_list_pagination') }}
</div>
<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
<table class="table table-bordered">
    <tr>
        <th>Student Name</th>
        <th>Grade / Section</th>
        <th>Down Payment</th>
        @if ($request['filter_month'] == '')
            @foreach($months_array as $mon)
                <th>{{ $mon }}</th>
            @endforeach
        @else
            <th>{{ $months_array[$request['filter_month']-1] }}</th>
        @endif
        <th>Balance</th>
    </tr>
    <tbody>
        @foreach ($StudentTuitionFee as $data)
            <tr>
                <td>
                    @if ($data->student)
                        <small>{{ $data->student->last_name . ', ' . $data->student->first_name . ' ' . $data->student->middle_name }}</small>
                    @endif
                </td>
                <td>
                    @if ($data->student)
                        <small>{{ $data->student->grade->grade . ' / ' . $data->student->section->section_name }}</small>
                    @endif
                </td>
                <td>
                    <span class="text-red">&#8369; {{ a_number_format($data->down_payment) }}</span>
                </td>
                @if ($request['filter_month'] == '')
                    <td>
                            @if ($data->month_1_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_1_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_1_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_2_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_2_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_2_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_3_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_3_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_3_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_4_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_4_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_4_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_5_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_5_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_5_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_6_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_6_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_6_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_7_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_7_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_7_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_8_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_8_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_8_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_9_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_9_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_9_payment) }}
                                </span>
                            @endif
                    </td>
                    <td>
                            @if ($data->month_10_payment < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">&#8369; {{ a_number_format($data->month_10_payment) }}</span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month_10_payment) }}
                                </span>
                            @endif
                    </td>
                @else
                    <td>
                            @if ($data->month < $data->monthly_payment || $data->monthly_payment == 0)
                                <span class="text-red">
                                    &#8369; {{ a_number_format($data->month) }}
                                </span>
                            @else
                                <span class="text-green">
                                    &#8369; {{ a_number_format($data->month) }}
                                </span>
                            @endif
                    </td>
                @endif
                
                <td>
                    <span class="text-red">&#8369; {{ a_number_format($data->total_remaining) }}</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>