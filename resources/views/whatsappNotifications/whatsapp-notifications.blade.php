@extends('layouts.app')

@section('content')
    <style>
        .tr-overlay {
            width: 100%;
            background-color: #333;
            overflow: hidden;
            margin: 30px 0px;
            text-align: center;
        }

        .wrapper {
            height: auto;
        }

        .tr-overlay .container {
            color: white;
        }

        .multiselect input[type="checkbox"] {
            /* display: none; */
        }

        .multiselect label {
            display: block;
            text-indent: -1.2em;
            padding: 0.2em 0 0.2em 1.2em;
        }

        .multiselect-on {
            color: #ffffff;
            background-color: #0E8EFF;
        }

        .multiselect-blurred {
            background: lightgray;
        }

        .table-responsive.mb-3 {
    max-height: 600px;
}
    </style>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="action-top float-end mb-3">
                <a class="btn btn-outline-primary" href="{{ route('venues.index') }}">
                    <i class="bi bi-skip-backward-circle me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <!-- CSV Upload Form -->
        <div class="card">
            <div class="card-header">
                <h4>Import CSV File</h4>
                <p>
                    There is 2 columns need to be in sheet
                    <br/>
                    1. Country Code (Without +)
                    <br>
                    2. Phone
                </p>
            </div>
            <div class="card-body">
                <form action="{{ route('whatsapp.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload CSV File:</label>
                        <input type="file" name="file" id="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import CSV</button>
                </form>
            </div>
        </div>

        <!-- Message Sending Form -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between">
                <h4>Send WhatsApp Message </h4>
                <p>Total Count : {{ count($recipients) }}</p>
                <a href="{{route('whatsapp.form.logs')}}"  target="_blank" class="btn btn-warning float-right "> Logs</a>
                <button type="button" class="btn btn-danger" id="deleteAllBtn">Delete All Selected</button>

            </div>
            <div class="card-body">
                <form action="{{ route('whatsapp.send') }}"  method="POST" id="send-whatsapp">
                    @csrf
                    <!-- Table to Display Recipients with Checkboxes -->
                    <div class="table-responsive mb-3">
                        <table id="recipientTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Country Code</th>
                                    <th>Phone Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recipients as $recipient)
                                    <tr>
                                        <td><input type="checkbox" class="recipient-checkbox" value="{{ $recipient->id }}"></td>
                                        <td>{{ $recipient->country_code }}</td>
                                        <td>{{ $recipient->phone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label for="message" class="form-label">Campaign Name:</label>
                        <input name="campaign_name" id="campaign_name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message:</label>
                        <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                    </div>

                    <!-- Hidden Field for Selected Recipients -->
                    <input type="hidden" name="selected_recipients" id="selected_recipients">

                    <button type="submit" class="btn btn-success">Send Message</button>



                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <!-- jQuery and DataTable -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#recipientTable').DataTable({
                "pageLength": 500,
                "lengthMenu": [500, 1000, 2000, 5000]  // Optional: Customize available page lengths
            });

            // Handle "Select All" checkbox
            $('#selectAll').click(function() {
                $('.recipient-checkbox').prop('checked', this.checked);
            });

            // Handle form submission
            $('#send-whatsapp').submit(function(e) {
                var selectedRecipients = [];
                $('.recipient-checkbox:checked').each(function() {
                    selectedRecipients.push($(this).val());
                });

                // Set the hidden input with the selected recipient IDs
                $('#selected_recipients').val(selectedRecipients.join(','));

                // If no recipients are selected, prevent form submission
                if (selectedRecipients.length == 0) {
                    alert('Please select at least one recipient.');
                    e.preventDefault();
                }
            });
        });

        $('#deleteAllBtn').click(function() {
        // Collect selected recipient IDs
        var selectedRecipients = [];
        $('.recipient-checkbox:checked').each(function() {
            selectedRecipients.push($(this).val());
        });

        // If no recipients are selected, show an alert
        if (selectedRecipients.length === 0) {
            alert('Please select at least one recipient to delete.');
            return;
        }

        // Confirm before deleting
        if (confirm('Are you sure you want to delete all selected recipients?')) {
            // Send an AJAX request to the server to delete the selected recipients
            $.ajax({
                url: '{{ route("whatsapp.delete") }}', // Define the route for deletion
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    recipients: selectedRecipients
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the deleted rows from the table
                        $('.recipient-checkbox:checked').each(function() {
                            $(this).closest('tr').remove();
                        });
                        alert('Selected recipients deleted successfully.');
                    } else {
                        alert('An error occurred while deleting the recipients.');
                    }
                },
                error: function() {
                    alert('An error occurred while processing your request.');
                }
            });
        }
    });
    </script>
@endsection
