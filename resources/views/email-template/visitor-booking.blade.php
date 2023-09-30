<!DOCTYPE html>
<html>
<head>
    <title>Visitor Booking Confirmation</title>
</head>
<body>
    <p>Dear {{ $bookingData['name'] }},</p>

    <p>Thank you for booking your visit with us. Below are the details of your booking:</p>

    <ul>
        <li><strong>Name:</strong> {{ $bookingData['name'] }}</li>
        <li><strong>Email:</strong> {{ $bookingData['email'] }}</li>
        <li><strong>Phone:</strong> {{ $bookingData['phone'] }}</li>
        <li><strong>Purpose of Visiting:</strong> {{ $bookingData['purpose'] }}</li>
        <li><strong>Date and Time:</strong> {{ $bookingData['visit_datetime'] }}</li>
    </ul>

    <p>We look forward to welcoming you on your visit.</p>

    <p>Best regards,</p>
    <p>Kahay Faqeer</p>
</body>
</html>