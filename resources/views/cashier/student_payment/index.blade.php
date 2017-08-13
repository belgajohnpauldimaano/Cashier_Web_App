@extends('layouts.main')

@section('content_title', 'Student Payment')

@section ('content')
    <div class=" pull-right">
        <button class="btn btn-danger btn-flat btn-sm js-student_summary_balance_export_pdf"><i class="fa fa-file-pdf-o"></i> Export pdf</button>
        <button class="btn btn-danger btn-flat btn-sm js-student_summary_simple_balance_export_pdf"><i class="fa fa-file-pdf-o"></i> Export summary pdf</button>
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
                <form action="{{ route('cashier.student_payment.student_summary_balance') }}" method="POST" id="form_student_summary_balance">
                    {{csrf_field()}}
                    <input type="hidden" name="pdf_search_filter">
                    <input type="hidden" name="pdf_filter_grade"> 
                    <input type="hidden" name="pdf_filter_section">   
                </form>
                <form action="{{ route('cashier.student_payment.student_summary_simple_balance') }}" method="POST" id="form_student_summary_simple_balance">
                    {{csrf_field()}}
                    <input type="hidden" name="pdf_search_filter">
                    <input type="hidden" name="pdf_filter_grade"> 
                    <input type="hidden" name="pdf_filter_section">   
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
                        <th>Tuition</th>
                        <th>Discount</th>
                        <th>Net Tuition</th>
                        <th>Paid Tuition</th>
                        <th>Outstanding Balance</th>
                        <th>Additional Fees</th> 
                        <th>Additional Fees Paid</th>  
                         {{--  <th>Remaining Balance</th>   --}}
                         {{--  <th>Additional Fee Balance</th>   --}}
                        <th>Actions</th>
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
                                $additional_fee_total = 0;
                                
                                $additiona_fee_total_payment = $student->tuition[0]->additional_fee_total;

                                $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;
                                if ($outstanding_balance <= 0)
                                {
                                    $outstanding_balance = 0;
                                }
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
                                <td>
                                    @if ($student->grade_tuition)
                                        {{ a_number_format($tuition_fee) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($student->discount_list)
                                        {{ a_number_format($discount) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($student->discount_list)
                                        {{ a_number_format($net_tuition) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($student->tuition)
                                        {{ a_number_format( $student->tuition[0]->total_payment) }}
                                    @endif
                                </td>
                                   
                                <td>
                                        {{ a_number_format( $outstanding_balance) }}
                                </td>
                                <td>
                                    @if ($student->additional_fee)
                                        @foreach($student->additional_fee as $additional) 
                                            <?php  
                                                $additional_fee_total += $additional->additional_amount; 
                                            ?>
                                         @endforeach
                                        {{ a_number_format($additional_fee_total - $additiona_fee_total_payment) }}
                                    @endif
                                </td>  
                                 <td>
                                         {{ a_number_format( $student->tuition[0]->additional_fee_total) }} 
                                </td> 
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                           <i class="fa fa-bars"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if ($outstanding_balance > 0) 
                                                <li><a href="#" class="js-pay_tuition" data-id="{{ $student->id }}">Pay Tuition</a></li>
                                            @endif
                                            @if ($additional_fee_total > 0) 
                                                <li><a href="#" class="js-pay_addtional" data-id="{{ $student->id }}">Pay Other Fees</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                        {{--  @if ($outstanding_balance > 0) 
                                            <button class="btn btn-flat btn-default btn-sm js-pay_tuition" data-id="{{ $student->id }}">
                                                Pay Tuition
                                            </button>
                                        @endif
                                
                                        @if ($additional_fee_total > 0) 
                                            <button class="btn btn-flat btn-default btn-sm js-pay_addtional" data-id="{{ $student->id }}">
                                                Pay Other Fees
                                            </button>
                                        @endif  --}}
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
    <script>
        $('body').on('click', '.js-pay_tuition', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            
            if (id == '')
            {
                return;
            }

            show_form_modal_pay_tuition({
                url     : "{{ route('cashier.student_payment.show_form_modal_pay_tuition') }}",
                reqData : {
                            _token  : '{{ csrf_token() }}',
                            id      : id
                },
                target  : $('.js-form_modal_holder')
            });
        });

        $('body').on('click', '.js-pay_addtional', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            
            if (id == '')
            {
                return;
            }
            show_form_modal_pay_tuition({
                url     : "{{ route('cashier.student_payment.show_form_modal_additional_payment') }}",
                reqData : {
                            _token  : '{{ csrf_token() }}',
                            id      : id
                },
                target  : $('.js-form_modal_holder')
            });
        });
        
        

        $('body').on('submit', '#form_student_additional_payment', function (e) {
            e.preventDefault();
            
            var formData = new FormData($(this)[0]);
            var fetchFormData = new FormData($('#search')[0]);
            $.ajax({
                url : "{{ route('cashier.student_payment.additional_fee_payment_process') }}",
                type : 'POST',
                data : formData,
                dataType : 'JSON',
                processData : false,
                contentType : false,
                success     : function (resData) {
                    if (resData.code == 1)
                    {
                        show_toast_message({heading : 'Error', icon : 'error', text : resData.general_message, hideAfter : 4000 });
                    }
                    else if (resData.code == 2)
                    {

                    }
                    else 
                    {
                        $('.js-form_modal_holder').children('.modal').modal('hide');
                        show_toast_message({heading : 'Success', icon : 'success', text : resData.general_message, hideAfter : 4000 });
                        fetch_data({
                            url         : "{{ route('cashier.student_payment.fetch_data') }}",
                            formData    : fetchFormData,
                            target      : $('.js-content_holder')
                        });
                    }
                }
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
                        url         : "{{ route('cashier.student_payment.fetch_data') }}",
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
                url         : "{{ route('cashier.student_payment.fetch_data') }}",
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
                url         : "{{ route('cashier.student_payment.fetch_data') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });

        $('body').on('click', '.js-student_summary_balance_export_pdf', function (e) {
            e.preventDefault();
            $('#form_student_summary_balance').submit();
        });
        $('body').on('submit', '#form_student_summary_balance', function (e) {
            $(this).attr('target', '_blank');
        });
        
        
        $('body').on('click', '.js-student_summary_simple_balance_export_pdf', function (e) {
            e.preventDefault();
            $('#form_student_summary_simple_balance').submit();
        });
        $('body').on('submit', '#form_student_summary_simple_balance', function (e) {
            $(this).attr('target', '_blank');
        });

        function show_form_modal_pay_tuition (data)
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

        $('body').on('submit', '#form_student_payment', function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var fetchFormData = new FormData($('#search')[0]);
            $.ajax({
                url : "{{ route('cashier.student_payment.tuition_payment_process') }}",
                type : 'POST',
                data : formData,
                dataType : 'JSON',
                processData : false,
                contentType : false,
                success     : function (resData) {
                    if (resData.code == 1)
                    {
                        show_toast_message({heading : 'Error', icon : 'error', text : resData.general_message, hideAfter : 4000 });
                    }
                    else if (resData.code == 2)
                    {

                    }
                    else 
                    {
                        $('.js-form_modal_holder').children('.modal').modal('hide');
                        show_toast_message({heading : 'Success', icon : 'success', text : resData.general_message, hideAfter : 4000 });
                        fetch_data({
                            url         : "{{ route('cashier.student_payment.fetch_data') }}",
                            formData    : fetchFormData,
                            target      : $('.js-content_holder')
                        });
                    }
                }
            });

        });
    </script>
@endsection