<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FeedbackRise - History</title>

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ time() }}">
    
    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}?v={{ time() }}">
    
    <!-- History CSS -->
    <link rel="stylesheet" href="{{ asset('css/history.css') }}?v={{ time() }}">
</head>

<body>

    <div class="page">

        <!-- Shared Navbar -->
        @include('components.navbar', ['active' => 'history'])

        <main class="history-page">

            <!-- History Title -->
            <h1 class="history-title">
                Analysis History
            </h1>

            <p class="history-subtitle">
                Review your previous feedback analyses and revisit
                insights whenever you need them.
            </p>

            <!-- Total + Search -->
            <div class="history-toolbar">

                <span id="historyCount" class="history-total">
                    0 analyses in total
                </span>

                <div class="history-search">

                    <input
                        type="search"
                        id="historySearch"
                        placeholder="Search your analysis..."
                        autocomplete="off"
                        aria-label="Search your analysis"
                    >

                    <svg
                        class="history-search-icon"
                        width="28"
                        height="28"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20L16.5 16.5"></path>
                    </svg>

                </div>

            </div>

            <!-- History List -->
            <section
                id="historyList"
                class="history-list"
                aria-live="polite"
            ></section>

            <!-- Empty State: No History -->
            <section id="historyEmpty" class="history-empty" hidden>

                <p>Belum ada analisis.</p>

                <a href="{{ url('/home') }}" class="history-empty-button">
                    Start New Analysis
                </a>

            </section>

            <!-- Empty State: No Search Result -->
            <p id="historyNoResult" class="history-no-result" hidden>
                Tidak ada analisis yang cocok dengan pencarian Anda.
            </p>

        </main>


        <!-- Footer (sama dengan Home) -->
        <footer class="footer">

            <div class="footer-inner">

                <div class="footer-main">

                    <!-- Logo and Description -->
                    <div class="footer-brand">

                        <img
                            src="{{ asset('images/logo-footer.png') }}"
                            alt="FeedbackRise"
                            class="footer-logo"
                        >

                        <p class="footer-description">
                            AI-powered platform that analyzes customer
                            feedback, identifies key issues, prioritizes
                            them, and provides actionable recommendations
                            for developers.
                        </p>

                    </div>


                    <!-- Contact and Navigation -->
                    <div class="footer-info">

                        <div class="footer-contact">

                            <div class="footer-contact-item">

                                <img
                                    src="{{ asset('images/icon-telf.png') }}"
                                    alt=""
                                    class="footer-contact-icon"
                                >

                                <div class="footer-contact-text">

                                    <span class="footer-contact-label">
                                        Phone
                                    </span>

                                    <a
                                        href="tel:+6281203749284"
                                        class="footer-contact-value"
                                    >
                                        +62 81203749284
                                    </a>

                                </div>

                            </div>


                            <div class="footer-contact-item">

                                <img
                                    src="{{ asset('images/icon-email.png') }}"
                                    alt=""
                                    class="footer-contact-icon"
                                >

                                <div class="footer-contact-text">

                                    <span class="footer-contact-label">
                                        Email
                                    </span>

                                    <a
                                        href="mailto:FeedbackRise@gmail.com"
                                        class="footer-contact-value"
                                    >
                                        FeedbackRise@gmail.com
                                    </a>

                                </div>

                            </div>

                        </div>


                        <nav class="footer-nav">

                            <h3>Navigation</h3>

                            <ul>
                                <li>
                                    <a href="{{ url('/home') }}">Home</a>
                                </li>

                                <li>
                                    <a href="{{ url('/history') }}">History</a>
                                </li>
                            </ul>

                        </nav>

                    </div>

                </div>


                <div class="footer-bottom">

                    &copy; {{ date('Y') }} FeedbackRise

                    <span class="footer-dot">&bull;</span>

                    All Rights Reserved

                </div>

            </div>

        </footer>

    </div>


    <!-- Shared Navbar JavaScript -->
    <script
        src="{{ asset('js/components/navbar.js') }}?v={{ time() }}"
        defer
    ></script>

    <!-- History JavaScript -->
    <script
        src="{{ asset('js/page/history.js') }}?v={{ time() }}"
        defer
    ></script>

</body>
</html>