# Project 7 - OpenClassrooms
**Name of the project :** Créez un web service exposant une API (Create a web service exposing an API)

## Badge

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7442a45be8344f659760f35a5d7ca000)](https://www.codacy.com/manual/dokaNc/Projet7-OC/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=dokaNc/Projet7-OC&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a4efc5469ac2a0ff584/maintainability)](https://codeclimate.com/github/dokaNc/Projet7-OC/maintainability)

## Installation
### - Step 1  
Make sure you have Git installed and up to date on your machine

www.git-scm.com  
### - Step 2

Clone the repository on your local server

``git clone https://github.com/dokaNc/Projet7-OC.git``  

### - Step 3

Make sure that composer is installed and up to date on your machine

www.getcomposer.org/doc/00-intro.md  

### - Step 4

After installing composer, please launch ``composer install`` at the root of your project.  
All dependencies will be installed and stored in the folder **/vendor**.

### - Step 5  

Create the database using the file found in the folder ``sql/api_bilemo.sql``.  

### - Step 6  

Modify the accesses to your database in the file ``.env [DATABASE_URL] (line 28)``.  

### - Step 7

Modify 'JWT_PASSPHRASE' in the file ``.env [JWT_PASSPHRASE] (line 34)``

### - Step 8

Generate the SSH keys:
``$ mkdir -p config/jwt``
``$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096``
``$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout``

### - Step 9  

Install the "Postman" software to use the API.
www.postman.com/downloads

### - Step 10  

Using Postman, create a new "user" via the following link: ``/api/register``  

### - Step 11  

From ``PHPMYADMIN``, modify the "roles" column of your "user" account by ``["ROLE_SUPERADMIN"]`` to have full access to the API.   

### - Step 12

You can now use the API by referring to the documentation.
``/doc``
