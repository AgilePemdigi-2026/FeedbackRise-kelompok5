<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FeedbackRise - Home</title>

    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ time() }}">
</head>

<body>

    <div class="page">

        <header class="navbar">

            <!-- Logo (nama FEEDBACKRISE sudah ada di gambar logo) -->
            <a href="{{ url('/home') }}" class="brand">

                <img
                    src="{{ asset('images/logo-feedbackrise.png') }}"
                    alt="FeedbackRise"
                    class="logo"
                >

            </a>

            <nav class="navigation">

                <a
                    href="#"
                    class="nav-link active"
                >
                    Home
                </a>

                <a
                    href="#"
                    class="nav-link"
                >
                    History
                </a>

            </nav>

            <div class="profile-container">

                <button
                    type="button"
                    class="profile-button"
                    id="profileButton"
                    aria-label="Profile"
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

                <div
                    class="profile-menu"
                    id="profileMenu"
                >
                    <button
                        type="button"
                        id="logoutButton"
                    >
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


        <main>

            <!-- Hero Section -->
            <section class="hero">

                <h1>
                    What would you like to<br>
                    analyze today?
                </h1>

                <form
                    class="issue-form"
                    id="issueForm"
                    action="#"
                    method="POST"
                >

                    @csrf

                    <input
                        type="text"
                        name="issue_url"
                        id="issueUrl"
                        placeholder="Input Issue URL"
                        autocomplete="off"
                        required
                    >

                    <button
                        type="submit"
                        class="submit-button"
                        id="analyzeButton"
                        aria-label="Analyze Issue"
                    >
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12 18V6"
                                stroke="white"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                            <path
                                d="M7 11L12 6L17 11"
                                stroke="white"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>

                </form>

            </section>


            <!-- FeedbackRise Introduction Section -->
            <section class="feedbackrise-intro">

                <!-- Center Heading -->
                <div class="feedbackrise-intro-heading">

                    <h2>
                        Understand what your customers need
                        <span>Improve what matters</span>
                    </h2>

                </div>


                <!-- Content -->
                <div class="feedbackrise-intro-content">

                    <!-- Text -->
                    <div class="feedbackrise-intro-text">

                        <h1>
                            FEEDBACKRISE
                        </h1>

                        <p>
                            FEEDBACKRISE is a platform where customer feedback is
                            analyzed, categorized by common issues, prioritized based
                            on importance and transformed into actionable
                            recommendations for product improvement.
                        </p>

                    </div>


                    <!-- Illustration -->
                    <div class="feedbackrise-intro-image">

                        <img
                            src="{{ asset('images/icons-orang.png') }}"
                            alt="FeedbackRise Illustration"
                        >

                    </div>

                </div>

            </section>


            <!-- Our Features Section -->
            <section class="features">

                <div class="features-inner">

                    <div class="features-header">

                        <h2 class="features-title">
                            OUR <span>FEATURES</span>
                        </h2>

                        <p class="features-subtitle">
                            Everything you need to turn customer feedback
                            into clear insights and actionable improvements.
                        </p>

                    </div>


                    <div class="features-grid">

                        <!-- Smart Issue Grouping -->
                        <article class="feature-card feature-card--grouping">

                            <h3>Smart Issue Grouping</h3>

                            <p>
                                Groups similar feedback into relevant
                                issue categories for easier analysis.
                            </p>

                        </article>


                        <!-- Priority Detection -->
                        <article class="feature-card feature-card--priority">

                            <h3>Priority Detection</h3>

                            <p>
                                Determines the most important issues based
                                on frequency, severity, trend, and impact.
                            </p>

                        </article>


                        <!-- Actionable Recommendations -->
                        <article class="feature-card feature-card--actions">

                            <h3>Actionable Recommendations</h3>

                            <p>
                                Provides suggested actions to help developers
                                address high-priority issues.
                            </p>

                        </article>

                    </div>

                </div>

            </section>


            <!-- How It Works Section -->
            <section class="features how-it-works">

                <div class="features-inner">

                    <div class="features-header">

                        <h2 class="features-title">
                            HOW IT <span>WORKS</span>
                        </h2>

                        <p class="features-subtitle">
                            See how customer feedback is transformed into
                            clear insights and actionable recommendations
                            in just a few simple steps.
                        </p>

                    </div>


                    <div class="features-grid">

                        <!-- Step 1 -->
                        <article class="how-card how-card--input">

                            <h3>Input Customer Feedback</h3>

                            <p>
                                Upload or enter customer reviews,
                                complaints, or requests.
                            </p>

                        </article>


                        <!-- Step 2 -->
                        <article class="how-card how-card--analyze">

                            <h3>Analyze and Prioritize</h3>

                            <p>
                                Groups similar feedback and
                                identifies issue priorities.
                            </p>

                        </article>


                        <!-- Step 3 -->
                        <article class="how-card how-card--insights">

                            <h3>Get Insights and Recommendations</h3>

                            <p>
                                View key issues, priorities, and
                                recommended actions.
                            </p>

                        </article>

                    </div>

                </div>

            </section>

        </main>


        <!-- Footer -->
        <footer class="footer">

            <div class="footer-inner">

                <div class="footer-main">

                    <!-- Logo dan deskripsi -->
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


                    <!-- Kontak dan navigasi -->
                    <div class="footer-info">

                        <div class="footer-contact">

                            <div class="footer-contact-item">

                                <img
                                    src="{{ asset('images/icon-telf.png') }}"
                                    alt=""
                                    class="footer-contact-icon"
                                >

                                <div class="footer-contact-text">
                                    <span class="footer-contact-label">Phone</span>

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
                                    <span class="footer-contact-label">Email</span>

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
                                <li><a href="{{ url('/home') }}">Home</a></li>
                                <li><a href="#">History</a></li>
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


    <script src="{{ asset('js/page/home.js') }}?v={{ time() }}"></script>

</body>
</html>