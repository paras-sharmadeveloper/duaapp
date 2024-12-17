<!-- index.blade.php -->

@extends('layouts.app')

@section('content')

<div class="card">
 @include('alerts')

<style>
  .c2 {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
}
input#datetime {
    width: 50%;
}
form {
    width: 100%;
    display: flex;
    gap: 20px;
    padding: 10px;
}

td.action-dv {
    display: flex;
}


td.action-dv button {
    font-size: 15px;
}


td.action-dv {
    gap: 10px;
}
    </style>
    <div class="card-body">
        <div class="row d-flex">
            <div class="c1">
                <h5 class="card-title">Door Logs</h5>
            </div>
            <div class="c2">
                <form method="get">
                    <input type="date"
                    value="{{ request()->get('DateFilter') ?? '' }}"
                    name="DateFilter"
                    class="form-control"
                    id="datetime">
                    <button type="submit" class="btn btn-info">Filter</button>
                    <button type="button" class="btn btn-dark" onclick="resetForm()">Reset</button>
                </form>

                <a href="{{ route('generate.pdf') }}?date={{request()->get('DateFilter') ?? ''}}" class="btn btn-dark">Generate Pdf </a>
            </div>
        </div>

        <table class="table-with-buttons table table-responsive cell-border" id="LogsTable">
            <thead>
                <tr>
                    <th>Door Access Timestamp</th>
                    <th> Dua Ghar </th>
                    <th> Dua Type </th>
                    <th> Token Number </th>
                    <th> Out of Sequence Access </th>
                    <th> Token URL </th>
                    <th>Admin Action Tab</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $list)
                @php

                  $visitor =   getVisitor($list->SCode);

                @endphp
                 <tr class="{{ $list->out_of_seq == 1 ? 'row-red' : '' }}">
                    <td>{{$list->created_at->format('d-m-Y h:i:s A') }} </td>
                    <td> {{ ($visitor) ? $visitor->slot->venueAddress->city : '' }} </td>
                    <td> {{ ($visitor) ? $visitor->slot->type : ''}} </td>
                    <td> {{ ($visitor) ? $visitor->booking_number : ''}} </td>
                    <td> {{ $list->out_of_seq == 1 ? 'Yes' : '' }} </td>
                    <td><a href="{{ ($visitor) ? route('booking.status', $visitor->booking_uniqueid):"#" }}"
                        target="_blank">{{ ($visitor)  ? route('booking.status', $visitor->booking_uniqueid) : '' }} </a>
                    </td>
                    <td class="action-dv">
                        <button id="out_of_seq_{{ $list->id }}" data-targetid="out_of_seq_{{ $list->id }}" data-id="{{ $list->id }}" class="btn btn-danger out_of_seq"> Out of sequence</button>
                        <button id="undo_of_seq_{{ $list->id }}" data-targetid="undo_of_seq_{{ $list->id }}" data-id="{{ $list->id }}" class="btn btn-dark undo_of_seq" style="display:none"> Undo </button>

                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


</div>


@endsection
@section('page-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script>
    $("#generatePdfBtn").click(function() {

$("#spinner-div").show();


var is = downloadPdf()


})

function downloadPdf() {

const element = document.getElementById('LogsTable');

const formattedDate = new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
const options = {
    margin: 10,
    format: 'a4',
    filename: "{{ date('dMY') }}" + '-doorlogreport.pdf',
    // image: {
    //     type: 'jpeg',
    //     quality: 1.0
    // },
    html2canvas: {
        scale: 1
    },
    jsPDF: {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
    }
};
$(".myheading").addClass('d-none')
$("#spinner-div").hide();

return html2pdf(element, options);
}

function resetForm() {
        // Reset form fields
        document.querySelector('input[name="DateFilter"]').value = '';

        // Redirect to remove query parameters
        window.location.href = window.location.pathname;
    }

    $(document).ready(function () {
    // Out of Sequence Button Clicked
    $('.out_of_seq').on('click', function () {
        var id = $(this).data('id');
        var targetButton = $(this);
        var targetUndoButton = $('#undo_of_seq_' + id);

        // Send AJAX Request to mark as Out of Sequence
        $.ajax({
            url: '/admin/update-out-of-seq/' + id,
            method: 'POST',
            data: {
                type: 'out_of_seq',
                out_of_seq: 1,  // Set the status to '1' (Out of Sequence)
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function (response) {
                // Hide the "Out of Sequence" button and show the "Undo" button
                targetButton.hide();
                targetUndoButton.show();
            },
            error: function (response) {
                alert('Error updating status');
            }
        });
    });

    // Undo Out of Sequence Button Clicked
    $('.undo_of_seq').on('click', function () {
        var id = $(this).data('id');
        var targetButton = $('#out_of_seq_' + id);
        var targetUndoButton = $(this);

        // Send AJAX Request to undo Out of Sequence
        $.ajax({
            url: '/admin/update-out-of-seq/' + id,
            method: 'POST',
            data: {
                type: 'undo_of_seq',
                out_of_seq: 0,  // Set the status to '0' (Undo Out of Sequence)
                _token: '{{ csrf_token() }}' // CSRF token for security
            },
            success: function (response) {
                // Hide the "Undo" button and show the "Out of Sequence" button
                targetButton.show();
                targetUndoButton.hide();
            },
            error: function (response) {
                alert('Error updating status');
            }
        });
    });
});

</script>

@endsection
