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
                                    
                                    <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                        {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['type'] }}
                                    </span>
                                    
                                </td>
                                <td>
                                    @if ($selected_discount_types == 1)
                                        {{ a_number_format($student->tuition_fee * $student->scholar) }}
                                    @elseif ($selected_discount_types == 2)
                                        {{ a_number_format($student->school_subsidy) }}
                                    @elseif ($selected_discount_types == 3)
                                        {{ a_number_format($student->tuition_fee * $student->employee_scholar) }}
                                    @elseif ($selected_discount_types == 4)
                                        {{ a_number_format($student->tuition_fee * $student->acad_scholar) }}
                                    @elseif ($selected_discount_types == 5)
                                        {{ a_number_format($student->tuition_fee * $student->family_member) }}
                                    @elseif ($selected_discount_types == 6)
                                        {{ a_number_format($student->tuition_fee * $student->nbi_alumni) }}
                                    @elseif ($selected_discount_types == 7)
                                        {{ a_number_format($student->tuition_fee * $student->cash_discount) }}
                                    @elseif ($selected_discount_types == 8)
                                        {{ a_number_format($student->tuition_fee * $student->cwoir_discount) }}
                                    @elseif ($selected_discount_types == 9)
                                        {{ a_number_format($student->cwoir_discount) }}
                                    @endif
                                    {{$student->tuition_fee}}
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