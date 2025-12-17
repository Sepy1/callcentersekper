@if ($paginator->hasPages())
<nav>
    <ul class="pagination pagination-sm mb-0">

        {{-- Previous --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() ?? '#' }}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link {{ $page == $paginator->currentPage() ? 'bg-gradient-primary text-white border-0' : '' }}"
                           href="{{ $url }}">
                            {{ $page }}
                        </a>
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() ?? '#' }}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>

    </ul>
</nav>
@endif
