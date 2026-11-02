
Feature: Forgot Password

  As a User
  I want to reset my password
  So that I can regain access to my FeedbackRise account

  Scenario: Successfully request password reset
    Given User is on the Forgot Password page at "/forgot-password"
    When User enters a registered email
    And User presses the "Send Email" button
    Then System should send a password reset email through Firebase
    And User should see a password reset confirmation message

  Scenario: Submit an empty email
    Given User is on the Forgot Password page at "/forgot-password"
    When User leaves the email field empty
    And User presses the "Send Email" button
    Then System should prevent the form submission
    And User should see an email validation message

  Scenario: Submit an invalid email format
    Given User is on the Forgot Password page at "/forgot-password"
    When User enters an invalid email format
    And User presses the "Send Email" button
    Then System should prevent the form submission
    And User should see an email validation message

  Scenario: Firebase fails to send password reset email
    Given User is on the Forgot Password page at "/forgot-password"
    When User enters a registered email
    And Firebase encounters an error while sending the reset email
    Then User should see a password reset failure message
    And User should remain on the Forgot Password page at "/forgot-password"
