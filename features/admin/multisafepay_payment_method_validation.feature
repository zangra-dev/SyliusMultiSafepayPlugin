@managing_multisafepay_payment_method
Feature: MultiSafepay payment method validation
    In order to avoid making mistakes when managing a payment method
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a channel named "Web-RUB" in "RUB" currency
        And the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new multisafepay payment method without specifying required configuration
        Given I want to create a new MultiSafepay payment method
        When I name it "MultiSafepay" in "English (United States)"
        And I add it
        Then I should be notified that "API Key" fields cannot be blank
