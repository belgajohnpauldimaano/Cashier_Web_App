
<div class="modal fade" id="form_additional_payment_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Payment</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            @if ($outstanding_balance > 0)
                <form id="form_student_additional_payment" class=" box-body">
                    <div class="modal-body">
                        @if($total_additional_payment < $total_additional_fee)
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $Student->id }}"> 
                            <div class="form-group">
                                <label for="">Balance</label>
                                <h3 class="text-red">
                                    &#8369; {{a_number_format($outstanding_balance)}}
                                </h3>
                            </div>
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
                                <label for="">Fee type <span class="text-red"></span></label>
                                <select name="fee_type" id="fee_type" class="form-control">
                                    <option value="">Please Select</option>
                                    @if ($Student->additional_fee)
                                        @foreach ($Student->additional_fee as $key => $additional_fee)
                                            <option value="{{ $key }}" {{ $individual_payment[$key] >= $individual_fee[$key] ? 'disabled' : '' }} >{{ $additional_fee->additional_title }} {{ $individual_payment[$key] >= $individual_fee[$key] ? ' - Paid' : '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="help-block text-center" id="fee_type-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="">Date Received <span class="text-red"></span></label>
                                <input type="text" class="form-control" name="date_received" id="date_received">
                                <div class="help-block text-center" id="date_received-error"></div>
                            </div>
                        @endif
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th colspan="3" class="text-center">Breakdown</th>
                            </tr>
                            <tr>
                                <th class="text-center">Fee Name</th>
                                <th class="text-center">Fee</th>
                                <th class="text-center">Payment</th>
                                <th class="text-center">Balance</th>
                            </tr>
                            @if ($Student->additional_fee)
                                @foreach ($Student->additional_fee as $key => $additional_fee)
                                    <tr>
                                        <th class="text-center">{{ $additional_fee->additional_title }}</th>
                                        <td>
                                            <h5 class="text-red text-right">
                                                {{ a_number_format($individual_fee[$key]) }}
                                            </h5>
                                        </td>
                                        <td>
                                            <h5 class="text-red text-right">
                                                {{ a_number_format($individual_payment[$key]) }}
                                            </h5>
                                        </td>
                                        <td>
                                            <h5 class="text-red text-right">
                                                {{ a_number_format($individual_fee[$key] - $individual_payment[$key]) }}
                                            </h5>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <th class="text-center text-red">Total</th>
                                <td>
                                    <div class="text-red text-right">
                                        {{ a_number_format($total_additional_fee) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-red text-right">
                                        {{ a_number_format($total_additional_payment) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-red text-right">
                                        {{ a_number_format($total_additional_fee - $total_additional_payment) }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                        @if ($outstanding_balance > 0)
                            <button type="submit" class="btn btn-primary btn-flat js-btn_save_payment">Save</button>
                        @endif
                    </div>
                </form>
            @else
                @if ($student_tuition->count() > 0)
                    <div class="modal-body">
                        <h3 class="text-center text-red">Already paid.</h3>
                    </div>
                @else 
                    <div class="modal-body">
                        <h3 class="text-center text-red">No additional payment to be paid.</h3>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>