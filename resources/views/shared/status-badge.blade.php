@php $s = strtoupper($status); @endphp
<span class="badge badge-status
  {{ $s=='PENDING'?'badge-pending':'' }}
  {{ $s=='DONE'?'badge-done':'' }}
  {{ $s=='FAILED'?'badge-failed':'' }}">
  {{ $s }}
</span>
