Feature: Login

  As a User
  I want to log in
  So that I can access FeedbackRise according to my access rights

  Scenario: Login with valid credentials
    Given User is on the Login page at "/login"
    When User enters email/username and password
    And User presses the "Login" button
    Then System should validate the completeness and format of the login data
    And System should verify the email/username and password against the account data
    And System should identify the user's access rights
    And User should be redirected to the main page at "/home" according to the user's access rights

  Scenario: Login with incomplete or invalid data
    Given User is on the Login page at "/login"
    When User enters incomplete or invalid login data
    And User presses the "Login" button
    Then User should see an error message that email/username and password must be entered correctly
    And User should remain on the Login page at "/login"
    When User corrects the email/username and password
    And User presses the "Login" button again
    Then System should validate the login data again
    And User should follow the login process

  Scenario: Login with incorrect email/username or password
    Given User is on the Login page at "/login"
    When User enters an incorrect email/username or password
    And User presses the "Login" button
    Then User should see a message that the email/username or password is incorrect
    And User should remain on the Login page at "/login"
    When User enters the email/username and password again
    And User presses the "Login" button again
    Then System should verify the account credentials
    And User should be redirected to the main page at "/home"

  Scenario: Login with an unregistered account
    Given User is on the Login page at "/login"
    When User enters an email/username that is not registered
    And User presses the "Login" button
    Then User should see a message that the account is not registered
    And User should be redirected to the Registration page at "/register"

  Scenario: Login fails due to a system error
    Given User is on the Login page at "/login"
    When User enters valid email/username and password
    And System encounters an error while accessing the account data
    Then User should see a message that the login cannot be processed
    And User should remain on the Login page at "/login"