<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    @if(Auth::check())
                        <div class="text-center mt-4">
                            <h5>Welcome, {{ Auth::user()->registration_number }}!</h5>
                            <p>Enable Data Sharing: {{ Auth::user()->enable_data_sharing ? 'Yes' : 'No' }}</p>
                            <p>Opt In for Research: {{ Auth::user()->opt_in_for_research ? 'Yes' : 'No' }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Logout</button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4">You are not logged in. <a href="{{ route('web-login.form') }}">Go to Login</a></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
