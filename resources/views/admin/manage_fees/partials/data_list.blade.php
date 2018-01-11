<div class="overlay hidden"><i class="fa fa-spin fa-refresh"></i></div>
<table class="table table-bordered">
    <tr>
        <th>Grade</th>
        <th>Tuition Fee</th>
        <th>Miscallenous Fee</th>
        <th>Other Fees</th>
        <th>Actions</th>
    </tr>
    <tbody>
        @foreach ($Grade_Tuition as $data)
        <tr>
            <td>
                {{ $data->grade }}
            </td>
        <td>
            @if (count($data->tuition_fee) > 0)
                &#8369; {{ a_number_format($data->tuition_fee[0]->tuition_fee) }}
            @endif
        </td>
        <td>
            @if (count($data->tuition_fee) > 0)
                &#8369; {{ a_number_format($data->tuition_fee[0]->misc_fee) }}
            @endif
        </td>
        <td>
            <?php $additional_fee = 0; ?>
            @if (count($data->tuition_fee) > 0)
                @foreach ($data->additional_fee as $add_fee)
                    <?php $additional_fee += $add_fee->additional_amount; ?>
                @endforeach
            @endif
            &#8369; {{ a_number_format($additional_fee) }}
        </td>
        <td>
            <div class="input-group-btn">
                <button type="button" class="btn btn-default btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="fa fa-bars"></span>
                </button>
                <ul class="dropdown-menu">
                     <li><a href="#" class="js-edit_fees" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i>Edit</a></li> 
                    {{--  <li><a href="#" class="js-delete_student_info" data-id="{{ $student->id }}"><i class="fa fa-trash"></i>Delete</a></li>  --}}
                </ul>
            </div>
        </td>
            </tr>
        @endforeach
    </tbody>
</table>