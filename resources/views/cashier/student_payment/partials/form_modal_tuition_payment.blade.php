
<div class="modal fade" id="form_tuition_fee_payment_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Payment</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_student_payment" class=" box-body">
                <div class="modal-body">
                    {{ csrf_field() }}
                     @if($student_id) 
                         <input type="hidden" name="id" value="{{ $student_id }}"> 
                     @endif 
                    <div class="form-group">
                        <label for="">Amount</label>
                        <h3 class="text-red">
                            &#8369; {{a_number_format($monthly)}}
                        </h3>
                    </div>
                    <div class="form-group">
                        <label for="">Payment <span class="text-red"></span></label>
                        <input type="number" class="form-control" name="payment" id="payment" min="1">
                        <div class="help-block text-center" id="payment-error"></div>
                    </div>
                    
                    <div class="form-group text-right">
                        <label for="">Outstanding Balance</label>
                        <h3 class="text-red">
                            &#8369; {{ a_number_format($outstanding_balance) }}
                        </h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-flat js-btn_save_payment">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>