
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Fees Information</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_manage_fees" class=" box-body">
                    
                <input type="hidden" name="sy_id" value="{{ $sy_id }}">
                <input type="hidden" name="grade_id" value="{{ $Grade->id }}">
                <div class="modal-body">
                    {{ csrf_field() }}
                    @if($TuitionFee)
                        <input type="hidden" name="id" value="{{ $TuitionFee->id }}">
                    @endif
                    <h3 class="text-blue">{{ $Grade->grade }}</h3>
                    
                    {{--  <div class="form-group">
                        <label for="">Grade <span class="text-red">*</span></label>
                        <select name="grade" id="" class="form-control">
                            <option value="">Select grade</option>
                            @foreach ($Grade as $data)
                                <option value="{{ $data->id }}" {{ ($grade_id == $data->id ? 'selected' : '' ) }} >{{$data->grade}}</option>
                            @endforeach
                        </select>
                        <div class="help-block text-center" id="grade-error"></div>
                    </div>  --}}
                    
                    <hr>
                    <div class="form-group">
                        <label for="">Tuition Fee <span class="text-red">*</span></label>
                        <input type="number" min="0" step="any" class="form-control" name="tuition_fee" id="tuition_fee" value="{{ ($TuitionFee ? $TuitionFee->tuition_fee : '' ) }}">
                        <div class="help-block text-center" id="tuition_fee-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Miscalleneous Fee <span class="text-red">*</span></label>
                        <input type="number" min="0" step="any" class="form-control" name="misc_fee" id="misc_fee" value="{{ ($TuitionFee ? $TuitionFee->misc_fee : '' ) }}">
                        <div class="help-block text-center" id="misc_fee-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Payment Upon Enrollemnt <span class="text-red">*</span></label>
                        <input type="number" min="0" step="any" class="form-control" name="upon_enrollment" id="upon_enrollment" value="{{ ($TuitionFee ? $TuitionFee->upon_enrollment : '' ) }}">
                        <div class="help-block text-center" id="upon_enrollment-error"></div>
                    </div>
                    
                    @if (count($AdditionalFee) > 0)
                        @foreach ($AdditionalFee as $data)
                            <?php
                                $input_name = '';
                                if ($data->additional_title == 'Books (Annually)')
                                { 
                                    $input_name = 'book';
                                }
                                else if ($data->additional_title == 'Speech Lab (Annually)')
                                {
                                    $input_name = 'speech_lab';
                                }
                                else if ($data->additional_title == 'P.E Uniform/Set')
                                {
                                    $input_name = 'pe_uniform';
                                }
                                else if ($data->additional_title == 'School Uniform/Set')
                                {
                                    $input_name = 'school_uniform';
                                }
                            ?>
                            <div class="form-group">
                                <input type="hidden" name="{{ $input_name }}_id" value="{{ $data->id }}">
                                <label for="">{{ $data->additional_title }} {{ $input_name }} <span class="text-red">*</span></label>
                                <input type="number" min="0" step="any" class="form-control" name="{{ $input_name }}" id="{{ $input_name }}" value="{{ $data->additional_amount }}">
                                <div class="help-block text-center" id="{{ $input_name }}-error"></div>
                            </div>
                        @endforeach
                    @else
                        <div class="form-group">
                            <label for="">Books (Annually) <span class="text-red">*</span></label>
                            <input type="number" min="0" step="any" class="form-control" name="book" id="book" value="0">
                            <div class="help-block text-center" id="book-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">Speech Lab (Annually) <span class="text-red">*</span></label>
                            <input type="number" min="0" step="any" class="form-control" name="speech_lab" id="speech_lab" value="0">
                            <div class="help-block text-center" id="speech_lab-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">P.E Uniform/Set <span class="text-red">*</span></label>
                            <input type="number" min="0" step="any" class="form-control" name="pe_uniform" id="pe_uniform" value="0">
                            <div class="help-block text-center" id="pe_uniform-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="">School Uniform/Set <span class="text-red">*</span></label>
                            <input type="number" min="0" step="any" class="form-control" name="school_uniform" id="school_uniform" value="0">
                            <div class="help-block text-center" id="school_uniform-error"></div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-flat">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>