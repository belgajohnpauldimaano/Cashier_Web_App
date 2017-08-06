<!doctype>
<html>
    <head>
        <title>Student Summary Balance</title>
           {{--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">     --}}

        <style>
           body {
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                font-size: 12px;
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
            <div class="text-right">Total Tuition Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_tuition_balance) }}</strong></div>
            <div class="text-right">Total Other Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_additional_fee) }}</strong></div>
            <div class="text-right">Total Balance <strong class="text-danger">{{ a_number_format($StudentTuitionFee->total_additional_fee + $StudentTuitionFee->total_tuition_balance)}}</strong></div>
            <br>
            <table class="table table-bordered">
                <tr>
                    <th>Student Name</th>
                    <th>Tuition Balace</th>
                    <th>Other Balance</th>
                    <th>Total Balance</th>
                </tr>
                @foreach ($Students as $data)
                    <tr>
                        <td>{{ $data->last_name . ', ' .  $data->first_name . ' ' . $data->middle_name }}</td>
                        <td>{{ a_number_format($data->tuition[0]->total_remaining) }}</td>
                         <td>{{ a_number_format($data->tuition[0]->additional_fee) }}</td> 
                         <td class="text-danger">{{ a_number_format($data->tuition[0]->additional_fee + $data->tuition[0]->total_remaining) }}</td> 
                    </tr>
                @endforeach
            </table>
        </div>
    </body>
</html>