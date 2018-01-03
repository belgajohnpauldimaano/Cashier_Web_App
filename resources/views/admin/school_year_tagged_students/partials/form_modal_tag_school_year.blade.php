
<div class="modal fade" id="form_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Student Information</h4>
            </div>
            
            <div class="overlay hidden"><i class="fa fa-refresh fa-spin"></i></div>
            <form id="form_student_tag_school_yer" class=" box-body">
                <div class="modal-body">
                        {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $Student->id }}">
                    <h3>
                        {{ $Student->last_name . ' ' . $Student->first_name . ', ' . $Student->middle_name }}
                    </h3>
                    {{--  <div class="form-group">
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
                                        <option value="{{ $data->id }}">{{ $data->section_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="help-block text-center" id="section-error"></div>
                        </div>
                    <select class="form-control"  name="school_year" id="school_year">
                        <option value="">Select School Year</option>
                        @if($SchoolYear)
                            @foreach ($SchoolYear as $data)
                                <option value="{{ $data->id }}">{{ $data->school_year }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="help-block text-center" id="school_year-error"></div>
                    {{--  @foreach ($Discount as $data)
                        <div class="checkbox">
                            <label><input type="checkbox" name="discounts[{{ $data->id }}]" > {{ $data->discount_title }} </label>  --}}
                            {{--  <input type="text" name="discounts_id[]" value="{{ $data->id }}">  --}}
                        {{--  </div>
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
                                        <option value="100">100%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>School Subsidy</td>
                                <td>
                                    <input type="number" step="any" name="school_subsidy" id="school_subsidy" class="form-control" />
                                </td>
                            </tr>
                            <tr>
                                <td>Employee Scholar</td>
                                <td>
                                    <select name="employee_scholar" id="employee_scholar" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100">100%</option>
                                        <option value="50">50%</option>
                                    </select>
                                </td>
                            </tr>
                            {{--  <tr>
                                <td title="Gov't Subsidy - SV & PEAC-FAPE">Gov't Subsidy</td>
                                <td>
                                    <input type="number" step="any" name="gov_subsidy" id="gov_subsidy" class="form-control" value="{{ ( $Student ? ($Student->discount_list ? $Student->discount_list->gov_subsidy  : '') : '') }}" />
                                </td>
                            </tr>  --}}
                            <tr>
                                <td>Academic Scholarship</td>
                                <td>
                                    <select name="acad_scholar" id="acad_scholar" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100" >100%</option>
                                        <option value="50" >50%</option>
                                        <option value="20">20%</option>
                                        <option value="10">10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Family Member</td>
                                <td>
                                    <select name="family_member" id="family_member" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="100">100%</option>
                                        <option value="50">50%</option>
                                        <option value="10">10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>NBI Alumni</td>
                                <td>
                                    <select name="nbi_alumni" id="nbi_alumni" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10">10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Cash Discount</td>
                                <td>
                                    <select name="cash_discount" id="cash_discount" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10">10%</option>
                                        <option value="5">5% - Semi Annually</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Choir Discount</td>
                                <td>
                                    <select name="cwoir_discount" id="cwoir_discount" class="form-control">
                                        <option value="">No Discount</option>
                                        <option value="10">10%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>St. Joseph Discount</td>
                                <td>
                                    <input type="number" step="any" name="st_jospeh_discount" id="st_jospeh_discount" class="form-control"/>
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