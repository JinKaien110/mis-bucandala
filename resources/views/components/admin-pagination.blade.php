@php
/**
 * Admin number pagination component
 * Use: <x-admin-pagination :paginator="$paginator" />
 */
@endphp

@if(!isset($paginator) || !$paginator)
    {{-- nothing --}}
@else
    @php
        $current = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : 1;
        $prevUrl = method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : null;
        $nextUrl = method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : null;
        $last = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : 1;
    @endphp

    @if($paginator->hasPages())
        <nav aria-label="Pagination">
            <ul class="pagination pagination-modern mb-0">
                {{-- Previous --}}
                @if($current <= 1 || !$prevUrl)
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $prevUrl }}" data-page="{{ (int)$current - 1 }}" rel="prev">‹</a></li>
                @endif

                {{-- Match the full numbered pagination shown on /admin/residents. --}}
                @for($page = 1; $page <= $last; $page++)
                    @if((int)$page === (int)$current)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}" data-page="{{ $page }}">{{ $page }}</a></li>
                    @endif
                @endfor

                {{-- Next --}}
                @if(!$paginator->hasMorePages() || !$nextUrl)
                    <li class="page-item disabled"><span class="page-link">›</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $nextUrl }}" data-page="{{ (int)$current + 1 }}" rel="next">›</a></li>
                @endif
            </ul>
        </nav>
    @endif

@endif
