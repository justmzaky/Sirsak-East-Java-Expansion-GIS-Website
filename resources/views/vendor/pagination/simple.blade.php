@if ($paginator->hasPages())
<div style="display:flex;align-items:center;gap:4px">
    @if ($paginator->onFirstPage())
        <span style="display:inline-flex;align-items:center;height:30px;padding:0 10px;border-radius:7px;border:1px solid var(--border);color:var(--text3);font-size:12px">
            <i class="ti ti-chevron-left" style="font-size:13px"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display:inline-flex;align-items:center;height:30px;padding:0 10px;border-radius:7px;border:1px solid var(--border2);color:var(--text2);font-size:12px;text-decoration:none">
            <i class="ti ti-chevron-left" style="font-size:13px"></i>
        </a>
    @endif

    <span style="font-size:12px;color:var(--text2);padding:0 8px">
        Halaman {{ $paginator->currentPage() }}
    </span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display:inline-flex;align-items:center;height:30px;padding:0 10px;border-radius:7px;border:1px solid var(--border2);color:var(--text2);font-size:12px;text-decoration:none">
            <i class="ti ti-chevron-right" style="font-size:13px"></i>
        </a>
    @else
        <span style="display:inline-flex;align-items:center;height:30px;padding:0 10px;border-radius:7px;border:1px solid var(--border);color:var(--text3);font-size:12px">
            <i class="ti ti-chevron-right" style="font-size:13px"></i>
        </span>
    @endif
</div>
@endif
