
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Discount Information</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_discount" class=" box-body">
                <div class="modal-body">
                        {{ csrf_field() }}
                        @if($Discount)
                            <input type="hidden" name="id" value="{{ $Discount->id }}">
                        @endif
                    <div class="form-group">
                        <label for="">Discount Title <span class="text-red">*</span></label>
                        <input type="text" class="form-control" name="discount_title" id="discount_title" value="{{ ($Discount ? $Discount->discount_title : '' ) }}">
                        <div class="help-block text-center" id="discount_title-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="">Discount Amount <span class="text-red">*</span></label>
                        <input type="text" class="form-control" name="discount_amount" id="discount_amount" value="{{ ($Discount ? $Discount->discount_amount : '' ) }}">
                        <div class="help-block text-center" id="discount_amount-error"></div>
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