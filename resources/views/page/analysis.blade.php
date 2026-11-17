
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FeedbackRise - Analysis</title>

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ time() }}">

    <!-- Shared Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}?v={{ time() }}">

    <!-- Analysis CSS -->
    <link rel="stylesheet" href="{{ asset('css/page/analysis.css') }}?v={{ time() }}">
</head>

<body>

    <div class="page">

        <!-- SHARED NAVBAR -->
        @include('components.navbar', ['active' => 'home'])


        <!-- ANALYSIS CONTENT -->
        <main class="analysis-page">

            <h1 class="analysis-title">
                Analysis results and feedback recommendations
            </h1>

            <p
                id="analysisStatus"
                class="analysis-status"
                role="status"
                aria-live="polite"
                hidden
            ></p>

            <div id="analysisDetails" hidden>

                <!-- TOP SECTION -->
                <section class="overview-grid">

                    <!-- TOP PRIORITY ISSUE -->
                    <article class="overview-card priority-overview">

                        <h2>Top Priority Issue</h2>

                        <div class="top-issue-box">
                            <h3 id="topIssueTitle">-</h3>
                            <p id="topIssueSummary">-</p>
                        </div>

                        <div class="feedback-total">
                            <h3>Total Feedback</h3>
                            <p id="totalFeedback">0 Feedback</p>
                        </div>

                    </article>


                    <!-- TOP 3 RECOMMENDATIONS -->
                    <article class="overview-card recommendations-overview">

                        <h2>
                            Top 3 Priority Issues &amp; Developer Recommendations
                        </h2>

                        <div
                            id="topRecommendations"
                            class="top-recommendations"
                        ></div>

                    </article>

                </section>


                <!-- DETAIL SECTION -->
                <section class="detail-section">

                    <h2>Detail</h2>

                    <div class="detail-table-wrapper">

                        <table class="detail-table">

                            <thead>
                                <tr>
                                    <th>Issues</th>
                                    <th>Summary Issues</th>
                                    <th>Recommendations</th>
                                </tr>
                            </thead>

                            <tbody id="detailTableBody"></tbody>

                        </table>

                    </div>

                </section>

            </div>


            <!-- EMPTY STATE -->
            <section
                id="analysisEmpty"
                class="analysis-empty"
                hidden
            >

                <p>Belum ada hasil analisis.</p>

                <a href="{{ url('/home') }}" class="back-button">
                    ← New Analysis
                </a>

            </section>

        </main>


        <!-- FOOTER -->
        <footer class="footer">

            <div class="footer-inner">

                <div class="footer-main">

                    <!-- LOGO AND DESCRIPTION -->
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


                    <!-- CONTACT AND NAVIGATION -->
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


                <!-- COPYRIGHT -->
                <div class="footer-bottom">

                    &copy; {{ date('Y') }} FeedbackRise

                    <span class="footer-dot">&bull;</span>

                    All Rights Reserved

                </div>

            </div>

        </footer>

    </div>


    <!-- SHARED NAVBAR JAVASCRIPT -->
    <script
        src="{{ asset('js/components/navbar.js') }}?v={{ time() }}"
        defer
    ></script>

    <!-- ANALYSIS JAVASCRIPT -->
    <script
        src="{{ asset('js/page/analysis.js') }}?v={{ time() }}"
        defer
    ></script>

</body>
</html>
