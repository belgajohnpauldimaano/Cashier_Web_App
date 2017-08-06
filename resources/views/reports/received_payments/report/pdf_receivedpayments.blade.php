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
                @if ($range_from)
                    Date Range from 
                    {{ $range_from }}
                @endif
                @if ($range_to)
                    to 
                    {{ $range_to }}
                @endif
            </div>
            <div class="text-right">Date Generated : {{ date('F d, Y') }}</div>
            <br>
            <div class="text-right">Total Received Payment <strong class="text-danger"><h3>P {{ ($payment_sum->sum ? a_number_format($payment_sum->sum) : '0.00') }}</h3></strong></div>
            <br>
            <table class="table table-bordered">
                <tr>
                    <th>Student Name</th>
                    <th>Grade / Section</th>
                    <th>Payment Type</th>
                    <th>Payment Amount</th>
                    <th>Received by</th>
                    <th>Date Received</th>
                </tr>
                <tbody>
                    @foreach ($StudentPaymentLog as $data)
                        <tr>
                            <td>
                                {{ $data->student->last_name . ' ' . $data->student->first_name . ' ' . $data->student->middle_name }}
                            </td>
                            <td>
                                {{ $data->student->grade->grade . ' / ' . $data->student->section->section_name }}
                            </td>
                            <td>
                                @if ($data->payment_type == 1)
                                    <span class="">Tuition Fee Payment</span>
                                @else
                                    <span class="">Other Additional Payments</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-red">P {{ a_number_format($data->payment) }}</span>
                            </td>
                            <td>
                                @if ($data->user)
                                    <span class="">{{ $data->user->first_name }}</span>
                                @else
                                    <span>n/a</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($data->created_at, 'Asia/Manila')->format('F d, Y h:i:s A') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>