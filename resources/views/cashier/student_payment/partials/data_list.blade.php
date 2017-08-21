<form action="{{ route('cashier.student_payment.student_summary_balance') }}" method="POST" id="form_student_summary_balance">
    {{csrf_field()}}
    <input type="hidden" name="pdf_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" name="pdf_filter_grade" value="{{ $request['filter_grade'] }}"> 
    <input type="hidden" name="pdf_filter_section" value="{{ $request['filter_section'] }}">   
</form>


<form action="{{ route('cashier.student_payment.student_summary_simple_balance') }}" method="POST" id="form_student_summary_simple_balance">
    {{csrf_field()}}
    <input type="hidden" name="pdf_search_filter" value="{{ $request['search_filter'] }}">
    <input type="hidden" name="pdf_filter_grade" value="{{ $request['filter_grade'] }}"> 
    <input type="hidden" name="pdf_filter_section" value="{{ $request['filter_section'] }}">   
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
                        {{--  <th>Additional Fees</th> 
                        <th>Additional Fees Paid</th>   \  --}}
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
                                //$additional_fee_total = 0;
                                //$additiona_fee_total_payment = $student->tuition[0]->additional_fee_total;
                                
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
                                {{--  <td>
                                    @if ($student->additional_fee)
                                        @foreach($student->additional_fee as $additional)   --}}
                                            <?php  
                                                //$additional_fee_total += $additional->additional_amount; 
                                            ?>
                                         {{--  @endforeach
                                        {{ a_number_format($additional_fee_total - $additiona_fee_total_payment) }}
                                    @endif
                                </td>  
                                 <td>
                                         {{ a_number_format( $student->tuition[0]->additional_fee_total) }} 
                                </td>   --}}
                                <td>
                                    @if ($outstanding_balance > 0) 
                                        <button class="btn btn-primary btn-flat btn-sm js-pay_tuition" data-id="{{ $student->id }}">Pay</button>    
                                    @else
                                        <button class="btn btn-primary btn-flat btn-sm js-pay_tuition" data-id="{{ $student->id }}">View (Paid)</button>    
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>