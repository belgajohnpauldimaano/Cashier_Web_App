
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Student Information</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_student" class=" box-body">
                <div class="modal-body">
                    {{ csrf_field() }}
                    @if($TuitionFee)
                        <input type="hidden" name="id" value="{{ $TuitionFee->id }}">
                    @endif
                    Grade
                    <h3 class="text-blue">{{ $TuitionFee->grade->grade }}</h3>
                    <hr>
                    <div class="form-group">
                        <label for="">Tuition Fee <span class="text-red">*</span></label>
                        <input type="text" class="form-control" name="tuition_fee" id="tuition_fee" value="{{ ($TuitionFee ? $TuitionFee->tuition_fee : '' ) }}">
                        <div class="help-block text-center" id="tuition_fee-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Miscalleneous Fee <span class="text-red">*</span></label>
                        <input type="text" class="form-control" name="misc_fee" id="misc_fee" value="{{ ($TuitionFee ? $TuitionFee->misc_fee : '' ) }}">
                        <div class="help-block text-center" id="misc_fee-error"></div>
                    </div>
                                        
                    @if ($AdditionalFee)
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
                                <input type="number" min="1" class="form-control" name="{{ $input_name }}" id="{{ $input_name }}" value="{{ $data->additional_amount }}">
                                <div class="help-block text-center" id="{{ $input_name }}-error"></div>
                            </div>
                        @endforeach
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