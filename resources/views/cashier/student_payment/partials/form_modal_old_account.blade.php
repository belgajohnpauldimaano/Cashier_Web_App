
<div class="modal fade" id="form_old_account_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Old Account Payment</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_old_account" class=" box-body">
                <div class="modal-body">
                        {{ csrf_field() }}
                        @if($student_id) 
                            <input type="hidden" name="id" value="{{ $student_id }}"> 
                        @endif                             
                        <div class="form-group">
                            <label for="">Payment <span class="text-red"></span></label>
                            <input type="number" class="form-control" name="payment" id="payment" min="1">
                            <div class="help-block text-center" id="payment-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">OR Number <span class="text-red"></span></label>
                            <input type="text" class="form-control" name="or_number" id="or_number" min="1">
                            <div class="help-block text-center" id="or_number-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">School Year <span class="text-red"></span></label>
                            <select class="form-control" name="payment_type" id="payment_type">
                              <option value="4">2016-2017</option>
                              <option value="5">2015-2016</option>
                            </select>
                            <div class="help-block text-center" id="payment_type_received-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Date Received <span class="text-red"></span></label>
                            <input type="text" class="form-control" name="date_received" id="date_received">
                            <div class="help-block text-center" id="date_received-error"></div>
                        </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-flat js-btn_save_payment">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
