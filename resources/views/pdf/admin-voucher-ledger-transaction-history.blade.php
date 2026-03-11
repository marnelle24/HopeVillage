<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - {{ $entry->adminVoucher?->name ?? 'Admin Voucher' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #374151; }
        .header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 16px; }
        .header img { height: 75px; width: auto; }
        .flex { display: flex; align-items: center; gap: 16px; }
        h1 { font-size: 18px; color: #111827; margin: 0; padding: 0; }
        h2 { font-size: 13px; color: #4b5563; margin: 16px 0 8px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        tr:nth-child(even) { background: #f9fafb; }
        .meta { margin-bottom: 20px; color: #6b7280; font-size: 10px; }
        .meta span { display: block; margin: 2px 0; }
        .total { font-weight: 600; margin-top: 12px; font-size: 20px; }
        .no-data { padding: 24px; text-align: center; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <table style="padding-bottom: 16px;">
            <tr style="padding-bottom: 16px;">
                <td>
                    <img src="{{ public_path('hv-logo.png') }}" alt="Hope Village">
                </td>
                <td>
                    <p style="font-size: 20px; font-weight: bold; margin:0; padding:0;">Hope Village Kaki Bukit Recreation Centre</p>
                    <p style="font-size: 12px; margin:0; padding:0;">Address: 7 Kaki Bukit Ave 3, #01-110, Singapore 415814</p>
                </td>
            </tr>
        </table>
        <br />
        <br />
        <h1>Admin Voucher Transaction History</h1>
    </div>

    <div class="meta">
        <span><strong>Merchant:</strong> {{ $entry->merchant?->name ?? '—' }}</span>
        <span><strong>Voucher:</strong> {{ $entry->adminVoucher?->name ?? '—' }} ({{ $entry->adminVoucher?->voucher_code ?? '—' }})</span>
        <span><strong>Period:</strong> {{ $entry->period_month->format('F Y') }}</span>
        <span><strong>Total Dispensed:</strong> ${{ number_format((float) $entry->total_amount_dispensed, 2) }}</span>
        <span><strong>Total Reimbursed:</strong> ${{ number_format((float) $entry->total_reimbursed, 2) }}</span>
        <span><strong>Outstanding Balance:</strong> ${{ number_format((float) $entry->outstanding_balance, 2) }}</span>
    </div>

    <h2>Voucher Redemption Transactions</h2>

    @if ($transactions->isEmpty())
        <div class="no-data">No transactions found for this period.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member Name</th>
                    <th>Member Email</th>
                    <th>Redeemed At</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $index => $tx)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $tx->member_name ?? '—' }}</td>
                        <td>{{ $tx->member_email ?? '—' }}</td>
                        <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($tx->redeemed_at)->format('M d, Y H:i') }}</td>
                        <td style="text-align: right;">${{ number_format((float) $tx->amount_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 600; font-size: 20px;">Total:</td>
                    <td style="text-align: right; font-weight: 600; font-size: 20px;">${{ number_format((float) $transactions->sum('amount_cost'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if ($reimbursements->isNotEmpty())
        <hr style="margin: 20px 0;" />
        <h2 style="font-size: 20px;">Voucher Reimbursement Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reimbursed At</th>
                    <th>Notes</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reimbursements as $index => $reimbursement)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($reimbursement->reimbursed_at)->format('M d, Y') }}</td>
                        <td>{{ $reimbursement->notes ?? '—' }}</td>
                        <td style="text-align: right;">${{ number_format((float) $reimbursement->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600; font-size: 20px;">Total:</td>
                    <td style="text-align: right; font-weight: 600; font-size: 20px;">${{ number_format((float) $reimbursements->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif
</body>
</html>
