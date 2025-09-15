@extends('layouts.dashboard')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Application</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $p)
                <tr>
                    <td>#{{ $p->id }}</td>
                    <td>#{{ $p->application_id }}</td>
                    <td>${{ number_format(($p->amount_cents ?? 0)/100, 2) }}</td>
                    <td><span class="badge bg-{{ $p->status === 'completed' ? 'success' : 'secondary' }}">{{ $p->status }}</span></td>
                    <td>{{ optional($p->paid_at)->format('M d, Y h:ia') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted p-4">No payments yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
