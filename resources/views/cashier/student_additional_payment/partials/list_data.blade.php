<form action="{{ route('cashier.student_additional_payment.student_additional_fee_report') }}" method="POST" id="form_student_additional_fee_report">
                    {{csrf_field()}}
                    <input type="hidden" name="pdf_search_filter" value="{{ $request['search_filter'] }}">
                    <input type="hidden" name="pdf_filter_grade" value="{{ $request['filter_grade'] }}"> 
                    <input type="hidden" name="pdf_filter_section" value="{{ $request['filter_section'] }}">   
                    <input type="hidden" name="pdf_filter_school_year" value="{{ $request['filter_school_year'] }}">   
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
                        <th>Books</th>
                        <th>Speech Lab</th>
                        <th>PE Uniform</th>
                        <th>School Uniform</th>
                        <th>Total Additional Fee</th>
                        <th>Total Payment</th>
                        <th>Outstanding Balance</th>
                        <th>Actions</th>
                    </tr>
                    <tbody>
                        @foreach ($Students as $student)
                            <?php
                                $total_additional_fee = 0 ;
                                $total_additional_payment = 0;
                                $outstanding_balance = 0;
                                $individual_fee = [];
                                if ($student->additional_fee)
                                {
                                    foreach ($student->additional_fee as $additional_fee)
                                    {
                                        $total_additional_fee += $additional_fee->additional_amount;
                                        $individual_fee[] = $additional_fee->additional_amount;
                                    }
                                }
                                
                                if ($student->additional_fee_payment)
                                {
                                    $total_additional_payment += $student->additional_fee_payment->books;
                                    $total_additional_payment += $student->additional_fee_payment->speech_lab;
                                    $total_additional_payment += $student->additional_fee_payment->pe_uniform;
                                    $total_additional_payment += $student->additional_fee_payment->school_uniform;
                                }

                                $outstanding_balance = $total_additional_fee - $total_additional_payment;
                                
                                if ($student->status == 0)
                                {
                                    $outstanding_balance = 0;
                                }
                            ?>
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
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[0] > $student->additional_fee_payment->books ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($student->additional_fee_payment->books) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[1] > $student->additional_fee_payment->speech_lab ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($student->additional_fee_payment->speech_lab) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[2] > $student->additional_fee_payment->pe_uniform ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($student->additional_fee_payment->pe_uniform) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($student->additional_fee_payment)
                                        <span class="{{ $individual_fee[3] > $student->additional_fee_payment->school_uniform ? 'text-red' : 'text-green'}}">
                                            {{ a_number_format($student->additional_fee_payment->school_uniform) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                   <strong class="text-red">{{ a_number_format($total_additional_fee) }}</strong>
                                </td>
                                <td>
                                   <strong class="text-green">{{ a_number_format($total_additional_payment) }}</strong>
                                </td>     
                                <td>
                                   <strong class="text-blue">{{ a_number_format($outstanding_balance) }}</strong>
                                </td>
                                <td>
                                    @if ($student->status == 0)
                                        <span class="text-red">Inactive</span>
                                    @else
                                        @if ($outstanding_balance > 0) 
                                            <button class="btn btn-primary btn-flat btn-sm js-pay" data-id="{{ $student->student_info->id }}">Pay</button>    
                                        @else
                                            <button class="btn btn-primary btn-flat btn-sm js-pay" data-id="{{ $student->student_info->id }}">View (Paid)</button>    
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>