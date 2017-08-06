
<div class="modal fade" id="form_additional_payment_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Payment</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            @if ($additional_amount > 0)
                <form id="form_student_additional_payment" class=" box-body">
                    <div class="modal-body">
                        {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $student_id }}"> 
                        <div class="form-group">
                            <label for="">Amount</label>
                            <h3 class="text-red">
                                &#8369; {{a_number_format($additional_amount)}}
                            </h3>
                        </div>
                        <div class="form-group">
                            <label for="">Payment <span class="text-red"></span></label>
                            <input type="number" class="form-control" name="payment" id="payment" min="1">
                            <div class="help-block text-center" id="payment-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-flat js-btn_save_payment">Save</button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <h3 class="text-center text-red">Already paid.</h3>
                </div>
            @endif
        </div>
    </div>
</div>