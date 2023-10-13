<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('procurement') }}" class="nav-link {{ Request::is('procurement') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cart-plus"></i>
        <p>Procurement</i></p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('procurement') }}" class="nav-link {{ Request::is('clients') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Clients</i></p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('procurement') }}" class="nav-link {{ Request::is('budgets') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-contract"></i>
        <p>Budgets</i></p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('procurement') }}" class="nav-link {{ Request::is('projects') ? 'active' : '' }}">
        <i class="nav-icon fas fa-shopping-cart"></i>
        <p>Projects</i></p>
    </a>
</li>