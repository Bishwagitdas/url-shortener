@extends('layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="text-center mb-4">URL Shortener</h2>

        <form action="{{ route('url.shorten') }}" method="POST" class="d-flex mb-4">
            @csrf
            <input type="url" name="url" class="form-control me-2" placeholder="Enter long URL" required>
            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>

        @if(session('short_url'))
            <div class="alert alert-success">
                Shortened URL: <a href="{{ session('short_url') }}" target="_blank">{{ session('short_url') }}</a>
            </div>
        @endif

        <h4 class="text-center mt-4">Shortened URLs</h4>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Original URL</th>
                    <th>Short URL</th>
                    <th>Clicks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($urls as $url)
                    <tr>
                        <td>{{ $url->original_url }}</td>
                        <td><a href="{{ url($url->short_code) }}" target="_blank">{{ url($url->short_code) }}</a></td>
                        <td>{{ $url->clicks }}</td>
                        <td>
                            <a href="{{ route('url.analytics', $url->short_code) }}" class="btn btn-info btn-sm">View</a>
                            <form action="{{ route('url.delete', $url->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
