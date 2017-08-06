<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $Discount->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Discount Title</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($Discount as $data)
                            <tr>
                                <td>
                                    {{ $data->discount_title }}
                                </td>
                                <td>
                                    &#8369; {{ a_number_format($data->discount_amount) }}
                                </td>
                                <td>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="fa fa-bars"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="js-edit_discount_info" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i>Edit</a></li>
                                            <li><a href="#" class="js-delete_discount_info" data-id="{{ $data->id }}"><i class="fa fa-trash"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>