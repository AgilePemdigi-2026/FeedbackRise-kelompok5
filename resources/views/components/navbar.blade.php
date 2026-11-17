
@php
    $active = $active ?? '';
@endphp

<header class="navbar">

    <!-- LOGO -->
    <a href="{{ url('/home') }}" class="brand">
        <img
            src="{{ asset('images/logo-feedbackrise.png') }}"
            alt="FeedbackRise"
            class="logo"
        >
    </a>

    <!-- NAVIGATION -->
    <nav class="navigation">

        <a
            href="{{ url('/home') }}"
            class="nav-link {{ $active === 'home' ? 'active' : '' }}"
            @if ($active === 'home') aria-current="page" @endif
        >
            Home
        </a>

        <a
            href="{{ url('/history') }}"
            class="nav-link {{ $active === 'history' ? 'active' : '' }}"
            @if ($active === 'history') aria-current="page" @endif
        >
            History
        </a>

    </nav>

    <!-- PROFILE -->
    <div class="profile-container">

        <button
            type="button"
            class="profile-button"
            id="profileButton"
            aria-label="Profile"
            aria-expanded="false"
        >
            <svg
                width="27"
                height="27"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <circle
                    cx="12"
                    cy="8"
                    r="4"
                    stroke="currentColor"
                    stroke-width="1.6"
                />

                <path
                    d="M4.5 21C4.5 16.8 7.5 14 12 14C16.5 14 19.5 16.8 19.5 21"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                />
            </svg>
        </button>

        <!-- PROFILE DROPDOWN -->
        <div class="profile-menu" id="profileMenu">

            <button type="button" id="logoutButton">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M9 5H5C4.44772 5 4 5.44772 4 6V18C4 18.5523 4.44772 19 5 19H9"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />

                    <path
                        d="M13 8L17 12L13 16"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M17 12H9"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />
                </svg>

                <span>Logout</span>

            </button>

        </div>

    </div>

</header>
