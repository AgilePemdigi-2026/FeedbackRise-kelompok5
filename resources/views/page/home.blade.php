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

            <div class="brand">

                <img
                    src="{{ asset('images/logo-feedbackrise.png') }}"
                    alt="FeedbackRise"
                    class="logo"
                >

                <span class="brand-name">
                    FEEDBACKRISE
                </span>

            </div>

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

        </main>

    </div>


    <script src="{{ asset('js/page/home.js') }}?v={{ time() }}"></script>

</body>
</html>