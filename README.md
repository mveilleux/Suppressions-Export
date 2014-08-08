# Dyn Email Delivery - Suppressions Export

A PHP application to periodically email you a CSV of suppressions added to you Dyn Email Delivery account.

## Usage

After checking out the latest source:

* Add your API key, destination email address and From email address to the suppression_config.ini file.  (Multiple To: addresses can be listed)
* Setup a cron job to execute the main.php application at 02:00 UTC each morning.

Note: Within the main.php file, this program is currently setup to email you twice a week, Monday and Friday for the past few days.

## Contribute

* Please continue to add more tests!
