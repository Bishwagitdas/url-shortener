@extends('layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="text-center">Analytics for <a href="{{ url($shortened->short_code) }}" target="_blank">{{ url($shortened->short_code) }}</a></h2>
        <p class="text-center">Total Clicks: <strong>{{ $shortened->clicks }}</strong></p>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clicks as $click)
                    <tr>
                        <td>{{ $click->ip_address }}</td>
                        <td>{{ $click->user_agent }}</td>
                        <td>{{ $click->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
