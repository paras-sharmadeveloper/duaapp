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
                    <th> Actions </th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $list)
                @php

                  $visitor =   getVisitor($list->SCode);

                @endphp
                <tr>
                    <td>{{$list->created_at->format('d-m-Y h:i:s A') }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->venueAddress->city : '' }} </td>
                    <td> {{ ($visitor) ? $visitor->venueSloting->type : ''}} </td>
                    <td> {{ ($visitor) ? $visitor->booking_number : ''}} </td>
                    <td> Yes </td>
                    <td><a href="{{ ($visitor) ? route('booking.status', $visitor->booking_uniqueid):"#" }}"
                        target="_blank">{{ ($visitor)  ? route('booking.status', $visitor->booking_uniqueid) : '' }} </a>
                    </td>
                    <td>
                        <button id="out_of_seq_{{ $list->id }}" data-targetid="out_of_seq_{{ $list->id }}" data-id="{{ $list->id }}" class="btn btn-danger out_of_seq"> out of Sequence</button>
                        <button id="undo_of_seq_{{ $list->id }}" data-targetid="undo_of_seq_{{ $list->id }}" data-id="{{ $list->id }}" class="btn btn-dark undo_of_seq"> undo </button>

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
</script>

@endsection
