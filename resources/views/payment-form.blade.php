<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Paytm...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #00BAF2;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader"></div>
    <h2>Redirecting to Paytm...</h2>
    <p>Please wait, you will be redirected to Paytm to complete your payment.</p>
    
    <form id="paytmForm" method="post" action="{{ $action }}" name="paytmForm">
        @foreach($params as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
    </form>
    
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('paytmForm').submit();
        });
    </script>
</body>
</html>
