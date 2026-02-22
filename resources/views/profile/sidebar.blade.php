<div class="account-nav flex-grow-1">
    <h4 class="account-nav__title">Navigation</h4>
    <ul class="account-nav__list">
        <li class="account-nav__item @if($selected == 'dashboard') account-nav__item--active @endif"><a href="/profile/dashboard">Dashboard</a></li>
        <li class="account-nav__item @if($selected == 'edit') account-nav__item--active @endif"><a href="/profile/edit">Profilni o'zgartirish</a></li>
        <li class="account-nav__item @if($selected == 'messages') account-nav__item--active @endif"><a href="/profile/messages">Xabarlar</a></li>
        <li class="account-nav__item @if($selected == 'listings') account-nav__item--active @endif"><a href="/profile/listings">E'lonlar</a></li>
        <li class="account-nav__item @if($selected == 'change-password') account-nav__item--active @endif"><a href="/profile/change-password">Parolni o'zgartirish</a></li>
        <li class="account-nav__divider" role="presentation"></li>
        <li class="account-nav__item"><a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Chiqish</a></li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>

    </ul>
</div>
