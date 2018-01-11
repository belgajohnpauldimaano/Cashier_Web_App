
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
                    @if($outstanding_balance > 0)
                        {{ csrf_field() }}
                        <input type="hidden" name="sy_id" value="{{ $filter_school_year }}">
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
                            <label for="">Payment Type <span class="text-red"></span></label>
                            <select name="payment_type" id="payment_type" class="form-control">
                                <option value="1">Tuition Payment</option>
                                <option value="6">Government Subsidy</option>
                            </select>
                            <div class="help-block text-center" id="payment_type-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Payment <span class="text-red"></span></label>
                            <input type="number" step="any" class="form-control" name="payment" id="payment" min="1">
                            <div class="help-block text-center" id="payment-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">OR Number <span class="text-red"></span></label>
                            <input type="text" class="form-control" name="or_number" id="or_number" min="1">
                            <div class="help-block text-center" id="or_number-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="">Date Received <span class="text-red"></span></label>
                            <input type="text" class="form-control" name="date_received" id="date_received">
                            <div class="help-block text-center" id="date_received-error"></div>
                        </div>
                    @endif
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Tuition</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($tuition) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Misc. Fee</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($misc_fee) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Tuition</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($total_tuition) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Payment Upon Enrollement</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($upon_enrollment) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Total tuition payment</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($total_tuition_payment) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($net_tuition_no_discount) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>
                                <h5 class="text-red">
                                    &#8369; {{ a_number_format($discount) }}
                                </h5>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group text-right">
                        <label for="">Outstanding Balance</label>
                        <h3 class="text-red">
                            &#8369; {{ a_number_format($outstanding_balance) }}
                        </h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    @if($outstanding_balance > 0)
                        <button type="submit" class="btn btn-primary btn-flat js-btn_save_payment">Save</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
