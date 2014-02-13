# EzBlogBundle

Repository showing you step by step how to create a simple blog in pure eZ Publish 5.

## Install
1. Download and install eZ Publish 5 (>= v2013.07.0), either on [share.ez.no](http://share.ez.no) or directly from Composer.
2. In `src/` folder, create a `Acme/` folder.
3. Go to `src/Acme/` and clone this repository in there. In the end you should have `src/Acme/EzBlogBundle/`.
4. Import `blog_settings.yml` in `ezpublish/config/ezpublish.yml` by adding:
    
    ```
    imports:
         - { resource: "@AcmeEzBlogBundle/Resources/config/blog_settings.yml" }
        
    ```

5. Register `EzBlogBundle` in `ezpublish/ezPublishKernel.php` by adding this line to `$bundles` in `registerBundles()` method:

    ```
     new Acme\EzBlogBundle\AcmeEzBlogBundle(),
    
    ```

6. Generate assets by running the following commands:

    ```
     php ezpublish/console assets:install web --symlink
    ```
    
    ```
     php ezpublish/console cache:clear
    ```

