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
2. 
    - Register `\MangoSylius\SyliusContactFormPlugin\MangoSyliusContactFormPlugin` in your Kernel.
    - Or add to `bundles.php` `MangoSylius\SyliusContactFormPlugin\MangoSyliusContactFormPlugin::class => ['all' => true],`
3. Add in the `routing.yml` or `routes.yaml
 ```yaml
mango_sylius_contact_plugin_shop:
    resource: "@MangoSyliusContactFormPlugin/Resources/config/shopRouting.yml"
mango_sylius_contact_plugin_admin:
    prefix: /admin
    resource: "@MangoSyliusContactFormPlugin/Resources/config/adminRouting.yml"
mango_sylius_contact_plugin_account:
    resource: "@MangoSyliusContactFormPlugin/Resources/config/accountRouting.yml"

```
4. Import `@MangoSyliusContactFormPlugin/Resources/config/config.yml` in _sylius.yml.
5. Override the template in SyliusShopBundle:Contact:request.html.twig

   ```twig
   {% extends '@SyliusShop/layout.html.twig' %}
   
   {% form_theme form '@SyliusShop/Form/theme.html.twig' %}
   
   {% block content %}
       <div class="ui hidden divider"></div>
       <div class="ui two column centered stackable grid">
           <div class="column">
               {{ render(controller(
                   'MangoSylius\\SyliusContactFormPlugin\\Controller\\ContactFormController::createContactMessage'
               )) }}
           </div>
       </div>
   {% endblock %}
   
   {% block javascripts %}
       {{ include('@MangoSyliusContactFormPlugin/ContactForm/recaptcha.html.twig') }}
   {% endblock %}
    ```
6. Define parameters in `.env` file

    ```
    # Recaptcha public key setter for contact form
    CONTACT_FORM_RECAPTCHA_PUBLIC_KEY=
    # Recaptcha secret key setter for contact form
    CONTACT_FORM_RECAPTCHA_SECRET_KEY=
    ```
   
## Configuration
Set parameters in parameters.yml
   ```
    mango_sylius_contact_form:
        # Define if an email should be send to the manager when contact form is send
          send_manager_mail: true/false
        # Define if an email should be send to the customer when contact form is send
          send_customer_mail: true/false
        # Define 'name' field requirement in contact form
          name_required: true/false
        # Define 'phone' field requirement in contact form
          phone_required: true/false
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
