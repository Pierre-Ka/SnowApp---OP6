# SnowTrick Application
***
Analyse CodeClimate :
https://codeclimate.com/github/Pierre-Ka/SnowApp-OP6

Repository GitHub :
https://github.com/Pierre-Ka/SnowApp-OP6

To run the project you will need to have :
* apache
* php 8
* mysql
* phpMyAdmin
* symfony
***
## Installation
1. Create a new projet and Clone this repository :
```
    git clone https://github.com/Pierre-Ka/SnowApp-OP6.git
    cd SnowApp-OP6/
```
2. Configure Database : 
* Add : DATABASE_URL="mysql://username:password@127.0.0.1:3306/dbname" in the .env files
3. Install the dependencies :
```
    composer install
    php bin/console cache:clear
```
3. Run command :
```
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
```
4. Run server :
```
    symfony server:start
```

You can now connect to the blog at the following URL and enjoy its features.

![Smile](https://www.freepngimg.com/download/face/73751-emoticon-smiley-face-wink-mouth-smile.png)
