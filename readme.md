## INSTALLATION for PRODUCTION

- Clone project and cd to project directory
- Run `cp .env.example .env`
- Config App url, environment, database connection, mailer in file `.env`
- Run `composer install`
- Run `php artisan key:generate`
- Run `php artisan jwt:secret`
- Run `php artisan module:migrate`
- Run `php artisan storage:link`
- Run `npm install`
- Run `npm run prod` or `npm run production`


### For Local development
- Install software in guide `local/README`
- Start Development server by running command `vagrant up`
- Stop server by command `vagrant halt`
- Access SSH server by command `vagrant ssh`
- Project path (in development server) is `/workplace`
- Seed example data by command `php artisan db:seed`
- Build assets (js/css) by command `npm run dev`
- Watching assets change by command `npm run watch-poll`
- Ide helper by command `php artisan ide-helper:generate` and `php artisan ide-helper:meta`


### Access
Api/Web: http://dev.redex.vn  
phpMyAdmin: http://db.redex.vn (user `php` without password)  
DB: name `redex` / user `redex` password `123456`
