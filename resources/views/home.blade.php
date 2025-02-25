@extends('layout')

@section('content')
<div class="row justify-content-center">
    <!-- Success and Error Alerts -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @elseif(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="col-md-12">
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

        @if($urls->isEmpty())
            <p class="text-center">No data found.</p>
        @else
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Original URL</th>
                        <th>Short URL</th>
                        <th>Clicks</th>
                        <th>Expired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($urls as $url)
                    <tr>
                        <td>{{ $url->original_url }}</td>
                        <td><a href="{{ url($url->short_code) }}" target="_blank">{{ url($url->short_code) }}</a></td>
                        <td>{{ $url->clicks }}</td>
                        <td>{{ $url->expires_at }}</td>
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
        @endif
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            <div class="pagination-container">
                <ul class="pagination">
                    <li class="page-item {{ $urls->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $urls->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                    @foreach ($urls->getUrlRange(1, $urls->lastPage()) as $page => $url)
                    <li class="page-item {{ $urls->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach
                    <li class="page-item {{ $urls->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $urls->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
