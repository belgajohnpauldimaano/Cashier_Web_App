<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $Students->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        {{--  <th>Grade</th>
                        <th>Section</th>  --}}
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
                        <tr>
                            <td>
                                {{ $student->student_info->last_name }}, {{ $student->student_info->first_name }} {{ $student->student_info->middle_name }}
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
                                        <li><a href="#" class="js-delete_student_info" data-id="{{ $student->id }}"><i class="fa fa-trash"></i>Deactivate</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>