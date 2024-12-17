<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DUA / DUM Tokens Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .title {
            text-align: center;
        }
        .title img {
            height: 70px;
            width: 70px;
        }
    </style>
</head>
<body>

    <div class="title">
        <img src="{{ public_path('assets/theme/img/logo.png') }}" alt="Logo">
        <h4>DUA / DUM TOKENS SUMMARY - {{ date('l d-M-Y') }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>Row Label</th>
                <th>Count of Token</th>
                <th>Whatsapp Msg</th>
                <th>Check-in</th>
                <th>Print</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Website</td>
                <td>{{ $websiteTotal }}</td>
                <td>{{ $websiteTotalPercentage ?? 0 }}</td>
                <td>{{ $websiteCheckIn ?? 0 }}</td>
                <td>{{ $websitePrintToken ?? 0 }}</td>
            </tr>
            <tr>
                <td>Website (Dua)</td>
                <td>{{ $websiteDuaTotal }}</td>
                <td>{{ $websiteTotalPercentageDua ?? 0 }}</td>
                <td>{{ $websiteCheckInDua ?? 0 }}</td>
                <td>{{ $websitePrintTokenDua ?? 0 }}</td>
            </tr>
            <!-- Add other rows similarly -->
        </tbody>
    </table>

</body>
</html>
