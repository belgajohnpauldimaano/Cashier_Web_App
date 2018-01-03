<form action="{{ route('reports.receivedpayments.export_pdf_received_payments') }}" id="form_received_payments_search_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="filter_start_date" name="filter_start_date" value="{{ $request['filter_start_date'] }}">
    <input type="hidden" id="filter_end_date" name="filter_end_date" value="{{ $request['filter_end_date'] }}">
    <input type="hidden" id="report_payment_type" name="report_payment_type" value="{{ $request['payment_type'] }}">
    <input type="hidden" name="report_school_year" value="{{ $request['filter_school_year'] }}"> 
    
</form>
<form action="{{ route('reports.receivedpayments.received_payments_summary_report') }}" id="form_received_payments_search_summary_report" method="POST">
    {{ csrf_field() }}   
    <input type="hidden" id="report_search_filter" name="report_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="{{ $request['filter_grade'] }}">
    <input type="hidden" id="report_filter_section" name="report_filter_section" value="{{ $request['filter_section'] }}">
    <input type="hidden" id="filter_start_date" name="filter_start_date" value="{{ $request['filter_start_date'] }}">
    <input type="hidden" id="filter_end_date" name="filter_end_date" value="{{ $request['filter_end_date'] }}">
    <input type="hidden" id="report_payment_type" name="report_payment_type" value="{{ $request['payment_type'] }}">
    <input type="hidden" name="report_school_year" value="{{ $request['filter_school_year'] }}"> 
    
</form>


<div class="pull-left margin">
    @if ($payment_sum->sum)
        <strong>
            Total Payment Received : <h3><span class="text-red text-md">&#8369; {{ a_number_format($payment_sum->sum) }}</span></h3>
        </strong>
    @else
        <strong>
            Total Payment Received : <h3><span class="text-red text-md">&#8369; 0.00</span></h3>
        </strong>
    @endif
</div>
<div class="pull-right">
    {{ $StudentPaymentLog->links('admin.manage_student.partials.student_data_list_pagination') }}
</div>
<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
<table class="table table-bordered">
    <tr>
        <th>Student Name</th>
        <th>Grade / Section</th>
        <th>Payment Type</th>
        <th>Payment Amount</th>
        <th>OR Number</th>
        <th>Date Received</th>
        <th>Received by</th>
        <th>Date Created</th>
        <th>Actions</th>
    </tr>
    <tbody>
        @foreach ($StudentPaymentLog as $data)
            <tr>
                <td>
                    {{ $data->student->last_name . ' ' . $data->student->first_name . ' ' . $data->student->middle_name }}
                </td>
                <td>
                    {{ $data->student->student_school_year_tag->grade->grade . ' / ' . $data->student->student_school_year_tag->section->section_name }}
                </td>
                <td>
                    @if ($data->payment_type == 1)
                        <span class="label bg-green">Tuition Fee Payment</span>
                    @else
                        <span class="label {{ \App\AdditionalFee::ADDITIONAL_FEES[$data->payment_type]['css_style'] }}">{{ \App\AdditionalFee::ADDITIONAL_FEES[$data->payment_type]['fee_name'] }}</span>
                    @endif
                </td>
                <td>
                    <span class="text-red">&#8369; {{ a_number_format($data->payment) }}</span>
                </td>
                <td>
                    <span class="">{{ $data->or_number }}</span>
                </td>   
                <td>
                    {{ \Carbon\Carbon::parse($data->received_date)->format('F d, Y') }}
                </td>
                <td>
                    @if ($data->user)
                        <span class="">{{ $data->user->first_name }}</span>
                    @else
                        <span>n/a</span>
                    @endif
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($data->created_at, 'Asia/Manila')->format('F d, Y') }}
                </td>
                <td>
                    <div class="input-group-btn  pull-left">
                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="fa fa-bars"></span>
                        </button>
                        <ul class="dropdown-menu text-right">
                            <li class=""><a href="#" class="js-edit_entry" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i>Edit</a></li>
                            <li class=""><a href="#" class="js-delete_entry" data-id="{{ $data->id }}"><i class="fa fa-trash"></i>Delete</a></li>
                            {{--  <li><a href="{{ route('admin.manage_fees.index', $data->id) }}"><i class="fa fa-pencil"></i>Manage Fees</a></li>  --}}
                            {{--  <li><a href="#" class="js-delete_discount_info" data-id="{{ $data->id }}"><i class="fa fa-trash"></i>Delete</a></li>  --}}
                        </ul>
                    </div>
                    {{--  <button class="btn btn-flat btn-primary btn-sm js-edit_entry" data-id="{{ $data->id }}">Edit Entry</button>  --}}
                </td>
        @endforeach
    </tbody>
</table>