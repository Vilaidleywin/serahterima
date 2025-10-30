@php
  $s = strtoupper(trim($status ?? ''));
@endphp

@if ($s === 'SUBMITTED')
  <span class="badge-status badge-submitted">SUBMITTED</span>
@elseif ($s === 'REJECTED')
  <span class="badge-status badge-rejected">REJECTED</span>
@else
  <span class="badge-status badge-default">{{ $s ?: 'UNKNOWN' }}</span>
@endif
