# Peter Jackson Opicians

## Local Setup
- Set up variable in the `.env` file if different
- Run `docker-compose up`

## Deploy
- Application root on the server needs to be set to `/public`
- Run `composer install` in root

## Notes
- `wp-config.php` has been modified to use `.env` variables
- The while wordpress installation is in `/public`. This directory should be set as the root on the server.