@extends('layouts.main')

@section('content_title', 'Student Payment')

@section('styles')
  <link rel="stylesheet" href="{{ asset('cms/plugins/datepicker/datepicker3.css') }}">
@endsection

@section ('content')
    <div class=" pull-right">
        <button class="btn btn-danger btn-flat btn-sm js-student_additional_fee_pdf"><i class="fa fa-file-pdf-o"></i> Export pdf</button>
    </div>
    
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
                                    @if($Section)
                                        @foreach ($Section as $data)
                                            <option value="{{ $data->id }}">{{ $data->section_name }}</option>
                                        @endforeach
                                    @endif
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

                        <div class="col-sm-12 col-md-3 col-lg-3 pull-right"> 
                            <div class="form-group">
                                <label for="">Show Entries</label>
                                <select name="show_count" id="show_count" class="form-control js-search_filters">
                                    <option value="">All</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="js-content_holder box box-solid">
                <form action="{{ route('cashier.student_additional_payment.student_additional_fee_report') }}" method="POST" id="form_student_additional_fee_report">
                    {{csrf_field()}}
                    <input type="hidden" name="pdf_search_filter">
                    <input type="hidden" name="pdf_filter_grade" value="1"> 
                    <input type="hidden" name="pdf_filter_section">   
                    <input type="hidden" name="pdf_filter_school_year" value="{{ ($SchoolYear ? $SchoolYear[0]->id : '') }}"> 
                </form>
                <div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $Students->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Books</th>
                        <th>Speech Lab</th>
                        <th>Total Books Payment</th>
                        <th>Total Speech Lab Payment</th>
                        <th>Books Balance</th>
						<th>Speech Lab Balance</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
                            <?php
                                $total_additional_fee = 0 ;
                                $total_additional_payment = 0;
                                $outstanding_balance = 0;
								$payment_books = 0;
								$payment_sl = 0;
                                $individual_fee = [];
                                if ($student->additional_fee)
                                {
                                    foreach ($student->additional_fee as $additional_fee)
                                    {
                                        $total_additional_fee += $additional_fee->additional_amount;
                                        $individual_fee[] = $additional_fee->additional_amount;
                                    }
                                }
                                
                                if ($student->additional_fee_payment)
                                {
                                    $total_additional_payment += $student->additional_fee_payment->books;
                                    $total_additional_payment += $student->additional_fee_payment->speech_lab;
                                    $total_additional_payment += $student->additional_fee_payment->pe_uniform;
                                    $total_additional_payment += $student->additional_fee_payment->school_uniform;
									$payment_books = $student->additional_fee_payment->books;
									$payment_sl = $student->additional_fee_payment->speech_lab;
                                }

                                $outstanding_balance = $total_additional_fee - $total_additional_payment;
                                
                                if ($student->status == 0)
                                {
                                    $outstanding_balance = 0;
                                }
                            ?>
                            <tr>
                                <td>
                                    {{ $student->student_info->last_name }}, {{ $student->student_info->first_name }} {{ $student->student_info->middle_name }}
                                    {{--  {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}  --}}
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
                                <td>
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[0] > $student->additional_fee_payment->books ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($individual_fee[0]) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[1] > $student->additional_fee_payment->speech_lab ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($individual_fee[1]) }}
                                        </span>
                                    @endif
                                </td>                       
                                <td>
								@if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[0] > $student->additional_fee_payment->books ? 'text-red' : 'text-green'}}">
                                   {{ a_number_format($payment_books) }}
								   </span>
                                    @endif
                                </td>
                                <td>
								@if ($student->additional_fee_payment)
                                <span class="{{ $individual_fee[1] > $student->additional_fee_payment->speech_lab ? 'text-red' : 'text-green'}}">
                                   {{ a_number_format($payment_sl) }}
                                </td>  
								</span>
                                 @endif								
                                <td>
								@if ($student->additional_fee_payment)
                                <span class="{{ $individual_fee[0] > $student->additional_fee_payment->books ? 'text-red' : 'text-green'}}">
                                   {{ a_number_format($individual_fee[0] - $payment_books) }}
                                </td>
								</span>
                                 @endif	
								 <td>
								 @if ($student->additional_fee_payment)
                                <span class="{{ $individual_fee[1] > $student->additional_fee_payment->speech_lab ? 'text-red' : 'text-green'}}">
                                   {{ a_number_format($individual_fee[1] - $payment_sl) }}
                                </td>
                                <td>
								</span>
                                 @endif	
                                    @if ($student->status == 0)
                                        <span class="text-red">Inactive</span>
                                    @else
                                        @if ($outstanding_balance > 0) 
                                            <button class="btn btn-primary btn-flat btn-sm js-pay" data-id="{{ $student->student_info->id }}">Pay</button>    
                                        @else
                                            <button class="btn btn-primary btn-flat btn-sm js-pay" data-id="{{ $student->student_info->id }}">View (Paid)</button>    
                                        @endif
                                    @endif
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

    <script src="{{ asset('cms/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script>
        $('body').on('click', '.js-pay', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            
            if (id == '')
            {
                return;
            }

            show_form_modal_pay({
                url     : "{{ route('cashier.student_additional_payment.form_modal_additional_payment') }}",
                reqData : {
                            _token  : '{{ csrf_token() }}',
                            id      : id,
                            filter_school_year : $('#filter_school_year').val()
                },
                target  : $('.js-form_modal_holder')
            });
        });

        $('body').on('submit', '#form_student', function (e) {
            e.preventDefault();
            
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            
            save_data({
                url : "{{ route('admin.manage_student.save_data') }}",
                form : $(this),
                fetch_data : {
                    func    : fetch_data,
                    params  : {
                        url         : "{{ route('cashier.student_additional_payment.list_data') }}",
                        formData    : formData,
                        target      : $('.js-content_holder')
                    }
                }
            });
        });
        $('body').on('submit', '#search', function (e) {
            e.preventDefault();
        });
        $('body').on('change', '.js-search_filters', function (e) {
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            fetch_data({
                url         : "{{ route('cashier.student_additional_payment.list_data') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });

        $('body').on('click' , '.paginate_item', function (e) {
            e.preventDefault();
            var page = $(this).data('page');
            var formData = new FormData($('#search')[0]);
            formData.append('page', page);
            fetch_data({
                url         : "{{ route('cashier.student_additional_payment.list_data') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });

        $('body').on('click', '.js-student_additional_fee_pdf', function (e) {
            e.preventDefault();
            $('#form_student_additional_fee_report').submit();
        });
        $('body').on('submit', '#form_student_additional_fee_report', function (e) {
            $(this).attr('target', '_blank');
        });
        
        $('body').on('submit', '#form_student_additional_payment', function (e) {
            e.preventDefault();
            
            var formData = new FormData($(this)[0]);
            var fetchFormData = new FormData($('#search')[0]);
            var formEl = $(this);

            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary btn-flat";
            alertify.defaults.theme.cancel = "btn btn-danger btn-flat";
            alertify.confirm('Confirmation', 'Are you sure you want to proceed?', 
            function(){ 
                $(this).parents('.box').children('.overlay').removeClass('hidden');
                $.ajax({
                    url : "{{ route('cashier.student_additional_payment.process_payment') }}",
                    type : 'POST',
                    data : formData,
                    dataType : 'JSON',
                    processData : false,
                    contentType : false,
                    success     : function (resData) {
                        formEl.parents('.box').children('.overlay').addClass('hidden');
                        $(this).children('.form-group').children('.help-block').children('code').fadeOut('slow', function () {
                        
                        });
                        if (resData.code == 1)
                        {
                            show_toast_message({heading : 'Error', icon : 'error', text : resData.general_message, hideAfter : 4000 });
                            var text = [];
                            for(var err in resData.messages)
                            {
                                $('#' + err +'-error').html('<code style="display:none">'+ resData.messages[err] +'</code>');
                                $('#' + err +'-error').children('code').fadeIn('slow');
                                text.push(resData.messages[err]);
                            }
                            show_toast_message({heading : 'Error', icon : 'error', text : text, hideAfter : 10000 });
                        }
                        else if (resData.code == 2)
                        {

                        }
                        else 
                        {
                            $('.js-form_modal_holder').children('.modal').modal('hide');
                            show_toast_message({heading : 'Success', icon : 'success', text : resData.general_message, hideAfter : 4000 });
                            fetch_data({
                                url         : "{{ route('cashier.student_additional_payment.list_data') }}",
                                formData    : fetchFormData,
                                target      : $('.js-content_holder')
                            });
                        }
                    }
                });
             }, function(){  

            });
        });

        function show_form_modal_pay (data)
        {
            $.ajax({
                url : data.url,
                type : 'POST',
                data : data.reqData,
                success : function (resData) {
                    console.log(resData);
                    data.target.html(resData);
                    data.target.children('.modal').modal({backdrop : 'static'});
                }
            });
        }

        $('body').on('shown.bs.modal', '#form_additional_payment_modal', function () {
            $('#date_received').datepicker({
                Default: new Date(),
                format: 'mm-dd-yyyy'
            }).datepicker("setDate", new Date());;
        });
    </script>
@endsection