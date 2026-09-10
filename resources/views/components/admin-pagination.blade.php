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
        $elements = method_exists($paginator, 'getElements') ? $paginator->getElements() : [];
    @endphp

    @if($paginator->hasPages())
        <nav aria-label="Pagination">
            <ul class="pagination pagination-modern mb-0">
                {{-- Previous --}}
                @if($current <= 1 || !$prevUrl)
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="#" data-page="{{ (int)$current - 1 }}" rel="prev">‹</a></li>
                @endif

                {{-- Page Numbers (Laravel paginator elements) --}}
                @foreach($elements as $element)
                    @if(is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                    @elseif(is_array($element))
                        @foreach($element as $page => $url)
                            @php $active = ((int)$page === (int)$current); @endphp
                            @if($active)
                                <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                            @else
                                @if($url)
                                    @php
                                        // Make pagination behave like /admin/residents (API-driven): clicking should not follow the link.
                                        // Frontend can use [data-page] to load the requested page.
                                        $dataPage = $page;
                                    @endphp
                                    <li class="page-item"><a class="page-link" href="#" data-page="{{ $dataPage }}">{{ $page }}</a></li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $page }}</span></li>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if(!$paginator->hasMorePages() || !$nextUrl)
                    <li class="page-item disabled"><span class="page-link">›</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="#" data-page="{{ (int)$current + 1 }}" rel="next">›</a></li>
                @endif
            </ul>
        </nav>
    @endif

    <style>
        /* Singular modern pagination styling for all admin pages */
        .pagination-modern .page-link {
            border: 1px solid #e5e7eb;
            border-radius: 10px !important;
            color: #6b7280;
            font-weight: 700;
            padding: 0.5rem 0.75rem;
            background: #fff;
        }
        .pagination-modern .page-item.active .page-link {
            background: #1055C9;
            border-color: #1055C9;
            color: #fff;
            box-shadow: 0 8px 20px rgba(16, 85, 201, 0.18);
        }
        .pagination-modern .page-item:not(.disabled) .page-link:hover {
            border-color: #1055C9;
            color: #1055C9;
        }
        .pagination-modern .page-item.disabled .page-link {
            background: #f9fafb;
            color: #9ca3af;
        }
    </style>
@endif

