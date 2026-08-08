{{-- Onglets entre les sous-rubriques de "Boutique en ligne". D'autres
     entrées (Catalogue, Commandes, Livraison, Paiement...) seront ajoutées
     ici au fur et à mesure des phases suivantes. --}}
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.store.general.*') ? 'active' : '' }}" href="{{ route('admin.store.general.edit') }}">
      <i class="bi bi-sliders me-1"></i>Général
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.store.appearance.*') ? 'active' : '' }}" href="{{ route('admin.store.appearance.edit') }}">
      <i class="bi bi-palette me-1"></i>Apparence
    </a>
  </li>
  <li class="nav-item ms-auto">
    <a class="nav-link" href="{{ route('store.home') }}" target="_blank" rel="noopener">
      <i class="bi bi-box-arrow-up-right me-1"></i>Voir la boutique
    </a>
  </li>
</ul>
