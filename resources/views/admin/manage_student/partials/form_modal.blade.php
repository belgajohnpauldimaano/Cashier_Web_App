
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
                        @if($Student)
                            <input type="hidden" name="id" value="{{ $Student->id }}">
                        @endif
                    <div class="form-group">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-flat">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>