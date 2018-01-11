@extends('layouts.main')

@section('content_title')
    Student Discount List
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
                        
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">Discount Type</label>
                                <select name="filter_discount_type" id="filter_discount_type" class="form-control js-filter_discount_type">
                                    <option value="">All</option>
                                    @foreach ($discount_types as $key => $data)
                                        <option value="{{ $key }}">{{ $data->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-3 col-lg-3"> 
                            <div class="form-group">
                                <label for="">School Year</label>
                                <select name="filter_school_year" id="filter_school_year" class="form-control js-filter_school_year">
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
                                    {{--  <option value="" selected="selected">All</option>  --}}
                                    <option value="10" selected="selected">10</option>
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
                <div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $Students->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <div class="text-red">
                    Total Discounts 
                    <h3>{{ a_number_format($Students_sum->total_discount) }}</h3>
                </div>
                
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        {{--  <th>Discount Type</th>  --}}
                        @foreach ($discount_types as $key => $data)
                            <th>{{ $data->type }}</th>
                        @endforeach
                        <th>Discount Amount</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
                            <?php
                                $total_scholar              = $student->scholar * $student->tuition_fee;
                                $total_school_subsidy       = $student->school_subsidy;
                                $total_employee_scholar     = $student->employee_scholar * $student->tuition_fee;
                                $total_acad_scholar         = $student->acad_scholar * $student->tuition_fee;
                                $total_family_member        = $student->family_member * $student->tuition_fee;
                                $total_nbi_alumni           = $student->nbi_alumni * $student->tuition_fee;
                                $total_cash_discount        = $student->cash_discount * $student->tuition_fee;
                                $total_cwoir_discount       = $student->cwoir_discount * $student->tuition_fee;
                                $total_st_joseph_discount   = $student->st_joseph_discount;
                                $total_discounts            = $total_scholar +
                                $total_school_subsidy +
                                $total_employee_scholar +
                                $total_acad_scholar +
                                $total_family_member +
                                $total_nbi_alumni +
                                $total_cash_discount +
                                $total_cwoir_discount +
                                $total_st_joseph_discount;
                            ?>
                            @if($total_discounts > 0)
                                <tr>
                                    <td>
                                        {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                                    </td>
                                    <td>
                                        {{ $student->student_grade }}
                                    </td>
                                    <td>
                                        {{ $student->section_name }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_scholar) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->school_subsidy) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_employee_scholar) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_acad_scholar) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_family_member) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_nbi_alumni) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_cash_discount) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_cwoir_discount) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->st_joseph_discount) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->other_discount) }}
                                    </td>
                                    <td>
                                        {{ a_number_format($student->total_discount) }}
                                    </td>
                                </tr>
                            @endif
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
        });
        $('body').on('change', '#search', function (e) {
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            
            fetch_data({
                url         : "{{ route('admin.student_discount_list.list_data') }}",
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
                url         : "{{ route('admin.student_discount_list.list_data') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });

        
        
    </script>
@endsection