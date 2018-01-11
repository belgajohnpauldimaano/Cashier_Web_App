
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Student Information</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            @if ($StudentPaymentLog)
                <form id="form_entry_correction" class=" box-body">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <input type="hidden" name="sy_id" value="{{ $StudentPaymentLog->school_year_id }}">
                        <input type="hidden" name="id" value="{{ $StudentPaymentLog->id }}">
                        <div class="form-group">
                            <label for="">Name <span class="text-red"></span></label>
                            {{--  <label for="">{{ $StudentPaymentLog->student->last_name . ' ' . $StudentPaymentLog->student->first_name . ', ' . $StudentPaymentLog->student->middle_name }}</label>  --}}
                            <div>
                                <h3>
                                    {{ $StudentPaymentLog->student->last_name . ' ' . $StudentPaymentLog->student->first_name . ', ' . $StudentPaymentLog->student->middle_name }}
                                </h3>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="">Payment Type <span class="text-red"></span></label>
                            {{--  <label for="">{{ $StudentPaymentLog->student->last_name . ' ' . $StudentPaymentLog->student->first_name . ', ' . $StudentPaymentLog->student->middle_name }}</label>  --}}
                            @if ($StudentPaymentLog->payment_type != 1)
                                @if ($StudentPaymentLog->student)
                                    @if ($StudentPaymentLog->student->grade)
                                        @if ($StudentPaymentLog->student->additional_fee)
                                            <select name="payment_type" id="payment_type" class="form-control">
                                                <option value="">Select type of payment</option>
                                                @foreach($StudentPaymentLog->student->additional_fee as $key => $data)
                                                    <option value="{{ $key + 2 }}" {{($key+2 == $StudentPaymentLog->payment_type ? 'selected' : '')}} {{( $data->additional_amount == 0 ? 'disabled' : '')}}>{{ $data->additional_title . " (". $data->additional_amount .")" }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block text-center" id="payment_type-error"></div>
                                        @endif
                                    @endif
                                @endif
                                {{--  <select name="payment_type" id="payment_type" class="form-control">
                                    <option value="">Select type of payment</option>
                                    @foreach($payment_type as $key => $data)
                                        <option value="{{ $key + 2 }}" {{($key+2 == $StudentPaymentLog->payment_type ? 'selected' : '')}}>{{ $data }}</option>
                                    @endforeach
                                </select>  --}}
                            @else
                                <input type="hidden" name="payment_type" value="1">
                                <div>Tuition Fee</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="">Amount <span class="text-red">*</span></label>
                            <input type="number" step="any"  class="form-control" name="amount" id="amount" value="{{ ($StudentPaymentLog->payment) }}">
                            <div class="help-block text-center" id="amount-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">OR Number <span class="text-red">*</span></label>
                            <input type="number" step="any"  class="form-control" name="or_number" id="or_number" value="{{ ($StudentPaymentLog->or_number) }}">
                            <div class="help-block text-center" id="or_number-error"></div>
                        </div>
                        
                        {{--  <div class="form-group">
                            <label for="">First Name <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{ ($Student ? $Student->first_name : '' ) }}">
                            <div class="help-block text-center" id="first_name-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Middle Name <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="middle_name" id="middle_name" value="{{ ($Student ? $Student->middle_name : '' ) }}">
                            <div class="help-block text-center" id="middle_name-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Last Name <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{ ($Student ? $Student->last_name : '' ) }}">
                            <div class="help-block text-center" id="last_name-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">Grade <span class="text-red">*</span></label>
                            <select class="form-control"  name="grade" id="grade">
                                <option value="">Select Grade</option>
                                @if($Grade)
                                    @foreach ($Grade as $data)
                                        <option value="{{ $data->id }}" {{ ($Student ? ( $Student->grade ? ($Student->grade->grade == $data->grade ? 'selected' : '') : '') : '' ) }} >{{ $data->grade }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="help-block text-center" id="grade-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">Section <span class="text-red">*</span></label>
                            <select class="form-control"  name="section" id="section">
                                <option value="">Select Section</option>
                                @if($Section)
                                    @foreach ($Section as $data)
                                        <option value="{{ $data->id }}" {{ ($Student ? ( $Student->section ? ($Student->section->section_name == $data->section_name ? 'selected' : '') : '') : '' ) }} >{{ $data->section_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="help-block text-center" id="section-error"></div>
                        </div>
                    </div>  --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-flat">Save</button>
                    </div>
                </form>
            @else
                <div class="text-red">No data found</div>
            @endif
        </div>
    </div>
</div>