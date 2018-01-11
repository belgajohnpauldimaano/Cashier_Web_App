<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
                <div class="pull-right">
                    {{ $Students->links('admin.manage_student.partials.student_data_list_pagination') }}
                </div>
                <div class="text-red">
                    Total Discounts 
                    <h3>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        @if ($selected_discount_types == 0)
                            {{ a_number_format($Students_sum->total_discount) }}
                        @elseif ($selected_discount_types == 1)
                            {{ a_number_format($Students_sum->total_scholar) }}
                        @elseif ($selected_discount_types == 2)
                            {{ a_number_format($Students_sum->total_school_subsidy) }}
                        @elseif ($selected_discount_types == 3)
                            {{ a_number_format($Students_sum->total_employee_scholar) }}
                        @elseif ($selected_discount_types == 4)
                            {{ a_number_format($Students_sum->total_acad_scholar) }}
                        @elseif ($selected_discount_types == 5)
                            {{ a_number_format($Students_sum->total_family_member) }}
                        @elseif ($selected_discount_types == 6)
                            {{ a_number_format($Students_sum->total_nbi_alumni) }}
                        @elseif ($selected_discount_types == 7)
                            {{ a_number_format($Students_sum->total_cash_discount) }}
                        @elseif ($selected_discount_types == 8)
                            {{ a_number_format($Students_sum->total_cwoir_discount) }}
                        @elseif ($selected_discount_types == 9)
                            {{ a_number_format($Students_sum->total_st_joseph_discount) }}
                        @elseif ($selected_discount_types == 10)
                            {{ a_number_format($Students_sum->total_other_discount) }}
                        @endif

                    </h3>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Grade</th>
                        <th>Section</th>
                        @if ($selected_discount_types == 0)
                            @foreach ($discount_types as $key => $data)
                                <th>{{ $data->type }}</th>
                            @endforeach
                        @else
                            <th>Discount Type</th>
                        @endif
                        <th>Discount Amount</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
                            <tr>
                                <td>
                                    {{ $student->id }} {{ $student->school_year_id }} {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                                </td>
                                <td>
                                    {{ $student->student_grade }}
                                </td>
                                <td>
                                    {{ $student->section_name }}
                                </td>
                            @if($selected_discount_types == 0)
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
                            @elseif(($selected_discount_types > 0))
                                <td>    
                                    <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                        {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['type'] }}
                                    </span>
                                    &nbsp;
                                    @if ($selected_discount_types == 3)
                                        <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                            {{ $student->employee_scholar * 100 }}%
                                        </span>
                                        @elseif ($selected_discount_types == 4)
                                        <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                            {{ $student->acad_scholar * 100 }}%
                                        </span>
                                        @elseif ($selected_discount_types == 5)
                                        <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                            {{ $student->family_member * 100 }}% 
                                        </span>
                                        @elseif ($selected_discount_types == 7)
                                        <span class="label {{ \App\Discount::DISCOUNT_TYPES[$selected_discount_types]['css_style'] }} active">
                                            {{ $student->cash_discount * 100 }}%
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($selected_discount_types == 1)
                                        {{ a_number_format($student->total_scholar) }}
                                    @elseif ($selected_discount_types == 2)
                                        {{ a_number_format($student->school_subsidy) }}
                                    @elseif ($selected_discount_types == 3)
                                        {{ a_number_format($student->total_employee_scholar) }}
                                    @elseif ($selected_discount_types == 4)
                                        {{ a_number_format($student->total_acad_scholar) }}
                                    @elseif ($selected_discount_types == 5)
                                        {{ a_number_format($student->total_family_member) }}
                                    @elseif ($selected_discount_types == 6)
                                        {{ a_number_format($student->total_nbi_alumni) }}
                                    @elseif ($selected_discount_types == 7)
                                        {{ a_number_format($student->total_cash_discount) }}
                                    @elseif ($selected_discount_types == 8)
                                        {{ a_number_format($student->total_cwoir_discount) }}
                                    @elseif ($selected_discount_types == 9)
                                        {{ a_number_format($student->st_joseph_discount) }}
                                    @elseif ($selected_discount_types == 10)
                                        {{ a_number_format($student->other_discount) }}
                                    @endif
                                </td>
                            @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>