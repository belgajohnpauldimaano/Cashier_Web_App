<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $SchoolYear->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>School Year </th>
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($SchoolYear as $data)
                            <tr>
                                <td>
                                    {{ $data->school_year }}
                                </td>
                                <td>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="fa fa-bars"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="js-edit_sy_info" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i>Edit</a></li>
                                            {{--  <li><a href="#" class="js-delete_discount_info" data-id="{{ $data->id }}"><i class="fa fa-trash"></i>Delete</a></li>  --}}
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>