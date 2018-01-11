
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">School Year</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_school_year" class=" box-body">
                <div class="modal-body">
                        {{ csrf_field() }}
                        @if($SchoolYear)
                            <input type="hidden" name="id" value="{{ $SchoolYear->id }}">
                        @endif
                    <div class="form-group">
                        <label for="">School Year <span class="text-red">*</span></label>
                        <input type="text" class="form-control" name="school_year" id="school_year" value="{{ ($SchoolYear ? $SchoolYear->school_year : '' ) }}">
                        <div class="help-block text-center" id="school_year-error"></div>
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