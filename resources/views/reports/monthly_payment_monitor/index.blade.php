@extends('layouts.main')

@section('content_title', 'Payment Monthly Monitor')

@section('styles')
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
                                <label for="">Filter Month From</label>
                                <select name="filter_month" id="filter_month" class="form-control js-search_filters">
                                    <option value="" selected>All</option>
                                    <option value="1">June</option>
                                    <option value="2">July</option>
                                    <option value="3">August</option>
                                    <option value="4">September</option>
                                    <option value="5">October</option>
                                    <option value="6">November</option>
                                    <option value="7">December</option>
                                    <option value="8">January</option>
                                    <option value="9">February</option>
                                    <option value="10">March</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Filter Month To</label>
                                <select name="filter_month_to" id="filter_month_to" class="form-control js-search_filters">
                                    <option value="" selected>All</option>
                                    <option value="1">June</option>
                                    <option value="2">July</option>
                                    <option value="3">August</option>
                                    <option value="4">September</option>
                                    <option value="5">October</option>
                                    <option value="6">November</option>
                                    <option value="7">December</option>
                                    <option value="8">January</option>
                                    <option value="9">February</option>
                                    <option value="10">March</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-flat btn-primary btn-sm js-btn_search_filters" type="button"><i class="fa fa-search"></i> Search</button>
                    <button class="btn btn-flat btn-danger btn-sm js-btn_export_pdf" type="button"><i class="fa fa-file-pdf-o"></i> export to pdf</button>
                </form>
            </div> 

            <div class="js-content_holder box box-solid">
                <form action="{{ route('reports.monthly_payment_monitor.export_pdf_monthly_payment_monitor') }}" id="form_monthly_payment_monitor_report" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="report_search_filter" name="report_search_filter" >
                    <input type="hidden" id="report_filter_grade"  name="report_filter_grade">
                    <input type="hidden" id="report_filter_section" name="report_filter_section" >
                    <input type="hidden" id="report_filter_month" name="report_filter_month">
                    <input type="hidden" id="report_filter_month_to" name="report_filter_month_to">
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
                        @foreach($months_array as $mon)
                            <th>{{ $mon }}</th>
                        @endforeach
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
                                $net_tuition = ($tuition - $discount) +  $student->grade_tuition[0]->misc_fee;
                                $upon_enrollment = $student->grade_tuition[0]->upon_enrollment;
                                $outstanding_balance = $net_tuition - $student->tuition[0]->total_payment;
                                $tmp_tuition = (($tuition - $discount) + $student->grade_tuition[0]->misc_fee) - $student->tuition[0]->down_payment; // - ($student->tuition[0]->down_payment == $student->grade_tuition[0]->misc_fee + (($tuition - $discount) >= 2000 ? 2000 : ($tuition - $discount)) ? (($tuition - $discount) >= 2000 ? 2000 : ($tuition - $discount)) : $student->tuition[0]->down_payment == 0 ? (($tuition - $discount) >= 2000 ? 2000 : ($tuition - $discount)) : 0);
                                
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
                                <td> 
                                    <span class="text-red"> {{ a_number_format($student->tuition[0]->down_payment) }} / {{ $upon_enrollment }}</span>
                                </td>
                                    @for($i=0;$i<10;$i++)
                                        <td>
                                            @if ($tmp_tuition > $tmp_monthly_amount)
                                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                                / {{a_number_format($tmp_monthly_amount)}}
                                                <?php
                                                    $tmp_tuition = $tmp_tuition - $tmp_monthly_amount;
                                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                                    $total_monthly_amount += $tmp_monthly_amount;
                                                ?>
                                            @else
                                                {{ a_number_format($student->tuition[0][$month_field[$i]]) }}
                                                / {{a_number_format($tmp_tuition)}}
                                                <?php
                                                    $total_monthly_payment += $student->tuition[0][$month_field[$i]];
                                                    $total_monthly_amount += $tmp_tuition;
                                                    $tmp_tuition = $tmp_tuition - $tmp_tuition;
                                                ?>
                                            @endif
                                        </td>
                                    @endfor
                                <td>
                                    <span class="text-red">
                                        {{ a_number_format(($total_monthly_amount - $total_monthly_payment) + $left_unpaid_down) }}
                                    </span>
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
        $('body').on('submit', '#search', function (e) {
            e.preventDefault();
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            fetch_data({
                url         : "{{ route('reports.monthly_payment_monitor.list') }}",
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

        $('body').on('click' , '.paginate_item', function (e) {
            e.preventDefault();
            var page = $(this).data('page');
            var formData = new FormData($('#search')[0]);
            formData.append('page', page);
            fetch_data({
                url         : "{{ route('reports.monthly_payment_monitor.list') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });
        $('body').on('submit', '#form_monthly_payment_monitor_report', function () {
            $(this).attr('target', '_blank');
        });
        $('body').on('click', '.js-btn_export_pdf', function (e) {
            e.preventDefault();
            $('#form_monthly_payment_monitor_report').submit();
        })
    </script>
@endsection