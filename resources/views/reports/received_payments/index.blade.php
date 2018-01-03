@extends('layouts.main')

@section('content_title', 'Received Payments')

@section('styles')
    <link rel="stylesheet" href="{{ asset('cms/plugins/datepicker/datepicker3.css') }}">
@endsection

@section ('content')

    <div class="clearfix margin"></div>
    <div class="box box-solid">
        <div class="box-body">
            
             <div class="filters">
                <form action="" id="search">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Search</label>
                                <input type="text" id="search_filter" name="search_filter" class="form-control js-search_filters">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Grade</label>
                                <select name="filter_grade" id="filter_grade" class="form-control js-search_filters">
                                    <option value="">All</option>
                                    @if($Grade)
                                        @foreach ($Grade as $data)
                                            <option value="{{ $data->id }}">{{ $data->grade }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Section</label>
                                <select name="filter_section" id="filter_section" class="form-control js-search_filters">
                                    <option value="">All</option>
                                    @if($Section)
                                        @foreach ($Section as $data)
                                            <option value="{{ $data->id }}">{{ $data->section_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 pull-right"> 
                            <div class="form-group">
                                <label for="">Show Entries</label>
                                <select name="show_count" id="show_count" class="form-control js-search_filters">
                                    <option value="">All</option>
                                    {{--  <option value="1" selected="selected">1</option>
                                    <option value="2">2</option>  --}}
                                    <option value="10" selected="selected">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Date Filter Start</label>
                                <input type="text" id="filter_start_date" name="filter_start_date" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Date Filter End</label>
                                <input type="text" id="filter_end_date" name="filter_end_date" class="form-control  datepicker">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Payment Type</label>
                                <select name="payment_type" id="payment_type" class="form-control js-search_filters">
                                    <option value="" selected>All</option>
                                    <option value="1">Tuition Fee</option>
                                    <option value="6">Other Fees Only</option>
                                    @foreach (\App\AdditionalFee::ADDITIONAL_FEES as $key => $additional_fee_types)
                                        <option value="{{ $key }}">{{ $additional_fee_types['fee_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">School Year</label>
                                <select name="filter_school_year" id="filter_school_year" class="form-control js-search_filters">
                                    {{--  <option value="">All</option>  --}}
                                    @if($SchoolYear)
                                        @foreach ($SchoolYear as $data)
                                            <option value="{{ $data->id }}">{{ $data->school_year }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-flat btn-primary btn-sm js-btn_search_filters" type="button"><i class="fa fa-search"></i> Search</button>
                    <button class="btn btn-flat btn-danger btn-sm js-btn_export_pdf" type="button"><i class="fa fa-file-pdf-o"></i> export to pdf</button>
                    <button class="btn btn-flat btn-danger btn-sm js-btn_summary_export_pdf" type="button"><i class="fa fa-file-pdf-o"></i> export summary to pdf</button>
                </form>
            </div> 

            <div class="js-content_holder box box-solid">
                <form action="{{ route('reports.receivedpayments.export_pdf_received_payments') }}" id="form_received_payments_search_report" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="report_search_filter" name="report_search_filter" >
                    <input type="hidden" id="report_filter_grade"  name="report_filter_grade">
                    <input type="hidden" id="report_filter_section" name="report_filter_section" >
                    <input type="hidden" id="filter_start_date" name="filter_start_date">
                    <input type="hidden" id="filter_end_date" name="filter_end_date">
                    <input type="hidden" name="report_school_year" value="{{ ($SchoolYear ? $SchoolYear[0]->id : '') }}"> 
                </form>
                <form action="{{ route('reports.receivedpayments.received_payments_summary_report') }}" id="form_received_payments_search_summary_report" method="POST">
                    {{ csrf_field() }}   
                    <input type="hidden" id="report_search_filter" name="report_search_filter" value="">
                    <input type="hidden" id="report_filter_grade"  name="report_filter_grade" value="">
                    <input type="hidden" id="report_filter_section" name="report_filter_section" value="">
                    <input type="hidden" id="filter_start_date" name="filter_start_date" value="">
                    <input type="hidden" id="filter_end_date" name="filter_end_date" value="">
                    <input type="hidden" id="report_payment_type" name="report_payment_type" value="">
                    <input type="hidden" name="report_school_year" value="{{ ($SchoolYear ? $SchoolYear[0]->id : '') }}"> 
                    
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
                <div>
                {{--  <div class="pull-left">
                    <span>{{ $payment_sum->sum }}</span>
                </div>
                    <span class="pull-right">
                        {{ $StudentPaymentLog->links('admin.manage_student.partials.student_data_list_pagination') }}
                    </span>
                </div>  --}}
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
                                    {{ \Carbon\Carbon::parse($data->received_date, 'Asia/Manila')->format('F d, Y') }}                                    
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
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="fa fa-bars"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="js-edit_entry" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i>Edit Entry</a></li>
                                            <li class=""><a href="#" class="js-delete_entry" data-id="{{ $data->id }}"><i class="fa fa-trash"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                    {{--  <button class="btn btn-flat btn-primary btn-sm js-edit_entry" data-id="{{ $data->id }}"></button>  --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section ('scripts')
    <!-- jquery-toast-plugin -->
    <script src="{{ asset('cms/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script>
        // datepicker init
        $('.datepicker').datepicker({
            autoclose: true
        });

        $('body').on('submit', '#search', function (e) {
            e.preventDefault();
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            fetch_data({
                url         : "{{ route('reports.receivedpayments.list') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });
        $('body').on('click', '.js-btn_search_filters', function (e) {
            e.preventDefault();
            $('#search').submit();
        });
        $('body').on('change', '.js-search_filters', function (e) {
            $('#search').submit();
        });
        var page = 1;
        $('body').on('click' , '.paginate_item', function (e) {
            e.preventDefault();
            page = $(this).data('page');
            var formData = new FormData($('#search')[0]);
            formData.append('page', page);
            fetch_data({
                url         : "{{ route('reports.receivedpayments.list') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });
        $('body').on('submit', '#form_received_payments_search_report', function () {
            $(this).attr('target', '_blank');
        });
        $('body').on('click', '.js-btn_export_pdf', function (e) {
            e.preventDefault();
            $('#form_received_payments_search_report').submit();
        });
        
        $('body').on('submit', '#form_received_payments_search_summary_report', function () {
            $(this).attr('target', '_blank');
        });
        $('body').on('click', '.js-btn_summary_export_pdf', function (e) {
            e.preventDefault();
            $('#form_received_payments_search_summary_report').submit();
        });
        
        $('body').on('click', '.js-edit_entry', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            show_form_modal({
                url         : "{{ route('reports.receivedpayments.edit_payment_modal') }}",
                reqData     : {
                                _token  : '{{ csrf_token() }}',
                                id      : id,
                                sy_id   : $('#filter_school_year').val()
                            },
                target      : $('.js-form_modal_holder'),
                func        : {}
            });
            
        });

        $('body').on('submit', '#form_entry_correction', function (e) {
            e.preventDefault();
            
            var formData = new FormData($('#search')[0]);
            formData.append('page', page);
            
            save_data({
                url : "{{ route('reports.receivedpayments.save_edit_entry') }}",
                form : $(this),
                fetch_data : {
                    func    : fetch_data,
                    params  : {
                        url         : "{{ route('reports.receivedpayments.list') }}",
                        formData    : formData,
                        target      : $('.js-content_holder')
                    }
                }
            })
        });

        $('body').on('click', '.js-delete_entry', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            var formData = new FormData($('#search')[0]);
            formData.append('page', page);
            
            delete_data({
                url : "{{ route('reports.receivedpayments.delete_entry') }}",
                reqData : {
                    _token  : '{{ csrf_token() }}',
                    id      : id,
                },
                fetch_data : {
                    func    : fetch_data,
                    params  : {
                        url         : "{{ route('reports.receivedpayments.list') }}",
                        formData    : formData,
                        target      : $('.js-content_holder')
                    }
                }
            });
        });
    </script>
@endsection