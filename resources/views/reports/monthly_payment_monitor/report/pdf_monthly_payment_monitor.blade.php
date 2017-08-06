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
                    @if ($request['report_filter_month'] == '')
                        @foreach($months_array as $mon)
                            <th>{{ $mon }}</th>
                        @endforeach
                    @else
                        <th>{{ $months_array[$request['report_filter_month']-1] }}</th>
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
                                <span class="text-red">P {{ a_number_format($data->down_payment) }}</span>
                            </td>
                            @if ($request['report_filter_month'] == '')
                                <td>
                                    P {{ a_number_format($data->month_1_payment) }}         
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_2_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_3_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_4_payment) }}
                                <td>
                                    P {{ a_number_format($data->month_5_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_6_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_7_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_8_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_9_payment) }}
                                </td>
                                <td>
                                    P {{ a_number_format($data->month_10_payment) }}
                                </td>
                            @else
                                <td>
                                    P {{ a_number_format($data->month) }}
                                </td>
                            @endif
                            
                            <td>
                                <span class="text-red">P {{ a_number_format($data->total_remaining) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>