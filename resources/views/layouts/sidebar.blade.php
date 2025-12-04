<div class="sidebar">
    <ul>
        @foreach($menuItems as $item)
            <li>
                <a href="{{ route($item['route']) }}">
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
