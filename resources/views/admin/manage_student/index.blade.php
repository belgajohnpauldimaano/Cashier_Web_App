@extends('layouts.main')

@section('content_title', 'Manage Students Information')

@section ('content')
    <div class=" pull-right">
        <button class="btn btn-primary btn-flat js-create_new_student"><i class="fa fa-plus"></i> Add new student information</button>
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
                                    <option value="1" selected="selected">1</option>
                                    <option value="2">2</option>
                                    <option value="10">10</option>
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
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
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
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="fa fa-bars"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="js-edit_student_info" data-id="{{ $student->id }}"><i class="fa fa-pencil"></i>Edit</a></li>
                                            <li><a href="#" class="js-delete_student_info" data-id="{{ $student->id }}"><i class="fa fa-trash"></i>Delete</a></li>
                                        </ul>
                                    </div>
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
        $('body').on('click', '.js-create_new_student', function () {
            show_form_modal({
                url         : "{{ route('admin.manage_student.form_modal') }}",
                reqData     : {
                                _token  : '{{ csrf_token() }}'
                            },
                target      : $('.js-form_modal_holder'),
                func        : {}
            });

        });

        $('body').on('click', '.js-edit_student_info', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (id == '')
            {
                return;
            }
            show_form_modal({
                url         : "{{ route('admin.manage_student.form_modal') }}",
                reqData     : {
                                _token  : '{{ csrf_token() }}',
                                id      : id
                            },
                target      : $('.js-form_modal_holder'),
                func        : {}
            });

        });
        
        $('body').on('click', '.js-delete_student_info', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (id == '')
            {
                return;
            }
            
            var formData = new FormData($('#search')[0]);
            formData.append('page', 1);
            delete_data({
                url         : "{{ route('admin.manage_student.delete') }}",
                reqData     : {
                                _token  : '{{ csrf_token() }}',
                                id      : id
                            },
                target      : $('.js-form_modal_holder'),
                fetch_data : {
                    func    : fetch_data,
                    params  : {
                        url         : "{{ route('admin.manage_student.list') }}",
                        formData    : formData,
                        target      : $('.js-content_holder')
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
                        url         : "{{ route('admin.manage_student.list') }}",
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
                url         : "{{ route('admin.manage_student.list') }}",
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
                url         : "{{ route('admin.manage_student.list') }}",
                formData    : formData,
                target      : $('.js-content_holder')
            });
        });

        /*function show_form_modal (data)
        {
            alert('a');
            $.ajax({
                url : data.url,
                type : 'POST',
                data : data.reqData,
                success : function (resData) {
                    target.html(resData);
                    target.children('.modal').modal({backdrop : 'static'});
                }
            });
        }*/
    </script>
@endsection