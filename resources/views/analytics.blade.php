@extends('layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <h2 class="text-center">Analytics for <a href="{{ url($shortened->short_code) }}" target="_blank">{{ url($shortened->short_code) }}</a></h2>
        <p class="text-center">Total Clicks: <strong>{{ $totalClicks }}</strong></p> <!-- Total Clicks -->

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Created At</th>
                </tr>
            </thead>
            @if($urls->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No data found.</td>
                </tr>
            @else
                <tbody>
                    @foreach($urls as $index =>$click)
                        <tr>
                            <td>{{ ($urls->currentPage() - 1) * $urls->perPage() + $loop->iteration }}</td>
                            <td>{{ $click->ip_address }}</td>
                            <td>{{ $click->user_agent }}</td>
                            <td>{{ $click->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        </table>

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
