MenuBundle
=============

The `MenuBundle` means easy-to-implement and feature-rich menus in your Symfony application!

## Installation

### Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require desarrolla2/menu-bundle
```
This command requires you to have Composer installed globally, as explained
in the `installation chapter` of the Composer documentation.

### Enable the Bundle


Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Knp\Bundle\Desarrolla2\MenuBundle(),
        );

        // ...
    }

    // ...
}
```
    
## Create your first menu    

An example builder class would look like this:

```php

<?php

namespace AdminBundle\Menu;

use Desarrolla2\MenuBundle\Menu\MenuInterface;

class MainMenu implements MenuInterface
{
    public function getMenu()
    {
        return [
            'class' => 'sidebar-menu',
            'items' => [
                [
                    'name' => 'Users',
                    'icon' => 'fa fa-user',

                    'items' => [
                        [
                            'name' => 'Admins',
                            'route' => 'admin_core_user_admin_list',
                            'active' => [
                                'admin_core_user_admin_[\w]+',
                            ],
                        ],
                        [
                            'name' => 'Clients',
                            'route' => 'admin_core_user_client_list',
                            'active' => [
                                'admin_core_user_client_[\w]+',
                            ],
                        ],
                    ],
                ],                
                [
                    'name' => 'Groups',
                    'route' => 'admin_core_group_list',
                    'icon' => 'fa fa-users',
                    'active' => [
                        'admin_core_group_[\w]+',
                        '_admin.group.[\w\.]',
                    ],
                ],
            ],
        ];
    }
}
```


## Render

To actually render the menu, just do the following from anywhere in any template:

```html+jinja
{{ renderMenu('TeacherBundle\\Menu\\MainMenu','sidebar') }}
```

If you are defined your menu as a service, you can render as follow:

```html+jinja
{{ renderMenu('my.menu.service.name','sidebar') }}
```
