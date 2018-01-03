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
                                    <option value="">All</option>
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
                    <h3>{{ a_number_format($total) }}</h3>
                </div>
                
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Discount Type</th>
                        <th>Discount Amount</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
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
                                    
                                    <span class="label {{ \App\Discount::DISCOUNT_TYPES[1]['css_style'] }}">
                                        {{ \App\Discount::DISCOUNT_TYPES[1]['type'] }}
                                    </span>
                                    
                                </td>
                                <td>
                                    {{ a_number_format($student->tuition_fee * $student->scholar) }}
                                </td>
                                {{--  <td>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="fa fa-bars"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="js-edit_student_info" data-id="{{ $student->id }}"><i class="fa fa-pencil"></i>Edit</a></li>
                                            <li><a href="#" class="js-delete_student_info" data-id="{{ $student->id }}"><i class="fa fa-trash"></i>Deactivate</a></li>
                                        </ul>
                                    </div>
                                </td>  --}}
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