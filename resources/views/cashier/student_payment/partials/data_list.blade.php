<form action="{{ route('cashier.student_payment.student_summary_balance') }}" method="POST" id="form_student_summary_balance">
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
                        <th>Remaining Balance</th>
                        <th>Additional Fee Balance</th>
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
                                    @if ($student->tuition->count())
                                         @if ($student->tuition[0]->total_remaining > 0) 
                                            <strong class="text-red">&#8369; {{ a_number_format($student->tuition[0]->total_remaining) }}</strong>
                                        @else
                                            <strong class="text-green"><i class="fa fa-check"></i> Paid</strong>
                                        @endif 
                                    @endif
                                </td>
                                <td>
                                    @if ($student->tuition->count())
                                        @if ($student->tuition[0]->total_remaining > 0)
                                            <strong class="text-red">&#8369; {{ a_number_format($student->tuition[0]->additional_fee) }}</strong>
                                        @endif
                                    @endif
                                </td>
                                
                                <td>
                                    @if ($student->tuition->count())
                                         @if ($student->tuition[0]->total_remaining > 0) 
                                            <button class="btn btn-flat btn-default btn-sm js-pay_tuition" data-id="{{ $student->id }}">
                                                Pay Tuition
                                            </button>
                                        @else
                                            <strong class="text-green"><i class="fa fa-check"></i> Paid</strong>
                                        @endif
                                        
                                         @if ($student->tuition[0]->total_remaining > 0) 
                                            <button class="btn btn-flat btn-default btn-sm js-pay_addtional" data-id="{{ $student->id }}">
                                                Pay Other Fees
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>