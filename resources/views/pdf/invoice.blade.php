<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>List Wise - Invoice #{{ '' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            padding: 0;
            margin: 0;
        }

        body {
            color: #25282f;
            font-family: "Helvetica Neue", serif;
            font-size: 12px;
            padding: 0 !important;
            margin: 0 !important;
            position: relative;
            background-image: url({{ public_path('/images/Invoice.jpg') }});
            background-position: top left;
            background-repeat: no-repeat;
            background-size: cover;
        }

        h1, h2, h3, h4, h5, h6, p {
            padding: 0;
            margin: 0;
        }

        table {
            border-collapse: collapse;
            border-color: #007894;
            border-style: solid;
            width: 100% !important;
        }

        .header {
            font-weight: bold;
        }

        .subheader {
            color: grey;
        }

        #user {
            position: absolute;
            top: 3.5cm;
            left: 4cm;
        }

        #invoice {
            position: absolute;
            top: 3.47cm;
            right: 1cm;
            text-align: right;
        }

        #product {
            position: absolute;
            top: 9cm;
            left: 0;
            right: 0;
            padding: 0 1cm 0 3.4cm;
        }

        #main-table thead tr th {
            color: #fff;
            background-color: #007894;
            padding: 12px 0;
            text-align: center;
        }

        #main-table td {
            font-size: 1.1rem;
            font-weight: bold;
            padding: 12px 0;
            text-align: center;
            border-bottom-width: 1px;
        }

        #main-table tr td:first-child,
        #main-table tr th:first-child {
            border-left-width: 1px;
        }

        #main-table tr td:last-child,
        #main-table tr th:last-child {
            border-right-width: 1px;
        }

        #info {
            position: absolute;
            top: 22.52cm;
            right: 12.2cm;
            text-align: right;
            font-size: 1.2rem !important;
            line-height: 26px;
        }

        #total {
            position: absolute;
            top: 20.45cm;
            right: 1.2cm;
            text-align: right;
            line-height: 32px;
        }
    </style>
</head>
<body>

<div id="user">
    <h2 class="header"><b>{{ ($user['first_name'] ?? '').' '.($user['last_name'] ?? '') }}</b></h2>
    <h3 class="subheader">{{ $user['company'] ?? '' }}</h3>
    <h3 class="subheader">Phone: {{ $user['phone_number'] ?? '' }}</h3>
    <h3 class="subheader">Email: {{ $user['email'] ?? '' }}</h3>
</div>

<div id="invoice">
    <h2 class="header"><b>{{ $invoice['sale_id'] ?? '--' }}</b></h2>
    <h2 class="header" style="font-size:1.1rem;line-height:1.75rem">
        <b>{{  Carbon\Carbon::now()->toDateTimeString() }}</b></h2>
</div>

<div id="product">
    <table id="main-table">
        <thead>
        <tr>
            <th>Plan Name</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Sale Value</th>
            <th>Currency</th>
            <th>Sale Discount</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>

<div id="info">
    <h2 class="header"><b>{{ '' }}</b></h2>
    <h2 class="header"><b>{{ '' }}</b></h2>
</div>

<div id="total">
    <h2 class="header"><b>$ {{ '' }}</b></h2>
    <h2 class="header"><b>$ {{ '' }}</b></h2>
    <h2 class="header" style="padding-top:48px;font-size:1.8rem;text-shadow:8px 8px 16px rgba(0,0,0,.4)">
        <b>$ {{ '' }}</b>
    </h2>
</div>

</body>
</html>
