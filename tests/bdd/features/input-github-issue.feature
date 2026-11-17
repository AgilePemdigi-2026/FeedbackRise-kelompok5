
Feature: Input GitHub Issue and View Analysis Results

  Background:
    Given the user is on the Home page at "/home"

  Scenario: Successfully analyze a GitHub Issue
    Given the GitHub Issue input field is displayed
    When the user enters a valid GitHub repository Issues URL
    And the user clicks the "Analyze" button
    Then the system retrieves GitHub Issue information
    And the system processes the issues using AI Service
    And the system generates issue analysis and development recommendations
    And the user is redirected to "/analysis"
    And the analysis results are displayed

  Scenario: Analyze without entering a GitHub Issue link
    Given the GitHub Issue input field is displayed
    When the user leaves the GitHub Issue input empty
    And the user clicks the "Analyze" button
    Then the browser displays a required field validation message
    And the form submission is prevented
    And the user remains on "/home"

  Scenario: GitHub Issue cannot be processed
    Given the GitHub Issue input field is displayed
    When the user enters a valid GitHub repository Issues URL that cannot be processed
    And the user clicks the "Analyze" button
    Then the system displays an error message
    And the Analyze button is enabled again
    And the user remains on "/home"
