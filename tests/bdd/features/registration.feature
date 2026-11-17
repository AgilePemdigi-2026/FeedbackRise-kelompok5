Feature: Registration

  As a User
  I want to register an account
  So that I can create an account and access FeedbackRise

  Scenario: Register with valid data
    Given User starts on the Login page for registration at "/login"
    When User selects the "Registration" menu
    Then Registration page should be displayed at "/register"
    And User should see the Registration form
    When User enters valid registration data
    And User clicks the Registration form's "Register" button
    Then System should validate the completeness and format of the data
    And System should check the availability of the email/username
    And System should save the user's account data
    And User should see a registration success notification
    And User should be redirected to the Login page at "/login"

  Scenario: Register with incomplete or invalid data
    Given User is on the Registration page at "/register"
    When User enters incomplete or invalid data
    And User clicks the Registration form's "Register" button
    Then User should see an error message
    And User should be asked to correct the entered data
    When User corrects the entered data
    And User clicks the Registration form's "Register" button again
    Then System should validate the completeness and format of the data again
    And User should follow the registration process

  Scenario: Register with an already registered email/username
    Given User is on the Registration page at "/register"
    When User enters an already registered email/username
    And User clicks the Registration form's "Register" button
    Then System should detect that the email/username is already registered
    And User should see a message that the email/username is already in use
    When User enters another email/username
    And User clicks the Registration form's "Register" button again
    Then System should check the availability of the email/username again
    And User should follow the registration process

  Scenario: Registration fails because the system cannot save the account data
    Given User is on the Registration page at "/register"
    And User has entered valid registration data and the save operation will fail
    When User clicks the Registration form's "Register" button
    Then User should see a registration failure message
    And User should remain on the Registration page at "/register"