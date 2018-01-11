
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
                        <input type="hidden" name="sy_id" value="{{ $sy_id }}">
                        <input type="hidden" name="student_id" value="{{ $Student->student_id }}">
                    {{--  <div class="form-group">
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
                    </div>  --}}
                    
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

                    {{--  @foreach ($Discount as $data)
                        <div class="checkbox">
                            <label><input type="checkbox" name="discounts[{{ $data->id }}]" > {{ $data->discount_title }} </label>
                             <input type="text" name="discounts_id[]" value="{{ $data->id }}">
                        </div>
                    @endforeach  --}}
                     <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="2">Discounts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Scholar</td>
                                <td>
                                    <select name="scholar" id="scholar" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->scholar * 100 == 100 ? 'selected' : '') : '') : '') }}>100%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>School Subsidy</td>
                                <td>
                                    <input type="number" name="school_subsidy" id="school_subsidy" class="form-control" value="{{ ( $Student ? ($Student->discount_list ? $Student->discount_list->school_subsidy  : '') : '') }}" />
                                </td>
                            </tr>
                            <tr>
                                <td>Employee Scholar</td>
                                <td>
                                    <select name="employee_scholar" id="employee_scholar" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->employee_scholar * 100 == 100 ? 'selected' : '') : '') : '') }}>100%</option>
                                        <option value="50" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->employee_scholar * 100 == 50 ? 'selected' : '') : '') : '') }}>50%</option>
                                    </select>
                                </td>
                            </tr>
                            {{--  <tr>
                                <td title="Gov't Subsidy - SV & PEAC-FAPE">Gov't Subsidy</td>
                                <td>
                                    <input type="number" name="gov_subsidy" id="gov_subsidy" class="form-control" value="{{ ( $Student ? ($Student->discount_list ? $Student->discount_list->gov_subsidy  : '') : '') }}" />
                                </td>
                            </tr>  --}}
                            <tr>
                                <td>Academic Scholarship</td>
                                <td>
                                    <select name="acad_scholar" id="acad_scholar" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->acad_scholar * 100 == 100 ? 'selected' : '') : '') : '') }}>100%</option>
                                        <option value="50" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->acad_scholar * 100 == 50 ? 'selected' : '') : '') : '') }}>50%</option>
                                        <option value="20" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->acad_scholar * 100 == 20 ? 'selected' : '') : '') : '') }}>20%</option>
                                        <option value="10" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->acad_scholar * 100 == 10 ? 'selected' : '') : '') : '') }}>10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Family Member</td>
                                <td>
                                    <select name="family_member" id="family_member" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->family_member * 100 == 100 ? 'selected' : '') : '') : '') }}>100%</option>
                                        <option value="50" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->family_member * 100 == 50 ? 'selected' : '') : '') : '') }}>50%</option>
                                        <option value="10" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->family_member * 100 == 10 ? 'selected' : '') : '') : '') }}>10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>NBI Alumni</td>
                                <td>
                                    <select name="nbi_alumni" id="nbi_alumni" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->nbi_alumni * 100 == 10 ? 'selected' : '') : '') : '') }}>10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Cash Discount</td>
                                <td>
                                    <select name="cash_discount" id="cash_discount" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->cash_discount * 100 == 10 ? 'selected' : '') : '') : '') }}>10%</option>
                                        <option value="5" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->cash_discount * 100 == 5 ? 'selected' : '') : '') : '') }}>5% - Semi Annually</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Cwoir Discount</td>
                                <td>
                                    <select name="cwoir_discount" id="cwoir_discount" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10" {{ ( $Student ? ($Student->discount_list ? ($Student->discount_list->cwoir_discount * 100 == 10 ? 'selected' : '') : '') : '') }}>10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>St. Joseph Discount</td>
                                <td>
                                    <input type="number" name="st_jospeh_discount" id="st_jospeh_discount" class="form-control" value="{{ ( $Student ? ($Student->discount_list ? $Student->discount_list->st_joseph_discount  : '') : '') }}" />
                                </td>
                            </tr>
                            <tr>
                                <td>Other Discount</td>
                                <td>
                                    <input type="number" step="any" name="other_discount" id="other_discount" class="form-control" value="{{ ( $Student ? ($Student->discount_list ? $Student->discount_list->other_discount  : '') : '') }}" />
                                </td>
                            </tr>
                            <tr>
                                <td>Book Remarks</td>
                                <td>
                                    <input type="number" step="any" name="book_remarks" id="book_remarks" class="form-control" value="{{ ( $AdditionalFeePayment ? $AdditionalFeePayment->book_remarks : '0') }}" />
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-flat">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>