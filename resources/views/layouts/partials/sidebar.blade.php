<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">
      <img src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="{{ $entreprise->name }}">
    </div>
    <div>
      <div class="brand-text">{{ $entreprise->name }}</div>
      <div class="brand-sub">SI Boutique</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">Menu principal</div>
    @foreach($menuItems as $item)
      @if(isset($item['children']))
        @php
          $groupId = 'nav-group-' . \Illuminate\Support\Str::slug($item['label']);
          $groupIsActive = collect($item['children'])->contains(fn ($child) => request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])));
        @endphp
        <a href="#{{ $groupId }}" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $groupIsActive ? 'true' : 'false' }}" aria-controls="{{ $groupId }}"
           class="nav-link nav-link--group {{ $groupIsActive ? 'active' : '' }}">
          <i class="bi {{ $item['icon'] }}"></i>
          <span>{{ $item['label'] }}</span>
          <i class="bi bi-chevron-down nav-link__chevron"></i>
        </a>
        <div class="collapse {{ $groupIsActive ? 'show' : '' }}" id="{{ $groupId }}">
          <div class="nav-subgroup">
            @foreach($item['children'] as $child)
              <a href="{{ route($child['route'], $child['params'] ?? []) }}"
                 class="nav-link nav-link--sub {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'active' : '' }}">
                <span>{{ $child['label'] }}</span>
              </a>
            @endforeach
          </div>
        </div>
      @else
        <a href="{{ route($item['route']) }}"
           class="nav-link {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'active' : '' }}">
          <i class="bi {{ $item['icon'] }}"></i>
          <span>{{ $item['label'] }}</span>
        </a>
      @endif
    @endforeach
  </nav>

  <div class="sidebar-footer">
    &copy; {{ date('Y') }} {{ $entreprise->name }} SI
  </div>
</aside>
