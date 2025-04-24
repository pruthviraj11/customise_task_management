<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($activityLogs as $log)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No activities found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="pagination-wrapper mt-2 d-flex justify-content-center">
    {{ $activityLogs->links('pagination::bootstrap-4') }}
</div>
