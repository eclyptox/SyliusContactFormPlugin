<p align="center">
    <a href="https://www.mangoweb.cz/en/" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/38423357?s=200&v=4"/>
    </a>
</p>
<h1 align="center">
Contact Form Plugin
<br />
    <a href="https://packagist.org/packages/mangoweb-sylius/sylius-contact-form-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/mangoweb-sylius/sylius-contact-form-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/mangoweb-sylius/sylius-contact-form-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/mangoweb-sylius/sylius-contact-form-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/mangoweb-sylius/SyliusContactFormPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/mangoweb-sylius/SyliusContactFormPlugin/master.svg" />
    </a>
</h1>

## Features

* Extend contact form
* Add Message administrative panel
    * conversation history
    * Possibility to respond instantly

## Installation

1. Run `$ composer require mangoweb-sylius/sylius-contact-form-plugin`.
2. Register `\MangoSylius\ContactFormPlugin\MangoSyliusContactFormPlugin` in your Kernel.
3. Import `@MangoSyliusContactFormPlugin/Resources/config/routing.yml` in the routing.yml.
4. Import `@MangoSyliusContactFormPlugin/Resources/config/config.yml` in _sylius.yml.
5. Include template `Resources/views/ContactForm/recaptcha.html.twig` in `@SyliusShop/Contact/request.html.twig`.
6. Define parameters in `.env` file

    ```
    # Define if an email should be send to the customer
    SEND_CUSTOMER_MAIL=
    # Define 'name' field requirement in contact form
    CONTACT_NAME_REQUIRED=
    # Define 'phone' field requirement in contact form
    CONTACT_PHONE_REQUIRED=
    # Recaptcha public key setter for contact form
    RECAPTCHA_PUBLIC_KEY=
    # Recaptcha secret key setter for contact form
    RECAPTCHA_SECRET_KEY=
    ```
   
## Usage

* Log into admin panel
* Click on `Contact` in the `Customer` section in main menu
* Select the conversation you want to answer to
* Write your reply message
* Click `Send` button below

## Development

### Usage

- Create symlink from .env.dist to .env or create your own .env file
- Develop your plugin in `/src`
- See `bin/` for useful commands

### Testing

After your changes you must ensure that the tests are still passing.

```bash
$ composer install
$ bin/console doctrine:schema:create -e test
$ bin/behat.sh
$ bin/phpstan.sh
$ bin/ecs.sh
```

License
-------
This library is under the MIT license.

Credits
-------
Developed by [manGoweb](https://www.mangoweb.eu/).